<script lang="ts">
    import type { FacultyProps } from '$lib/types';

    import { router, Link, useForm, usePoll } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Checkbox } from '$lib/components/ui/checkbox/index';
    import { Toggle } from '$lib/components/ui/toggle';
    import * as Select from '$lib/components/ui/select';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import * as Popover from '$lib/components/ui/popover/index.js';
    import Table from '$lib/components/table/Table.svelte';
    import Icon from '@iconify/svelte';
    import { onMount } from 'svelte';

    const { start, stop } = usePoll(2000);

    export let faculties: FacultyProps[];
    export let years: number[];
    export let isAdmin: boolean;

    let selected: { [x: number]: boolean | 'indeterminate' } = faculties.reduce(
        (selectedRecordAcc, { faculty_id }) => {
            return { ...selectedRecordAcc, [faculty_id]: false };
        },
        {},
    );
    $: hasSelected = Object.values(selected).some((val) => val);

    function bulkDisable() {
        if (confirm('Do you really want to disable the selected users?')) {
            const selectedRoleIds = Object.entries(selected)
                .filter(([_, isSelected]) => isSelected)
                .map(([index, _]) => Number(index));

            router.put(
                '/api/disable/faculties',
                {
                    selectedRoleIds,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        for (const selectedRoleId of selectedRoleIds) {
                            selected[selectedRoleId] = false;
                        }
                    },
                },
            );
        }
    }

    let searchQuery: string;
    function search() {
        // Pause Polling
        stop();

        router.get(
            '/dashboard/faculties',
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                year: filterYear,
                show: filterUser,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    let filterYear: number = new Date().getFullYear();
    function filterByYear(newYear) {
        // Pause Polling
        stop();

        filterYear = newYear;

        router.get(
            '/dashboard/faculties',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                show: filterUser,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    let filterUser = 'all';
    function filterByUser(newFilterUser) {
        // Pause Polling
        stop();

        filterUser = newFilterUser;

        router.get(
            '/dashboard/faculties',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                show: filterUser,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    onMount(() => {
        const facultyListColumns = localStorage.getItem('facultyListColumns');
        if (facultyListColumns) {
            showColumns = JSON.parse(facultyListColumns);
        } else {
            showColumns = {
                firstName: true,
                middleName: false,
                lastName: true,
                email: true,
                section: true,
            };
            localStorage.setItem(
                'facultyListColumns',
                JSON.stringify(showColumns),
            );
        }
    });

    let showColumns = {};

    $: if (Object.keys(showColumns).length !== 0) {
        localStorage.setItem('facultyListColumns', JSON.stringify(showColumns));
    }

    let sortColumn = 'last_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
        // Pause Polling
        stop();

        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/faculties`,
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                year: filterYear,
                show: filterUser,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    let userFormElement;
    let isAddModalOpen;

    let userForm = useForm({
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        section: null,
        year: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/faculty', {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen = false;
            },
        });
    }

    let formUserRoleId = null;
    function openAddForm() {
        $userForm.first_name = null;
        $userForm.middle_name = null;
        $userForm.last_name = null;
        $userForm.email = null;
        $userForm.section = null;
        $userForm.year = filterYear;

        formUserRoleId = null;
        isAddModalOpen = true;
    }

    function openUpdateForm(facultyId: number) {
        const faculty = faculties.find(
            (faculty) => faculty.faculty_id === facultyId,
        );

        $userForm.first_name = faculty.first_name;
        $userForm.middle_name = faculty.middle_name;
        $userForm.last_name = faculty.last_name;
        $userForm.email = faculty.email;
        $userForm.section = faculty.section;
        $userForm.year = faculty.year;

        formUserRoleId = facultyId;
        isAddModalOpen = true;
    }

    function updateUser() {
        if (!formUserRoleId) {
            return;
        }
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post(`/api/update/faculty/${formUserRoleId}`, {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen = false;
            },
        });
    }

    let isExportDropdownOpen;
    let isExportModalOpen;

    let exportForm = useForm({
        year: null,
        include_enabled: null,
        include_disabled: null,
    });

    let exportFormRoute;
    let exportFormText;
    function openExportForm(exportFormName: string) {
        switch (exportFormName) {
            case 'faculty-list':
                exportFormRoute = 'list';
                exportFormText = 'Faculty List';
                break;
            default:
                return;
        }

        $exportForm.year = filterYear;
        $exportForm.include_enabled = true;
        $exportForm.include_disabled = false;

        isExportDropdownOpen = false;
        isExportModalOpen = true;
    }

    let exportFormElement;
    function redirectExportForm() {
        if (!exportFormElement.checkValidity()) {
            exportFormElement.reportValidity();
            return;
        }

        // todo 1: use useForm().get() instead of form action for exportForm
        // need to find a way to call exportForm.get() on a new tab instance, instead of the current tab
        // otherwise, form action has to be used instead, and <Input type="checkbox" /> instead of <Checkbox />
        // todo 2: find a way to call onSuccess for actions in a different tab?
        /*

        $exportForm.get(`/export/faculties/${exportFormRoute}`, {
            preserveScroll: true,
            onSuccess: () => {
                isExportDropdownOpen = false;
                isExportModalOpen = false;
            },
        });
        */

        isExportDropdownOpen = false;
        isExportModalOpen = false;
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Faculty List" />

    <div class="flex flex-col items-start justify-between gap-4 md:flex-row">
        <Link class="w-full md:w-auto" href="/dashboard" method="get">
            <Button class="flex w-full flex-row gap-2"
                ><Icon icon="lets-icons:back" />Back to Dashboard</Button
            ></Link
        >
        <div
            class="flex w-full flex-col items-start gap-2 md:w-auto md:flex-row"
        >
            <div class="flex w-full flex-row gap-2 md:flex-col">
                {#if isAdmin}
                    <Link
                        class="w-full md:w-auto"
                        href="/import/faculties/upload"
                        ><Button
                            class="flex w-full flex-row gap-2 md:w-auto"
                            variant="outline"
                            ><Icon icon="uil:import" />Import</Button
                        ></Link
                    >
                {/if}

                <Dialog.Root bind:open={isExportModalOpen}>
                    <Button
                        class="flex w-full flex-row gap-2 md:w-auto"
                        variant="outline"
                        on:click={() => openExportForm('faculty-list')}
                        ><Icon icon="uil:export" />Export</Button
                    >
                    <Dialog.Content
                        class="h-full max-h-full max-w-full overflow-auto sm:h-auto sm:max-h-[80vh] sm:max-w-lg"
                    >
                        <Dialog.Header>
                            <Dialog.Title>Export {exportFormText}</Dialog.Title>
                        </Dialog.Header>
                        <form
                            action="/export/faculties/{exportFormRoute}"
                            class="flex flex-col gap-4"
                            target="_blank"
                            bind:this={exportFormElement}
                            on:submit={redirectExportForm}
                        >
                            <div
                                class="grid grid-cols-[auto,1fr] items-center gap-4"
                            >
                                <Label for="export_year">Year</Label>
                                <div class="flex flex-col">
                                    <Input
                                        id="export_year"
                                        name="year"
                                        type="number"
                                        bind:value={$exportForm.year}
                                    />
                                    {#if $exportForm.errors.year}
                                        <ErrorText>
                                            {$exportForm.errors.year}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="export_include_enabled"
                                    >Include Enabled Faculty Accounts</Label
                                >
                                <div class="flex flex-col items-center">
                                    <Input
                                        id="export_include_enabled"
                                        name="include_enabled"
                                        type="checkbox"
                                        value="1"
                                        bind:checked={
                                            $exportForm.include_enabled
                                        }
                                    />
                                    {#if $exportForm.errors.include_enabled}
                                        <ErrorText>
                                            {$exportForm.errors.include_enabled}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="export_include_disabled"
                                    >Include Disabled Faculty Accounts</Label
                                >
                                <div class="flex flex-col items-center">
                                    <Input
                                        id="export_include_disabled"
                                        name="include_disabled"
                                        type="checkbox"
                                        value="1"
                                        bind:checked={
                                            $exportForm.include_disabled
                                        }
                                    />
                                    {#if $exportForm.errors.include_disabled}
                                        <ErrorText>
                                            {$exportForm.errors
                                                .include_disabled}
                                        </ErrorText>
                                    {/if}
                                </div>
                            </div>

                            <Dialog.Footer class="flex flex-col-reverse gap-2">
                                <Dialog.Close>
                                    <Button class="w-full" variant="outline"
                                        >Cancel</Button
                                    >
                                </Dialog.Close>
                                <Button type="submit"
                                    >Export {exportFormText}</Button
                                >
                            </Dialog.Footer>
                        </form>
                    </Dialog.Content>
                </Dialog.Root>
            </div>

            <div class="flex w-full flex-row gap-2 md:flex-col">
                <Link
                    class="w-full md:w-auto"
                    href="/add-multiple/faculties/upload"
                    ><Button
                        class="flex w-full flex-row gap-2 md:w-auto"
                        variant="outline"
                        ><Icon icon="uil:import" />Add Multiple</Button
                    ></Link
                >

                <Dialog.Root bind:open={isAddModalOpen}>
                    <Button
                        class="flex w-full flex-row gap-2 md:w-auto"
                        on:click={openAddForm}
                        ><Icon icon="material-symbols:add" />Add Faculty</Button
                    >
                    <Dialog.Content
                        class="h-full max-h-full max-w-full overflow-auto sm:h-auto sm:max-h-[80vh] sm:max-w-lg"
                    >
                        <Dialog.Header>
                            <Dialog.Title
                                >{formUserRoleId
                                    ? 'Edit Faculty'
                                    : 'Add Faculty'}</Dialog.Title
                            >
                        </Dialog.Header>
                        <form
                            bind:this={userFormElement}
                            class="flex flex-col gap-4"
                            on:submit|preventDefault={formUserRoleId
                                ? updateUser
                                : addUser}
                        >
                            <div
                                class="grid grid-cols-[auto,1fr] items-center gap-4"
                            >
                                <Label for="year">
                                    <Required />Year
                                </Label>
                                <div class="flex flex-col">
                                    <Input
                                        name="year"
                                        type="number"
                                        bind:value={$userForm.year}
                                        required
                                    />
                                    {#if $userForm.errors.year}
                                        <ErrorText>
                                            {$userForm.errors.year}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="first_name"
                                    ><Required />First Name</Label
                                >
                                <div class="flex flex-col">
                                    <Input
                                        name="first_name"
                                        type="text"
                                        bind:value={$userForm.first_name}
                                        required
                                    />
                                    {#if $userForm.errors.first_name}
                                        <ErrorText>
                                            {$userForm.errors.first_name}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="middle_name">Middle Name</Label>
                                <div class="flex flex-col">
                                    <Input
                                        name="middle_name"
                                        type="text"
                                        bind:value={$userForm.middle_name}
                                    />
                                    {#if $userForm.errors.middle_name}
                                        <ErrorText>
                                            {$userForm.errors.middle_name}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="last_name"
                                    ><Required />Last Name</Label
                                >
                                <div class="flex flex-col">
                                    <Input
                                        name="last_name"
                                        type="text"
                                        bind:value={$userForm.last_name}
                                        required
                                    />
                                    {#if $userForm.errors.last_name}
                                        <ErrorText>
                                            {$userForm.errors.last_name}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="email"><Required />Email</Label>
                                <div class="flex flex-col">
                                    <Input
                                        name="email"
                                        type="email"
                                        bind:value={$userForm.email}
                                        required
                                    />
                                    {#if $userForm.errors.email}
                                        <ErrorText>
                                            {$userForm.errors.email}
                                        </ErrorText>
                                    {/if}
                                </div>

                                <Label for="section">Section</Label>
                                <div class="flex flex-col">
                                    <Input
                                        name="section"
                                        type="text"
                                        bind:value={$userForm.section}
                                    />
                                    {#if $userForm.errors.section}
                                        <ErrorText>
                                            {$userForm.errors.section}
                                        </ErrorText>
                                    {/if}
                                </div>
                            </div>
                            <Dialog.Footer class="flex flex-col-reverse gap-2">
                                <Dialog.Close>
                                    <Button class="w-full" variant="outline"
                                        >Cancel</Button
                                    >
                                </Dialog.Close>
                                <Button type="submit"
                                    >{formUserRoleId
                                        ? 'Update Faculty'
                                        : 'Add Faculty'}</Button
                                >
                            </Dialog.Footer>
                        </form>
                    </Dialog.Content>
                </Dialog.Root>
            </div>
        </div>
    </div>

    {#if isAdmin}
        <div
            class="flex w-full flex-col items-center justify-end gap-4 md:flex-row"
        >
            <Button
                on:click={bulkDisable}
                class="flex w-full flex-row gap-2 md:w-auto"
                variant="destructive"
                disabled={!hasSelected}>Disable Selected</Button
            >
        </div>
    {/if}

    <!-- Name Search Bar -->
    <div class="flex flex-col gap-2 md:flex-row">
        <Input
            type="text"
            placeholder="Search by Name"
            class="w-auto grow"
            bind:value={searchQuery}
            on:keyup={search}
        />

        <div class="flex flex-row flex-wrap items-center justify-center gap-2">
            <Popover.Root>
                <Popover.Trigger asChild let:builder>
                    <Button builders={[builder]} variant="outline"
                        >Filter Columns</Button
                    >
                </Popover.Trigger>
                <Popover.Content
                    class="flex max-h-80 w-fit flex-col gap-1 overflow-auto"
                >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.firstName}>First Name</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.middleName}
                        >Middle Name</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.lastName}>Last Name</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.email}>Email</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.section}>Section</Toggle
                    >
                </Popover.Content>
            </Popover.Root>

            <Select.Root
                selected={{ label: 'All', value: 'all' }}
                onSelectedChange={(v) => {
                    v && filterByUser(v.value);
                }}
            >
                <Select.Trigger class="flex w-fit flex-row gap-2 px-4">
                    <strong>Show:</strong>
                    <Select.Value />
                </Select.Trigger>
                <Select.Content>
                    <Select.Item value="all">All</Select.Item>
                    <Select.Item value="enabled">Enabled</Select.Item>
                    <Select.Item value="disabled">Disabled</Select.Item>
                </Select.Content>
            </Select.Root>
            <Select.Root
                selected={{ label: filterYear.toString(), value: filterYear }}
                onSelectedChange={(v) => {
                    v && filterByYear(v.value);
                }}
            >
                <Select.Trigger class="flex w-fit flex-row gap-2 px-4">
                    <strong>Year:</strong>
                    <Select.Value placeholder="Year" />
                </Select.Trigger>
                <Select.Content>
                    {#each years as year}
                        <Select.Item value={year}>{year}</Select.Item>
                    {/each}
                </Select.Content>
            </Select.Root>
        </div>
    </div>

    <!-- List of Faculties -->
    <Table>
        <TableRow header>
            {#if isAdmin}
                <TableColumnHeader />
            {/if}
            {#if showColumns.firstName}
                <TableColumnHeader
                    isActive={sortColumn === 'first_name'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('first_name')}
                >
                    First Name
                </TableColumnHeader>
            {/if}
            {#if showColumns.middleName}
                <TableColumnHeader
                    isActive={sortColumn === 'middle_name'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('middle_name')}
                >
                    Middle Name
                </TableColumnHeader>
            {/if}
            {#if showColumns.lastName}
                <TableColumnHeader
                    isActive={sortColumn === 'last_name'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('last_name')}
                >
                    Last Name
                </TableColumnHeader>
            {/if}
            {#if showColumns.email}
                <TableColumnHeader
                    isActive={sortColumn === 'email'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('email')}
                >
                    Email
                </TableColumnHeader>
            {/if}
            {#if showColumns.section}
                <TableColumnHeader
                    isActive={sortColumn === 'section'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('section')}
                >
                    Section
                </TableColumnHeader>
            {/if}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each faculties as faculty (faculty.faculty_id)}
            {@const {
                faculty_id,
                first_name,
                middle_name,
                last_name,
                email,
                section,
                is_disabled,
            } = faculty}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[faculty_id])}
            >
                {#if isAdmin}
                    <TableCell
                        ><Checkbox
                            bind:checked={selected[faculty_id]}
                        /></TableCell
                    >
                {/if}
                {#if showColumns.firstName}
                    <TableCell>{first_name}</TableCell>
                {/if}
                {#if showColumns.middleName}
                    <TableCell>{middle_name}</TableCell>
                {/if}
                {#if showColumns.lastName}
                    <TableCell>{last_name}</TableCell>
                {/if}
                {#if showColumns.email}
                    <TableCell>{email}</TableCell>
                {/if}
                {#if showColumns.section}
                    <TableCell>{section ?? ''}</TableCell>
                {/if}
                <TableCell
                    ><div
                        class="flex flex-row items-center justify-center gap-2"
                    >
                        <Button
                            class="w-20 {colorVariants.blue}"
                            on:click={() => openUpdateForm(faculty_id)}
                            >Edit</Button
                        >
                        {#if isAdmin}
                            {#if is_disabled}
                                <Link
                                    href="/api/enable/faculty/{faculty_id}"
                                    as="button"
                                    preserveScroll
                                    method="put"
                                    ><Button class="w-20 {colorVariants.green}"
                                        >Enable</Button
                                    ></Link
                                >
                            {:else}
                                <Button
                                    class="w-20 {colorVariants.red}"
                                    on:click={() => {
                                        if (
                                            confirm(
                                                'Do you really want to disable this user?',
                                            )
                                        ) {
                                            router.put(
                                                `/api/disable/faculty/${faculty_id}`,
                                                {},
                                                { preserveScroll: true },
                                            );
                                        }
                                    }}>Disable</Button
                                >
                            {/if}
                        {/if}
                    </div></TableCell
                >
            </TableRow>
        {/each}
    </Table>
</div>
