<script>
    import Status from '$lib/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    export let file_name;
    export let sub_status;
    export let faculty = false;
    export let student_id;
    export let requirement_id;
</script>

<li>
    <div
        class="my-1 flex flex-col justify-between rounded-xl bg-white p-3 dark:bg-black sm:flex-row"
    >
        <div class="flex flex-col items-center justify-center sm:items-start">
            <div class="text-md">{file_name}</div>
        </div>
        <div
            class="flex flex-col content-center items-center justify-center gap-2 sm:flex-row"
        >
            {#if student_id && requirement_id && sub_status !== 'pending'}
                <a
                    href="/file/student/{student_id}/{requirement_id}"
                    class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                    >View</a
                >
            {/if}
            {#if faculty}
                {#if sub_status === 'For Review'}
                    <!-- Using `use:inertia` instead of Link temporarily due to some issues -->
                    <Link
                        href="/dashboard/faculty/students/{student_id}/{requirement_id}/reject"
                        method="post"
                        preserveScroll
                        class="flex w-20 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    >
                        Reject
                    </Link>
                    <Link
                        href="/dashboard/faculty/students/{student_id}/{requirement_id}/validate"
                        method="post"
                        preserveScroll
                        class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                    >
                        Validate
                    </Link>
                {:else if sub_status === 'Accepted'}
                    <Link
                        href="/dashboard/faculty/students/{student_id}/{requirement_id}/invalidate"
                        method="post"
                        preserveScroll
                        class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    >
                        Invalidate
                    </Link>
                {/if}
            {/if}
            <Status type={sub_status} />
        </div>
    </div>
</li>
