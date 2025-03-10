<script>
    import { Label } from '$lib/components/ui/label';

    export let txt = '';
</script>

<Label
    class="w-stretch flex flex-col border-b-4 p-4 text-3xl sm:flex-row sm:justify-between sm:text-4xl"
>
    {txt}
    <slot />
</Label>
