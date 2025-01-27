<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from './StatusCell.svelte';

    export let supervisors;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/supervisors?search=${searchQuery}`);
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

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

    <!-- List of Supervisors -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Supervisor Submissions</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Company</th
                    >
                    <th scope="col" class="border-l-2 p-2 {borderColor}"
                        >Mid-semester Report</th
                    >
                    <th scope="col" class="border-l-2 p-2 {borderColor}"
                        >Final Report</th
                    >
                </tr>
                {#each supervisors as supervisor}
                    {@const {
                        supervisor_id,
                        first_name,
                        last_name,
                        company_name,
                        midsem_status,
                        final_status,
                    } = supervisor}
                    <tr class="border-t-2 {borderColor}">
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{company_name}</td
                        >
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><StatusCell
                                status={midsem_status}
                                href="/dashboard/supervisors/{supervisor_id}/midsem"
                            />
                        </td>
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><StatusCell
                                status={final_status}
                                href="/dashboard/supervisors/{supervisor_id}/final"
                            />
                        </td>
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
