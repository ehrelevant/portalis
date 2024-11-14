<script>
    import { useForm } from '@inertiajs/svelte';

    export let errors = {};

    let form = useForm({
        requirementId: null,
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
            <select form="student_upload" bind:value={$form.requirementId}>
                <option value="1">Internship Agreement</option>
                <option value="2">Medical Certificate</option>
                <option value="3">Signed Work Plan</option>
                <option value="4">Student's ID</option>
                <option value="5">Faculty Adviser's IDs</option>
                <option value="6">Supervisor's IDs</option>
                <option value="7">Parent/Guardian's ID</option>
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
