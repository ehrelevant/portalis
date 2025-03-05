<script>
    import Status from '$lib/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    import { Label } from "$lib/components/ui/label";

    export let requirementId;
    export let requirementName;
    export let deadline;
    export let submissionStatus;
    export let studentNumber;
</script>

<div
    class="my-2 flex flex-col justify-between rounded-xl border-b border-t p-4 bg-background sm:flex-row"
>
    <div class="flex flex-col items-center justify-center sm:items-start">
        <Label class="text-lg">{requirementName}</Label>
        {#if deadline}
            {@const deadlineDateTime = new Date(deadline)}
            <p class="text-xs">
                (Deadline: {deadlineDateTime.toLocaleDateString(undefined, {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                })})
            </p>
        {/if}
    </div>
    <div
        class="flex flex-col content-center items-center justify-center gap-2 sm:flex-row"
    >
        {#if submissionStatus !== 'None'}
            <a
                href="/file/submission/{studentNumber}/{requirementId}"
                target="_blank"
                class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                >View</a
            >
        {/if}
        <Link
            href="/requirement/{requirementId}/upload"
            class="flex w-20 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            >Submit</Link
        >
        <Status type={submissionStatus} />
    </div>
</div>
