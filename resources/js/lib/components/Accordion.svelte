<script>
    import * as Accordion from '$lib/components/ui/accordion';
</script>

<!-- Only works when parent containers are flexboxes -->
<Accordion.Root class="w-full" value="item">
    <Accordion.Item class="rounded-xl border-b" value="item">
        <Accordion.Trigger class="flex flex-row text-left">
            <slot name="summary" />
        </Accordion.Trigger>
        <Accordion.Content class="p-2">
            <slot />
        </Accordion.Content>
    </Accordion.Item>
</Accordion.Root>
