<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import Status from '$lib/components/Status.svelte';

    import * as Dialog from '$lib/components/ui/dialog/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Input } from '$lib/components/ui/input/index';
    import { Button } from '$lib/components/ui/button/index';
    import { colorVariants } from '$lib/customVariants';
    import { Textarea } from '$lib/components/ui/textarea';

    export let errors = {};
    $: console.log(errors);

    export let student;
    export let values;
    export let rating_categories;
    export let categorized_rating_questions;
    export let open_questions;
    export let form_info;

    export let isAdmin;
    export let evaluatorUserId;
    export let evaluatorRoleId;
    export let status;

    let formElement;

    let form = useForm({
        ...values,
    });

    function draftForm() {
        $form.post(`/form/${form_info.short_name}/draft/${evaluatorRoleId}`);
    }

    function submitForm() {
        if (!formElement.checkValidity()) {
            formElement.reportValidity();
            return;
        }
        $form.post(`/form/${form_info.short_name}/submit/${evaluatorRoleId}`);
    }

    let isReturnFormOpen = false;
    const returnForm = useForm({
        remarks: null,
    });

    function returnFormSubmission() {
        $returnForm.post(
            `/form/${form_info.short_name}/reject/${evaluatorUserId}`,
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

    <form bind:this={formElement} class="flex flex-col">
        <div class="flex flex-col gap-4">
            {#each rating_categories as rating_category}
                {@const { id: category_id, category_name } = rating_category}

                <Accordion>
                    <h2 slot="summary" class="text-2xl">{category_name}</h2>

                    <div
                        class="grid grid-cols-1 items-center gap-4 overflow-x-auto p-2"
                    >
                        {#each Object.entries($form.categorized_ratings[category_id]) as [rating_id, _]}
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
                                    max={categorized_rating_questions[
                                        category_id
                                    ][rating_id].max_score}
                                    min={categorized_rating_questions[
                                        category_id
                                    ][rating_id].min_score}
                                    required
                                    bind:value={
                                        $form.categorized_ratings[category_id][
                                            rating_id
                                        ]
                                    }
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
                        <Textarea bind:value={$form.opens[open_id]} />
                    </div>
                </Accordion>
            {/each}

            {#if ['Returned', 'None'].includes(status)}
                <div class="m-2 flex justify-center gap-4">
                    <Input
                        name="draft"
                        type="button"
                        value="Save Draft"
                        class="cursor-pointer {colorVariants.blue}"
                        on:click={draftForm}
                    />
                    <Input
                        name="submit"
                        type="button"
                        value="Submit Response"
                        class="cursor-pointer {colorVariants.green}"
                        on:click={submitForm}
                    />
                </div>
            {/if}
        </div>
    </form>

    {#if isAdmin}
        <div class="flex flex-row justify-center gap-2">
            <Status type={status} />
            {#if ['Accepted'].includes(status)}
                <Link
                    as="button"
                    href="/form/{form_info.short_name}/invalidate/{evaluatorUserId}"
                    method="post"
                    ><Button variant="destructive">Invalidate</Button></Link
                >
            {:else if ['For Review'].includes(status)}
                <Link
                    as="button"
                    href="/form/{form_info.short_name}/validate/{evaluatorUserId}"
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
    {/if}
</div>
