<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let supervisors;
    export let form_infos;
    export let companies;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/admin/supervisors',
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
            `/dashboard/admin/supervisors`,
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

    function setCompany(evt, supervisorId) {
        const companyId = evt.target.value;

        router.put(
            `/supervisors/${supervisorId}/assign/company/${companyId}`,
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let addFormElement;
    let isModalOpen;

    function openModal() {
        isModalOpen = true;
    }

    let addUserForm = useForm({
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        company_id: null,
    });

    function addUser() {
        if (!addFormElement.checkValidity()) {
            addFormElement.reportValidity();
            return;
        }
        $addUserForm.post(
            '/dashboard/admin/supervisors/add',
            {},
            {
                preserveScroll: true,
            },
        );
    }

    Inertia.on('success', () => {
        isModalOpen = false;
    });

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

    <!-- Name Search Bar -->
    <div class="flex flex-row content-center justify-center">
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
            on:keyup={search}
        />
    </div>

    <!-- List of Supervisors -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Supervisors</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <ColumnHeader
                        isActive={sortColumn === 'last_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('last_name')}
                        first
                    >
                        Last Name
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'first_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('first_name')}
                    >
                        First Name
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'company_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('company_name')}
                    >
                        Company
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'email'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('email')}
                    >
                        Email
                    </ColumnHeader>
                    {#each Object.entries(form_infos) as [_, form_info]}
                        {@const { form_name } = form_info}
                        <ColumnHeader>{form_name}</ColumnHeader>
                    {/each}
                    <ColumnHeader>Actions</ColumnHeader>
                </tr>
                {#each supervisors as supervisor (supervisor.supervisor_id)}
                    {@const {
                        supervisor_id,
                        first_name,
                        last_name,
                        email,
                        company_id: supervisor_company_id,
                        form_statuses,
                    } = supervisor}
                    <tr class="border-t-2 {borderColor}">
                        <td class="border-r-2 p-2 {borderColor}">{last_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setCompany(evt, supervisor_id)}
                                >
                                    <option
                                        selected={!supervisor_company_id}
                                        value
                                    />
                                    {#each companies as company}
                                        {@const { id, company_name } = company}
                                        <option
                                            selected={id ===
                                                supervisor_company_id}
                                            value={id}>{company_name}</option
                                        >
                                    {/each}
                                </select>
                            </div>
                        </td>
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    isAdmin
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/answer/{supervisor_id}"
                                />
                            </td>
                        {/each}
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><Link
                                href="/dashboard/admin/supervisors/delete/{supervisor_id}"
                                class="rounded-xl bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                method="delete">Delete</Link
                            >
                        </td>
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-between">
        <button
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            on:click={openModal}>Add Supervisor</button
        >
        <Link
            href="/dashboard"
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>

<Modal bind:isOpen={isModalOpen}>
    <form
        bind:this={addFormElement}
        class="flex flex-col gap-4"
        on:submit|preventDefault={addUser}
    >
        <div class="grid grid-cols-[auto,1fr] items-center gap-4">
            <label for="first_name"><Required />First Name</label>
            <div class="flex flex-col">
                <input
                    name="first_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.first_name}
                    required
                />
                {#if $addUserForm.errors.first_name}
                    <ErrorText>
                        {$addUserForm.errors.first_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="middle_name"><Required />Middle Name</label>
            <div class="flex flex-col">
                <input
                    name="middle_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.middle_name}
                    required
                />
                {#if $addUserForm.errors.middle_name}
                    <ErrorText>
                        {$addUserForm.errors.middle_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="last_name"><Required />Last Name</label>
            <div class="flex flex-col">
                <input
                    name="last_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.last_name}
                    required
                />
                {#if $addUserForm.errors.last_name}
                    <ErrorText>
                        {$addUserForm.errors.last_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="email"><Required />Email</label>
            <div class="flex flex-col">
                <input
                    name="email"
                    type="email"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.email}
                    required
                />
                {#if $addUserForm.errors.email}
                    <ErrorText>
                        {$addUserForm.errors.email}
                    </ErrorText>
                {/if}
            </div>

            <label for="company">Company</label>
            <div class="flex flex-col">
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    name="company"
                    bind:value={$addUserForm.company_id}
                >
                    <option selected value />
                    {#each companies as company}
                        {@const { id, company_name } = company}
                        <option value={id}>{company_name}</option>
                    {/each}
                </select>
                {#if $addUserForm.errors.company}
                    <ErrorText>
                        {$addUserForm.errors.company}
                    </ErrorText>
                {/if}
            </div>
        </div>
        <input
            class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value="Add Supervisor"
        />
    </form>
</Modal>
