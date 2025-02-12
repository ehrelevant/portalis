<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';

    export let companies;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/admin/companies?search=${searchQuery}`);
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Student List" />

    <!-- Search Function -->
    <form
        class="flex flex-row content-center justify-center"
        on:submit|preventDefault={search}
    >
        <button class="flex items-center px-2" type="submit">
            <Search />
        </button>
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
        />
    </form>

    <!-- List of Companies -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Student Submissions</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="p-2 {borderColor}">Company Name</th>
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
