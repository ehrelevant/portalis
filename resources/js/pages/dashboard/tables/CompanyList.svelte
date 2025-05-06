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
    import * as DropdownMenu from '$lib/components/ui/dropdown-menu';
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

    let isExportDropdownOpen;
    let isExportModalOpen;

    let exportForm = useForm({
        year: null,
        include_enabled: null,
        include_disabled: null,
    });

    let exportFormRoute;
    let exportFormText;
    function openExportForm(exportFormName: string) {
        switch (exportFormName) {
            case "company-list":
                exportFormRoute = 'list';
                exportFormText = 'Company List';
                break;
            default:
                return;
        }

        $exportForm.year = filterYear;
        $exportForm.include_enabled = true;
        $exportForm.include_disabled = false;

        isExportDropdownOpen = false;
        isExportModalOpen = true;
    }

    let exportFormElement;
    function redirectExportForm() {
        if (!exportFormElement.checkValidity()) {
            exportFormElement.reportValidity();
            return;
        }

        $exportForm.get(`/export/companies/${exportFormRoute}`, {
            preserveScroll: true,
            onSuccess: () => {
                isExportDropdownOpen = false;
                isExportModalOpen = false;
            },
        });
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Company List" />

    <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
        <Link class="w-full md:w-auto" href="/dashboard" method="get">
            <Button class="flex w-full flex-row gap-2"
                ><Icon icon="lets-icons:back" />Back to Dashboard</Button
            ></Link
        >
        <div
            class="flex w-full flex-col items-center gap-4 md:w-auto md:flex-row"
        >
            <Link class="w-full md:w-auto" href="/import/companies/upload"
                ><Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link class="w-full md:w-auto" href="/add-multiple/companies/upload"
                ><Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <Dialog.Root bind:open={isExportModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    variant="outline"
                    on:click={() => openExportForm("company-list")}
                    ><Icon icon="uil:export" />Export</Button
                >
                <Dialog.Content class="max-h-full h-full max-w-full sm:max-w-lg sm:max-h-[80vh] sm:h-auto overflow-auto">
                    <Dialog.Header>
                        <Dialog.Title>Export {exportFormText}</Dialog.Title>
                    </Dialog.Header>
                    <form
                        action="/export/companies/{exportFormRoute}"
                        class="flex flex-col gap-4"
                        target="_blank"
                        bind:this={exportFormElement}
                        on:submit={redirectExportForm}
                    >
                        <div
                            class="grid grid-cols-[auto,1fr] items-center gap-4"
                        >
                            <Label for="export_year"
                                >Year</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    id="export_year"
                                    name="year"
                                    type="number"
                                    bind:value={$exportForm.year}
                                />
                                {#if $exportForm.errors.year}
                                    <ErrorText>
                                        {$exportForm.errors.year}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_enabled"
                                >Include Enabled Companies</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    id="export_include_enabled"
                                    name="include_enabled"
                                    type="checkbox"
                                    value=1
                                    bind:checked={$exportForm.include_enabled}
                                />
                                {#if $exportForm.errors.include_enabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_enabled}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="export_include_disabled"
                                >Include Disabled Companies</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    id="export_include_disabled"
                                    name="include_disabled"
                                    type="checkbox"
                                    value=1
                                    bind:checked={$exportForm.include_disabled}
                                />
                                {#if $exportForm.errors.include_disabled}
                                    <ErrorText>
                                        {$exportForm.errors.include_disabled}
                                    </ErrorText>
                                {/if}
                            </div>
                        </div>

                        <Dialog.Footer class="flex flex-col-reverse gap-2">
                            <Dialog.Close>
                                <Button class="w-full" variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button type="submit"
                                >Export {exportFormText}</Button
                            >
                        </Dialog.Footer>
                    </form>
                </Dialog.Content>
            </Dialog.Root>

            <Dialog.Root bind:open={isAddModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 md:w-auto"
                    on:click={openAddForm}
                    ><Icon icon="material-symbols:add" />Add Company</Button
                >
                <Dialog.Content class="max-h-full h-full max-w-full sm:max-w-lg sm:max-h-[80vh] sm:h-auto overflow-auto">
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

                        <Dialog.Footer class="flex flex-col-reverse gap-2">
                            <Dialog.Close>
                                <Button class="w-full" variant="outline">Cancel</Button>
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

    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <Button
            on:click={bulkDisable}
            class="flex w-full flex-row gap-2 md:w-auto"
            variant="destructive"
            disabled={!hasSelected}>Disable Selected</Button
        >

        <div
            class="flex flex-row flex-wrap justify-center items-center gap-2 md:gap-4"
        >
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
