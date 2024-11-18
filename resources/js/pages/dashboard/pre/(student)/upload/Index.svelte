<script>
    import { useForm, Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Close from '@assets/x.svelte';

    export let errors = {};

    let form = useForm({
        requirementId: null,
        file: null,
    });

    function handleSubmit() {
        $form.post('/dashboard/pre/submit');
    }
</script>

<div class="main-screen flex w-full flex-col justify-center p-4">
    <Header txt="Pre-Internship Phase: Upload" />

    <div class="flex grow items-center justify-center">
        <form
            on:submit|preventDefault={handleSubmit}
            id="student_upload"
            class="flex flex-col"
        >
            <label class="py-4">
                <div class="w-stretch flex flex-row justify-between pb-2">
                    <p class="flex items-center text-xl">
                        1. Select document to submit:
                    </p>

                    <Link href="/dashboard/pre">
                        <Close />
                    </Link>
                </div>

                <select
                    form="student_upload"
                    class="text-md h-10 w-full cursor-pointer border-light-primary-text text-light-primary-text"
                    bind:value={$form.requirementId}
                >
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
                <p class="dark:text-floating-red-dark pb-1 text-floating-red">
                    {errors.formName}
                </p>
            {/if}
            <label class="text-xl">
                2. Upload <strong>.pdf</strong> file below: <br />
                <input
                    type="file"
                    class="flex cursor-pointer pt-3"
                    on:input={(e) => ($form.file = e.target.files[0])}
                />
                <p class="flex justify-end italic">(2MB max)</p>
                {#if $form.progress}
                    <progress value={$form.progress.percentage} max="100">
                        {$form.progress.percentage}%
                    </progress>
                {/if}
            </label>
            {#if errors.file}
                <div class="dark:text-floating-red-dark pb-1 text-floating-red">
                    {errors.file}
                </div>
            {:else}
                <div class="pb-7"></div>
            {/if}
            <div class="m-2 flex justify-center">
                <input
                    type="submit"
                    value="Submit Document"
                    disabled={$form.processing}
                    class="w-fit cursor-pointer border-2 bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
                />
            </div>
        </form>
    </div>
</div>
