<script>
    export let header = false;
    export let disabled = false;
</script>

<tr
    class="border-border transition-colors {header
        ? 'bg-secondary'
        : 'border-t-2'} {disabled
        ? 'bg-destructive/20 hover:bg-destructive/30'
        : !header
          ? 'hover:bg-accent/40'
          : ''}"
>
    <slot />
</tr>
