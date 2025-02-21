<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let companies;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/companies',
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

    let sortColumn = 'company_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn) {
        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/companies`,
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
    <Header txt="Companies List" />

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

    <!-- List of Companies -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Companies</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <ColumnHeader
                        isActive={sortColumn === 'company_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('company_name')}
                        first
                    >
                        Company Name
                    </ColumnHeader>
                </tr>
                {#each companies as company}
                    {@const { company_name } = company}
                    <tr class="border-t-2 {borderColor}">
                        <td class="p-2 {borderColor}">{company_name}</td>
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
