<script>
    import { useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Accordion from '@shared/components/Accordion.svelte';

    export let errors = {};
    $: console.log(errors);

    export let students;

    const evaluations = [...students];

    let form = useForm({
        evaluations: evaluations,
    });

    function draftForm() {
        console.log($form);
        $form.put('/dashboard/report/midsem/draft');
    }

    function submitForm() {
        $form.put('/dashboard/report/midsem/submit');
    }
</script>

<div class="main-screen flex flex-col p-4">
    <Header txt="Mid-semester Report Form" />

    <form class="flex flex-col">
        <div class="flex flex-col gap-4">
            <Accordion>
                <h2 slot="summary" class="text-2xl">Non-Technical Criteria</h2>

                <div
                    class="grid grid-cols-[auto,repeat(4,1fr)] items-center justify-center gap-2"
                >
                    <p class="col-start-2 text-center">Work Ethic (10)</p>
                    <p class="text-center">Attitude and Personality (10)</p>
                    <p class="text-center">Attendance and Punctuality (10)</p>
                    <p class="text-center">Respect for Authority (10)</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[1]}
                        />
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[2]}
                        />
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[3]}
                        />
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[4]}
                        />
                    {/each}
                </div>
            </Accordion>

            <Accordion>
                <h2 slot="summary" class="text-2xl">Technical Criteria</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    <p class="col-start-2 text-center">Work Output (60)</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            max="60"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[5]}
                        />
                    {/each}
                </div>
            </Accordion>

            <Accordion>
                <h2 slot="summary" class="text-2xl">Number of Hours</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    <p class="col-start-2 text-center">Total Hours</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            type="number"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].hours}
                        />
                    {/each}
                </div>
            </Accordion>

            <Accordion>
                <h2 slot="summary" class="text-2xl">Comments or Concerns</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <textarea
                            class="w-full bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            bind:value={$form.evaluations[i]['open_ended'][1]}
                        />
                    {/each}
                </div>
            </Accordion>

            <div class="m-2 flex justify-center gap-4">
                <input
                    name="draft"
                    type="button"
                    value="Save Draft"
                    class="w-fit cursor-pointer border-2 bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
                    on:click={draftForm}
                />
                <input
                    name="submit"
                    type="button"
                    value="Submit Response"
                    class="w-fit cursor-pointer border-2 bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
                    on:click={submitForm}
                />
            </div>
        </div>
    </form>
</div>
