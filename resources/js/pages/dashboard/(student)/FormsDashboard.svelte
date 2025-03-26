<script>
    import { usePoll } from '@inertiajs/svelte';

    import AccordionLocal from '$lib/components/Accordion.svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import ListLink from '$lib/components/ListLink.svelte';
    import * as Accordion from '$lib/components/ui/accordion';
    import { Label } from '$lib/components/ui/label';

    export let form_statuses;

    usePoll(5000);
</script>

<section class="main-screen flex w-full flex-col p-4">
    <Header txt="During Internship Phase" />

    <AccordionLocal>
        <Label slot="summary" class="cursor-pointer text-2xl"
            >Student Forms</Label
        >
        <Accordion.Content class="px-4">
            <div class="flex flex-col gap-4">
                {#each form_statuses as form_status}
                    {@const {
                        form_name,
                        short_name,
                        status,
                        deadline,
                        remarks,
                    } = form_status}
                    <ListLink
                        name="{form_name} Form"
                        submitHref="/form/{short_name}/answer"
                        {status}
                        {deadline}
                        {remarks}
                    ></ListLink>
                {/each}
            </div>
        </Accordion.Content>
    </AccordionLocal>
</section>
