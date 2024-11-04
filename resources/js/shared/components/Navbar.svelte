<script>
    import { inertia } from '@inertiajs/svelte';
</script>

<nav class="bg-slate-100">
    <ul>
        <a use:inertia href="/"><li>Home</li></a>
        <a use:inertia href="/dashboard"><li>Dashboard</li></a>
        <a use:inertia href="/privacy"><li>Privacy</li></a>
        <a use:inertia href="/account"><li>Account</li></a>
    </ul>
</nav>
