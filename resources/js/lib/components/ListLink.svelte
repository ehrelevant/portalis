<script>
    import Status from '$lib/components/Status.svelte';

    import { Label } from '$lib/components/ui/label';
    import { Button } from '$lib/components/ui/button';
    import { Link } from '@inertiajs/svelte';

    export let name;
    export let deadline;
    export let status;
    export let viewHref = null;
    export let submitHref;

    $: deadlineDate = deadline && new Date(deadline);
    $: isLate = deadline && deadlineDate < new Date();
    $: isLinkActive = !isLate && ['None', 'Returned'].includes(status);
</script>

<Link
    href={submitHref}
    class="order-r my-2 flex flex-col justify-between rounded-xl border-b-2 border-dark-primary bg-muted p-4 sm:flex-row
        {!isLinkActive ? 'pointer-events-none' : ''} {isLate
        ? 'bg-red/30'
        : ''}"
>
    <div
        class="pointer-events-auto flex flex-col items-center justify-center sm:items-start"
    >
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
        class="pointer-events-auto flex flex-col content-center items-center justify-center gap-2 sm:flex-row"
    >
        {#if viewHref && status !== 'None'}
            <Button
                href={viewHref}
                target="_blank"
                class="rounded-xl bg-dark-primary text-dark-primary-text hover:bg-opacity-90"
                >View</Button
            >
        {/if}
        <Status type={status} />
    </div>
</Link>
