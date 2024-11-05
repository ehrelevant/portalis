<script>
    import Navbar from '@shared/components/Navbar.svelte';
</script>

<main class="flex min-h-screen flex-row bg-light-secondary-text">
    <Navbar />
    <slot />
</main>
