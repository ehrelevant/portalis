<script>
    import { useForm } from '@inertiajs/svelte';

    export let errors = {};

    let form = useForm({
        formName: null,
        file: null,
    });

    function handleSubmit() {
        $form.post('/dashboard/pre/submit');
    }
</script>

<div class="main-screen">
    <p>Student Pre Dashboard</p>
    <form
        on:submit|preventDefault={handleSubmit}
        id="student_upload"
        class="flex flex-col"
    >
        <label>
            Select document to submit:
            <select form="student_upload" bind:value={$form.formName}>
                <option value="agreement">Internship Agreement</option>
                <option value="medicalCert">Medical Certificate</option>
                <option value="workPlan">Signed Work Plan</option>
                <option value="studentId">Student's ID</option>
                <option value="facultyId">Faculty Adviser's IDs</option>
                <option value="supervisorId">Supervisor's IDs</option>
                <option value="guardianIDs">Parent/Guardian's ID</option>
            </select>
        </label>
        {#if errors.formName}
            <p class="pb-1 text-floating-red dark:text-floating-red-dark">
                {errors.formName}
            </p>
        {/if}
        <label>
            Upload <strong>.pdf</strong> file below:
            <input
                type="file"
                on:input={(e) => ($form.file = e.target.files[0])}
            />
            <p class="italic">(2mb Max)</p>
            {#if $form.progress}
                <progress value={$form.progress.percentage} max="100">
                    {$form.progress.percentage}%
                </progress>
            {/if}
        </label>
        {#if errors.file}
            <div class="pb-1 text-floating-red dark:text-floating-red-dark">
                {errors.file}
            </div>
        {/if}
        <input
            type="submit"
            value="Submit Document"
            disabled={$form.processing}
            class="border-2"
        />
    </form>
</div>
