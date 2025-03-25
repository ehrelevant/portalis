<script>
    export let txt = '';
</script>

<h1
    class="w-stretch flex flex-col border-b-4 p-4 text-3xl sm:flex-row sm:justify-between sm:text-4xl"
>
    {txt}
    <slot />
</h1>
