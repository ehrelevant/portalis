<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from '@/js/shared/components/StatusCell.svelte';
    import ColumnHeader from '@/js/shared/components/ColumnHeader.svelte';

    export let students;
    export let form_infos;
    export let supervisors;

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

    <!-- List of Supervisors -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Student Submissions</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
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
                        Name
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
                </tr>
                {#each students as student}
                    {@const {
                        student_number,
                        first_name,
                        last_name,
                        section,
                        company,
                        form_statuses,
                        supervisor_id,
                        email,
                        wordpress_name,
                        wordpress_email,
                    } = student}
                    <tr class="border-t-2 {borderColor}">
                        <th scope="row" class="border-r-2 p-2 {borderColor}"
                            >{student_number}</th
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{section}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{supervisors[supervisor_id].last_name}, {supervisors[
                                supervisor_id
                            ].first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{company}</td
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
                                        .short_name}/view/{student_number}"
                                />
                            </td>
                        {/each}
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex flex-row items-start justify-between">
        <div class="flex w-fit flex-col gap-2">
            <a
                target="_blank"
                href="/export/students/sections"
                class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 hover:opacity-90 dark:bg-dark-primary"
                method="get">Export Student Sections</a
            >

            <a
                target="_blank"
                href="/export/students/company-evaluations"
                class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 hover:opacity-90 dark:bg-dark-primary"
                method="get">Export Company Evaluations</a
            >

            <a
                target="_blank"
                href="/export/students/student-assessments"
                class="flex w-full flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 hover:opacity-90 dark:bg-dark-primary"
                method="get">Export Student Assessments</a
            >
        </div>

        <Link
            href="/dashboard"
            class="flex w-fit flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>
