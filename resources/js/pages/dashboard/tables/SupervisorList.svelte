<script lang="ts">
    import type { Company, FormIdName, SupervisorProps } from '$lib/types';

    import { router, Link, useForm, usePoll } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Checkbox } from '$lib/components/ui/checkbox/index';
    import { Toggle } from '$lib/components/ui/toggle';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import * as Select from '$lib/components/ui/select';
    import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
    import * as Popover from '$lib/components/ui/popover/index.js';
    import Icon from '@iconify/svelte';
    import { onMount } from 'svelte';

    const { start, stop } = usePoll(2000);

    export let supervisors: SupervisorProps[];
    export let formIdNames: FormIdName[];
    export let companies: Company[];
    export let years: number[];
    export let isAdmin: boolean;

    let selected: { [x: number]: boolean | 'indeterminate' } =
        supervisors.reduce((selectedRecordAcc, { supervisor_id }) => {
            return { ...selectedRecordAcc, [supervisor_id]: false };
        }, {});
    $: hasSelected = Object.values(selected).some((val) => val);

    function bulkDisable() {
        if (confirm('Do you really want to disable the selected users?')) {
            const selectedRoleIds = Object.entries(selected)
                .filter(([_, isSelected]) => isSelected)
                .map(([index, _]) => Number(index));

            router.put(
                '/api/disable/supervisors',
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

    function getFormFromId(targetFormId: number) {
        return formIdNames.find(({ form_id }) => form_id === targetFormId);
    }

    let searchQuery: string;
    function search() {
        // Pause Polling
        stop();

        router.get(
            '/dashboard/supervisors',
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
            '/dashboard/supervisors',
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
            '/dashboard/supervisors',
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
        const supervisorListColumns = localStorage.getItem('supervisorListColumns')
        if (supervisorListColumns) {
            showColumns = JSON.parse(supervisorListColumns);
        } else {
            showColumns = {
                firstName: true,
                middleName: false,
                lastName: true,
                company: true,
                email: true,
                forms: true,
            };
            localStorage.setItem('supervisorListColumns', JSON.stringify(showColumns));
        }
    });

    let showColumns = {};

    $: if (Object.keys(showColumns).length !== 0) {
        localStorage.setItem('supervisorListColumns', JSON.stringify(showColumns));
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
            `/dashboard/supervisors`,
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

    function setCompany(supervisorId: number, companyId: number) {
        // Pause Polling
        stop();

        router.put(
            `/supervisors/${supervisorId}/assign/company/${companyId}`,
            {},
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
        company_id: null,
        year: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/supervisor', {
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
        $userForm.company_id = null;
        $userForm.year = filterYear;

        formUserRoleId = null;
        isAddModalOpen = true;
    }

    function openUpdateForm(supervisorId: number) {
        const supervisor = supervisors.find(
            (supervisor) => supervisor.supervisor_id === supervisorId,
        );

        $userForm.first_name = supervisor.first_name;
        $userForm.middle_name = supervisor.middle_name;
        $userForm.last_name = supervisor.last_name;
        $userForm.email = supervisor.email;
        $userForm.company_id = supervisor.company_id;
        $userForm.year = supervisor.year;

        formUserRoleId = supervisorId;
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
        $userForm.post(`/api/update/supervisor/${formUserRoleId}`, {
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
        include_with_company: null,
        include_without_company: null,
    });

    let exportFormRoute;
    let exportFormText;
    function openExportForm(exportFormName: string) {
        switch (exportFormName) {
            case 'supervisor-list':
                exportFormRoute = 'list';
                exportFormText = 'Supervisor List';
                break;
            default:
                return;
        }

        $exportForm.year = filterYear;
        $exportForm.include_enabled = true;
        $exportForm.include_disabled = false;
        $exportForm.include_with_company = true;
        $exportForm.include_without_company = true;

        isExportDropdownOpen = false;
        isExportModalOpen = true;
    }

    let exportFormElement;
    function redirectExportForm() {
        if (!exportFormElement.checkValidity()) {
            exportFormElement.reportValidity();
            return;
        }

        $exportForm.get(`/export/supervisors/${exportFormRoute}`, {
            preserveScroll: true,
            onSuccess: () => {
                isExportDropdownOpen = false;
                isExportModalOpen = false;
            },
        });
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

    <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
        <Link class="w-full md:w-auto" href="/dashboard" method="get">
            <Button class="flex w-full flex-row gap-2"
                ><Icon icon="lets-icons:back" />Back to Dashboard</Button
            ></Link
        >
        <div
            class="flex w-full flex-col items-center gap-4 md:w-auto md:flex-row"
        >
            <Link class="w-full md:w-auto" href="/import/supervisors/upload"
                ><Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link
                class="w-full md:w-auto"
                href="/add-multiple/supervisors/upload"
                ><Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <Dialog.Root bind:open={isExportModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"
                    on:click={() => openExportForm('supervisor-list')}
                    ><Icon icon="uil:export" />Export</Button
                >
                <Dialog.Content
                    class="h-full max-h-full max-w-full overflow-auto sm:h-auto sm:max-h-[80vh] sm:max-w-lg"
                >
                    <Dialog.Header>
                        <Dialog.Title>Export {exportFormText}</Dialog.Title>
                    </Dialog.Header>
                    <form
                        action="/export/supervisors/{exportFormRoute}"
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
                                >Include Enabled Supervisor Accounts</Label
                            >
                            <div class="flex flex-col items-center">
                                <Checkbox
                                    id="export_include_enabled"
                                    name="include_enabled"
                                    value="1"
                                    bind:checked={$exportForm.include_enabled}
                                />
                                {#if $exportForm.errors.include_enabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_enabled}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_disabled"
                                >Include Disabled Supervisor Accounts</Label
                            >
                            <div class="flex flex-col items-center">
                                <Checkbox
                                    id="export_include_disabled"
                                    name="include_disabled"
                                    value="1"
                                    bind:checked={$exportForm.include_disabled}
                                />
                                {#if $exportForm.errors.include_disabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_disabled}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_with_company"
                                >Include Supervisors With Company</Label
                            >
                            <div class="flex flex-col items-center">
                                <Checkbox
                                    id="export_include_with_company"
                                    name="include_with_company"
                                    value="1"
                                    bind:checked={
                                        $exportForm.include_with_company
                                    }
                                />
                                {#if $exportForm.errors.include_with_company}
                                    <ErrorText>
                                        {$exportForm.errors
                                            .include_with_company}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_without_company"
                                >Include Supervisors Without Company</Label
                            >
                            <div class="flex flex-col items-center">
                                <Checkbox
                                    id="export_include_without_company"
                                    name="include_without_company"
                                    value="1"
                                    bind:checked={
                                        $exportForm.include_without_company
                                    }
                                />
                                {#if $exportForm.errors.include_without_company}
                                    <ErrorText>
                                        {$exportForm.errors
                                            .include_without_company}
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

            <Dialog.Root bind:open={isAddModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    on:click={openAddForm}
                    ><Icon icon="material-symbols:add" />Add Supervisor</Button
                >
                <Dialog.Content
                    class="h-full max-h-full max-w-full overflow-auto sm:h-auto sm:max-h-[80vh] sm:max-w-lg"
                >
                    <Dialog.Header>
                        <Dialog.Title
                            >{formUserRoleId
                                ? 'Edit Supervisor'
                                : 'Add Supervisor'}</Dialog.Title
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

                            <Label for="last_name"><Required />Last Name</Label>
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

                            <Label for="company">Company</Label>
                            <div class="flex flex-col">
                                <Select.Root
                                    selected={!$userForm.company_id
                                        ? { label: '-', value: '' }
                                        : {
                                              label: companies.find(
                                                  (company) =>
                                                      company.company_id ===
                                                      $userForm.company_id,
                                              ).company_name,
                                              value: $userForm.company_id,
                                          }}
                                    onSelectedChange={(v) => {
                                        v && ($userForm.company_id = v.value);
                                    }}
                                >
                                    <Select.Trigger class="px-4">
                                        <Select.Value placeholder="Company" />
                                    </Select.Trigger>
                                    <Select.Content>
                                        <Select.Item value="">-</Select.Item>
                                        {#each companies as company}
                                            {@const {
                                                company_id,
                                                company_name,
                                            } = company}
                                            <Select.Item value={company_id}
                                                >{company_name}</Select.Item
                                            >
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
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
                                    ? 'Update Supervisor'
                                    : 'Add Supervisor'}</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>
        </div>
    </div>

    <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
        <Button
            on:click={bulkDisable}
            class="flex w-full flex-row gap-2 md:w-auto"
            variant="destructive"
            disabled={!hasSelected}>Disable Selected</Button
        >

        <div
            class="flex flex-row flex-wrap items-center justify-center gap-2 md:gap-4"
        >
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
                        bind:pressed={showColumns.company}>Company</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.email}>Email</Toggle
                    >
                    <Toggle
                        class="justify-start py-2"
                        bind:pressed={showColumns.forms}
                        >Form Submissions</Toggle
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

    <!-- Name Search Bar -->
    <div class="flex flex-row content-center justify-center">
        <Input
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
            on:keyup={search}
        />
    </div>

    <!-- List of Supervisors -->
    <Table>
        <TableRow header>
            <TableColumnHeader />
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
                    isActive={sortColumn === 'first_name'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('first_name')}
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
            {#if showColumns.company}
                <TableColumnHeader
                    isActive={sortColumn === 'company_name'}
                    isAscending={sortIsAscending}
                    clickHandler={() => sortByColumn('company_name')}
                >
                    Company
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
            {#if showColumns.forms}
                {#each formIdNames as formIdName}
                    {@const { form_name } = formIdName}
                    <TableColumnHeader>{form_name}</TableColumnHeader>
                {/each}
            {/if}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each supervisors as supervisor (supervisor.supervisor_id)}
            {@const {
                supervisor_id,
                first_name,
                middle_name,
                last_name,
                email,
                company_name: supervisor_company_name,
                company_id: supervisor_company_id,
                form_id_statuses,
                is_disabled,
            } = supervisor}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[supervisor_id])}
            >
                <TableCell
                    ><Checkbox
                        bind:checked={selected[supervisor_id]}
                    /></TableCell
                >
                {#if showColumns.firstName}
                    <TableCell>{first_name}</TableCell>
                {/if}
                {#if showColumns.middleName}
                    <TableCell>{middle_name}</TableCell>
                {/if}
                {#if showColumns.lastName}
                    <TableCell>{last_name}</TableCell>
                {/if}
                {#if showColumns.company}
                    <TableCell>
                        <Select.Root
                            selected={!supervisor_company_id
                                ? { label: '-', value: null }
                                : {
                                      label: supervisor_company_name,
                                      value: supervisor_company_id,
                                  }}
                            onSelectedChange={(v) => {
                                v && setCompany(supervisor_id, v.value);
                            }}
                        >
                            <Select.Trigger class="px-4">
                                <Select.Value placeholder="Company" />
                            </Select.Trigger>
                            <Select.Content>
                                <Select.Item value="">-</Select.Item>
                                {#each companies as company}
                                    {@const { company_id, company_name } =
                                        company}
                                    <Select.Item value={company_id}
                                        >{company_name}</Select.Item
                                    >
                                {/each}
                            </Select.Content>
                        </Select.Root>
                    </TableCell>
                {/if}
                {#if showColumns.email}
                    <TableCell>{email}</TableCell>
                {/if}
                {#if showColumns.forms}
                    {#each form_id_statuses as form_id_status}
                        {@const { form_id, status } = form_id_status}
                        <TableCell center>
                            {#if isAdmin}
                                <StatusCell
                                    {isAdmin}
                                    {status}
                                    href="/form/{getFormFromId(form_id)
                                        .short_name}/answer/{supervisor_id}"
                                />
                            {:else}
                                <StatusCell
                                    {isAdmin}
                                    {status}
                                    href="/form/{getFormFromId(form_id)
                                        .short_name}/view/{supervisor_id}"
                                />
                            {/if}
                        </TableCell>
                    {/each}
                {/if}
                <TableCell
                    ><div class="flex flex-row justify-center gap-2">
                        <Button
                            class="w-20 {colorVariants.blue}"
                            on:click={() => openUpdateForm(supervisor_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/supervisor/{supervisor_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                class="w-20 "
                                ><Button class="w-full {colorVariants.green}"
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
                                            `/api/disable/supervisor/${supervisor_id}`,
                                            {},
                                            { preserveScroll: true },
                                        );
                                    }
                                }}>Disable</Button
                            >
                        {/if}
                    </div></TableCell
                >
            </TableRow>
        {/each}
    </Table>
</div>
