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
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import * as Select from '$lib/components/ui/select';
    import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
    import Icon from '@iconify/svelte';

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

    function setSection(studentId, sectionName) {
        router.put(
            `/students/${studentId}/assign/section/${sectionName}`,
            {},
            {
                preserveScroll: true,
            },
        );
    }

    function setSupervisor(studentId, supervisorId) {
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
                href="/list/students/upload"
                class="flex w-full flex-row gap-2 sm:w-auto"
                variant="outline">Import Students</Button
            >
            <DropdownMenu.Root>
                <DropdownMenu.Trigger asChild let:builder>
                    <Button
                        builders={[builder]}
                        variant="outline"
                        class="flex w-full flex-row gap-2 sm:w-auto"
                        ><Icon icon="uil:export" />Export</Button
                    >
                </DropdownMenu.Trigger>
                <DropdownMenu.Content>
                    <DropdownMenu.Item
                        href="/export/students/sections"
                        target="_blank"
                        >Export Student Section</DropdownMenu.Item
                    >
                    <DropdownMenu.Item
                        href="/export/students/sections"
                        target="_blank"
                        >Export Company Evaluations</DropdownMenu.Item
                    >
                    <DropdownMenu.Item
                        href="/export/students/student-assessments"
                        target="_blank"
                        >Export Student Assessments</DropdownMenu.Item
                    >
                </DropdownMenu.Content>
            </DropdownMenu.Root>
            <Button
                class="flex w-full flex-row gap-2 sm:w-auto"
                on:click={openAddForm}
                ><Icon icon="material-symbols:add" />Add Student</Button
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

    <!-- List of Students -->
    <Table>
        <TableRow header>
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
                company_id: student_company_id,
                company,
                email,
                wordpress_name,
                wordpress_email,
                form_statuses,
                has_dropped,
                submissions,
                is_disabled,
            } = student}
            <TableRow disabled={is_disabled}>
                <TableCell>{student_number}</TableCell>
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>
                    <Select.Root
                        selected={has_dropped
                            ? { label: 'DRP', value: 'DRP' }
                            : !student_section
                              ? { label: '-', value: '' }
                              : {
                                    label: student_section,
                                    value: student_section,
                                }}
                        onSelectedChange={(v) => {
                            v && setSection(student_id, v.value);
                        }}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Section" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="">-</Select.Item>
                            {#each sections as section}
                                <Select.Item value={section}
                                    >{section}</Select.Item
                                >
                            {/each}
                            <Select.Item value="DRP">DRP</Select.Item>
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>
                    <Select.Root
                        selected={!supervisor_id
                            ? { label: '-', value: '' }
                            : {
                                  label: `${companySupervisors[student_company_id][supervisor_id].last_name}, ${companySupervisors[student_company_id][supervisor_id].first_name}`,
                                  value: supervisor_id,
                              }}
                        onSelectedChange={(v) => {
                            v && setSupervisor(student_id, v.value);
                        }}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Supervisor Name" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="">-</Select.Item>
                            {#each companies as company}
                                {@const { id: company_id, company_name } =
                                    company}
                                <Select.Group>
                                    <Select.Label>{company_name}</Select.Label>
                                    {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                        {@const { first_name, last_name } =
                                            companySupervisor}
                                        <Select.Item value={companySupervisorId}
                                            >{last_name}, {first_name}</Select.Item
                                        >
                                    {/each}
                                </Select.Group>
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>{company ?? ''}</TableCell>
                <TableCell>{email}</TableCell>
                <TableCell>{wordpress_name}</TableCell>
                <TableCell>{wordpress_email}</TableCell>
                {#each submissions as submission}
                    {@const { requirement_id, status } = submission}
                    <TableCell center
                        ><StatusCell
                            isAdmin
                            href="/requirement/{requirement_id}/view/{student_id}"
                            {status}
                        />
                    </TableCell>
                {/each}
                {#each Object.entries(form_statuses) as [form_id, form_status]}
                    <TableCell center
                        ><StatusCell
                            isAdmin
                            status={form_status}
                            href="/form/{form_infos[form_id]
                                .short_name}/answer/{student_id}"
                        />
                    </TableCell>
                {/each}
                <TableCell
                    ><div class="flex flex-row gap-2">
                        <Button
                            class={colorVariants.blue}
                            on:click={() => openUpdateForm(student_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/student/{student_id}"
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
                                            `/api/disable/student/${student_id}`,
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
