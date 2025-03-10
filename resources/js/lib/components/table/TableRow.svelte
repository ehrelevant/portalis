<script>
    export let header = false;
    export let disabled = false;
</script>

<tr
    class="border-border {header ? 'bg-secondary' : 'border-t-2'} {disabled
        ? 'bg-destructive/20'
        : ''}"
>
    <slot />
</tr>
