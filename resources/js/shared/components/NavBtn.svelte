<script>
    import { Link, page } from '@inertiajs/svelte';

    export let href = '#';
    export let Icon;

    $: isActive = $page.url === href ? 'active-nav' : '';
</script>

<li class="sidebar-div {isActive}">
    <Link {href}>
        <div class="sidebar-icon">
            <svelte:component this={Icon} />
        </div>

        <slot />
    </Link>
</li>
