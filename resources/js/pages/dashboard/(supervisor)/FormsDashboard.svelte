<script>
    import AccordionLocal from '$lib/components/Accordion.svelte';
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';

    import Status from '$lib/components/Status.svelte';
    import ListLink from '$lib/components/ListLink.svelte';
    import * as Accordion from '$lib/components/ui/accordion';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Button } from '$lib/components/ui/button/index';
    import { colorVariants } from '$lib/customVariants';
    import { Textarea } from '$lib/components/ui/textarea';

    export let phase;
    export let students;
    export let form_statuses;

    let isReturnFormOpen = false;
    const returnForm = useForm({
        remarks: null,
    });

    function returnFormSubmission(student_user_id) {
        $returnForm.post(`/form/self-evaluation/reject/${student_user_id}`, {
            preserveScroll: true,
            onSuccess: () => {
                $returnForm.reset();
                isReturnFormOpen = false;
            },
        });
    }
</script>

<section class="main-screen flex w-full flex-col p-4">
    <Header txt="During Internship Phase" />

    <div class="flex flex-col gap-4">
        {#if phase === 'post'}
            <AccordionLocal>
                <Label slot="summary" class="cursor-pointer text-2xl"
                    >Total hours worked as assessed by interns</Label
                >
                <Accordion.Content class="px-4">
                    <div
                        class="grid grid-cols-[auto,1fr,auto] items-center justify-center gap-2"
                    >
                        <p class="col-start-2 text-center">
                            Total hours worked
                        </p>
                        <p class="text-center">Validation Status</p>

                        {#each Object.entries(students) as [_, student]}
                            {@const {
                                student_user_id,
                                last_name,
                                first_name,
                                total_hours,
                                self_assessment_status: status,
                            } = student}
                            <p>{last_name}, {first_name}</p>
                            <input
                                class="bg-white p-2 text-center text-xl text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                value={total_hours}
                                disabled
                            />
                            <div class="flex flex-row justify-start gap-2">
                                <Status type={status} />
                                {#if ['Accepted'].includes(status)}
                                    <Link
                                        as="button"
                                        href="/form/self-evaluation/invalidate/{student_user_id}"
                                        method="post"
                                        ><Button variant="destructive"
                                            >Invalidate</Button
                                        ></Link
                                    >
                                {:else if ['For Review'].includes(status)}
                                    <Link
                                        as="button"
                                        href="/form/self-evaluation/validate/{student_user_id}"
                                        method="post"
                                        ><Button class={colorVariants.green}
                                            >Accept</Button
                                        ></Link
                                    >
                                    <Dialog.Root bind:open={isReturnFormOpen}>
                                        <Dialog.Trigger>
                                            <Button variant="destructive"
                                                >Return to Student</Button
                                            >
                                        </Dialog.Trigger>
                                        <Dialog.Content
                                            class="sm:max-w-[425px]"
                                        >
                                            <Dialog.Header>
                                                <Dialog.Title
                                                    >Return to Student</Dialog.Title
                                                >
                                            </Dialog.Header>
                                            <form
                                                on:submit|preventDefault={returnFormSubmission}
                                                class="flex flex-col gap-4"
                                            >
                                                <Label for="remarks"
                                                    >Remarks</Label
                                                >
                                                <Textarea
                                                    id="remarks"
                                                    bind:value={
                                                        $returnForm.remarks
                                                    }
                                                />
                                                <Dialog.Footer>
                                                    <Dialog.Close>
                                                        <Button
                                                            variant="outline"
                                                            >Cancel</Button
                                                        >
                                                    </Dialog.Close>
                                                    <Button
                                                        variant="destructive"
                                                        type="submit"
                                                        >Return to Student</Button
                                                    >
                                                </Dialog.Footer>
                                            </form>
                                        </Dialog.Content>
                                    </Dialog.Root>
                                {/if}
                            </div>
                        {/each}
                    </div>
                </Accordion.Content>
            </AccordionLocal>
        {/if}
        <AccordionLocal>
            <Label slot="summary" class="cursor-pointer text-2xl"
                >Supervisor Forms</Label
            >
            <Accordion.Content class="px-4">
                <div class="flex flex-col gap-4">
                    {#each form_statuses as form_status}
                        {@const {
                            form_name,
                            short_name,
                            status,
                            deadline,
                            remarks,
                        } = form_status}
                        <ListLink
                            name="{form_name} Form"
                            submitHref="/form/{short_name}/answer"
                            {status}
                            {deadline}
                            {remarks}
                        ></ListLink>
                    {/each}
                </div>
            </Accordion.Content>
        </AccordionLocal>
    </div>
</section>
