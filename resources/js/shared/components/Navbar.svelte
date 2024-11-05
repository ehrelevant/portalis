<script>
    import { inertia } from '@inertiajs/svelte';
    
    import Home from '@assets/home_logo.svelte';
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Account from '@assets/account_logo.svelte';
</script>

<nav class="
    hidden md:block
    bg-light-primary
    w-24
    text-center text-light-primary-text
">
    <ul>
        <a use:inertia href="/">
            <div class="
                transition ease-linear
                py-4
                hover:bg-light-secondary
                hover:text-light-secondary-text
            ">
                <div class="flex justify-center ">
                    <Home />
                </div>
                Home
            </div>
        </a>

        <a use:inertia href="/dashboard">
            <div class="
                transition ease-linear
                py-4
                hover:bg-light-secondary
                hover:text-light-secondary-text
            ">
                <div class="flex justify-center ">
                    <Dashboard />
                </div>
                Dashboard
            </div>
        </a>

        <a use:inertia href="/privacy">
            <div class="
                transition ease-linear
                py-4
                hover:bg-light-secondary
                hover:text-light-secondary-text
            ">
                <div class="flex justify-center ">
                    <Privacy />
                </div>
                Privacy
            </div>
        </a>

        <a use:inertia href="/account">
            <div class="
                transition ease-linear
                py-4
                hover:bg-light-secondary
                hover:text-light-secondary-text
            ">
                <div class="flex justify-center ">
                    <Account />
                </div>
                Account
            </div>
        </a>
    </ul>
</nav>
