<script>
    import { Label } from "$lib/components/ui/label";

    export let txt = ""
</script>

<Label
    class="w-stretch my-4 flex flex-col sm:flex-row p-4 text-3xl sm:justify-between sm:text-4xl border-b-4"
>
    {txt}
    <slot />
</Label>
