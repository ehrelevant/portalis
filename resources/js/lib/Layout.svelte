<script>
    import Navbar from '$lib/components/Navbar.svelte';
    import { ModeWatcher } from 'mode-watcher';
</script>

<ModeWatcher />
<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
