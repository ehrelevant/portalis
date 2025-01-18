<script>
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let company_name;
    export let report_status;
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
                {#if report_status != 'pending'}
                    <!-- Removes link if answered already -->
                    <div
                        class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                    >
                        <div class="flex items-center">Mid-semester Report</div>
                        <Status type={report_status} />
                    </div>
                {:else}
                    <Link
                        href="/dashboard/report/midsem"
                        class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                    >
                        <div class="flex items-center">Mid-semester Report</div>
                        <Status type={report_status} />
                    </Link>
                {/if}
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
                    href="/dashboard/report/final"
                    class="flex flex-row justify-between rounded-xl bg-white p-4 hover:opacity-90 dark:bg-black"
                >
                    <div class="flex items-center">Final Report</div>
                    <Status s_type={intern_evaluation_status} />
                </Link>
            {/if}
        </Accordion>
    </div>
</section>
