<script>
    import Navbar from '$lib/components/Navbar.svelte';
    import { ModeWatcher } from 'mode-watcher';
    import { usePoll } from '@inertiajs/svelte';

    // Regularly poll server every 30 seconds
    usePoll(30000);
</script>

<ModeWatcher />
<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
