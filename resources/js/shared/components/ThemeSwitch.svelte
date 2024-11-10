<script>
    import LightMode from '@assets/lightmode_logo.svelte';
    import DarkMode from '@assets/darkmode_logo.svelte';

    export let icon = LightMode;
    let darkMode = true;

    function handleSwitchMode() {
        darkMode = !darkMode;

        if (darkMode) {
            icon = DarkMode;
            document.documentElement.classList.add('dark');
        } else {
            icon = LightMode;
            document.documentElement.classList.remove('dark');
        }
    }
</script>

<button
    class="
    h-16 w-16
    cursor-pointer content-center
    justify-items-center text-light-primary-text
    hover:text-light-secondary dark:text-dark-primary-text
    dark:hover:text-dark-secondary"
    on:click={handleSwitchMode}
>
    <svelte:component this={icon} />
</button>
