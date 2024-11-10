<script>
    import { router } from '@inertiajs/svelte';
</script>

<div class="main-screen">
    Dashboard
    <button on:click={() => router.post('/logout')}>Logout</button>
</div>
