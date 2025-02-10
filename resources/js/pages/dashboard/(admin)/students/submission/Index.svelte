<script>
    import Status from '@/js/shared/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    export let student_number;
    export let requirement_id;
    export let status;
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Student Requirement - {requirement_id}" />
    <div class="flex h-full w-full flex-col gap-4">
        <iframe
            src="/file/submission/{student_number}/{requirement_id}"
            title="requirement {requirement_id}"
            class="h-full grow"
        >
            This browser does not support PDFs.
        </iframe>
        <div class="flex flex-row justify-center gap-2">
            <Status type={status} />
            {#if status === 'validated'}
                <Link
                    href="/requirement/{requirement_id}/view/{student_number}/invalidate"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    method="post">Invalidate</Link
                >
            {:else if status !== 'rejected'}
                <Link
                    href="/requirement/{requirement_id}/view/{student_number}/validate"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                    method="post">Validate</Link
                >
                <Link
                    href="/requirement/{requirement_id}/view/{student_number}/reject"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    method="post">Reject</Link
                >
            {/if}
        </div>
    </div>
</div>
