<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let company_name;
    export let weekly_report_statuses;
    export let intern_evaluation_status;
</script>

<div class="main-screen flex w-full flex-col gap-8 p-4">
    <div>
        <Header txt="During Internship Phase" />
        <h2 class="text-2xl">Company: {company_name}</h2>
    </div>

    <div class="flex flex-col gap-2">
        <h2 class="text-xl">Weekly Performance Evaluation</h2>
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
    </div>

    <div class="flex flex-col gap-2">
        <h2 class="text-xl">Final Report</h2>

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
    </div>
</div>
