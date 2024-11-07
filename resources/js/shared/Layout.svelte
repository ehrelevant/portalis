<script>
    import Navbar from '@shared/components/Navbar.svelte';
</script>

<main class="flex min-h-screen min-w-screen flex-row">
    <Navbar />
    <slot />
</main>
