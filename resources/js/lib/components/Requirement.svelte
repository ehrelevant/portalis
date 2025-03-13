<script>
    import Status from '$lib/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    import { Label } from '$lib/components/ui/label';
    import { Button } from '$lib/components/ui/button';

    export let requirementId;
    export let requirementName;
    export let deadline;
    export let submissionStatus;
    export let studentId;
</script>

<div
    class="my-2 flex flex-col justify-between rounded-xl bg-muted placeholder:border-dark-primary border-b-2 p-4 sm:flex-row"
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
            <Button
                href="/file/submission/{studentId}/{requirementId}"
                target="_blank"
                class="rounded-xl bg-dark-primary text-dark-primary-text hover:bg-opacity-90"
                >View</Button
            >
        {/if}
        <Button
            href="/requirement/{requirementId}/upload"
            class="rounded-xl bg-dark-primary text-dark-primary-text hover:bg-opacity-90"
            >Submit</Button
        >
        <Status type={submissionStatus} />
    </div>
</div>
