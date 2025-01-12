<script>
    export let open = false;

    function toggleAccordion() {
        open = !open;
    }
</script>

<div class="flex flex-col">
    <button
        class="cursor-pointer bg-light-primary px-4 py-2 text-left dark:bg-dark-primary {open
            ? 'rounded-t-xl'
            : 'rounded-xl'}"
        on:click={toggleAccordion}
        type="button"
    >
        <slot name="summary" />
    </button>

    {#if open}
        <div class="rounded-b-xl bg-gray-50 p-4 dark:bg-gray-900">
            <slot />
        </div>
    {/if}
</div>
