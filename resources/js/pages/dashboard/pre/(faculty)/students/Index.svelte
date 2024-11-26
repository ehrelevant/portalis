<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Status from '@shared/components/Status.svelte';

    export let students;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/pre/students?search=${searchQuery}`);
    }
</script>

<div class="main-screen w-full p-4">
    <Header txt="Pre-Internship Phase" />

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

    <!-- List of Students -->
    <div class="py-4">
        <ul>
            {#each students as student}
                {@const {
                    student_number,
                    first_name,
                    middle_name,
                    last_name,
                    total_status,
                } = student}
                <Link href="/dashboard/pre/students/{student_number}">
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
                                <div>{student_number}</div>
                            </div>
                            <div class="flex items-center">
                                <Status s_type={total_status} />
                            </div>
                        </div>
                    </li>
                </Link>
            {/each}
        </ul>
    </div>
</div>
