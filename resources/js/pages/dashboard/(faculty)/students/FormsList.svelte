<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Required from '$lib/components/Required.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let students;
    export let sections;
    export let form_infos;
    export let companies;
    export let companySupervisors;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/students',
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

    let sortColumn = 'student_number';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn) {
        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/students`,
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

    function setSection(evt, studentId) {
        const sectionName = evt.target.value;

        router.put(
            `/students/${studentId}/assign/section/${sectionName}`,
            {},
            {
                preserveScroll: true,
            },
        );
    }

    function setSupervisor(evt, studentId) {
        const supervisorId = evt.target.value;

        router.put(
            `/students/${studentId}/assign/supervisor/${supervisorId}`,
            {},
            {
                preserveScroll: true,
            },
        );
    }

    let userFormElement;
    let isModalOpen;

    let userForm = useForm({
        student_number: null,
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        section: null,
        supervisor_id: null,
        wordpress_name: null,
        wordpress_email: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post(
            '/api/add/student',
            {},
            {
                preserveScroll: true,
            },
        );
    }

    function openAddForm() {
        $userForm.student_number = null;
        $userForm.first_name = null;
        $userForm.middle_name = null;
        $userForm.last_name = null;
        $userForm.email = null;
        $userForm.section = null;
        $userForm.supervisor_id = null;
        $userForm.wordpress_name = null;
        $userForm.wordpress_email = null;

        isModalOpen = true;
    }

    let formUserRoleId = null;
    function openUpdateForm(studentId) {
        const student = students.find(
            (student) => student.student_id === studentId,
        );

        $userForm.student_number = student.student_number;
        $userForm.first_name = student.first_name;
        $userForm.middle_name = student.middle_name;
        $userForm.last_name = student.last_name;
        $userForm.email = student.email;
        $userForm.section = student.section;
        $userForm.supervisor_id = student.supervisor_id;
        $userForm.wordpress_name = student.wordpress_name;
        $userForm.wordpress_email = student.wordpress_email;

        formUserRoleId = studentId;
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
            `/api/update/student/${formUserRoleId}`,
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
    <Header txt="Student List" />

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

    <!-- List of Students -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Students</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-gray-900"
            >
                <tr class="border-b-2 {borderColor}">
                    <ColumnHeader
                        isActive={sortColumn === 'student_number'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('student_number')}
                        first
                    >
                        SN
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'last_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('last_name')}
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
                        isActive={sortColumn === 'section'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('section')}
                    >
                        Section
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'supervisor_last_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() =>
                            sortByColumn('supervisor_last_name')}
                    >
                        Supervisor Name
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'company_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('company_name')}
                    >
                        Company Interned
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'email'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('email')}
                    >
                        Email
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'wordpress_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('wordpress_name')}
                    >
                        Wordpress Name
                    </ColumnHeader>
                    <ColumnHeader
                        isActive={sortColumn === 'wordpress_email'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('wordpress_email')}
                    >
                        Wordpress Email
                    </ColumnHeader>
                    {#each Object.entries(form_infos) as [_, form_info]}
                        {@const { form_name } = form_info}
                        <ColumnHeader>{form_name}</ColumnHeader>
                    {/each}
                    <ColumnHeader>Actions</ColumnHeader>
                </tr>
                {#each students as student (student.student_id)}
                    {@const {
                        student_id,
                        student_number,
                        first_name,
                        last_name,
                        section: student_section,
                        supervisor_id,
                        company,
                        email,
                        wordpress_name,
                        wordpress_email,
                        form_statuses,
                        has_dropped,
                        is_disabled,
                    } = student}
                    <tr
                        class="border-t-2 {borderColor} {is_disabled
                            ? 'bg-black text-gray-300'
                            : ''}"
                    >
                        <th class="border-r-2 p-2 {borderColor}"
                            >{student_number}</th
                        >
                        <td class="border-r-2 p-2 {borderColor}">{last_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{first_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSection(evt, student_id)}
                                >
                                    <option
                                        selected={!has_dropped &&
                                            student_section}
                                        value
                                    />
                                    {#each sections as section}
                                        <option
                                            selected={!has_dropped &&
                                                student_section === section}
                                            value={section}>{section}</option
                                        >
                                    {/each}
                                    <option selected={has_dropped} value="DRP"
                                        >DRP</option
                                    >
                                </select>
                            </div>
                        </td>
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSupervisor(evt, student_id)}
                                >
                                    <option selected={!supervisor_id} value />
                                    {#each companies as company}
                                        {@const {
                                            id: company_id,
                                            company_name,
                                        } = company}
                                        <optgroup label={company_name}>
                                            {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                                {@const {
                                                    id,
                                                    first_name,
                                                    last_name,
                                                } = companySupervisor}
                                                <option
                                                    value={companySupervisorId}
                                                    selected={id ===
                                                        supervisor_id}
                                                    >{last_name}, {first_name}</option
                                                >
                                            {/each}
                                        </optgroup>
                                    {/each}
                                    <optgroup label="No Company">
                                        {#each Object.entries(companySupervisors[0]) as [companySupervisorId, companySupervisor]}
                                            {@const {
                                                id,
                                                first_name,
                                                last_name,
                                            } = companySupervisor}
                                            <option
                                                value={companySupervisorId}
                                                selected={id === supervisor_id}
                                                >{last_name}, {first_name}</option
                                            >
                                        {/each}
                                    </optgroup>
                                </select>
                            </div>
                        </td>
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{company ?? ''}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_email}</td
                        >
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/view/{student_id}"
                                />
                            </td>
                        {/each}
                        <div
                            class="flex flex-row items-center justify-center gap-2 border-l-2 p-2"
                        >
                            <td class="text-center {borderColor}"
                                ><button
                                    class="h-full rounded-xl bg-floating-blue-light p-2 text-white hover:opacity-90 dark:bg-floating-blue"
                                    on:click={() => openUpdateForm(student_id)}
                                    >Edit</button
                                >
                            </td>
                            <td class="text-center {borderColor}">
                                {#if is_disabled}
                                    <Link
                                        href="/api/enable/student/{student_id}"
                                        class="h-full rounded-xl bg-light-primary p-2 text-white hover:opacity-90 dark:bg-dark-primary"
                                        as="button"
                                        preserveScroll
                                        method="put">Enable</Link
                                    >
                                {:else}
                                    <Link
                                        href="/api/disable/student/{student_id}"
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

    <div class="flex flex-row items-start justify-between">
        <div class="flex w-fit flex-col gap-4">
            <button
                class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary p-2 text-center hover:opacity-90 dark:bg-dark-primary"
                on:click={openAddForm}>+ Add Student</button
            >

            <div class="flex flex-col gap-2">
                <a
                    target="_blank"
                    href="/export/students/sections"
                    class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
                    >Export Student Sections</a
                >

                <a
                    target="_blank"
                    href="/export/students/company-evaluations"
                    class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
                    >Export Company Evaluations</a
                >

                <a
                    target="_blank"
                    href="/export/students/student-assessments"
                    class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
                    >Export Student Assessments</a
                >
            </div>
        </div>

        <Link
            href="/dashboard"
            class="flex w-fit flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
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
            <label for="student_number"><Required />Student Number</label>
            <div class="flex flex-col">
                <input
                    name="student_number"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.student_number}
                    required
                />
                {#if $userForm.errors.student_number}
                    <ErrorText>
                        {$userForm.errors.student_number}
                    </ErrorText>
                {/if}
            </div>

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
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    name="section"
                    bind:value={$userForm.section}
                >
                    <option selected value />
                    {#each sections as section}
                        <option value={section}>{section}</option>
                    {/each}
                </select>
                {#if $userForm.errors.section}
                    <ErrorText>
                        {$userForm.errors.section}
                    </ErrorText>
                {/if}
            </div>

            <label for="supervisor">Supervisor</label>
            <div class="flex flex-col">
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    name="supervisor"
                    bind:value={$userForm.supervisor_id}
                >
                    <option selected value />
                    {#each companies as company}
                        {@const { id: company_id, company_name } = company}
                        <optgroup label={company_name}>
                            {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                {@const { first_name, last_name } =
                                    companySupervisor}
                                <option value={companySupervisorId}
                                    >{last_name}, {first_name}</option
                                >
                            {/each}
                        </optgroup>
                    {/each}
                    <optgroup label="No Company">
                        {#each Object.entries(companySupervisors[0]) as [companySupervisorId, companySupervisor]}
                            {@const { first_name, last_name } =
                                companySupervisor}
                            <option value={companySupervisorId}
                                >{last_name}, {first_name}</option
                            >
                        {/each}
                    </optgroup>
                </select>
                {#if $userForm.errors.supervisor_id}
                    <ErrorText>
                        {$userForm.errors.supervisor_id}
                    </ErrorText>
                {/if}
            </div>

            <label for="wordpress name"><Required />Wordpress Username</label>
            <div class="flex flex-col">
                <input
                    name="wordpress name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.wordpress_name}
                    required
                />
                {#if $userForm.errors.wordpress_name}
                    <ErrorText>
                        {$userForm.errors.wordpress_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="wordpress email"><Required />Wordpress Email</label>
            <div class="flex flex-col">
                <input
                    name="wordpress email"
                    type="email"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$userForm.wordpress_email}
                    required
                />
                {#if $userForm.errors.wordpress_email}
                    <ErrorText>
                        {$userForm.errors.wordpress_email}
                    </ErrorText>
                {/if}
            </div>
        </div>

        <input
            class="cursor-pointer rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value={formUserRoleId ? 'Update Student' : 'Add Student'}
        />
    </form>
</Modal>
