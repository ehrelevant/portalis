<script>
    import { colorVariants } from '$lib/customVariants';
    import Badge from './ui/badge/badge.svelte';
    import Button from './ui/button/button.svelte';

    export let href;
    export let status;
    export let isAdmin = false;

    $: colors =
        status === 'Accepted'
            ? colorVariants.green
            : status === 'For Review'
              ? colorVariants.yellow
              : status === 'Returned'
                ? colorVariants.red
                : 'bg-secondary text-secondary-foreground hover:bg-secondary/80';
</script>

{#if status !== 'None' || isAdmin}
    <Button {href} class={colors} target="_blank">
        {status}
    </Button>
{/if}
