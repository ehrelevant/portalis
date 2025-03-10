<script>
    import Icon from '@iconify/svelte';

    export let clickHandler = null;
    export let isActive = false;
    export let isAscending = true;
</script>

<th
    scope="col"
    class="whitespace-nowrap border-black p-2 dark:border-white {clickHandler
        ? 'cursor-pointer'
        : ''}"
    on:click={clickHandler}
>
    <div class="flex h-full w-full flex-row items-center justify-center gap-2">
        <slot />
        {#if isActive}
            <Icon icon="bxs:up-arrow" vFlip={isAscending} />
        {/if}
    </div>
</th>
