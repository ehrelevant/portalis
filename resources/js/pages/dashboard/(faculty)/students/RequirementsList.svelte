<script>
    import { router, useForm, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import StatusCell from './StatusCell.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';

    export let students;
    export let requirements;
    export let sections;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/students?search=${searchQuery}`);
    }

    function setSection(evt, studentNumber) {
        const sectionName = evt.target.value;

        router.put(
            `/dashboard/students/${studentNumber}/assign/section/${sectionName}`,
        );
    }

    let deadlinesForm = useForm({
        requirements: [...requirements],
    });

    function saveDeadlines() {
        $deadlinesForm.put('/dashboard/students/update-deadlines');
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Student List" />

    <!-- Search Function -->
    <form
        class="flex flex-row content-center justify-center"
        on:submit|preventDefault={search}
    >
        <button class="flex items-center px-2" type="submit">
            <Search />
        </button>
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
        />
    </form>

    <Accordion>
        <h2 slot="summary" class="text-2xl">Requirement Deadlines</h2>

        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveDeadlines}
        >
            <div class="grid grid-cols-[auto,1fr] items-center gap-2">
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

    <!-- List of Students -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Student Submissions</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="border-r-2 p-2 {borderColor}">SN</th>
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Section</th
                    >
                    {#each requirements as requirement}
                        {@const { requirement_name } = requirement}
                        <th scope="col" class="border-l-2 p-2 {borderColor}"
                            >{requirement_name}</th
                        >
                    {/each}
                </tr>
                {#each students as student}
                    {@const {
                        student_number,
                        first_name,
                        last_name,
                        section: student_section,
                        has_dropped,
                        submissions,
                    } = student}
                    <tr class="border-t-2 {borderColor}">
                        <th scope="row" class="border-r-2 p-2 {borderColor}"
                            >{student_number}</th
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white px-2 text-light-primary-text dark:bg-gray-800 dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSection(evt, student_number)}
                                >
                                    <option
                                        selected={!has_dropped &&
                                            student_section}
                                        value
                                    />
                                    {#each sections as section}
                                        <option
                                            selected={!has_dropped &&
                                                student_section === section}
                                            value={section}>{section}</option
                                        >
                                    {/each}
                                    <option selected={has_dropped} value="DRP"
                                        >DRP</option
                                    >
                                </select>
                            </div>
                        </td>
                        {#each submissions as submission}
                            {@const { requirement_id, status } = submission}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    {requirement_id}
                                    {student_number}
                                    {status}
                                />
                            </td>{/each}
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-end">
        <Link
            href="/dashboard"
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>
