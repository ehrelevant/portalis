<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import Status from '$lib/components/Status.svelte';

    import * as Dialog from '$lib/components/ui/dialog/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Button } from '$lib/components/ui/button/index';
    import { colorVariants } from '$lib/customVariants';
    import { Textarea } from '$lib/components/ui/textarea';

    export let supervisor;
    export let evaluator_user_id;
    export let students;
    export let rating_categories;
    export let categorized_rating_questions;
    export let open_questions;
    export let form_info;
    export let status;

    let isReturnFormOpen = false;
    const returnForm = useForm({
        remarks: null,
    });

    function returnFormSubmission() {
        $returnForm.post(
            `/form/${form_info.short_name}/reject/${evaluator_user_id}`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    $returnForm.reset();
                    isReturnFormOpen = false;
                },
            },
        );
    }
</script>

<div class="main-screen flex flex-col gap-4 p-4">
    <Header
        txt="{supervisor.last_name}, {supervisor.first_name} — {form_info.form_name} Form"
    />

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
                            {@const { criterion } = rating_question}
                            <p class="text-center">{criterion}</p>
                        {/each}
                        {#each Object.entries(students) as [student_id, student]}
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
                                    {students[student_id].categorized_ratings[
                                        category_id
                                    ][rating_id]}
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
                        {#each Object.entries(students) as [student_id, student]}
                            {@const { last_name, first_name } = student}
                            <p>{last_name}, {first_name}</p>
                            <textarea
                                class="w-full bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                value={students[student_id].opens[open_id]}
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
        {#if ['Accepted'].includes(status)}
            <Link
                as="button"
                href="/form/{form_info.short_name}/invalidate/{evaluator_user_id}"
                method="post"
                ><Button variant="destructive">Invalidate</Button></Link
            >
        {:else if ['For Review'].includes(status)}
            <Link
                as="button"
                href="/form/{form_info.short_name}/validate/{evaluator_user_id}"
                method="post"
                ><Button class={colorVariants.green}>Accept</Button></Link
            >
            <Dialog.Root bind:open={isReturnFormOpen}>
                <Dialog.Trigger>
                    <Button variant="destructive">Return to Supervisor</Button>
                </Dialog.Trigger>
                <Dialog.Content class="sm:max-w-[425px]">
                    <Dialog.Header>
                        <Dialog.Title>Return to Supervisor</Dialog.Title>
                        <Dialog.Description>
                            Return {form_info.form_name} form submission to {supervisor.last_name},
                            {supervisor.first_name}.
                        </Dialog.Description>
                    </Dialog.Header>
                    <form
                        on:submit|preventDefault={returnFormSubmission}
                        class="flex flex-col gap-4"
                    >
                        <Label for="remarks">Remarks</Label>
                        <Textarea
                            id="remarks"
                            bind:value={$returnForm.remarks}
                        />
                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button variant="destructive" type="submit"
                                >Return to Supervisor</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>
        {/if}
    </div>
</div>
