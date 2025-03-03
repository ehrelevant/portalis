<script>
    import Header from '$lib/components/InternshipHeader.svelte';
    import Requirement from '$lib/components/Requirement.svelte';
    import Accordion from '$lib/components/Accordion.svelte';

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
        <Accordion open>
            <h2 slot="summary" class="text-2xl">Internship Documents</h2>
            <ul>
                {#each internshipDocumentSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                    <li>
                        <Requirement
                            requirementId={requirement_id}
                            requirementName={requirement_name}
                            {deadline}
                            submissionStatus={status}
                            studentId={student_id}
                        />
                    </li>
                {/each}
            </ul>
        </Accordion>
    {/if}

    {#if governmentIdSubmissions.length}
        <Accordion open>
            <h2 slot="summary" class="text-2xl">Government IDs</h2>
            <ul>
                {#each governmentIdSubmissions as submission}
                    {@const {
                        requirement_id,
                        requirement_name,
                        deadline,
                        status,
                    } = submission}
                    <li>
                        <Requirement
                            requirementId={requirement_id}
                            requirementName={requirement_name}
                            {deadline}
                            submissionStatus={status}
                            studentId={student_id}
                        />
                    </li>
                {/each}
            </ul>
        </Accordion>
    {/if}
</div>
