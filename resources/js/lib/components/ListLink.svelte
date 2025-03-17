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
    class="order-r my-2 flex flex-col justify-between rounded-xl border-b-2 border-dark-primary bg-muted p-4 sm:flex-row
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
                <Dialog.Trigger>
                    <Button>View Remarks</Button>
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
            <Button href={viewHref} target="_blank" class={colorVariants.blue}
                >View Submission</Button
            >
        {/if}
        {#if isLinkActive}
            <Link href={submitHref} target="_blank"
                ><Button class="bg-dark-primary hover:bg-opacity-90 text-white">Submit</Button></Link
            >
        {/if}
        <Status type={status} />
    </div>
</div>
