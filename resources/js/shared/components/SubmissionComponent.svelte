<script>
    import Status from '@shared/components/Status.svelte';
    import Valid from '@assets/validated_faculty_logo.svelte';
    import { Link } from '@inertiajs/svelte';

    export let file_name;
    export let sub_status;
    export let faculty = 0;
    export let student_number;
    export let requirement_id;
</script>

<li> <div class="flex flex-col sm:flex-row p-3 my-1 justify-between bg-white dark:bg-black rounded-xl">
    <div class="flex flex-col items-center justify-center sm:items-start">
        <div class="text-md"> {file_name} </div>
    </div>
    <div class="flex flex-col sm:flex-row items-center content-center justify-center">
        {#if student_number && requirement_id && sub_status !== 'pending'}
            <a
                href="/file/student/{student_number}/{requirement_id}"
                class="w-20 flex flex-row items-center justify-center rounded-full bg-light-primary dark:bg-dark-primary p-2 mr-3 hover:opacity-90"
                >View</a
            >
        {/if}
        {#if faculty === 1}
            {#if sub_status === 'submitted'}
                <Link
                    href="/dashboard/pre/students/{student_number}/{requirement_id}/reject"
                    method="post"
                    preserveScroll
                    class="w-20 flex flex-row items-center justify-center rounded-full bg-floating-red-light dark:bg-floating-red p-2 mr-3 hover:opacity-90"
                >
                    Reject
                </Link>
                <Link
                    href="/dashboard/pre/students/{student_number}/{requirement_id}/validate"
                    method="post"
                    preserveScroll
                    class="w-28 flex flex-row items-center justify-center rounded-full bg-light-primary dark:bg-dark-primary p-2 mr-3 hover:opacity-90"
                >
                    Validate
                </Link>
            {:else if sub_status === 'validated'}
                <Link
                    href="/dashboard/pre/students/{student_number}/{requirement_id}/invalidate"
                    method="post"
                    preserveScroll
                    class="w-28 flex flex-row items-center justify-center rounded-full bg-floating-red-light dark:bg-floating-red p-2 mr-3 hover:opacity-90"
                >
                    Invalidate
                </Link>
            {/if}
        {/if}
        <Status s_type={sub_status}/>
    </div>
</div> </li>
