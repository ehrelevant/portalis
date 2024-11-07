<script>
    import { inertia, page } from '@inertiajs/svelte';
    
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
    import Toggle from '@assets/theme_switch.svelte';
</script>

<div class="
    hidden md:grid
    content-between
    fixed overflow-auto
    bg-light-primary dark:bg-dark-primary
    w-24 h-screen
    text-center
">
    <div>
        <a use:inertia href="/">
            <div class="sidebar-div" class:active-nav={$page.url === '/'}>
                <div class="sidebar-icon">
                    <Home />
                </div>
                
                <div>
                    Home
                </div>
            </div>
        </a>

        <a use:inertia href="/dashboard">
            <div class="sidebar-div" class:active-nav={$page.url.startsWith('/dashboard')}>
                <div class="sidebar-icon">
                    <Dashboard />
                </div>
                
                <div>
                    Dashboard
                </div>
            </div>
        </a>

        <a use:inertia href="/privacy">
            <div class="sidebar-div" class:active-nav={$page.url.startsWith('/privacy')}>
                <div class="sidebar-icon">
                    <Privacy />
                </div>
                
                <div>
                    Privacy
                </div>
            </div>
        </a>

        <a use:inertia href="/account">
            <div class="sidebar-div" class:active-nav={$page.url.startsWith('/account')}>
                <div class="sidebar-icon">
                    <Account />
                </div>
                
                <div>
                    Account
                </div>
            </div>
        </a>
    </div>

    <div class = "py-4 justify-center">
        <Toggle />
    </div>
</div>
