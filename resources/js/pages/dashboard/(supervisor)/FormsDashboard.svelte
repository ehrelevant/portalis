<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let phase;
    export let students;
    export let company_name;
    export let form_statuses;
</script>

<section class="main-screen flex w-full flex-col p-4">
    <Header txt="During Internship Phase" />

    <div class="flex flex-col gap-4">
        <h2 class="text-2xl">Company: {company_name}</h2>
        {#if phase === 'post'}
            <Accordion>
                <h2 slot="summary" class="text-2xl">
                    Total hours worked as assessed by interns
                </h2>

                <div
                    class="grid grid-cols-[auto,1fr,auto] items-center justify-center gap-2"
                >
                    <p class="col-start-2 text-center">Total hours worked</p>
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
                                    href="/form/self-evaluation/invalidate/{student_user_id}"
                                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                    method="post">Invalidate</Link
                                >
                            {:else if status !== 'Returned' && status !== 'None'}
                                <Link
                                    href="/form/self-evaluation/validate/{student_user_id}"
                                    class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                                    method="post">Validate</Link
                                >
                                <Link
                                    href="/form/self-evaluation/reject/{student_user_id}"
                                    class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                    method="post">Reject</Link
                                >
                            {/if}
                        </div>
                    {/each}
                </div>
            </Accordion>
        {/if}

        {#each form_statuses as form_status}
            {@const { form_name, short_name, status, deadline } = form_status}
            <Accordion open>
                <h2 slot="summary" class="text-2xl">
                    {form_name}
                </h2>

                {#if status !== 'None' && status !== 'Returned'}
                    <!-- Removes link if answered already -->
                    <div
                        class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                    >
                        <div
                            class="flex flex-col items-center justify-center sm:items-start"
                        >
                            <p class="flex items-center">{form_name} Form</p>
                            {#if deadline}
                                {@const deadlineDateTime = new Date(deadline)}
                                <p class="text-xs">
                                    (Deadline: {deadlineDateTime.toLocaleDateString(
                                        undefined,
                                        {
                                            weekday: 'short',
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                            hour: 'numeric',
                                            minute: 'numeric',
                                            second: 'numeric',
                                        },
                                    )})
                                </p>
                            {/if}
                        </div>
                        <Status type={status} />
                    </div>
                {:else}
                    <Link
                        href="/form/{short_name}/answer"
                        class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                    >
                        <div
                            class="flex flex-col items-center justify-center sm:items-start"
                        >
                            <p class="flex items-center">{form_name} Form</p>
                            {#if deadline}
                                {@const deadlineDateTime = new Date(deadline)}
                                <p class="text-xs">
                                    (Deadline: {deadlineDateTime.toLocaleDateString(
                                        undefined,
                                        {
                                            weekday: 'short',
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric',
                                            hour: 'numeric',
                                            minute: 'numeric',
                                            second: 'numeric',
                                        },
                                    )})
                                </p>
                            {/if}
                        </div>
                        <Status type={status} />
                    </Link>
                {/if}
            </Accordion>
        {/each}
    </div>
</section>
