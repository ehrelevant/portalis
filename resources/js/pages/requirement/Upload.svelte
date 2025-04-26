<script>
    import { useForm, Link } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';

    export let errors = {};

    export let studentId;
    export let studentName;
    export let requirementId;
    export let requirementName;

    let form = useForm({
        file: null,
    });

    function handleSubmit() {
        $form.post(`/requirement/${requirementId}/submit/${studentId}`);
    }
</script>

<div class="main-screen flex w-full flex-col justify-center p-4">
    <Header
        txt="{studentName.last_name}, {studentName.first_name} — {requirementName}"
    />
    <div class="flex grow flex-col items-center justify-center">
        <form
            on:submit|preventDefault={handleSubmit}
            id="student_upload"
            class="flex flex-col"
        >
            <div class="flex w-full flex-row justify-between">
                <div class="text-xl pb-4">
                    Upload <strong>.pdf</strong> file below:
                </div>
            </div>

            <label class="text-xl">
                <input
                    type="file"
                    class="block w-full text-l border border-black rounded-lg cursor-pointer bg-gray-50 dark:text-dark-primary-text focus:outline-none dark:bg-gray-800"
                    on:input={(e) => ($form.file = e.currentTarget.files[0])}
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
            <div class="m-2 flex flex-col items-center justify-center">
                <input
                    type="submit"
                    value="Submit Document"
                    disabled={$form.processing}
                    class="my-1 w-fit cursor-pointer border-2 rounded-lg bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
                />

                <Link
                    href="/dashboard"
                    class="w-fit cursor-pointer border-2 rounded-lg px-3 py-2 text-2xl hover:opacity-90"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</div>
