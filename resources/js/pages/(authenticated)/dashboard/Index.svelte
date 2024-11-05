<script>
    import { router } from '@inertiajs/svelte';
</script>

<div>Dashboard</div>

<!-- Temporary Button for logging out -->
<button on:click={() => router.post('/logout')}>Logout</button>
