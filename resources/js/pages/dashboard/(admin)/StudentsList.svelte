<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Required from '$lib/components/Required.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import Table from '$lib/components/table/Table.svelte';

    export let students;
    export let requirements;
    export let sections;
    export let form_infos;
    export let companies;
    export let companySupervisors;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/admin/students',
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
            `/dashboard/admin/students`,
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
        $userForm.post('/api/add/student', {
            preserveScroll: true,
        });
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
        $userForm.post(`/api/update/student/${formUserRoleId}`, {
            preserveScroll: true,
        });
    }

    Inertia.on('success', () => {
        isModalOpen = false;
    });
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
    <Table>
        <TableRow>
            <TableColumnHeader
                isActive={sortColumn === 'student_number'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('student_number')}
            >
                SN
            </TableColumnHeader>
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
                isActive={sortColumn === 'section'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('section')}
            >
                Section
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'supervisor_last_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('supervisor_last_name')}
            >
                Supervisor Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'company_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('company_name')}
            >
                Company Interned
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'email'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('email')}
            >
                Email
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'wordpress_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('wordpress_name')}
            >
                Wordpress Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'wordpress_email'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('wordpress_email')}
            >
                Wordpress Email
            </TableColumnHeader>
            {#each requirements as requirement}
                {@const { requirement_name } = requirement}
                <TableColumnHeader>{requirement_name}</TableColumnHeader>
            {/each}
            {#each Object.entries(form_infos) as [_, form_info]}
                {@const { form_name } = form_info}
                <TableColumnHeader>{form_name}</TableColumnHeader>
            {/each}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
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
                submissions,
                is_disabled,
            } = student}
            <TableRow>
                <TableCell>{student_number}</TableCell>
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>
                    <div class="flex items-center justify-center">
                        <select
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            on:change={(evt) => setSection(evt, student_id)}
                        >
                            <option
                                selected={!has_dropped && student_section}
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
                </TableCell>
                <TableCell>
                    <div class="flex items-center justify-center">
                        <select
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            on:change={(evt) => setSupervisor(evt, student_id)}
                        >
                            <option selected={!supervisor_id} value />
                            {#each companies as company}
                                {@const { id: company_id, company_name } =
                                    company}
                                <optgroup label={company_name}>
                                    {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                        {@const { id, first_name, last_name } =
                                            companySupervisor}
                                        <option
                                            value={companySupervisorId}
                                            selected={id === supervisor_id}
                                            >{last_name}, {first_name}</option
                                        >
                                    {/each}
                                </optgroup>
                            {/each}
                            <optgroup label="No Company">
                                {#each Object.entries(companySupervisors[0]) as [companySupervisorId, companySupervisor]}
                                    {@const { id, first_name, last_name } =
                                        companySupervisor}
                                    <option
                                        value={companySupervisorId}
                                        selected={id === supervisor_id}
                                        >{last_name}, {first_name}</option
                                    >
                                {/each}
                            </optgroup>
                        </select>
                    </div>
                </TableCell>
                <TableCell>{company ?? ''}</TableCell>
                <TableCell>{email}</TableCell>
                <TableCell>{wordpress_name}</TableCell>
                <TableCell>{wordpress_email}</TableCell>
                {#each submissions as submission}
                    {@const { requirement_id, status } = submission}
                    <TableCell
                        ><StatusCell
                            isAdmin
                            href="/requirement/{requirement_id}/view/{student_id}"
                            {status}
                        />
                    </TableCell>
                {/each}
                {#each Object.entries(form_statuses) as [form_id, form_status]}
                    <TableCell
                        ><StatusCell
                            isAdmin
                            status={form_status}
                            href="/form/{form_infos[form_id]
                                .short_name}/answer/{student_id}"
                        />
                    </TableCell>
                {/each}
                <div
                    class="flex flex-row items-center justify-center gap-2 p-2"
                >
                    <TableCell
                        ><button
                            class="h-full rounded-xl bg-floating-blue-light p-2 text-white hover:opacity-90 dark:bg-floating-blue"
                            on:click={() => openUpdateForm(student_id)}
                            >Edit</button
                        >
                    </TableCell>
                    <TableCell>
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
                    </TableCell>
                </div>
            </TableRow>
        {/each}
    </Table>

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
