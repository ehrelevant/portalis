<script>
    import { router, page } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Status from '@shared/components/Status.svelte';

    export let students;
</script>

<div class="main-screen w-full p-4">
    <Header txt="Pre-Internship Phase" />

    <!-- Search Function -->
    <div class="flex flex-row content-center justify-center">
        <div class="flex items-center px-2">
            <Search />
        </div>
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="email"
            placeholder="Search by Name or Student Number"
        />
    </div>

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
                <a href="{$page.url}/students/{student_number}">
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
                </a>
            {/each}
        </ul>
    </div>
</div>
