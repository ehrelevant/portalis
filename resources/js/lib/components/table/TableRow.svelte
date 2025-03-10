<script>
    export let header = false;
</script>

<tr class="border-border {header ? 'bg-secondary' : 'border-t-2'}">
    <slot />
</tr>
