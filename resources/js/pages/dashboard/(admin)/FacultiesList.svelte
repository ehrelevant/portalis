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
        section: null,
    });

    function addUser() {
        if (!addFormElement.checkValidity()) {
            addFormElement.reportValidity();
            return;
        }
        $addUserForm.post(
            '/dashboard/admin/faculties/add',
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
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <ColumnHeader
                        isActive={sortColumn === 'last_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('last_name')}
                        first
                    >
                        Name
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
                    } = faculty}
                    <tr class="border-t-2 {borderColor}">
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-l-2 p-2 {borderColor}">{email}</td>
                        <td class="border-l-2 p-2 {borderColor}"
                            >{section ?? ''}</td
                        >
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><Link
                                href="/dashboard/admin/faculties/delete/{faculty_id}"
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
            on:click={openModal}>Add Faculty</button
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

            <label for="section">Section</label>
            <div class="flex flex-col">
                <input
                    name="section"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.section}
                />
                {#if $addUserForm.errors.section}
                    <ErrorText>
                        {$addUserForm.errors.section}
                    </ErrorText>
                {/if}
            </div>
        </div>
        <input
            class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value="Add Faculty"
        />
    </form>
</Modal>
