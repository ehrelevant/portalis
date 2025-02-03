<script>
    import { useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Accordion from '@shared/components/Accordion.svelte';

    export let errors = {};
    $: console.log(errors);

    export let students;
    export let rating_categories;
    export let categorized_rating_questions;
    export let open_questions;
    export let form_info;

    const answers = { ...students };

    let formElement;

    let form = useForm({
        answers: answers,
    });

    function draftForm() {
        $form.post(`/dashboard/report/${form_info.short_name}/draft`);
    }

    function submitForm() {
        if (!formElement.checkValidity()) {
            formElement.reportValidity();
            return;
        }
        $form.post(`/dashboard/report/${form_info.short_name}/submit`);
    }
</script>

<div class="main-screen flex flex-col p-4">
    <Header txt="{form_info.form_name} Form" />

    <form bind:this={formElement} class="flex flex-col">
        <div class="flex flex-col gap-4">
            {#each rating_categories as rating_category}
                {@const { id: category_id, category_name } = rating_category}

                <Accordion>
                    <h2 slot="summary" class="text-2xl">{category_name}</h2>

                    <div
                        style="grid-template-columns: auto repeat({Object.keys(
                            categorized_rating_questions[category_id],
                        ).length},1fr);"
                        class="grid grid-cols-[auto,repeat(3,1fr)] items-center justify-center gap-2"
                    >
                        <p />
                        {#each Object.entries(categorized_rating_questions[category_id]) as [_, rating_question]}
                            {@const { criterion, max_score } = rating_question}
                            <p class="text-center">{criterion} ({max_score})</p>
                        {/each}
                        {#each Object.entries(students) as [student_number, student]}
                            {@const {
                                last_name,
                                first_name,
                                categorized_ratings,
                            } = student}
                            <p>{last_name}, {first_name}</p>
                            {#each Object.entries(categorized_ratings[category_id]) as [rating_id, _]}
                                <input
                                    class="bg-white p-2 text-center text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    type="number"
                                    max={categorized_rating_questions[
                                        category_id
                                    ][rating_id].max_score}
                                    min="0"
                                    required
                                    bind:value={$form.answers[student_number]
                                        .categorized_ratings[category_id][
                                        rating_id
                                    ]}
                                />
                            {/each}
                        {/each}
                    </div>
                </Accordion>
            {/each}

            {#each Object.entries(open_questions) as [open_id, open_question]}
                <Accordion>
                    <h2 slot="summary" class="text-2xl">{open_question}</h2>
                    <div
                        class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                    >
                        {#each Object.entries(students) as [student_number, student]}
                            {@const { last_name, first_name } = student}
                            <p>{last_name}, {first_name}</p>
                            <textarea
                                class="w-full bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                bind:value={$form.answers[student_number].opens[
                                    open_id
                                ]}
                            />
                        {/each}
                    </div>
                </Accordion>
            {/each}

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
