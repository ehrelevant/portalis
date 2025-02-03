<script>
    import Header from '@shared/components/InternshipHeader.svelte';
    import Accordion from '@shared/components/Accordion.svelte';
    import { Link } from '@inertiajs/svelte';
    import Status from '@/js/shared/components/Status.svelte';

    export let supervisor_id;
    export let students;
    export let status;
</script>

<div class="main-screen flex flex-col gap-4 p-4">
    <Header txt="Intern Evaluation Report" />

    <div class="flex flex-col gap-4">
        <Accordion>
            <h2 slot="summary" class="text-2xl">Non-Technical Criteria</h2>

            <div
                class="grid grid-cols-[auto,repeat(5,1fr)] items-center justify-center gap-2"
            >
                <p class="col-start-2 text-center">Work Ethic (10)</p>
                <p class="text-center">Attitude and Personality (10)</p>
                <p class="text-center">Attendance and Punctuality (10)</p>
                <p class="text-center">Respect for Authority (10)</p>
                <p class="text-center">Total (40)</p>
                {#each students as student}
                    {@const { last_name, first_name, ratings } = student}
                    <p>{last_name}, {first_name}</p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[1]}
                    </p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[2]}
                    </p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[3]}
                    </p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[4]}
                    </p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[1] + ratings[2] + ratings[3] + ratings[4]}
                    </p>
                {/each}
            </div>
        </Accordion>
        <Accordion>
            <h2 slot="summary" class="text-2xl">Number of Hours</h2>

            <div
                class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
            >
                {#each students as student}
                    {@const { last_name, first_name, ratings } = student}
                    <p>{last_name}, {first_name}</p>
                    <p
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text text-center"
                    >
                        {ratings[5]}
                    </p>
                {/each}
            </div>
        </Accordion>
        <Accordion>
            <h2 slot="summary" class="text-2xl">
                What are the intern’s main strengths? In terms of technical
                skills, work ethic, etc.?
            </h2>

            <div
                class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
            >
                {#each students as student}
                    {@const { last_name, first_name, open_ended } = student}
                    <p>{last_name}, {first_name}</p>
                    <textarea
                        disabled
                        class="min-h-24 w-full resize-none bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                        value={open_ended[2]}
                    />
                {/each}
            </div>
        </Accordion>
        <Accordion>
            <h2 slot="summary" class="text-2xl">
                Are there areas (communication, technical, work ethic, etc.)
                where the intern seems to be lacking?
            </h2>

            <div
                class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
            >
                {#each students as student}
                    {@const { last_name, first_name, open_ended } = student}
                    <p>{last_name}, {first_name}</p>
                    <textarea
                        disabled
                        class="min-h-24 w-full resize-none bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                        value={open_ended[3]}
                    />
                {/each}
            </div>
        </Accordion>
        <Accordion>
            <h2 slot="summary" class="text-2xl">
                Would you consider the intern to be “industry-ready?“ Would you
                consider hiring the intern after he/she graduates?
            </h2>

            <div
                class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
            >
                {#each students as student}
                    {@const { last_name, first_name, open_ended } = student}
                    <p>{last_name}, {first_name}</p>
                    <textarea
                        disabled
                        class="min-h-24 w-full resize-none bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                        value={open_ended[4]}
                    />
                {/each}
            </div>
        </Accordion>
    </div>

    <!-- Back to Supervisor Details -->
    <div class="flex flex-row justify-center gap-2">
        <Status type={status} />
        {#if status === 'validated'}
            <Link
                href="/dashboard/supervisors/{supervisor_id}/final/invalidate"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Invalidate</Link
            >
        {:else if status !== 'rejected'}
            <Link
                href="/dashboard/supervisors/{supervisor_id}/final/validate"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                method="post">Validate</Link
            >
            <Link
                href="/dashboard/supervisors/{supervisor_id}/final/reject"
                class="flex w-28 flex-row items-center justify-center rounded-full bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                method="post">Reject</Link
            >
        {/if}
    </div>
</div>
