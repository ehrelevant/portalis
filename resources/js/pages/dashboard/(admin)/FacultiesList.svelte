<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let faculties;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/admin/faculties',
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
            `/dashboard/admin/faculties`,
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
        $userForm.post(
            '/api/add/faculty',
            {},
            {
                preserveScroll: true,
            },
        );
    }

    function openAddForm() {
        $userForm.first_name = null;
        $userForm.middle_name = null;
        $userForm.last_name = null;
        $userForm.email = null;
        $userForm.section = null;

        isModalOpen = true;
    }

    let formUserRoleId = null;
    function openUpdateForm(facultyId) {
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
        $userForm.post(
            `/api/update/faculty/${formUserRoleId}`,
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
    <Header txt="Faculties List" />

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

    <!-- List of Faculties -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Faculties</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-gray-900"
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
                        isActive={sortColumn === 'email'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('email')}
                    >
                        Email
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'section'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('section')}
                    >
                        Section
                    </ColumnHeader>
                    <ColumnHeader>Actions</ColumnHeader>
                </tr>
                {#each faculties as faculty (faculty.faculty_id)}
                    {@const {
                        faculty_id,
                        first_name,
                        last_name,
                        email,
                        section,
                        is_disabled,
                    } = faculty}
                    <tr
                        class="border-t-2 {borderColor} {is_disabled
                            ? 'bg-black text-gray-300'
                            : ''}"
                    >
                        <td class="border-r-2 p-2 {borderColor}">{last_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{first_name}</td
                        >
                        <td class="border-l-2 p-2 {borderColor}">{email}</td>
                        <td class="border-l-2 p-2 {borderColor}"
                            >{section ?? ''}</td
                        >
                        <div
                            class="flex flex-row items-center justify-center gap-2 border-l-2 p-2"
                        >
                            <td class="text-center {borderColor}"
                                ><button
                                    class="h-full rounded-xl bg-floating-blue-light p-2 hover:opacity-90 dark:bg-floating-blue"
                                    on:click={() => openUpdateForm(faculty_id)}
                                    >Edit</button
                                >
                            </td>
                            <td class="text-center {borderColor}">
                                {#if is_disabled}
                                    <Link
                                        href="/api/disable/faculty/{faculty_id}"
                                        class="h-full rounded-xl bg-light-primary p-2 text-white hover:opacity-90 dark:bg-dark-primary"
                                        as="button"
                                        preserveScroll
                                        method="put">Enable</Link
                                    >
                                {:else}
                                    <Link
                                        href="/api/disable/faculty/{faculty_id}"
                                        class="h-full rounded-xl bg-floating-red-light p-2 text-white hover:opacity-90 dark:bg-floating-red"
                                        as="button"
                                        preserveScroll
                                        method="put">Disable</Link
                                    >
                                {/if}
                            </td>
                        </div>
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-between">
        <button
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            on:click={openAddForm}>Add Faculty</button
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

            <label for="section">Section</label>
            <div class="flex flex-col">
                <input
                    name="section"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.section}
                />
                {#if $userForm.errors.section}
                    <ErrorText>
                        {$userForm.errors.section}
                    </ErrorText>
                {/if}
            </div>
        </div>
        <input
            class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value={formUserRoleId ? 'Update Faculty' : 'Add Faculty'}
        />
    </form>
</Modal>
