<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';

    export let supervisors;

    /** @type {string} */
    let searchText = '';

    function search() {
        router.get(`/dashboard/pre?search=${searchText}`);
    }
</script>

<div class="main-screen w-full p-4">
    <Header txt="Pre-Internship Phase" />

    <!-- Search Function -->
    <div class="flex flex-row content-center justify-center">
        <button class="flex items-center px-2" on:click={search}>
            <Search />
        </button>
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="text"
            placeholder="Search by Name"
            bind:value={searchText}
        />
    </div>

    <!-- List of Supervisorss -->
    <div class="py-4">
        <ul>
            {#each supervisors as supervisor}
                {@const { supervisor_id, first_name, middle_name, last_name } =
                    supervisor}
                <Link href="/dashboard/pre/supervisors/{supervisor_id}">
                    <!-- edit this later -->
                    <li>
                        <div
                            class="my-2 flex justify-between rounded-xl bg-white p-4 hover:opacity-80 dark:bg-black"
                        >
                            <div class="flex flex-col justify-center">
                                <div>
                                    {last_name}, {first_name}
                                    {middle_name}
                                </div>
                            </div>
                        </div>
                    </li>
                </Link>
            {/each}
        </ul>
    </div>
</div>
