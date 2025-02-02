<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Accordion from '@shared/components/Accordion.svelte';
    import Status from '@shared/components/Status.svelte';

    export let errors = {};
    $: console.log(errors);

    export let supervisor_id;
    export let students;
    export let rating_categories;
    export let categorized_rating_questions;
    export let open_questions;
    export let form_info;
    export let status;
</script>

<div class="main-screen flex flex-col gap-4 p-4">
    <Header txt="{form_info.form_name} Form" />

    <div class="flex flex-col">
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
                                <p
                                    class="bg-white p-2 text-center text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                >
                                    {students[student_number]
                                        .categorized_ratings[category_id][
                                        rating_id
                                    ]}
                                </p>
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
                                value={students[student_number].opens[open_id]}
                                disabled
                            />
                        {/each}
                    </div>
                </Accordion>
            {/each}
        </div>
    </div>

    <div class="flex flex-row justify-center gap-2">
        <Status type={status} />
        {#if status === 'validated'}
            <Link
                href="/dashboard/supervisors/{supervisor_id}/midsem/invalidate"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Invalidate</Link
            >
        {:else if status !== 'rejected'}
            <Link
                href="/dashboard/supervisors/{supervisor_id}/midsem/validate"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                method="post">Validate</Link
            >
            <Link
                href="/dashboard/supervisors/{supervisor_id}/midsem/reject"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Reject</Link
            >
        {/if}
    </div>
</div>
