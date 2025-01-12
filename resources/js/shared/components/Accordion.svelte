<script>
    import { slide } from 'svelte/transition';

    export let open = false;

    function toggleAccordion() {
        open = !open;
    }
</script>

<div class="flex flex-col">
    <button
        class="cursor-pointer bg-light-primary p-4 text-left transition-all dark:bg-dark-primary {open
            ? 'rounded-t-xl'
            : 'rounded-xl'}"
        on:click={toggleAccordion}
        type="button"
    >
        <slot name="summary" />
    </button>

    {#if open}
        <div
            class="rounded-b-xl bg-gray-50 p-4 dark:bg-gray-900"
            in:slide={{ axis: 'y', duration: 100, delay: 50 }}
            out:slide={{ axis: 'y', duration: 100, delay: 50 }}
        >
            <slot />
        </div>
    {/if}
</div>
