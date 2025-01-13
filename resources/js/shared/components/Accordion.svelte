<script>
    import { slide } from 'svelte/transition';
    import ArrowRight from '@assets/arrow_right.svelte';

    export let open = false;

    function toggleAccordion() {
        open = !open;
    }
</script>

<!-- Only works when parent containers are flexboxes -->
<div class="flex flex-col">
    <button
        class="flex cursor-pointer flex-row bg-light-primary p-4 text-left transition-[border-radius] dark:bg-dark-primary {open
            ? 'rounded-t-xl'
            : 'rounded-xl'}"
        on:click={toggleAccordion}
        type="button"
    >
        <div
            class="aspect-square h-full transition-transform aria-expanded:rotate-90"
            aria-expanded={open}
        >
            <ArrowRight />
        </div>
        <slot name="summary" />
    </button>

    {#if open}
        <div
            class="rounded-b-xl bg-gray-50 p-4 dark:bg-gray-900"
            transition:slide={{ axis: 'y', duration: 100, delay: 50 }}
        >
            <slot />
        </div>
    {/if}
</div>
