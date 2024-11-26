<script>
    import { Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Submission from '@shared/components/SubmissionComponent.svelte';

    export let student;
    $: ({ student_number, first_name, middle_name, last_name } = student);

    export let submissions;
</script>

<div class="main-screen w-full p-4">
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
    <div>
        <p class="pt-2 text-xl">Internship Documents</p>
        <ul>
            {#each submissions.slice(0, 3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    {student_number}
                    {requirement_id}
                    faculty={1}
                />
            {/each}
        </ul>

        <p class="pt-2 text-xl">Government IDs</p>
        <ul>
            {#each submissions.slice(3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    {student_number}
                    {requirement_id}
                    faculty={1}
                />
            {/each}
        </ul>
    </div>

    <!-- Back to Student List -->
    <div class="w-stretch flex justify-center p-4">
        <Link href="/dashboard/pre/students">
            <div
                class="border-2 bg-light-secondary p-4 text-center text-3xl text-light-secondary-text hover:opacity-90"
            >
                Back to Student List
            </div>
        </Link>
    </div>
</div>
