<script>
    import Icon from '@iconify/svelte';
    import { toggleMode } from 'mode-watcher';
</script>

<button
    on:click={toggleMode}
    class="
    aspect-square w-full
    cursor-pointer content-center
    justify-items-center text-custom-secondary
    transition-colors hover:text-white"
>
    <Icon
        icon="uil:sun"
        class="block h-full w-full transition-all dark:hidden"
    />
    <Icon
        icon="solar:moon-outline"
        class="hidden h-full w-full transition-all dark:block"
    />
    <span class="sr-only">Toggle theme</span>
</button>
