<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Account from '@assets/account_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';

    const phases = ['pre', 'during', 'post'];

    export let currentPhase;

    let settingsForm = useForm({
        phase: currentPhase,
    });

    function saveSettings() {
        $settingsForm.put('/globals/update-website-state');
    }

    export let requirements;
    export let forms;

    let deadlinesForm = useForm({
        requirements: [...requirements],
        forms: [...forms],
    });

    function saveDeadlines() {
        $deadlinesForm.put('/globals/update-deadlines');
    }
</script>

<div class="main-screen flex w-full flex-col gap-2 p-4">
    <Header txt="Faculty Dashboard" />

    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2">
        <Link
            href="/dashboard/students"
            class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Student List</p>
        </Link>

        {#if currentPhase === 'during' || currentPhase === 'post'}
            <Link
                href="/dashboard/supervisors"
                class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
            >
                <Account />
                <p>Supervisor List</p>
            </Link>
        {/if}
    </div>

    <Accordion>
        <h2 slot="summary" class="text-2xl">Settings</h2>

        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveSettings}
        >
            <div class="grid grid-cols-[auto,1fr] items-center gap-2">
                <label>
                    Website Phase:

                    <select
                        bind:value={$settingsForm.phase}
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    >
                        {#each phases as phase}
                            <option
                                selected={phase === currentPhase}
                                value={phase}>{phase}-internship</option
                            >
                        {/each}
                    </select>
                </label>
            </div>
            <div class="flex justify-end">
                <input
                    type="submit"
                    value="Save"
                    class="w-28 cursor-pointer rounded-full bg-floating-forest-light p-2 hover:opacity-90 dark:bg-floating-forest"
                />
            </div>
        </form>
    </Accordion>

    <Accordion>
        <h2 slot="summary" class="text-2xl">Deadlines</h2>

        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveDeadlines}
        >
            <div class="grid grid-cols-[auto,1fr] items-center gap-2">
                <h3 class="col-span-2 text-xl">Requirement Deadlines</h3>
                {#each $deadlinesForm.requirements as requirement, i}
                    {@const { requirement_name } = requirement}
                    <label for="{requirement_name} deadline">
                        {requirement_name}
                    </label>
                    <input
                        name="{requirement_name} deadline"
                        type="datetime-local"
                        step="1"
                        bind:value={$deadlinesForm.requirements[i].deadline}
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    />
                {/each}
                <h3 class="col-span-2 mt-4 text-xl">Form Deadlines</h3>
                {#each $deadlinesForm.forms as form, i}
                    {@const { form_name } = form}
                    <label for="{form_name} deadline">
                        {form_name}
                    </label>
                    <input
                        name="{form_name} deadline"
                        type="datetime-local"
                        step="1"
                        bind:value={$deadlinesForm.forms[i].deadline}
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    />
                {/each}
            </div>
            <div class="flex justify-end">
                <input
                    type="submit"
                    value="Save"
                    class="w-28 cursor-pointer rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
                />
            </div>
        </form>
    </Accordion>
</div>
