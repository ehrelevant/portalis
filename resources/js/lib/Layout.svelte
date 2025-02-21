<script>
    import Navbar from '$lib/components/Navbar.svelte';
</script>

<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
