<script lang="ts">
    import type { Company } from '$lib/types';

    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import Required from '$lib/components/Required.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Checkbox } from '$lib/components/ui/checkbox/index';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import Icon from '@iconify/svelte';

    export let companies: Company[];

    let searchQuery: string;
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

    let selected: { [x: number]: boolean | 'indeterminate' } = companies.reduce(
        (selectedRecordAcc, { company_id }) => {
            return { ...selectedRecordAcc, [company_id]: false };
        },
        {},
    );
    $: hasSelected = Object.values(selected).some((val) => val);

    function bulkDisable() {
        if (confirm('Do you really want to disable the selected users?')) {
            const selectedRoleIds = Object.entries(selected)
                .filter(([_, isSelected]) => isSelected)
                .map(([index, _]) => Number(index));

            router.put(
                '/api/disable/companies',
                {
                    selectedRoleIds,
                },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        for (const selectedRoleId of selectedRoleIds) {
                            selected[selectedRoleId] = false;
                        }
                    },
                },
            );
        }
    }

    let sortColumn = 'company_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
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
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }

    let formCompanyId = null;
    function openAddForm() {
        $companyForm.company_name = null;

        formCompanyId = null;
        isModalOpen = true;
    }

    function openUpdateForm(companyId: number) {
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
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Company List" />

    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div class="flex w-full flex-row items-center gap-4 sm:w-auto">
            <Link href="/dashboard" method="get">
                <Button class="flex flex-row gap-2"
                    ><Icon icon="lets-icons:back" />Back to Dashboard</Button
                ></Link
            >
        </div>
        <div
            class="flex w-full flex-col items-center gap-4 sm:w-auto sm:flex-row"
        >
            <Button
                on:click={bulkDisable}
                class="flex w-full flex-row gap-2 sm:w-auto"
                variant="destructive"
                disabled={!hasSelected}>Disable Selected</Button
            >
            <Link href="/import/companies/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link href="/add-multiple/companies/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <Button
                href="/export/companies/list"
                class="flex w-full flex-row gap-2 sm:w-auto"
                target="_blank"
                variant="outline">Export Companies</Button
            >
            <Dialog.Root bind:open={isModalOpen}>
                <Dialog.Trigger>
                    <Button
                        class="flex w-full flex-row gap-2 sm:w-auto"
                        on:click={openAddForm}
                        ><Icon icon="material-symbols:add" />Add Company</Button
                    >
                </Dialog.Trigger>
                <Dialog.Content>
                    <Dialog.Header>
                        <Dialog.Title>Add Company</Dialog.Title>
                    </Dialog.Header>
                    <form
                        bind:this={companyFormElement}
                        class="flex flex-col gap-4"
                        on:submit|preventDefault={formCompanyId
                            ? updateCompany
                            : addCompany}
                    >
                        <div
                            class="grid grid-cols-[auto,1fr] items-center gap-4"
                        >
                            <Label for="company_name"><Required />Company</Label
                            >
                            <Input
                                name="company_name"
                                type="text"
                                bind:value={$companyForm.company_name}
                                required
                            />
                        </div>

                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button type="submit"
                                >{formCompanyId
                                    ? 'Update Company'
                                    : 'Add Company'}</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>
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
            <TableColumnHeader />
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
            {@const { company_id, company_name, is_disabled } = company}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[company_id])}
            >
                <TableCell
                    ><Checkbox bind:checked={selected[company_id]} /></TableCell
                >
                <TableCell>{company_name}</TableCell>
                <TableCell
                    ><div
                        class="flex flex-row items-center justify-center gap-2"
                    >
                        <Button
                            class={colorVariants.blue}
                            on:click={() => openUpdateForm(company_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/company/{company_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                ><Button class={colorVariants.green}
                                    >Enable</Button
                                ></Link
                            >
                        {:else}
                            <Button
                                class={colorVariants.red}
                                on:click={() => {
                                    if (
                                        confirm(
                                            'Do you really want to disable this company?',
                                        )
                                    ) {
                                        router.put(
                                            `/api/disable/company/${company_id}`,
                                            { preserveScroll: true },
                                        );
                                    }
                                }}>Disable</Button
                            >
                        {/if}
                    </div></TableCell
                >
            </TableRow>
        {/each}
    </Table>
</div>
