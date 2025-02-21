<script>
    import Icon from '@iconify/svelte';

    export let clickHandler = null;
    export let isActive = false;
    export let isAscending = true;
    export let first = false;
</script>

<th
    scope="col"
    class="border-black p-2 dark:border-white
        {first ? '' : 'border-l-2'}  {clickHandler ? 'cursor-pointer' : ''}"
    on:click={clickHandler}
>
    <div class="flex h-full w-full flex-row items-center justify-center gap-2">
        <slot />
        {#if isActive}
            <Icon icon="bxs:up-arrow" vFlip={isAscending} />
        {/if}
    </div>
</th>
