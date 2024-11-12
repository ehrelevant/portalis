<script>
    import NavBtn from './NavBtn.svelte';
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
    import Toggle from './ThemeSwitch.svelte';
    import Menu from '@assets/menu_logo.svelte';

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
