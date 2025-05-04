<script lang="ts">
    import type { Company } from '$lib/types';

    import { router, Link, useForm, usePoll } from '@inertiajs/svelte';

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
    import * as Select from '$lib/components/ui/select';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import Icon from '@iconify/svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';

    const { start, stop } = usePoll(2000);

    export let companies: Company[];
    export let years: number[];

    let searchQuery: string;
    function search() {
        // Pause Polling
        stop()

        router.get(
            '/dashboard/companies',
            {
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                year: filterYear,
                show: filterCompany
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
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

    let filterYear: number = (new Date()).getFullYear();
    function filterByYear(newYear) {
        // Pause Polling
        stop()

        filterYear = newYear;

        router.get(
            '/dashboard/companies',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                show: filterCompany
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        )

        // Resume Polling
        start();
    }

    let filterCompany = 'all';
    function filterByCompany(newFilterCompany) {
        // Pause Polling
        stop()

        filterCompany = newFilterCompany;

        router.get(
            '/dashboard/companies',
            {
                year: filterYear,
                search: searchQuery,
                sort: sortColumn,
                ascending: sortIsAscending,
                show: filterCompany
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        )

        // Resume Polling
        start();
    }


    let sortColumn = 'company_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
        // Pause Polling
        stop()

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
                year: filterYear,
            },
            {
                preserveScroll: true,
                preserveState: true,
            },
        );

        // Resume Polling
        start();
    }

    let companyFormElement;
    let isAddModalOpen;

    let companyForm = useForm({
        company_name: null,
        year: null,
    });

    function addCompany() {
        if (!companyFormElement.checkValidity()) {
            companyFormElement.reportValidity();
            return;
        }
        $companyForm.post('/api/add/company', {
            preserveScroll: true,
            onSuccess: () => {
                isAddModalOpen = false;
            },
        });
    }

    let formCompanyId = null;
    function openAddForm() {
        $companyForm.company_name = null;
        $companyForm.year = filterYear;

        formCompanyId = null;
        isAddModalOpen = true;
    }

    function openUpdateForm(companyId: number) {
        const company = companies.find(
            (company) => company.company_id === companyId,
        );

        $companyForm.company_name = company.company_name;
        $companyForm.year = company.year;

        formCompanyId = companyId;
        isAddModalOpen = true;
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
                isAddModalOpen = false;
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
                variant="outline"><Icon icon="uil:export" /> Export</Button
            >
            <Dialog.Root bind:open={isAddModalOpen}>
                <Dialog.Trigger>
                    <Button
                        class="flex w-full flex-row gap-2 sm:w-auto"
                        on:click={openAddForm}
                        ><Icon icon="material-symbols:add" />Add Company</Button
                    >
                </Dialog.Trigger>
                <Dialog.Content class="max-h-[80vh] h-auto overflow-auto">
                    <Dialog.Header>
                        <Dialog.Title>{formCompanyId ? 'Edit Company' : 'Add Company'}</Dialog.Title>
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
                            <Label for="year">
                                <Required />Year
                            </Label>
                            <div class="flex flex-col">
                                <Input
                                    name="year"
                                    type="number"
                                    bind:value={$companyForm.year}
                                    required
                                />
                                {#if $companyForm.errors.year}
                                    <ErrorText>
                                        {$companyForm.errors.year}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="company_name"><Required />Company</Label
                            >
                            <Input
                                name="company_name"
                                type="text"
                                bind:value={$companyForm.company_name}
                                required
                            />
                            {#if $companyForm.errors.company_name}
                                <ErrorText>
                                    {$companyForm.errors.company_name}
                                </ErrorText>
                            {/if}
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

    <div class="flex flex-row items-center justify-end gap-4">
        <Select.Root
            selected={{label: "All", value: "all"}}
            onSelectedChange={(v) => {
                v && filterByCompany(v.value);
            }}
        >
            <Select.Trigger class="px-4 w-fit flex flex-row gap-2">
                <strong>Show:</strong> <Select.Value />
            </Select.Trigger>
            <Select.Content>
                <Select.Item value="all">All</Select.Item>
                <Select.Item value="enabled">Enabled</Select.Item>
                <Select.Item value="disabled">Disabled</Select.Item>
            </Select.Content>
        </Select.Root>
        <Select.Root
            selected={{label: filterYear.toString(), value: filterYear}}
            onSelectedChange={(v) => {
                v && filterByYear(v.value);
            }}
        >
            <Select.Trigger class="px-4 w-fit flex flex-row gap-2">
                <strong>Year:</strong> <Select.Value placeholder="Year" />
            </Select.Trigger>
            <Select.Content>
                {#each years as year}
                    <Select.Item
                        value={year}
                        >{year}</Select.Item
                    >
                {/each}
            </Select.Content>
        </Select.Root>
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
                            class="w-20 {colorVariants.blue}"
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
                                class="w-20 {colorVariants.red}"
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
