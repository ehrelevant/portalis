<script>
    import Header from '@shared/components/InternshipHeader.svelte';
    import Requirement from '@/js/shared/components/Requirement.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import Validated from '@/assets/validated_logo.svelte';
    import Submitted from '@/assets/submitted_logo.svelte';
    import Pending from '@/assets/pending_logo.svelte';

    export let student_number;
    export let submissions;
    //export let total_status;
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Pre-Internship Phase" />

    <!-- File Submission Statuses -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Internship Documents</h2>
        <ul>
            {#each submissions.slice(0, 3) as submission}
                {@const { requirement_id, requirement_name, deadline, status } =
                    submission}
                <li>
                    <Requirement
                        requirementId={requirement_id}
                        requirementName={requirement_name}
                        {deadline}
                        submissionStatus={status}
                        studentNumber={student_number}
                    />
                </li>
            {/each}
        </ul>
    </Accordion>

    <Accordion open>
        <h2 slot="summary" class="text-2xl">Government IDs</h2>
        <ul>
            {#each submissions.slice(3) as submission}
                {@const { requirement_id, requirement_name, deadline, status } =
                    submission}
                <li>
                    <Requirement
                        requirementId={requirement_id}
                        requirementName={requirement_name}
                        {deadline}
                        submissionStatus={status}
                        studentNumber={student_number}
                    />
                </li>
            {/each}
        </ul>
    </Accordion>
</div>
