<script>
    import NavBtn from './NavBtn.svelte';
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
    import Toggle from './ThemeSwitch.svelte';
    import Menu from '@assets/menu_logo.svelte';
    import Close from '@assets/x.svelte';
    import { slide } from 'svelte/transition';

    let isMenuOpen = false;

    function toggleMenu() {
        isMenuOpen = !isMenuOpen;
    }
</script>

<!-- Desktop Navbar -->
<nav
    class="
    sticky left-0 top-0 hidden
    max-h-screen min-h-full
    w-24 shrink-0 flex-col
    justify-between bg-light-primary
    text-center dark:bg-dark-primary sm:flex
"
>
    <ul class="flex flex-col">
        <NavBtn href="/" Icon={Home}>Home</NavBtn>
        <NavBtn href="/dashboard" Icon={Dashboard}>Dashboard</NavBtn>
        <NavBtn href="/privacy" Icon={Privacy}>Privacy</NavBtn>
        <NavBtn href="/account" Icon={Account}>Account</NavBtn>
    </ul>

    <div class="justify-center py-4">
        <Toggle />
    </div>
</nav>

<!-- Mobile Navbar -->
<nav
    class="
    min-w-screen sticky top-0
    flex flex-col
    bg-light-primary text-light-primary-text
    dark:bg-dark-primary dark:text-dark-primary-text
    sm:hidden
"
>
    <div class="flex flex-row items-center justify-between">
        <div class="content-center p-4">
            <p class="font-bold italic">Portalis:</p>
            CS195 Portal
        </div>

        <button class="cursor-pointer p-4" on:click={toggleMenu}>
            {#if isMenuOpen}
                <Close />
            {:else}
                <Menu />
            {/if}
        </button>
    </div>
    {#if isMenuOpen}
        <div
            class="z-50 w-screen"
            transition:slide={{ axis: 'y', duration: 150 }}
        >
            <div
                class="flex flex-col justify-between bg-light-primary/95 dark:bg-dark-primary/95"
            >
                <ul
                    class="flex w-full flex-col content-center justify-center text-center"
                >
                    <NavBtn href="/" Icon={Home}>Home</NavBtn>
                    <NavBtn href="/dashboard" Icon={Dashboard}>Dashboard</NavBtn
                    >
                    <NavBtn href="/privacy" Icon={Privacy}>Privacy</NavBtn>
                    <NavBtn href="/account" Icon={Account}>Account</NavBtn>
                </ul>

                <div class="flex items-center justify-center py-8">
                    <Toggle />
                </div>
            </div>
        </div>

        <div class="h-auto"></div>
    {/if}
</nav>
