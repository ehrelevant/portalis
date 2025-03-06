<script>
    import Header from '$lib/components/InternshipHeader.svelte';
    import Requirement from '$lib/components/Requirement.svelte';
    import AccordionLocal from '$lib/components/Accordion.svelte';

    import * as Accordion from "$lib/components/ui/accordion";
    import { Label } from "$lib/components/ui/label";

    export let student_number;
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
            <Label slot="summary" class="cursor-pointer text-2xl">Internship Documents</Label>
            <Accordion.Content class="px-4">
                {#each internshipDocumentSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                        <Requirement
                            requirementId={requirement_id}
                            requirementName={requirement_name}
                            {deadline}
                            submissionStatus={status}
                            studentNumber={student_number}
                        />
                {/each}
            </Accordion.Content>
        </AccordionLocal>
    {/if}

    {#if governmentIdSubmissions.length}
        <AccordionLocal>
            <Label slot="summary" class="cursor-pointer text-2xl">Government IDs</Label>
            <Accordion.Content class="px-4">
                {#each governmentIdSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                        <Requirement
                            requirementId={requirement_id}
                            requirementName={requirement_name}
                            {deadline}
                            submissionStatus={status}
                            studentNumber={student_number}
                        />
                {/each}
            </Accordion.Content>
        </AccordionLocal>
    {/if}
</div>
