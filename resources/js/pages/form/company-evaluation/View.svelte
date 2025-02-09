<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Accordion from '@shared/components/Accordion.svelte';
    import Status from '@shared/components/Status.svelte';

    export let evaluator_user_id;
    export let values;
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
                        class="grid grid-cols-1 items-center justify-center gap-4"
                    >
                        {#each Object.entries(values.categorized_ratings[category_id]) as [rating_id, _]}
                            <div class="flex flex-col gap-2">
                                <p class="text-pretty text-lg">
                                    {categorized_rating_questions[category_id][
                                        rating_id
                                    ].criterion}
                                </p>
                                <p
                                    class="bg-white p-2 text-center text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                >
                                    {values.categorized_ratings[category_id][
                                        rating_id
                                    ]}
                                </p>
                            </div>
                        {/each}
                    </div>
                </Accordion>
            {/each}

            {#each Object.entries(open_questions) as [open_id, open_question]}
                <Accordion>
                    <h2 slot="summary" class="text-2xl">{open_question}</h2>
                    <div
                        class="grid grid-cols-[1fr] items-center justify-center gap-x-4 gap-y-2"
                    >
                        <textarea
                            class="w-full bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                            bind:value={values.opens[open_id]}
                            disabled
                        />
                    </div>
                </Accordion>
            {/each}
        </div>
    </div>

    <div class="flex flex-row justify-center gap-2">
        <Status type={status} />
        {#if status === 'validated'}
            <Link
                href="/form/{form_info.short_name}/invalidate/{evaluator_user_id}"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Invalidate</Link
            >
        {:else if status !== 'rejected'}
            <Link
                href="/form/{form_info.short_name}/validate/{evaluator_user_id}"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                method="post">Validate</Link
            >
            <Link
                href="/form/{form_info.short_name}/reject/{evaluator_user_id}"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Reject</Link
            >
        {/if}
    </div>
</div>
