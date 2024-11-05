<script>
    import { inertia } from '@inertiajs/svelte';
    import Home from '@assets/home_logo.svg';
    import Dashboard from '@assets/dashboard_logo.svg';
    import Privacy from '@assets/privacy_logo.svg';
    import Account from '@assets/account_logo.svg';
</script>

<nav class="
    bg-light-primary
    w-24
    text-center text-light-primary-text
">
    <ul>
        <a use:inertia href="/">
            <li class="
                py-4 my-1
            ">
                <div class="flex justify-center ">
                    <img alt="Home Logo" src={ Home } class="w-10"/>
                </div>
                Home
            </li>
        </a>

        <a use:inertia href="/dashboard">
            <li class="
                py-4 my-1
            ">
                <div class="flex justify-center ">
                    <img alt="Dashboard Logo" src={ Dashboard } class="w-10"/>
                </div>
                Dashboard
            </li>
        </a>

        <a use:inertia href="/privacy">
            <li class="
                py-4 my-1
            ">
                <div class="flex justify-center ">
                    <img alt="Privacy Logo" src={ Privacy } class="w-10"/>
                </div>
                Privacy
            </li>
        </a>

        <a use:inertia href="/account">
            <li class="
                py-4 my-1
            ">
                <div class="flex justify-center ">
                    <img alt="Account Logo" src={ Account } class="w-10"/>
                </div>
                Account
            </li>
        </a>
    </ul>
</nav>
