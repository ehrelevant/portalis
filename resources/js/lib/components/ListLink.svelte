<script>
    import Status from '$lib/components/Status.svelte';

    import * as Dialog from '$lib/components/ui/dialog/index';
    import { Label } from '$lib/components/ui/label';
    import { Button } from '$lib/components/ui/button';
    import { Link } from '@inertiajs/svelte';
    import { colorVariants } from '$lib/customVariants';

    export let name;
    export let deadline;
    export let status;
    export let viewHref = null;
    export let submitHref;
    export let remarks = null;

    $: deadlineDate = deadline && new Date(deadline);
    $: isLate = deadline && deadlineDate < new Date();
    $: isLinkActive = !isLate && ['None', 'Returned'].includes(status);
</script>

<div
    class="order-r my-2 flex flex-col gap-4 justify-between rounded-xl border-b-2 border-custom-primary bg-muted p-4 lg:flex-row
        {isLate ? 'bg-red/30' : ''}"
>
    <div class="flex flex-col items-center justify-center sm:items-start">
        <Label class="text-lg">{name}</Label>
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
        {#if remarks && ['Returned'].includes(status)}
            <Dialog.Root>
                <Dialog.Trigger class="w-full sm:w-auto">
                    <Button class="w-full sm:w-auto">View Remarks</Button>
                </Dialog.Trigger>
                <Dialog.Content class="sm:max-w-[425px]">
                    <Dialog.Header>
                        <Dialog.Title>Submission Remarks</Dialog.Title>
                        <Dialog.Description class="min-h-24"
                            >{remarks}</Dialog.Description
                        >
                    </Dialog.Header>
                </Dialog.Content>
            </Dialog.Root>
        {/if}
        {#if viewHref && status !== 'None'}
            <Button href={viewHref} target="_blank" class="{colorVariants.blue} w-full sm:w-auto"
                >View Submission</Button
            >
        {/if}
        {#if isLinkActive}
        <Button href={submitHref} target="_blank" class="bg-custom-primary hover:bg-opacity-90 text-white w-full sm:w-auto"
            >Submit</Button
        >
        {/if}
        <Status type={status} />
    </div>
</div>
