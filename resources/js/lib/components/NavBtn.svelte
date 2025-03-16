<script>
    import { Link, page } from '@inertiajs/svelte';
    import Icon from "@iconify/svelte";
    import * as Tooltip from "$lib/components/ui/tooltip";

    export let href = '#';
    export let icon_handle;

    $: isActive = '/' + $page.url.split('/')[1] === href ? 'bg-dark-secondary text-dark-secondary-text' : 'hover:text-dark-primary-text text-dark-secondary';
</script>

<Tooltip.Root>
    <Tooltip.Trigger asChild let:builder>
        <a
        href={href}
        class="{isActive} flex h-9 w-9 items-center justify-center rounded-lg transition-colors md:h-8 md:w-8"
        use:builder.action
        {...builder}
        >
            <Icon icon={icon_handle} class="text-2xl"/>
            <span class="sr-only"> <slot /> </span>
        </a>
    </Tooltip.Trigger>
    <Tooltip.Content side="right"> <slot /> </Tooltip.Content>
</Tooltip.Root>
