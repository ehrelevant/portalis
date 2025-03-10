<script>
    import Icon from '@iconify/svelte';

    export let clickHandler = null;
    export let isActive = false;
    export let isAscending = true;
</script>

<th
    scope="col"
    class="whitespace-nowrap border-black px-6 py-2 dark:border-white {clickHandler
        ? 'cursor-pointer'
        : ''}"
    on:click={clickHandler}
>
    <div
        class="flex h-full w-full flex-row items-center justify-center gap-2 text-center"
    >
        <slot />
        {#if isActive}
            <Icon icon="tabler:caret-up-filled" vFlip={isAscending} />
        {:else if clickHandler}
            <Icon icon="tabler:caret-up-down-filled" vFlip={isAscending} />
        {/if}
    </div>
</th>
