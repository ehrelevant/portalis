<script lang="ts">
    import type {
        StudentProps,
        Requirement,
        FormIdName,
        Faculty,
        SupervisorCompanyIdName,
        Company,
    } from '$lib/types';

    import { router, Link, useForm, usePoll } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Checkbox } from '$lib/components/ui/checkbox/index';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import * as Select from '$lib/components/ui/select';
    import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
    import Icon from '@iconify/svelte';

    const { start, stop } = usePoll(2000);

    export let students: StudentProps[];
    export let requirements: Requirement[];
    export let faculties: Faculty[];
    export let supervisorCompanyIdNames: SupervisorCompanyIdName[];
    export let formIdNames: FormIdName[];
    export let companies: Company[];
    export let years: number[];
    export let isAdmin: boolean;
    export let phase: string;

    $: companiesSupervisors = [
        ...companies.map((company) => {
            return {
                ...company,
                supervisors: supervisorCompanyIdNames.filter(
                    (supervisor) =>
                        supervisor.company_id === company.company_id,
                ),
            };
        }),
        {
            company_id: null,
            company_name: null,
            is_disabled: false,
            supervisors: supervisorCompanyIdNames.filter(
                (supervisor) => supervisor.company_id === null,
            ),
        },
    ];

    let selected: { [x: number]: boolean | 'indeterminate' } = students.reduce(
        (selectedRecordAcc, { student_id }) => {
            return { ...selectedRecordAcc, [student_id]: false };
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
                '/api/disable/students',
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

    function getSupervisorNameFromId(targetSupervisorId: number) {
        const { first_name, last_name } = supervisorCompanyIdNames.find(
            ({ supervisor_id }) => supervisor_id === targetSupervisorId,
        );

        return `${last_name}, ${first_name}`;
    }

    let searchQuery: string;
    function search() {
        // Pause Polling
        stop()

        router.get(
            '/dashboard/students',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    let filterYear: number = (new Date()).getFullYear();
    function filterByYear(newYear) {
        // Pause Polling
        stop()

        filterYear = newYear;

        router.get(
            '/dashboard/students',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        )

        // Resume Polling
        start();
    }

    let sortColumn = 'student_number';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
        // Pause Polling
        stop()

        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/students`,
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    function setSection(studentId: number, sectionName: string) {
        // Pause Polling
        stop()

        router.put(
            `/students/${studentId}/assign/section/${sectionName}`,
            {},
            {
                preserveScroll: true,
            },
        );

        // Resume Polling
        start();
    }

    function setSupervisor(studentId: number, supervisorId: number) {
        // Pause Polling
        stop()

        router.put(
            `/students/${studentId}/assign/supervisor/${supervisorId}`,
            {},
            {
                preserveScroll: true,
            },
        );

        // Resume Polling
        start();
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
        year: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/student', {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }

    let formUserRoleId = null;
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
        $userForm.year = filterYear;

        formUserRoleId = null;
        isModalOpen = true;
    }

    function openUpdateForm(studentId: number) {
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
        $userForm.year = student.year;

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
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }

    let isExportDropdownOpen;
    let isExportModalOpen;

    let exportForm = useForm({
        year: null,
        include_enabled: null,
        include_disabled: null,
        include_with_section: null,
        include_without_section: null,
        include_drp: null,
    });

    let exportFormRoute;
    let exportFormText;
    function openExportForm(exportFormName: string) {
        switch (exportFormName) {
            case "student-list":
                exportFormRoute = 'list';
                exportFormText = 'Student List';
                break;
            case "student-assessments":
                exportFormRoute = 'student-assessments';
                exportFormText = 'Student Assessments';
                break;
            case "self-evaluations":
                exportFormRoute = 'self-evaluations';
                exportFormText = 'Student Self-Evaluations';
                break;
            case "company-evaluations":
                exportFormRoute = 'company-evaluations';
                exportFormText = 'Company Evaluations';
                break;
            default:
                return;
        }

        $exportForm.year = filterYear;
        $exportForm.include_enabled = true;
        $exportForm.include_disabled = false;
        $exportForm.include_with_section = true;
        $exportForm.include_without_section = true;
        $exportForm.include_drp = false;

        isExportDropdownOpen = false;
        isExportModalOpen = true;
    }

    let exportFormElement;
    function redirectExportForm() {
        if (!exportFormElement.checkValidity()) {
            exportFormElement.reportValidity();
            return;
        }

        $exportForm.get(`/export/students/${exportFormRoute}`, {
            preserveScroll: true,
            onSuccess: () => {
                isExportDropdownOpen = false;
                isExportModalOpen = false;
            },
        });
    }
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
                on:click={bulkDisable}
                class="flex w-full flex-row gap-2 sm:w-auto"
                variant="destructive"
                disabled={!hasSelected}>Disable Selected</Button
            >
            <Link href="/import/students/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link href="/add-multiple/students/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <DropdownMenu.Root bind:open={isExportDropdownOpen}>
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
                        class="flex w-full flex-row gap-2 sm:w-auto"
                        on:click={() => openExportForm("student-list")}
                        >(form) Export Student List</DropdownMenu.Item
                    >
                    <DropdownMenu.Item
                        href="/export/students/list"
                        target="_blank"
                        >Export Student List</DropdownMenu.Item
                    >
                    {#if isAdmin || phase !== 'pre'}
                        <DropdownMenu.Item
                            class="flex w-full flex-row gap-2 sm:w-auto"
                            on:click={() => openExportForm("company-evaluations")}
                            >(form) Export Company Evaluations</DropdownMenu.Item
                        >
                        <DropdownMenu.Item
                            class="flex w-full flex-row gap-2 sm:w-auto"
                            on:click={() => openExportForm("self-evaluations")}
                            >(form) Export Student Self-Evaluations</DropdownMenu.Item
                        >
                        <DropdownMenu.Item
                            class="flex w-full flex-row gap-2 sm:w-auto"
                            on:click={() => openExportForm("student-assessments")}
                            >(form) Export Student Assessments</DropdownMenu.Item
                        >
                        <DropdownMenu.Item
                            href="/export/students/company-evaluations"
                            target="_blank"
                            >Export Company Evaluations</DropdownMenu.Item
                        >
                        <DropdownMenu.Item
                            href="/export/students/self-evaluations"
                            target="_blank"
                            >Export Student Self-Evaluations</DropdownMenu.Item
                        >
                        <DropdownMenu.Item
                            href="/export/students/student-assessments"
                            target="_blank"
                            >Export Student Assessments</DropdownMenu.Item
                        >
                    {/if}
                </DropdownMenu.Content>
            </DropdownMenu.Root>

            <Dialog.Root bind:open={isExportModalOpen}>
                <Dialog.Content class="max-h-[80vh] h-auto overflow-auto">
                    <Dialog.Header>
                        <Dialog.Title>Export {exportFormText}</Dialog.Title>
                    </Dialog.Header>
                    <form
                        action="/export/students/{exportFormRoute}"
                        bind:this={exportFormElement}
                        class="flex flex-col gap-4"
                        on:submit={redirectExportForm}
                        target="_blank"
                    >
                        <div
                            class="grid grid-cols-[auto,1fr] items-center gap-4"
                        >
                            <Label for="export_year"
                                >Year</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_year"
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
                                >Include Enabled Student Accounts</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_include_enabled"
                                    type="checkbox"
                                    bind:checked={$exportForm.include_enabled}
                                />
                                {#if $exportForm.errors.include_enabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_enabled}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_disabled"
                                >Include Disabled Student Accounts</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_include_disabled"
                                    type="checkbox"
                                    bind:checked={$exportForm.include_disabled}
                                />
                                {#if $exportForm.errors.include_disabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_disabled}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_with_section"
                                >Include Students With Section</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_include_with_section"
                                    type="checkbox"
                                    bind:checked={$exportForm.include_with_section}
                                />
                                {#if $exportForm.errors.include_with_section}
                                    <ErrorText>
                                        {$exportForm.errors.include_with_section}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_without_section"
                                >Include Students Without Section</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_include_without_section"
                                    type="checkbox"
                                    bind:checked={$exportForm.include_without_section}
                                />
                                {#if $exportForm.errors.include_without_section}
                                    <ErrorText>
                                        {$exportForm.errors.include_without_section}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_dropped"
                                >Include Dropped Students</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="export_include_dropped"
                                    type="checkbox"
                                    bind:checked={$exportForm.include_drp}
                                />
                                {#if $exportForm.errors.include_drp}
                                    <ErrorText>
                                        {$exportForm.errors.include_drp}
                                    </ErrorText>
                                {/if}
                            </div>
                        </div>

                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button type="submit"
                                >Export {exportFormText}</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>

            <Dialog.Root bind:open={isModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    on:click={openAddForm}
                    ><Icon icon="material-symbols:add" />Add Student</Button
                >
                <Dialog.Content class="max-h-[80vh] h-auto overflow-auto">
                    <Dialog.Header>
                        <Dialog.Title>{formUserRoleId ? 'Edit Student' : 'Add Student'}</Dialog.Title>
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
                            <Label for="student_number"
                                ><Required />Student Number</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="student_number"
                                    type="text"
                                    bind:value={$userForm.student_number}
                                    required
                                />
                                {#if $userForm.errors.student_number}
                                    <ErrorText>
                                        {$userForm.errors.student_number}
                                    </ErrorText>
                                {/if}
                            </div>

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

                            <Label for="section">Section</Label>
                            <div class="flex flex-col">
                                <Select.Root
                                    selected={$userForm.section === 'DRP'
                                        ? { label: 'DRP', value: 'DRP' }
                                        : !$userForm.section
                                          ? { label: '-', value: '' }
                                          : {
                                                label: $userForm.section,
                                                value: $userForm.section,
                                            }}
                                    onSelectedChange={(v) => {
                                        v && ($userForm.section = v.value);
                                    }}
                                >
                                    <Select.Trigger class="px-4">
                                        <Select.Value placeholder="Section" />
                                    </Select.Trigger>
                                    <Select.Content>
                                        <Select.Item value="">-</Select.Item>
                                        {#each faculties as faculty}
                                            {@const { section } = faculty}
                                            <Select.Item value={section}
                                                >{section}</Select.Item
                                            >
                                        {/each}
                                        <Select.Item value="DRP"
                                            >DRP</Select.Item
                                        >
                                    </Select.Content>
                                </Select.Root>
                                {#if $userForm.errors.section}
                                    <ErrorText>
                                        {$userForm.errors.section}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="supervisor">Supervisor</Label>
                            <div class="flex flex-col">
                                <Select.Root
                                    selected={!$userForm.supervisor_id
                                        ? { label: '-', value: null }
                                        : {
                                              label: getSupervisorNameFromId(
                                                  $userForm.supervisor_id,
                                              ),
                                              value: $userForm.supervisor_id,
                                          }}
                                    onSelectedChange={(v) => {
                                        v &&
                                            ($userForm.supervisor_id = v.value);
                                    }}
                                >
                                    <Select.Trigger class="px-4">
                                        <Select.Value
                                            placeholder="Supervisor Name"
                                        />
                                    </Select.Trigger>
                                    <Select.Content>
                                        <Select.Item value={null}>-</Select.Item
                                        >
                                        {#each companiesSupervisors as companySupervisors}
                                            {@const {
                                                company_name,
                                                supervisors,
                                            } = companySupervisors}
                                            <Select.Group>
                                                <Select.Label
                                                    >{company_name ??
                                                        'No Company'}</Select.Label
                                                >
                                                {#each supervisors as supervisor}
                                                    {@const {
                                                        supervisor_id:
                                                            companySupervisorId,
                                                        first_name,
                                                        last_name,
                                                    } = supervisor}
                                                    <Select.Item
                                                        value={companySupervisorId}
                                                        >{last_name}, {first_name}</Select.Item
                                                    >
                                                {/each}
                                            </Select.Group>
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                                {#if $userForm.errors.supervisor_id}
                                    <ErrorText>
                                        {$userForm.errors.supervisor_id}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="wordpress name"
                                ><Required />Wordpress Username</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="wordpress name"
                                    type="text"
                                    bind:value={$userForm.wordpress_name}
                                    required
                                />
                                {#if $userForm.errors.wordpress_name}
                                    <ErrorText>
                                        {$userForm.errors.wordpress_name}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="wordpress email"
                                ><Required />Wordpress Email</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="wordpress email"
                                    type="email"
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

                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button type="submit"
                                >{formUserRoleId
                                    ? 'Update Student'
                                    : 'Add Student'}</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>
        </div>
    </div>

    <div class="flex flex-row items-center justify-end">
        <Select.Root
            selected={{label: filterYear.toString(), value: filterYear}}
            onSelectedChange={(v) => {
                v && filterByYear(v.value);
            }}
        >
            <Select.Trigger class="px-4 w-fit flex flex-row gap-2">
                <strong>Year:</strong> <Select.Value placeholder="Year" />
            </Select.Trigger>
            <Select.Content>
                {#each years as year}
                    <Select.Item
                        value={year}
                        >{year}</Select.Item
                    >
                {/each}
            </Select.Content>
        </Select.Root>
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
            <TableColumnHeader />
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
            {#if isAdmin || phase === 'pre'}
                {#each requirements as requirement}
                    {@const { requirement_name } = requirement}
                    <TableColumnHeader>{requirement_name}</TableColumnHeader>
                {/each}
            {/if}
            {#if isAdmin || phase !== 'pre'}
                {#each formIdNames as formIdName}
                    {@const { form_name } = formIdName}
                    <TableColumnHeader>{form_name}</TableColumnHeader>
                {/each}
            {/if}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each students as student (student.student_id)}
            {@const {
                student_id,
                student_number,
                first_name,
                last_name,
                email,
                wordpress_name,
                wordpress_email,
                // faculty_id,
                section,
                supervisor_id,
                // company_id,
                company_name,
                has_dropped,
                is_disabled,
                form_id_statuses,
                submission_id_statuses,
            } = student}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[student_id])}
            >
                <TableCell
                    ><Checkbox bind:checked={selected[student_id]} /></TableCell
                >
                <TableCell>{student_number}</TableCell>
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>
                    <Select.Root
                        selected={has_dropped
                            ? { label: 'DRP', value: 'DRP' }
                            : !section
                              ? { label: '-', value: '' }
                              : {
                                    label: section,
                                    value: section,
                                }}
                        onSelectedChange={(v) => {
                            v && setSection(student_id, v.value);
                        }}
                    >
                        <Select.Trigger class="px-4">
                            <Select.Value placeholder="Section" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="">-</Select.Item>
                            {#each faculties as faculty}
                                {@const { section: facultySection } = faculty}
                                <Select.Item value={facultySection}
                                    >{facultySection}</Select.Item
                                >
                            {/each}
                            <Select.Item value="DRP">DRP</Select.Item>
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>
                    <Select.Root
                        selected={!supervisor_id
                            ? { label: '-', value: null }
                            : {
                                  label: getSupervisorNameFromId(supervisor_id),
                                  value: supervisor_id,
                              }}
                        onSelectedChange={(v) => {
                            v && setSupervisor(student_id, v.value);
                        }}
                    >
                        <Select.Trigger class="px-4">
                            <Select.Value placeholder="Supervisor Name" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value={null}>-</Select.Item>
                            {#each companiesSupervisors as companySupervisors}
                                {@const { company_name, supervisors } =
                                    companySupervisors}
                                <Select.Group>
                                    <Select.Label
                                        >{company_name ??
                                            'No Company'}</Select.Label
                                    >
                                    {#each supervisors as supervisor}
                                        {@const {
                                            supervisor_id: companySupervisorId,
                                            first_name,
                                            last_name,
                                        } = supervisor}
                                        <Select.Item value={companySupervisorId}
                                            >{last_name}, {first_name}</Select.Item
                                        >
                                    {/each}
                                </Select.Group>
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>{company_name ?? ''}</TableCell>
                <TableCell>{email}</TableCell>
                <TableCell>{wordpress_name}</TableCell>
                <TableCell>{wordpress_email}</TableCell>
                {#if isAdmin || phase === 'pre'}
                    {#each submission_id_statuses as submission_id_status}
                        {@const { requirement_id, status } =
                            submission_id_status}
                        <TableCell center>
                            <StatusCell
                                {isAdmin}
                                {status}
                                href="/requirement/{requirement_id}/view/{student_id}"
                            />
                        </TableCell>
                    {/each}
                {/if}
                {#if isAdmin || phase !== 'pre'}
                    {#each form_id_statuses as form_id_status}
                        {@const { form_id, status } = form_id_status}
                        <TableCell center>
                            {#if isAdmin}
                                <StatusCell
                                    {isAdmin}
                                    {status}
                                    href="/form/{getFormFromId(form_id)
                                        .short_name}/answer/{student_id}"
                                />
                            {:else}
                                <StatusCell
                                    {isAdmin}
                                    {status}
                                    href="/form/{getFormFromId(form_id)
                                        .short_name}/view/{student_id}"
                                />
                            {/if}
                        </TableCell>
                    {/each}
                {/if}
                <TableCell
                    ><div class="flex flex-row gap-2">
                        <Button
                            class="w-20 {colorVariants.blue}"
                            on:click={() => openUpdateForm(student_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/student/{student_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                class="w-20"
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
