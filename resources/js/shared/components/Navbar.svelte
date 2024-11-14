<script>
    import NavBtn from './NavBtn.svelte';
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
    import Toggle from './ThemeSwitch.svelte';
    import Menu from '@assets/menu_logo.svelte';
    import Close from '@assets/x.svelte';

    let isMenuOpen = false;

    function openMenu() {
        isMenuOpen = true;
    }

    function closeMenu() {
        isMenuOpen = false;
    }
</script>

<!-- Desktop Navbar -->
<nav
    class="
    sticky top-0 left-0 hidden
    min-h-full max-h-screen
    w-24 flex-col justify-between
    bg-light-primary text-center
    dark:bg-dark-primary sm:flex
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
    sticky flex top-0
    min-w-screen
    h-24 flex-row
    justify-between
    text-light-primary-text dark:text-dark-primary-text
    bg-light-primary dark:bg-dark-primary 
    sm:hidden
">
    {#if isMenuOpen}
        <div class="z-50 h-screen w-screen">
            <div>
                <div class="flex flex-row h-24 justify-between content-center">
                    <div class="p-4 content-center"> <p class="font-bold italic">Portalis:</p> CS195 Portal</div> 
                    <button class="p-4 cursor-pointer" on:click={closeMenu}> <Close /> </button>
                </div>

                <div class="flex flex-col justify-between bg-light-primary/95 dark:bg-dark-primary/95">
                    <ul class="flex flex-col w-full justify-center content-center text-center">
                        <NavBtn href="/" Icon={Home}>Home</NavBtn>
                        <NavBtn href="/dashboard" Icon={Dashboard}>Dashboard</NavBtn>
                        <NavBtn href="/privacy" Icon={Privacy}>Privacy</NavBtn>
                        <NavBtn href="/account" Icon={Account}>Account</NavBtn>
                    </ul>

                    <div class="flex justify-center items-center py-8">
                        <Toggle />
                    </div>
                </div>
            </div>

            <div class="h-auto">
            </div>
        </div>
	{:else}
        <div class="p-4 content-center"> <p class="font-bold italic">Portalis:</p> CS195 Portal </div>
        
        <button class="p-4 cursor-pointer" on:click={openMenu}> <Menu /> </button>
	{/if}

</nav>