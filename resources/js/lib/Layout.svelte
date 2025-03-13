<script>
    import Navbar from '$lib/components/Navbar.svelte';
    import { ModeWatcher } from 'mode-watcher';
    import { page } from '@inertiajs/svelte';
    import { Toaster } from '$lib/components/ui/sonner';
    import { toast } from 'svelte-sonner';

    $: {
        if ($page.props.flash.info) {
            toast.info($page.props.flash.info);
        }
        if ($page.props.flash.error) {
            toast.error($page.props.flash.error);
        }
    }
</script>

<ModeWatcher />
<Toaster />
<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
