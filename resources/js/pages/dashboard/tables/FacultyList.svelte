<script lang="ts">
    import type { FacultyProps } from '$lib/types';

    import { router, Link, useForm } from '@inertiajs/svelte';

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
    import * as Dialog from '$lib/components/ui/dialog/index';
    import Table from '$lib/components/table/Table.svelte';
    import Icon from '@iconify/svelte';

    export let faculties: FacultyProps[];

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
        router.get(
            '/dashboard/faculties',
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let sortColumn = 'last_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
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
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let userFormElement;
    let isModalOpen;

    let userForm = useForm({
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        section: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/faculty', {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen = false;
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

        formUserRoleId = null;
        isModalOpen = true;
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

        formUserRoleId = facultyId;
        isModalOpen = true;
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
                isModalOpen = false;
            },
        });
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Faculty List" />

    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div class="flex w-full flex-row items-center gap-4 sm:w-auto">
            <Link href="/dashboard" method="get">
                <Button class="flex flex-row gap-2"
                    ><Icon icon="lets-icons:back" />Back to Dashboard</Button
                ></Link
            >
        </div>
        <div
            class="flex w-full flex-col items-center gap-4 sm:w-auto sm:flex-row"
        >
            <Button
                on:click={bulkDisable}
                class="flex w-full flex-row gap-2 sm:w-auto"
                variant="destructive"
                disabled={!hasSelected}>Disable Selected</Button
            >
            <Link href="/import/faculties/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link href="/add-multiple/faculties/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <Button
                href="/export/faculties/list"
                class="flex w-full flex-row gap-2 sm:w-auto"
                target="_blank"
                variant="outline">Export Faculties</Button
            >
            <Dialog.Root bind:open={isModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    on:click={openAddForm}
                    ><Icon icon="material-symbols:add" />Add Faculty</Button
                >
                <Dialog.Content>
                    <Dialog.Header>
                        <Dialog.Title>Add Faculty</Dialog.Title>
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
                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
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

    <!-- Name Search Bar -->
    <div class="flex flex-row content-center justify-center">
        <Input
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
            on:keyup={search}
        />
    </div>

    <!-- List of Faculties -->
    <Table>
        <TableRow header>
            <TableColumnHeader />
            <TableColumnHeader
                isActive={sortColumn === 'last_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('last_name')}
            >
                Last Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'first_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('first_name')}
            >
                First Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'email'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('email')}
            >
                Email
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'section'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('section')}
            >
                Section
            </TableColumnHeader>
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each faculties as faculty (faculty.faculty_id)}
            {@const {
                faculty_id,
                first_name,
                last_name,
                email,
                section,
                is_disabled,
            } = faculty}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[faculty_id])}
            >
                <TableCell
                    ><Checkbox bind:checked={selected[faculty_id]} /></TableCell
                >
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>{email}</TableCell>
                <TableCell>{section ?? ''}</TableCell>
                <TableCell
                    ><div
                        class="flex flex-row items-center justify-center gap-2"
                    >
                        <Button
                            class={colorVariants.blue}
                            on:click={() => openUpdateForm(faculty_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/faculty/{faculty_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                ><Button class="w-full {colorVariants.green}"
                                    >Enable</Button
                                ></Link
                            >
                        {:else}
                            <Button
                                class={colorVariants.red}
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
                    </div></TableCell
                >
            </TableRow>
        {/each}
    </Table>
</div>
