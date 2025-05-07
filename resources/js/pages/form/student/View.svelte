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
    import { Input } from '$lib/components/ui/input/index';

    export let student;
    export let evaluator_user_id;
    export let values;
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
        txt="{student.last_name}, {student.first_name} — {form_info.form_name} Form"
    />

    <div class="flex flex-col">
        <div class="flex flex-col gap-4">
            {#each rating_categories as rating_category}
                {@const { id: category_id, category_name } = rating_category}

                <Accordion>
                    <h2 slot="summary" class="text-2xl">{category_name}</h2>

                    <div
                        class="grid grid-cols-1 items-center gap-4 overflow-x-auto p-2"
                    >
                        {#each Object.entries(values.categorized_ratings[category_id]) as [rating_id, _]}
                            <div class="flex flex-col gap-2">
                                <Label
                                    for="rating_{rating_id}"
                                    class="text-pretty text-lg"
                                >
                                    {categorized_rating_questions[category_id][
                                        rating_id
                                    ].criterion}
                                </Label>
                                <Input
                                    name="rating_{rating_id}"
                                    type="number"
                                    class="disabled:opacity-100"
                                    value={values.categorized_ratings[
                                        category_id
                                    ][rating_id]}
                                    disabled
                                />
                            </div>
                        {/each}
                    </div>
                </Accordion>
            {/each}

            {#each Object.entries(open_questions) as [open_id, open_question]}
                <Accordion>
                    <h2 slot="summary" class="text-2xl">{open_question}</h2>
                    <div
                        class="grid grid-cols-1 items-center gap-x-4 gap-y-2 overflow-x-auto p-2"
                    >
                        <Textarea
                            class="disabled:opacity-100"
                            value={values.opens[open_id]}
                            disabled
                        />
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
                    <Button variant="destructive">Return to Student</Button>
                </Dialog.Trigger>
                <Dialog.Content class="sm:max-w-[425px]">
                    <Dialog.Header>
                        <Dialog.Title>Return to Student</Dialog.Title>
                        <Dialog.Description>
                            Return {form_info.form_name} form submission to {student.last_name},
                            {student.first_name}.
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
                                >Return to Student</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>
        {/if}
    </div>
</div>
