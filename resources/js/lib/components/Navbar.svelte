<script lang="ts">
    import * as Tooltip from '$lib/components/ui/tooltip';
    import NavBtn from './NavBtn.svelte';
    import Toggle from './ThemeSwitch.svelte';

    let isMenuOpen = false;
</script>

<aside
    class="sticky inset-y-0 left-0 z-30 hidden max-h-screen min-h-full w-14 shrink-0 bg-dark-primary sm:flex sm:flex-col"
>
    <nav class="flex flex-col items-center gap-4 px-2 sm:py-5">
        <NavBtn href="/" icon_handle="tabler:home">Home</NavBtn>
        <NavBtn href="/dashboard" icon_handle="ic:outline-dashboard"
            >Dashboard</NavBtn
        >
        <NavBtn href="/privacy" icon_handle="uil:lock">Privacy</NavBtn>
        <NavBtn href="/account" icon_handle="octicon:person-16">Account</NavBtn>
    </nav>
    <nav class="mt-auto flex flex-col items-center gap-4 px-2 sm:py-5">
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
    </nav>
</aside>

<aside
    class="sticky inset-y-0 left-0 z-30 flex flex-row max-w-screen min-w-full h-14 items-center shrink-0 bg-dark-primary sm:hidden"
>
    <nav class="flex flex-row items-center gap-4 px-2">
        <NavBtn href="/" icon_handle="tabler:home">Home</NavBtn>
        <NavBtn href="/dashboard" icon_handle="ic:outline-dashboard">Dashboard</NavBtn>
        <NavBtn href="/privacy" icon_handle="uil:lock">Privacy</NavBtn>
        <NavBtn href="/account" icon_handle="octicon:person-16">Account</NavBtn>
    </nav>

    <nav class="ml-auto flex flex-col items-center gap-4 px-2">
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
    </nav>
</aside>