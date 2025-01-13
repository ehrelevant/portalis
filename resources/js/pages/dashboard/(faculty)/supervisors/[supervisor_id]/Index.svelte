<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import Status from '@/js/shared/components/Status.svelte';
    import { Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';

    export let supervisor;
    $: ({ first_name, middle_name, last_name } = supervisor);

    export let supervisor_id;
    export let company_name;
    export let weekly_report_statuses;
    export let intern_evaluation_status;
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header txt="Supervisor's View" />

    <div
        class="bg-light-secondary p-4 text-xl text-light-secondary-text dark:bg-dark-secondary dark:text-dark-secondary-text"
    >
        <h2>Name: {last_name}, {first_name} {middle_name}</h2>
        <h3>Company: {company_name}</h3>
    </div>

    <div class="flex flex-col gap-4">
        <Accordion>
            <h2 slot="summary" class="text-2xl">
                Weekly Performance Evaluations
            </h2>
            <div class="flex flex-col gap-2">
                {#each weekly_report_statuses as weekly_report_status}
                    {@const { week, status } = weekly_report_status}

                    {#if status === 'pending'}
                        <!-- Removes link if answered already -->
                        <div
                            class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                        >
                            <div class="flex items-center">Week {week}</div>
                            <Status s_type={status} />
                        </div>
                    {:else}
                        <Link
                            href="/dashboard/faculty/supervisors/{supervisor_id}/report/{week}"
                            class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                        >
                            <div class="flex items-center">Week {week}</div>
                            <Status s_type={status} />
                        </Link>
                    {/if}
                {/each}
            </div>
        </Accordion>

        <Accordion>
            <h2 slot="summary" class="text-2xl">Final Report</h2>

            {#if intern_evaluation_status === 'pending'}
                <div
                    class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                >
                    <div class="flex items-center">Final Report</div>
                    <Status s_type={intern_evaluation_status} />
                </div>
            {:else}
                <Link
                    href="/dashboard/faculty/supervisors/{supervisor_id}/final"
                    class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                >
                    <div class="flex items-center">Final Report</div>
                    <Status s_type={intern_evaluation_status} />
                </Link>
            {/if}
        </Accordion>
    </div>

    <!-- Back to Supervisor List -->
    <div class="w-stretch flex justify-center p-4">
        <Link href="/dashboard/faculty/supervisors">
            <div
                class="hover:opacit y-90 border-2 bg-light-secondary p-4 text-center text-3xl text-light-secondary-text"
            >
                Back to Supervisor List
            </div>
        </Link>
    </div>
</div>
