<script>
    import Navbar from '$lib/components/Navbar.svelte';
    import { ModeWatcher } from 'mode-watcher';
    import { page } from '@inertiajs/svelte';
    import * as Alert from '$lib/components/ui/alert';
    import Icon from '@iconify/svelte';
    import { onMount } from 'svelte';
    import { fade } from 'svelte/transition';

    // In seconds
    const alertDuration = 15;
    let alertCounter = 0;
    $: {
        if ($page.props.flash.error || $page.props.flash.info) {
            alertCounter = alertDuration;
        }
    }

    onMount(() => {
        const counterInterval = setInterval(() => {
            alertCounter -= 1;
        }, 1000);

        return () => clearInterval(counterInterval);
    });
</script>

<ModeWatcher />
<main class="min-w-screen flex min-h-screen flex-col sm:flex-row">
    <Navbar />
    <slot />
</main>

{#if $page.props.flash.error && alertCounter > 0}
    <div transition:fade={{ duration: 150 }}>
        <Alert.Root
            variant="destructive"
            class="absolute bottom-4 left-0 right-0 z-50 mx-auto w-72 min-w-48"
        >
            <Icon icon="material-symbols:error" />
            <Alert.Title class="font-bold">Error</Alert.Title>
            <Alert.Description>{$page.props.flash.error}</Alert.Description>
        </Alert.Root>
    </div>
{:else if $page.props.flash.info && alertCounter > 0}
    <div transition:fade={{ duration: 150 }}>
        <Alert.Root
            class="absolute bottom-4 left-0 right-0 z-40 mx-auto w-72 min-w-48"
        >
            <Icon icon="material-symbols:info" />
            <Alert.Title class="font-bold">Info</Alert.Title>
            <Alert.Description>{$page.props.flash.info}</Alert.Description>
        </Alert.Root>
    </div>
{/if}
