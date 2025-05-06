<script>
    export let header = false;
    export let disabled = false;
    export let selected = false;
</script>

<tr
    class="z-30 border-border transition-colors {header
        ? 'sticky top-0 bg-secondary'
        : 'border-b-2'} {selected
        ? 'bg-blue/20 hover:bg-blue/30'
        : ''} {disabled
        ? 'bg-destructive/20 hover:bg-destructive/30'
        : !header
          ? 'hover:bg-accent/40'
          : ''}"
>
    <slot />
</tr>
