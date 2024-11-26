<script>
    import Status from '@shared/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    export let file_name;
    export let sub_status;
    export let faculty = 0;
    export let student_number;
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
            class="flex flex-col content-center items-center justify-center sm:flex-row"
        >
            {#if student_number && requirement_id && sub_status !== 'pending'}
                <a
                    href="/file/student/{student_number}/{requirement_id}"
                    class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary sm:mr-3"
                    >View</a
                >
            {/if}
            {#if faculty === 1}
                {#if sub_status === 'submitted'}
                    <div class="my-2 flex flex-row">
                        <Link
                            href="/dashboard/pre/students/{student_number}/{requirement_id}/reject"
                            method="post"
                            preserveScroll
                            class="mr-3 flex w-20 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                        >
                            Reject
                        </Link>
                        <Link
                            href="/dashboard/pre/students/{student_number}/{requirement_id}/validate"
                            method="post"
                            preserveScroll
                            class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary sm:mr-3"
                        >
                            Validate
                        </Link>
                    </div>
                {:else if sub_status === 'validated'}
                    <Link
                        href="/dashboard/pre/students/{student_number}/{requirement_id}/invalidate"
                        method="post"
                        preserveScroll
                        class="my-2 flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red sm:mr-3"
                    >
                        Invalidate
                    </Link>
                {/if}
            {/if}
            <Status s_type={sub_status} />
        </div>
    </div>
</li>
