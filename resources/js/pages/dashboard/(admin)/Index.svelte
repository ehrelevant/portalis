<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Account from '@assets/account_logo.svelte';

    const phases = ['pre', 'during', 'post'];

    export let currentPhase;
    export let requirements;
    export let forms;

    let settingsForm = useForm({
        phase: currentPhase,
        requirements: [...requirements],
        forms: [...forms],
    });

    function saveSettings() {
        $settingsForm.put('/globals/settings/update');
    }
</script>

<div class="main-screen flex w-full flex-col gap-2 p-4">
    <Header txt="Admin Dashboard" />

    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2">
        <Link
            href="/dashboard/admin/students"
            class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Student List</p>
        </Link>

        <Link
            href="/dashboard/admin/supervisors"
            class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Supervisor List</p>
        </Link>

        <Link
            href="/dashboard/admin/faculties"
            class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Faculty List</p>
        </Link>

        <Link
            href="/dashboard/admin/companies"
            class="flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Company List</p>
        </Link>
    </div>

    <div class="w-full rounded-xl bg-gray-900 p-8">
        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveSettings}
        >
            <div class="grid grid-cols-[auto,1fr] items-center gap-2">
                <h2 class="col-span-2 text-xl font-bold">Phase</h2>

                <label class="ml-4" for="phase">Website Phase</label>
                <select
                    name="phase"
                    bind:value={$settingsForm.phase}
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                >
                    {#each phases as phase}
                        <option selected={phase === currentPhase} value={phase}
                            >{phase}-internship</option
                        >
                    {/each}
                </select>

                <h2 class="col-span-2 mt-4 text-xl font-bold">
                    Requirement Deadlines
                </h2>
                {#each $settingsForm.requirements as requirement, i}
                    {@const { requirement_name } = requirement}
                    <label class="ml-4" for="{requirement_name} deadline">
                        {requirement_name}
                    </label>
                    <input
                        name="{requirement_name} deadline"
                        type="datetime-local"
                        step="1"
                        bind:value={$settingsForm.requirements[i].deadline}
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    />
                {/each}
                <h2 class="col-span-2 mt-4 text-xl font-bold">
                    Form Deadlines
                </h2>
                {#each $settingsForm.forms as form, i}
                    {@const { form_name } = form}
                    <label class="ml-4" for="{form_name} deadline">
                        {form_name}
                    </label>
                    <input
                        name="{form_name} deadline"
                        type="datetime-local"
                        step="1"
                        bind:value={$settingsForm.forms[i].deadline}
                        class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    />
                {/each}
            </div>
            <input
                type="submit"
                value="Save"
                class="w-full cursor-pointer rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            />
        </form>
    </div>
</div>
