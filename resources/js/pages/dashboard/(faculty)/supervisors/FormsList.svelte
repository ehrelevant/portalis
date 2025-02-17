<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from '@/js/shared/components/StatusCell.svelte';
    import ColumnHeader from '@/js/shared/components/ColumnHeader.svelte';

    export let supervisors;
    export let form_infos;

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
        <h2 slot="summary" class="text-2xl">Supervisor Submissions</h2>

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
                        <th scope="col" class="border-l-2 p-2 {borderColor}"
                            >{form_name}</th
                        >
                    {/each}
                </tr>
                {#each supervisors as supervisor}
                    {@const {
                        supervisor_id,
                        first_name,
                        last_name,
                        email,
                        company_name,
                        form_statuses,
                    } = supervisor}
                    <tr class="border-t-2 {borderColor}">
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{company_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{email}</td
                        >
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/view/{supervisor_id}"
                                />
                            </td>
                        {/each}
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-end">
        <Link
            href="/dashboard"
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>
