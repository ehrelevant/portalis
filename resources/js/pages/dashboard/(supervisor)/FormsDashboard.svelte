<script>
    import AccordionLocal from '$lib/components/Accordion.svelte';
    import { Link, router } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';

    import Status from '$lib/components/Status.svelte';
    import { Button } from '$lib/components/ui/button';
    import ListLink from '$lib/components/ListLink.svelte';
    import * as Accordion from '$lib/components/ui/accordion';
    import { Label } from '$lib/components/ui/label';

    export let phase;
    export let students;
    export let form_statuses;
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
                                {#if status === 'Accepted'}
                                    <Link
                                        as="button"
                                        href="/form/self-evaluation/invalidate/{student_user_id}"
                                        class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                        method="post">Invalidate</Link
                                    >
                                {:else if status !== 'Returned' && status !== 'None'}
                                    <Link
                                        as="button"
                                        href="/form/self-evaluation/validate/{student_user_id}"
                                        class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                                        method="post">Accept</Link
                                    >
                                    <Button
                                        variant="destructive"
                                        on:click={() => {
                                            if (
                                                confirm(
                                                    'Do you really want to return this to the user?',
                                                )
                                            ) {
                                                router.post(
                                                    `/form/self-evaluation/reject/${student_user_id}`,
                                                    {},
                                                    { preserveScroll: true },
                                                );
                                            }
                                        }}>Return To Student</Button
                                    >
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
                        {@const { form_name, short_name, status, deadline } =
                            form_status}
                        <ListLink
                            name="{form_name} Form"
                            submitHref="/form/{short_name}/answer"
                            {status}
                            {deadline}
                        ></ListLink>
                    {/each}
                </div>
            </Accordion.Content>
        </AccordionLocal>
    </div>
</section>
