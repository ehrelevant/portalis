<script>
    import { Link, page } from '@inertiajs/svelte';

    export let href = '#';
    export let Icon;

    $: isActive = $page.url === href ? 'active-nav' : '';
</script>

<li class="sidebar-div flex items-center justify-center {isActive}">
    <Link {href} class="h-full w-full py-4">
        <div class="sidebar-icon">
            <svelte:component this={Icon} />
        </div>

        <slot />
    </Link>
</li>
