<script>
    import { router, useForm, Link } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Search from '$assets/search_logo.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let students;
    export let requirements;
    export let sections;

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

    function setSection(evt, studentNumber) {
        const sectionName = evt.target.value;

        router.put(
            `/students/${studentNumber}/assign/section/${sectionName}`,
            {},
            {
                preserveScroll: true,
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

    <!-- List of Students -->
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
                    {#each requirements as requirement}
                        {@const { requirement_name } = requirement}
                        <ColumnHeader>{requirement_name}</ColumnHeader>
                    {/each}
                </tr>
                {#each students as student}
                    {@const {
                        student_number,
                        first_name,
                        last_name,
                        section: student_section,
                        has_dropped,
                        submissions,
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
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSection(evt, student_number)}
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
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_email}</td
                        >
                        {#each submissions as submission}
                            {@const { requirement_id, status } = submission}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    href="/requirement/{requirement_id}/view/{student_number}"
                                    {status}
                                />
                            </td>
                        {/each}
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex flex-row justify-between">
        <a
            target="_blank"
            href="/export/students/sections"
            class="flex w-fit flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
            method="get">Export Student Sections</a
        >
        <Link
            href="/dashboard"
            class="flex w-fit flex-row items-center justify-center rounded-full bg-light-primary px-4 py-2 text-center hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>
