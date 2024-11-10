<script>
    import LightMode from '@assets/lightmode_logo.svelte';
    import DarkMode from '@assets/darkmode_logo.svelte';

    export let icon = LightMode;
    let darkMode = true;

    function handleSwitchMode() {
        darkMode = !darkMode;
        
        darkMode ? icon = DarkMode : icon = LightMode;
        darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    }
</script>

<button class = "
    h-16 w-16
    justify-items-center content-center
    text-light-primary-text dark:text-dark-primary-text
    hover:text-light-secondary dark:hover:text-dark-secondary
    cursor-pointer"

    on:click={handleSwitchMode}
>
<svelte:component this={icon} />
</button>