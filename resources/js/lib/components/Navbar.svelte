<script lang="ts">
    import * as Tooltip from '$lib/components/ui/tooltip';
    import NavBtn from './NavBtn.svelte';
    import Toggle from './ThemeSwitch.svelte';
    import { page } from '@inertiajs/svelte';
</script>

<nav
    class="sticky inset-y-0 left-0 z-50 hidden max-h-screen min-h-full w-14 shrink-0 bg-custom-primary sm:flex sm:flex-col"
>
    <div class="flex flex-col items-center gap-4 px-2 sm:py-5">
        <NavBtn href="/" icon_handle="tabler:home">Home</NavBtn>
        <NavBtn href="/dashboard" icon_handle="ic:outline-dashboard"
            >Dashboard</NavBtn
        >
        <NavBtn href="/privacy" icon_handle="uil:lock">Privacy</NavBtn>
        <NavBtn href="/account" icon_handle="octicon:person-16">Account</NavBtn>
        {#if $page.props.auth.user}
            <NavBtn
                href="/logout"
                method="post"
                icon_handle="material-symbols:logout-rounded">Logout</NavBtn
            >
        {/if}
    </div>
    <div class="mt-auto flex flex-col items-center gap-4 px-2 sm:py-5">
        <Tooltip.Root>
            <Tooltip.Trigger>
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
                >
                    <Toggle />
                    <span class="sr-only">Toggle Theme</span>
                </div>
            </Tooltip.Trigger>
            <Tooltip.Content side="right">Toggle Theme</Tooltip.Content>
        </Tooltip.Root>
    </div>
</nav>

<nav
    class="max-w-screen sticky inset-y-0 left-0 z-40 flex h-14 min-w-full shrink-0 flex-row items-center bg-custom-primary sm:hidden"
>
    <div class="flex flex-row items-center gap-4 px-2">
        <NavBtn href="/" side="bottom" icon_handle="tabler:home">Home</NavBtn>
        <NavBtn
            href="/dashboard"
            side="bottom"
            icon_handle="ic:outline-dashboard">Dashboard</NavBtn
        >
        <NavBtn href="/privacy" side="bottom" icon_handle="uil:lock"
            >Privacy</NavBtn
        >
        <NavBtn href="/account" side="bottom" icon_handle="octicon:person-16"
            >Account</NavBtn
        >
        {#if $page.props.auth.user}
            <NavBtn
                href="/logout"
                method="post"
                side="bottom"
                icon_handle="material-symbols:logout-rounded">Logout</NavBtn
            >
        {/if}
    </div>

    <div class="ml-auto flex flex-col items-center gap-4 px-2">
        <Tooltip.Root>
            <Tooltip.Trigger>
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:text-foreground md:h-8 md:w-8"
                >
                    <Toggle />
                    <span class="sr-only">Toggle Theme</span>
                </div>
            </Tooltip.Trigger>
            <Tooltip.Content side="right">Toggle Theme</Tooltip.Content>
        </Tooltip.Root>
    </div>
</nav>
