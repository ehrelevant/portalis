<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from '@/js/shared/components/StatusCell.svelte';

    export let students;
    export let form_infos;
    export let supervisors;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/students?search=${searchQuery}`);
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

    <!-- List of Supervisors -->
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
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Supervisor Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Company Interned</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Email</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Wordpress Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Wordpress Email</th
                    >
                    {#each Object.entries(form_infos) as [_, form_info]}
                        {@const { form_name } = form_info}
                        <th scope="col" class="border-l-2 p-2 {borderColor}"
                            >{form_name}</th
                        >
                    {/each}
                </tr>
                {#each students as student}
                    {@const {
                        student_number,
                        first_name,
                        last_name,
                        section,
                        company,
                        form_statuses,
                        supervisor_id,
                        email,
                        wordpress_name,
                        wordpress_email,
                    } = student}
                    <tr class="border-t-2 {borderColor}">
                        <th scope="row" class="border-r-2 p-2 {borderColor}"
                            >{student_number}</th
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{section}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{supervisors[supervisor_id].last_name}, {supervisors[
                                supervisor_id
                            ].first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{company}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_email}</td
                        >
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/view/{student_number}"
                                />
                            </td>
                        {/each}
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>
    
    <div class="flex w-full justify-end">
        <Link
            href="/export/students/sections"
            class="flex w-60 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Export Student List</Link
        >
    </div>

    <div class="flex w-full justify-end">
        <Link
            href="/export/students/company-evaluations"
            class="flex w-60 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Export Company Evaluations</Link
        >
    </div>

    <div class="flex w-full justify-end">
        <Link
            href="/dashboard"
            class="flex w-60 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>
