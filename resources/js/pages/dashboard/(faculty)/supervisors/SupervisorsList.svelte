<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import { colorVariants } from '$lib/customVariants';
    import { Button } from '$lib/components/ui/button';
    import { Input } from '$lib/components/ui/input/index';
    import * as Select from '$lib/components/ui/select';
    import Icon from '@iconify/svelte';

    export let supervisors;
    export let form_infos;
    export let companies;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/supervisors',
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
    function sortByColumn(newSortColumn) {
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
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    function setCompany(supervisorId, companyId) {
        router.put(
            `/supervisors/${supervisorId}/assign/company/${companyId}`,
            {},
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
        company_id: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/supervisor', {
            preserveScroll: true,
        });
    }

    function openAddForm() {
        $userForm.first_name = null;
        $userForm.middle_name = null;
        $userForm.last_name = null;
        $userForm.email = null;
        $userForm.company_id = null;

        isModalOpen = true;
    }

    let formUserRoleId = null;
    function openUpdateForm(supervisorId) {
        const supervisor = supervisors.find(
            (supervisor) => supervisor.supervisor_id === supervisorId,
        );

        $userForm.first_name = supervisor.first_name;
        $userForm.middle_name = supervisor.middle_name;
        $userForm.last_name = supervisor.last_name;
        $userForm.email = supervisor.email;
        $userForm.company_id = supervisor.company_id;

        formUserRoleId = supervisorId;
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
        $userForm.post(`/api/update/supervisor/${formUserRoleId}`, {
            preserveScroll: true,
        });
    }

    Inertia.on('success', () => {
        isModalOpen = false;
    });
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

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
                class="flex w-full flex-row gap-2 sm:w-auto"
                on:click={openAddForm}
                ><Icon icon="material-symbols:add" />Add Supervisor</Button
            >
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
                isActive={sortColumn === 'company_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('company_name')}
            >
                Company
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'email'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('email')}
            >
                Email
            </TableColumnHeader>
            {#each Object.entries(form_infos) as [_, form_info]}
                {@const { form_name } = form_info}
                <TableColumnHeader>{form_name}</TableColumnHeader>
            {/each}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each supervisors as supervisor (supervisor.supervisor_id)}
            {@const {
                supervisor_id,
                first_name,
                last_name,
                email,
                company_name: supervisor_company_name,
                company_id: supervisor_company_id,
                form_statuses,
                is_disabled,
            } = supervisor}
            <TableRow disabled={is_disabled}>
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>
                    <Select.Root
                        selected={!supervisor_company_id
                            ? { label: '-', value: '' }
                            : {
                                  label: supervisor_company_name,
                                  value: supervisor_company_id,
                              }}
                        onSelectedChange={(v) => {
                            console.log(supervisor_company_name);
                            v && setCompany(supervisor_id, v.value);
                        }}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Company" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="">-</Select.Item>
                            {#each companies as company}
                                {@const { id, company_name } = company}
                                <Select.Item value={id}
                                    >{company_name}</Select.Item
                                >
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>{email}</TableCell>
                {#each Object.entries(form_statuses) as [form_id, form_status]}
                    <TableCell center
                        ><StatusCell
                            status={form_status}
                            href="/form/{form_infos[form_id]
                                .short_name}/view/{supervisor_id}"
                        />
                    </TableCell>
                {/each}
                <TableCell
                    ><div class="flex flex-row gap-2">
                        <Button
                            class={colorVariants.blue}
                            on:click={() => openUpdateForm(supervisor_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/supervisor/{supervisor_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                class="grow"
                                ><Button class="w-full {colorVariants.green}"
                                    >Enable</Button
                                ></Link
                            >
                        {:else}
                            <Button
                                class="w-full grow {colorVariants.red}"
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

<Modal bind:isOpen={isModalOpen}>
    <form
        bind:this={userFormElement}
        class="flex flex-col gap-4"
        on:submit|preventDefault={formUserRoleId ? updateUser : addUser}
    >
        <div class="grid grid-cols-[auto,1fr] items-center gap-4">
            <label for="first_name"><Required />First Name</label>
            <div class="flex flex-col">
                <input
                    name="first_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.first_name}
                    required
                />
                {#if $userForm.errors.first_name}
                    <ErrorText>
                        {$userForm.errors.first_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="middle_name">Middle Name</label>
            <div class="flex flex-col">
                <input
                    name="middle_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.middle_name}
                />
                {#if $userForm.errors.middle_name}
                    <ErrorText>
                        {$userForm.errors.middle_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="last_name"><Required />Last Name</label>
            <div class="flex flex-col">
                <input
                    name="last_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.last_name}
                    required
                />
                {#if $userForm.errors.last_name}
                    <ErrorText>
                        {$userForm.errors.last_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="email"><Required />Email</label>
            <div class="flex flex-col">
                <input
                    name="email"
                    type="email"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.email}
                    required
                />
                {#if $userForm.errors.email}
                    <ErrorText>
                        {$userForm.errors.email}
                    </ErrorText>
                {/if}
            </div>

            <label for="company">Company</label>
            <div class="flex flex-col">
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    name="company"
                    bind:value={$userForm.company_id}
                >
                    <option selected value />
                    {#each companies as company}
                        {@const { id, company_name } = company}
                        <option value={id}>{company_name}</option>
                    {/each}
                </select>
                {#if $userForm.errors.company}
                    <ErrorText>
                        {$userForm.errors.company}
                    </ErrorText>
                {/if}
            </div>
        </div>
        <input
            class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value={formUserRoleId ? 'Update Supervisor' : 'Add Supervisor'}
        />
    </form>
</Modal>
