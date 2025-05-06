<script>
    import { colorVariants } from '$lib/customVariants';
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
                : colorVariants.secondary;
</script>

{#if status !== 'None' || isAdmin}
    <Button {href} class="w-24 {colors}" target="_blank">
        {status}
    </Button>
{/if}
