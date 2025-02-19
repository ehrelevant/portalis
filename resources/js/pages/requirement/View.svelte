<script>
    import Status from '@/js/shared/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    export let studentNumber;
    export let studentName;
    export let requirementId;
    export let requirementName;
    export let status;
    export let isAdmin = false;

    $: console.log(status, isAdmin);
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header
        txt="{studentName.last_name}, {studentName.first_name} — {requirementName}"
    />
    <div class="flex h-full w-full flex-col gap-4">
        {#if status !== 'None'}
            <iframe
                src="/file/submission/{studentNumber}/{requirementId}"
                title={requirementName}
                class="h-full grow"
            >
                This browser does not support PDFs.
            </iframe>
        {/if}
        <div class="flex flex-row justify-center gap-2">
            <Status type={status} />
            {#if ['Accepted'].includes(status)}
                <Link
                    href="/requirement/{requirementId}/view/{studentNumber}/invalidate"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    method="post">Invalidate</Link
                >
            {:else if ['For Review'].includes(status)}
                <Link
                    href="/requirement/{requirementId}/view/{studentNumber}/validate"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                    method="post">Validate</Link
                >
                <Link
                    href="/requirement/{requirementId}/view/{studentNumber}/reject"
                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                    method="post">Reject</Link
                >
            {/if}
            {#if isAdmin}
                <Link
                    href="/requirement/{requirementId}/upload/{studentNumber}"
                    class="auto flex flex-row items-center justify-center rounded-full bg-floating-blue-light p-2 px-4 hover:opacity-90 dark:bg-floating-blue"
                    >Upload Document</Link
                >
            {/if}
        </div>
    </div>
</div>
