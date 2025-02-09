<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let company_name;
    export let form_statuses;
</script>

<section class="main-screen flex w-full flex-col p-4">
    <Header txt="During Internship Phase" />

    <div class="flex flex-col gap-4">
        <h2 class="text-2xl">Company: {company_name}</h2>

        {#each form_statuses as form_status}
            {@const { form_name, short_name, status, deadline } = form_status}
            <Accordion open>
                <h2 slot="summary" class="text-2xl">
                    {form_name}
                </h2>

                {#if status !== 'unsubmitted' && status !== 'rejected'}
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
