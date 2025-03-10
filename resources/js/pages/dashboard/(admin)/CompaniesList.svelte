<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import Required from '$lib/components/Required.svelte';
    import ColumnHeader from '$lib/components/ColumnHeader.svelte';

    export let companies;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/admin/companies',
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let sortColumn = 'company_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn) {
        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/admin/companies`,
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let companyFormElement;
    let isModalOpen;

    let companyForm = useForm({
        company_name: null,
    });

    function addCompany() {
        if (!companyFormElement.checkValidity()) {
            companyFormElement.reportValidity();
            return;
        }
        $companyForm.post('/api/add/company', {
            preserveScroll: true,
        });
    }

    function openAddForm() {
        $companyForm.company_name = null;

        isModalOpen = true;
    }

    let formCompanyId = null;
    function openUpdateForm(companyId) {
        const company = companies.find(
            (company) => company.company_id === companyId,
        );

        $companyForm.company_name = company.company_name;

        formCompanyId = companyId;
        isModalOpen = true;
    }

    function updateCompany() {
        if (!formCompanyId) {
            return;
        }
        if (!companyFormElement.checkValidity()) {
            companyFormElement.reportValidity();
            return;
        }
        $companyForm.post(`/api/update/company/${formCompanyId}`, {
            preserveScroll: true,
        });
    }

    Inertia.on('success', () => {
        isModalOpen = false;
    });

    Inertia.on('success', () => {
        isModalOpen = false;
    });

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Companies List" />

    <!-- Name Search Bar -->
    <div class="flex flex-row content-center justify-center">
        <input
            class="text-md w-full rounded-md p-2 text-light-primary-text sm:text-xl"
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
            on:keyup={search}
        />
    </div>

    <!-- List of Companies -->
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Companies</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <ColumnHeader
                        isActive={sortColumn === 'company_name'}
                        isAscending={sortIsAscending}
                        clickHandler={() => sortByColumn('company_name')}
                        first
                    >
                        Company Name
                    </ColumnHeader>
                    <ColumnHeader>Actions</ColumnHeader>
                </tr>
                {#each companies as company (company.company_id)}
                    {@const { company_id, company_name } = company}
                    <tr class="border-t-2 {borderColor}">
                        <td class="p-2 {borderColor}">{company_name}</td>
                        <div
                            class="flex flex-row items-center justify-center gap-2 border-l-2 p-2"
                        >
                            <td class="text-center {borderColor}"
                                ><button
                                    class="h-full rounded-xl bg-floating-blue-light p-2 hover:opacity-90 dark:bg-floating-blue"
                                    on:click={() => openUpdateForm(company_id)}
                                    >Edit</button
                                >
                            </td>
                            <td class="text-center {borderColor}"
                                ><Link
                                    href="/api/delete/company/{company_id}"
                                    class="h-full rounded-xl bg-floating-red-light p-2 hover:opacity-90 dark:bg-floating-red"
                                    as="button"
                                    preserveScroll
                                    method="delete">Delete</Link
                                >
                            </td>
                        </div>
                    </tr>
                {/each}
            </table>
        </div>
    </Accordion>

    <div class="flex w-full justify-between">
        <button
            class="flex w-52 flex-row items-center justify-center rounded-full bg-light-primary p-2 hover:opacity-90 dark:bg-dark-primary"
            on:click={openAddForm}>Add Company</button
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
        bind:this={companyFormElement}
        class="flex flex-col gap-4"
        on:submit|preventDefault={formCompanyId ? updateCompany : addCompany}
    >
        <div class="grid grid-cols-[auto,1fr] items-center gap-4">
            <label for="middle name"><Required />Company</label>
            <input
                name="middle name"
                type="text"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$companyForm.company_name}
                required
            />
        </div>
        <input
            class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value={formCompanyId ? 'Update Company' : 'Add Company'}
        />
    </form>
</Modal>
