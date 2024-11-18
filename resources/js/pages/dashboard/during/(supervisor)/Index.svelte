<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    import Status from '@shared/components/Status.svelte';

    export let company_name;

    $: console.log(company_name);

    export let weekly_report_statuses;
</script>

<div class="main-screen w-full p-4">
    <Header txt="During Internship Phase" />

    <div class="pb-4 text-2xl">
        Weekly Performance Evaluation <br />
        Company: {company_name}
    </div>

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
