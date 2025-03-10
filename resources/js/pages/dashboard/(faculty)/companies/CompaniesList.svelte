<script>
    import { Inertia } from '@inertiajs/inertia';
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Accordion from '$lib/components/Accordion.svelte';
    import Modal from '$lib/components/Modal.svelte';
    import Required from '$lib/components/Required.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import Button from '$lib/components/ui/button/button.svelte';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import Icon from '@iconify/svelte';

    export let companies;

    let searchQuery;
    function search() {
        router.get(
            '/dashboard/companies',
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
            `/dashboard/companies`,
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

    <div class="flex flex-row items-center justify-between gap-4">
        <div class="flex flex-row items-center gap-4">
            <Link href="/dashboard" method="get">
                <Button class="flex flex-row gap-2"
                    ><Icon icon="lets-icons:back" />Back to Dashboard</Button
                ></Link
            >
        </div>
        <div class="flex flex-row items-center gap-4">
            <Button class="flex flex-row gap-2" on:click={openAddForm}
                ><Icon icon="material-symbols:add" />Add Company</Button
            >
        </div>
    </div>

    <!-- Name Search Bar -->
    <div class="flex flex-row content-center justify-center">
        <Input
            type="text"
            placeholder="Search by Name"
            bind:value={searchQuery}
            on:keyup={search}
        />
    </div>

    <!-- List of Companies -->
    <Table>
        <TableRow header>
            <TableColumnHeader
                isActive={sortColumn === 'company_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('company_name')}
            >
                Company Name
            </TableColumnHeader>
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each companies as company (company.company_id)}
            {@const { company_id, company_name } = company}
            <TableRow>
                <TableCell>{company_name}</TableCell>
                <TableCell
                    ><div class="flex flex-row gap-2">
                        <Button
                            class="grow {colorVariants.blue}"
                            on:click={() => openUpdateForm(company_id)}
                            >Edit</Button
                        >
                        <Link
                            href="/api/delete/company/{company_id}"
                            as="button"
                            preserveScroll
                            method="delete"
                            class="grow"
                            ><Button class="w-full {colorVariants.red}"
                                >Delete</Button
                            ></Link
                        >
                    </div></TableCell
                >
            </TableRow>
        {/each}
    </Table>
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
            value={formCompanyId ? 'Update Comoany' : 'Add Company'}
        />
    </form>
</Modal>
