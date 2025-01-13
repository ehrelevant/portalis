<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Submission from '@shared/components/SubmissionComponent.svelte';

    export let student;
    $: ({ student_number, first_name, middle_name, last_name } = student);

    export let submissions;
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Pre-Internship: Student's View" />

    <div
        class="bg-light-secondary p-4 text-xl text-light-secondary-text dark:bg-dark-secondary dark:text-dark-secondary-text"
    >
        Name: {first_name}
        {middle_name}
        {last_name} <br />
        Student Number: {student_number}
    </div>

    <!-- File Submission Statuses -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Internship Documents</h2>
        <ul>
            {#each submissions.slice(0, 3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    {student_number}
                    {requirement_id}
                    faculty
                />
            {/each}
        </ul>
    </Accordion>

    <Accordion open>
        <h2 slot="summary" class="text-2xl">Government IDs</h2>
        <ul>
            {#each submissions.slice(3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    {student_number}
                    {requirement_id}
                    faculty
                />
            {/each}
        </ul>
    </Accordion>

    <!-- Back to Student List -->
    <div class="w-stretch flex justify-center p-4">
        <Link href="/dashboard/faculty/students">
            <div
                class="border-2 bg-light-secondary p-4 text-center text-3xl text-light-secondary-text hover:opacity-90"
            >
                Back to Student List
            </div>
        </Link>
    </div>
</div>
