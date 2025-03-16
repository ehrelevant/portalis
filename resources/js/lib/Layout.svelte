<script>
    import Navbar from '$lib/components/Navbar.svelte';
    import { ModeWatcher } from 'mode-watcher';
    import { page } from '@inertiajs/svelte';
    import { Toaster } from '$lib/components/ui/sonner';
    import { toast } from 'svelte-sonner';

    $: {
        if ($page.props.flash.message) toast($page.props.flash.message);
        if ($page.props.flash.success) toast.success($page.props.flash.success);
        if ($page.props.flash.info) toast.info($page.props.flash.info);
        if ($page.props.flash.warning) toast.warning($page.props.flash.warning);
        if ($page.props.flash.error) toast.error($page.props.flash.error);
    }
</script>

<ModeWatcher />
<Toaster richColors />
<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>
