<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let company_name;
    export let weekly_report_statuses;
    export let intern_evaluation_status;
</script>

<section class="main-screen flex w-full flex-col p-4">
    <Header txt="During Internship Phase" />

    <div class="flex flex-col gap-4">
        <h2 class="text-2xl">Company: {company_name}</h2>
        <Accordion>
            <h2 slot="summary" class="text-2xl">
                Weekly Performance Evaluation
            </h2>
            <div class="flex flex-col gap-2">
                {#each weekly_report_statuses as weekly_report_status}
                    {@const { week, status } = weekly_report_status}

                    {#if status != 'pending'}
                        <!-- Removes link if answered already -->
                        <div
                            class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                        >
                            <div class="flex items-center">Week {week}</div>
                            <Status s_type={status} />
                        </div>
                    {:else}
                        <Link
                            href="/dashboard/during/report/{week}"
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

            {#if intern_evaluation_status != 'pending'}
                <div
                    class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                >
                    <div class="flex items-center">Final Report</div>
                    <Status s_type={intern_evaluation_status} />
                </div>
            {:else}
                <Link
                    href="/dashboard/during/final"
                    class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                >
                    <div class="flex items-center">Final Report</div>
                    <Status s_type={intern_evaluation_status} />
                </Link>
            {/if}
        </Accordion>
    </div>
</section>
