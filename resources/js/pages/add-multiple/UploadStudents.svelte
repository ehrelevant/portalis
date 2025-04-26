<script>
    import { useForm, Link } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';

    import Required from '$lib/components/Required.svelte';
    import { Label } from '$lib/components/ui/label/index';
    import { Input } from '$lib/components/ui/input/index';

    export let errors = {};

    let currentYear = new Date().getUTCFullYear();

    let form = useForm({
        file: null,
        year: currentYear,
    });

    function handleSubmit() {
        if (
            confirm(
                'This action will add multiple students. Are you sure you want to proceed?',
            )
        ) {
            $form.post(`/add-multiple/students/submit`);
        }
    }
</script>

<div class="main-screen flex w-full flex-col justify-center p-4">
    <Header txt="Add Multiple Students" />
    <div class="flex grow flex-col items-center justify-center">
        <form on:submit|preventDefault={handleSubmit} class="flex flex-col">
            <div>
                <div class="text-xl">
                    These column names must be present when importing the <strong>.csv</strong> file:
                </div>
                <p class="flex justify-end italic">
                    (column names marked with [<Required />] must have a value for every row)
                </p>

                <ul class="list-disc pl-6 text-xl">
                    <li> <Required />first_name </li>
                    <li> middle_name </li>
                    <li> <Required />last_name </li>
                    <li> <Required />student_number </li>
                    <li> <Required />email </li>
                    <li> <Required />wordpress_name </li>
                    <li> <Required />wordpress_email </li>
                </ul>
            </div>

            <br />

            <div class="flex w-full flex-row justify-between">
                <div class="text-xl">
                    Upload <strong>.csv</strong> file below:
                </div>
            </div>

            <label class="text-xl">
                <input
                    type="file"
                    class="flex cursor-pointer pt-3"
                    on:input={(e) => ($form.file = e.currentTarget.files[0])}
                />
                <p class="flex justify-end italic">(2MB max)</p>
                {#if $form.progress}
                    <progress value={$form.progress.percentage} max="100">
                        {$form.progress.percentage}%
                    </progress>
                {/if}
            </label>

            <Label> Year </Label>
            <Input
                type="number"
                max={currentYear+1}
                min=0
                required
                bind:value={
                    $form.year
                }
            />

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
                    value="Import CSV"
                    disabled={$form.processing}
                    class="my-2 w-fit cursor-pointer border-2 bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
                />

                <Link
                    href="/dashboard/students"
                    class="w-fit cursor-pointer border-2 px-3 py-2 text-2xl text-dark-primary-text hover:opacity-90"
                >
                    Cancel
                </Link>
            </div>
        </form>
    </div>
</div>
