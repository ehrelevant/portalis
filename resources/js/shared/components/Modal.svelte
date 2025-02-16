<script>
    import XMark from '@/assets/x.svelte';

    export let isOpen = false;

    function closeModal() {
        isOpen = false;
    }
</script>

{#if isOpen}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 text-light-primary-text backdrop-blur-md dark:text-dark-primary-text"
    >
        <div
            class="flex max-h-full flex-col items-end gap-2 overflow-auto rounded-xl bg-white p-8 dark:bg-black"
        >
            <button on:click={closeModal}>
                <XMark />
            </button>
            <slot />
        </div>
    </div>
{/if}
