<script>
    import Header from '@shared/components/InternshipHeader.svelte';
    import Requirement from '@/js/shared/components/Requirement.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import Validated from '@/assets/validated_logo.svelte';
    import Submitted from '@/assets/submitted_logo.svelte';
    import Pending from '@/assets/pending_logo.svelte';

    export let student_number;
    export let submissions;
    export let total_status;
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Pre-Internship Phase" />

    {#if total_status === 'pending'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-brown-light text-floating-brown"
        >
            <div class="h-stretch w-3 bg-floating-brown"></div>
            <div class="content-center px-5"><Pending /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Pending Files</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Please update/upload ALL pending documents before their
                        respective deadlines.
                    </p>
                </div>
            </div>
        </div>
    {/if}

    {#if total_status === 'submitted'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-forest-light text-floating-forest"
        >
            <div class="h-stretch w-3 bg-floating-forest"></div>
            <div class="content-center px-5"><Submitted /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Submitted All Documents</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Please wait for your faculty advisor to validate your
                        documents.
                    </p>
                </div>
            </div>
        </div>
    {/if}

    {#if total_status === 'validated'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-blue-light text-floating-blue"
        >
            <div class="h-stretch w-3 bg-floating-blue"></div>
            <div class="content-center px-5"><Validated /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Validated All Documents</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Congratulations! Good luck on your internship!
                    </p>
                </div>
            </div>
        </div>
    {/if}

    <!-- File Submission Statuses -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Internship Documents</h2>
        <ul>
            {#each submissions.slice(0, 3) as submission}
                {@const { requirement_id, requirement_name, due_date, status } =
                    submission}
                <li>
                    <Requirement
                        requirementId={requirement_id}
                        requirementName={requirement_name}
                        dueDate={due_date}
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
                {@const { requirement_id, requirement_name, due_date, status } =
                    submission}
                <li>
                    <Requirement
                        requirementId={requirement_id}
                        requirementName={requirement_name}
                        dueDate={due_date}
                        submissionStatus={status}
                        studentNumber={student_number}
                    />
                </li>
            {/each}
        </ul>
    </Accordion>
</div>
