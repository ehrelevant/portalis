<script>
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import Modal from '@/js/shared/components/Modal.svelte';
    import Required from '@/js/shared/components/Required.svelte';

    export let companies;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/admin/companies?search=${searchQuery}`);
    }

    let addFormElement;
    let isModalOpen;

    function openModal() {
        isModalOpen = true;
    }

    let addCompanyForm = useForm({
        company_name: null,
    });

    function addCompany() {
        if (!addFormElement.checkValidity()) {
            addFormElement.reportValidity();
            return;
        }
        $addCompanyForm.post('/dashboard/admin/companies/add');
        isModalOpen = false;
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Companies List" />

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

    <!-- List of Companies -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Companies</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="p-2 {borderColor}">Company Name</th>
                    <th scope="col" class="border-l-2 p-2 {borderColor}"
                        >Actions</th
                    >
                </tr>
                {#each companies as company}
                    {@const { company_id, company_name } = company}
                    <tr class="border-t-2 {borderColor}">
                        <td class="p-2 {borderColor}">{company_name}</td>
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><Link
                                href="/dashboard/admin/companies/delete/{company_id}"
                                class="rounded-xl bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                method="delete">Delete</Link
                            >
                        </td>
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-between">
        <button
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            on:click={openModal}>Add Company</button
        >
        <Link
            href="/dashboard"
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            method="get">Back to Dashboard</Link
        >
    </div>
</div>

<Modal bind:isOpen={isModalOpen}>
    <form
        bind:this={addFormElement}
        class="flex flex-col gap-4"
        on:submit|preventDefault={addCompany}
    >
        <div class="grid grid-cols-[auto,1fr] items-center gap-4">
            <label for="middle name"><Required />Company</label>
            <input
                name="middle name"
                type="text"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addCompanyForm.company_name}
                required
            />

            <input
                class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
                type="submit"
                value="Add Company"
            />
        </div>
    </form>
</Modal>
