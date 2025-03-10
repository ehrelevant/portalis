<script lang="ts">
    import * as Tooltip from "$lib/components/ui/tooltip";
    import NavBtn from './NavBtn.svelte';
    import Toggle from './ThemeSwitch.svelte';
    import Menu from '$assets/menu_logo.svelte';
    import Close from '$assets/x.svelte';
    import { slide } from 'svelte/transition';

    let isMenuOpen = false;

    function toggleMenu() {
        isMenuOpen = !isMenuOpen;
    }
</script>

<aside class="bg-dark-primary sticky inset-y-0 left-0 z-10 hidden max-h-screen min-h-full w-14 flex-col sm:flex">
    <nav class="flex flex-col items-center gap-4 px-2 sm:py-5">
        <NavBtn href="/" icon_handle="tabler:home">Home</NavBtn>
        <NavBtn href="/dashboard" icon_handle="ic:outline-dashboard">Dashboard</NavBtn>
        <NavBtn href="/privacy" icon_handle="uil:lock">Privacy</NavBtn>
        <NavBtn href="/account" icon_handle="octicon:person-16">Account</NavBtn>
    </nav>
    <nav class="mt-auto flex flex-col items-center gap-4 px-2 sm:py-5">
      <Tooltip.Root>
        <Tooltip.Trigger>
          <div
            class="text-muted-foreground hover:text-foreground flex h-9 w-9 items-center justify-center rounded-lg transition-colors md:h-8 md:w-8"
          >
            <Toggle />
            <span class="sr-only">Toggle Theme</span>
        </div>
        </Tooltip.Trigger>
        <Tooltip.Content side="right">Toggle Theme</Tooltip.Content>
      </Tooltip.Root>
    </nav>
  </aside>