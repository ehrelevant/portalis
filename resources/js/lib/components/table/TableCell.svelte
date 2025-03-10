<script>
    export let center = false;
</script>

<td
    class="whitespace-nowrap border-black px-6 py-2 dark:border-white {center
        ? 'text-center'
        : ''}"
>
    <slot />
</td>
