<script>
    import { usePoll } from '@inertiajs/svelte';

    usePoll(2000);
</script>

<div
    class="h-[28em] w-full border-collapse overflow-auto rounded-lg border-4 border-border pb-10"
>
    <table class="relative w-full border-collapse">
        <slot />
    </table>
</div>
