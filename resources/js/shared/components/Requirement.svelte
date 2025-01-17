<script>
    import Status from '@shared/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    export let requirementId;
    export let requirementName;
    export let dueDate;
    export let submissionStatus;
    export let studentNumber;
</script>

<div
    class="my-1 flex flex-col justify-between rounded-xl bg-white p-3 dark:bg-black sm:flex-row"
>
    <div class="flex flex-col items-center justify-center sm:items-start">
        <p class="text-md">{requirementName}</p>
        {#if dueDate}
            <p class="text-sm">(Due Date: {dueDate})</p>
        {/if}
    </div>
    <div
        class="flex flex-col content-center items-center justify-center gap-2 sm:flex-row"
    >
        {#if submissionStatus !== 'pending'}
            <a
                href="/file/submission/{studentNumber}/{requirementId}"
                class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                >View</a
            >
        {/if}
        <Link
            href="/dashboard/requirement/{requirementId}/upload"
            class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            >Submit</Link
        >
        <Status type={submissionStatus} />
    </div>
</div>
