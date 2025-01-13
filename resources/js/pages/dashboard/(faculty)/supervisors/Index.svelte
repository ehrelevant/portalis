<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';

    export let supervisors;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/faculty/supervisors?search=${searchQuery}`);
    }
</script>

<div class="main-screen w-full p-4">
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

    <!-- List of Supervisorss -->
    <div class="py-4">
        <ul>
            {#each supervisors as supervisor}
                {@const { supervisor_id, first_name, middle_name, last_name } =
                    supervisor}
                <Link href="/dashboard/faculty/supervisors/{supervisor_id}">
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
