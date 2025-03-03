<script>
    import { onMount } from 'svelte';
    import Icon from "@iconify/svelte";

    
    let lightModeIcon = "uil:sun"
    let darkModeIcon = "solar:moon-outline";

    export let icon = lightModeIcon;
    let darkMode = true;

    onMount(() => {
        updateTheme();
    });

    function handleSwitchMode() {
        darkMode = !darkMode;
        updateTheme();
    }

    function updateTheme() {
        if (darkMode) {
            icon = darkModeIcon;

            // eslint-disable-next-line no-undef
            document.documentElement.classList.add('dark');
        } else {
            icon = lightModeIcon;

            // eslint-disable-next-line no-undef
            document.documentElement.classList.remove('dark');
        }
    }
</script>

<button
    class="
    h-16 w-16
    cursor-pointer content-center
    justify-items-center hover:text-dark-primary-text
    transition-colors text-dark-secondary"
    on:click={handleSwitchMode}
>
    <Icon icon={icon} class="text-2xl"/>
</button>
