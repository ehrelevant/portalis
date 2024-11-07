<script>
    import { inertia } from '@inertiajs/svelte';
    
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
</script>

<div class="
    hidden md:block
    fixed overflow-hidden
    bg-light-primary
    w-24 h-screen
    text-center text-light-primary-text
">
    <a use:inertia href="/">
        <div class="sidebar-div">
            <div class="sidebar-icon">
                <Home />
            </div>
            
            <div>
                Home
            </div>
        </div>
    </a>

    <a use:inertia href="/dashboard">
        <div class="sidebar-div">
            <div class="sidebar-icon">
                <Dashboard />
            </div>
            
            <div>
                Dashboard
            </div>
        </div>
    </a>

    <a use:inertia href="/privacy">
        <div class="sidebar-div">
            <div class="sidebar-icon">
                <Privacy />
            </div>
            
            <div>
                Privacy
            </div>
        </div>
    </a>

    <a use:inertia href="/account">
        <div class="sidebar-div">
            <div class="sidebar-icon">
                <Account />
            </div>
            
            <div>
                Account
            </div>
        </div>
    </a>
</div>
