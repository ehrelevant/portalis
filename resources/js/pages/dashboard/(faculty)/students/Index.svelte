<script>
    import { router } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import StatusCell from './StatusCell.svelte';

    export let students;
    export let requirementNames;
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

    <!-- List of Students -->
    <div class="w-full overflow-x-auto rounded-xl">
        <table
            class="w-full border-collapse overflow-x-scroll rounded-xl bg-black"
        >
            <tr class="border-b-2">
                <th scope="col" class="border-r-2 p-2">SN</th>
                <th scope="col" class="border-r-2 p-2">Name</th>
                <th scope="col" class="border-r-2 p-2">Section</th>
                {#each requirementNames as requirementName}
                    <th scope="col" class="border-l-2 p-2">{requirementName}</th
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
                <tr class="border-t-2">
                    <th scope="row" class="border-r-2 p-2">{student_number}</th>
                    <td class="border-r-2 p-2">{last_name}, {first_name}</td>
                    <td class="border-r-2 p-2">
                        <div class="flex items-center justify-center">
                            <select
                                class="bg-white px-2 text-light-primary-text dark:bg-gray-800 dark:text-dark-primary-text"
                                on:change={(evt) =>
                                    setSection(evt, student_number)}
                            >
                                <option
                                    selected={!has_dropped && student_section}
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
                        <td class="border-l-2 p-2 text-center"
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
</div>
