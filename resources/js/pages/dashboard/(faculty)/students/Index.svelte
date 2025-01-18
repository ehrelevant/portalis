<script>
    import { router, Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import StatusCell from './StatusCell.svelte';

    export let students;
    export let requirementNames;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/students?search=${searchQuery}`);
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
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
    <div class="h-full w-full overflow-x-auto">
        <table
            class="w-full border-collapse overflow-x-scroll rounded-xl bg-black"
        >
            <tr class="border-b-2">
                <th scope="col" class="p-2">SN</th>
                <th scope="col" class="p-2">Name</th>
                {#each requirementNames as requirementName}
                    <th scope="col" class="p-2">{requirementName}</th>
                {/each}
            </tr>
            {#each students as student}
                {@const { student_number, first_name, last_name, submissions } =
                    student}
                <tr class="border-t-2">
                    <th scope="row" class="p-2">{student_number}</th>
                    <td class="p-2">{last_name}, {first_name}</td>
                    {#each submissions as submission}
                        {@const { requirement_id, status } = submission}
                        <td class="p-2 text-center"
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
