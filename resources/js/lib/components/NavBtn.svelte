<script>
    import { Link, page } from '@inertiajs/svelte';
    import Icon from '@iconify/svelte';
    import * as Tooltip from '$lib/components/ui/tooltip';

    export let href = '#';
    export let method = 'get';
    export let icon_handle;
    export let side = 'right';

    $: isActive =
        '/' + $page.url.split('/')[1] === href
            ? 'bg-custom-secondary text-black'
            : 'hover:text-white text-custom-secondary';
</script>

<Tooltip.Root>
    <Tooltip.Trigger>
        <Link
            {href}
            {method}
            as={method === 'get' ? 'a' : 'button'}
            class="{isActive} flex h-9 w-9 items-center justify-center rounded-lg transition-colors md:h-8 md:w-8"
        >
            <Icon icon={icon_handle} class="text-2xl" />
            <span class="sr-only"> <slot /> </span>
        </Link>
    </Tooltip.Trigger>
    <Tooltip.Content {side}><slot /></Tooltip.Content>
</Tooltip.Root>
