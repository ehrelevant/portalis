<script>
    import Navbar from '@shared/components/Navbar.svelte';
</script>

<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
