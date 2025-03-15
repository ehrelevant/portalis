<script>
    import Header from '$lib/components/InternshipHeader.svelte';
    import AccordionLocal from '$lib/components/Accordion.svelte';

    import * as Accordion from '$lib/components/ui/accordion';
    import { Label } from '$lib/components/ui/label';
    import ListLink from '$lib/components/ListLink.svelte';

    export let student_id;
    export let submissions;

    $: internshipDocumentSubmissions = submissions.filter((submission) => {
        return [1, 2, 3].includes(submission.requirement_id);
    });

    $: governmentIdSubmissions = submissions.filter((submission) => {
        return [4, 5, 6].includes(submission.requirement_id);
    });
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Pre-Internship Phase" />

    <!-- File Submission Statuses -->
    {#if internshipDocumentSubmissions.length}
        <AccordionLocal>
            <Label slot="summary" class="cursor-pointer text-2xl"
                >Internship Documents</Label
            >
            <Accordion.Content class="px-4">
                {#each internshipDocumentSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                    <ListLink
                        name={requirement_name}
                        submitHref="/requirement/{requirement_id}/upload"
                        viewHref="/file/submission/{student_id}/{requirement_id}"
                        {deadline}
                        {status}
                    />
                {/each}
            </Accordion.Content>
        </AccordionLocal>
    {/if}

    {#if governmentIdSubmissions.length}
        <AccordionLocal>
            <Label slot="summary" class="cursor-pointer text-2xl"
                >Government IDs</Label
            >
            <Accordion.Content class="px-4">
                {#each governmentIdSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                    <ListLink
                        name={requirement_name}
                        submitHref="/requirement/{requirement_id}/upload"
                        viewHref="/file/submission/{student_id}/{requirement_id}"
                        {deadline}
                        {status}
                    />
                {/each}
            </Accordion.Content>
        </AccordionLocal>
    {/if}
</div>
