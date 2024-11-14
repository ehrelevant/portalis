<script>
    import { useForm, Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte'
    import Close from '@assets/x.svelte'

    export let errors = {};

    let form = useForm({
        requirementId: null,
        file: null,
    });

    function handleSubmit() {
        $form.post('/dashboard/pre/submit');
    }
</script>

<div class="main-screen w-full p-4 flex flex-col justify-center">
    <Header txt="Pre-Internship Phase: Upload" />
    
    <div class="flex grow justify-center items-center">
        <form
            on:submit|preventDefault={handleSubmit}
            id="student_upload"
            class="flex flex-col"
        >
            <label class="py-4">
                <div class="flex flex-row w-stretch justify-between pb-2">
                    <p class="flex text-xl items-center"> 1. Select document to submit: </p>
                    
                    <Link href="/dashboard/pre">
                        <Close />
                    </Link>
                </div>
                
                <select form="student_upload" class="text-light-primary-text text-md w-full h-10 cursor-pointer border-light-primary-text" bind:value={$form.requirementId}>
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
            <label class="text-xl">
                2. Upload <strong>.pdf</strong> file below: <br>
                <input
                    type="file"
                    class="flex cursor-pointer pt-3"
                    on:input={(e) => ($form.file = e.target.files[0])}
                />
                <p class="flex italic justify-end">(2MB max)</p>
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
            {:else}
                <div class="pb-7">
                </div>
            {/if}
            <div class="flex justify-center m-2">
                <input
                    type="submit"
                    value="Submit Document"
                    disabled={$form.processing}
                    class="text-3xl border-2 p-4 cursor-pointer w-fit bg-light-secondary text-dark-primary-text hover:opacity-90"
                />
            </div>
        </form>
    </div>
</div>
