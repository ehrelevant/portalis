<script>
    import * as Accordion from "$lib/components/ui/accordion";
</script>

<!-- Only works when parent containers are flexboxes -->
<Accordion.Root class="w-full" value="item">
    <Accordion.Item class="border-b rounded-xl" value="item">
        <Accordion.Trigger
            class="flex flex-row p-4 text-left"
        >
            <slot name="summary" />
        </Accordion.Trigger>
        {#if open}
            <slot />
        {/if}
    </Accordion.Item>
</Accordion.Root>
