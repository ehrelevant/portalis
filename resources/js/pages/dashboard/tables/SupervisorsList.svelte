<script lang="ts">
    import type { Company, FormIdName, SupervisorProps } from '$lib/types';

    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '$lib/components/InternshipHeader.svelte';
    import StatusCell from '$lib/components/StatusCell.svelte';
    import Required from '$lib/components/Required.svelte';
    import ErrorText from '$lib/components/ErrorText.svelte';
    import TableColumnHeader from '$lib/components/table/TableColumnHeader.svelte';
    import Table from '$lib/components/table/Table.svelte';
    import TableRow from '$lib/components/table/TableRow.svelte';
    import TableCell from '$lib/components/table/TableCell.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import { Input } from '$lib/components/ui/input/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Checkbox } from '$lib/components/ui/checkbox/index';
    import * as Dialog from '$lib/components/ui/dialog/index';
    import * as Select from '$lib/components/ui/select';
    import Icon from '@iconify/svelte';

    export let supervisors: SupervisorProps[];
    export let formIdNames: FormIdName[];
    export let companies: Company[];
    export let isAdmin: boolean;

    let selected: { [x: number]: boolean | 'indeterminate' } =
        supervisors.reduce((selectedRecordAcc, { supervisor_id }) => {
            return { ...selectedRecordAcc, [supervisor_id]: false };
        }, {});
    $: hasSelected = Object.values(selected).some((val) => val);

    function bulkDisable() {
        if (confirm('Do you really want to disable the selected users?')) {
            const selectedRoleIds = Object.entries(selected)
                .filter(([_, isSelected]) => isSelected)
                .map(([index, _]) => Number(index));

            router.put(
                '/api/disable/supervisors',
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

    function getFormFromId(targetFormId: number) {
        return formIdNames.find(({ form_id }) => form_id === targetFormId);
    }

    let searchQuery: string;
    function search() {
        router.get(
            '/dashboard/supervisors',
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

    let sortColumn = 'last_name';
    let sortIsAscending = true;
    function sortByColumn(newSortColumn: string) {
        if (sortColumn === newSortColumn) {
            sortIsAscending = !sortIsAscending;
        } else {
            sortIsAscending = true;
        }
        sortColumn = newSortColumn;

        router.get(
            `/dashboard/supervisors`,
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

    function setCompany(supervisorId: number, companyId: number) {
        router.put(
            `/supervisors/${supervisorId}/assign/company/${companyId}`,
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }

    let userFormElement;
    let isModalOpen;

    let userForm = useForm({
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        company_id: null,
    });

    function addUser() {
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post('/api/add/supervisor', {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }

    let formUserRoleId = null;
    function openAddForm() {
        $userForm.first_name = null;
        $userForm.middle_name = null;
        $userForm.last_name = null;
        $userForm.email = null;
        $userForm.company_id = null;

        formUserRoleId = null;
        isModalOpen = true;
    }

    function openUpdateForm(supervisorId: number) {
        const supervisor = supervisors.find(
            (supervisor) => supervisor.supervisor_id === supervisorId,
        );

        $userForm.first_name = supervisor.first_name;
        $userForm.middle_name = supervisor.middle_name;
        $userForm.last_name = supervisor.last_name;
        $userForm.email = supervisor.email;
        $userForm.company_id = supervisor.company_id;

        formUserRoleId = supervisorId;
        isModalOpen = true;
    }

    function updateUser() {
        if (!formUserRoleId) {
            return;
        }
        if (!userFormElement.checkValidity()) {
            userFormElement.reportValidity();
            return;
        }
        $userForm.post(`/api/update/supervisor/${formUserRoleId}`, {
            preserveScroll: true,
            onSuccess: () => {
                isModalOpen = false;
            },
        });
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

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
            <Link href="/import/supervisors/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"><Icon icon="uil:import" />Import</Button
                ></Link
            >
            <Link href="/add-multiple/supervisors/upload"
                ><Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    variant="outline"
                    ><Icon icon="uil:import" />Add Multiple</Button
                ></Link
            >
            <Button
                href="/export/supervisors/list"
                class="flex w-full flex-row gap-2 sm:w-auto"
                target="_blank"
                variant="outline">Export Supervisors</Button
            >
            <Dialog.Root bind:open={isModalOpen}>
                <Button
                    class="flex w-full flex-row gap-2 sm:w-auto"
                    on:click={openAddForm}
                    ><Icon icon="material-symbols:add" />Add Supervisor</Button
                >
                <Dialog.Content>
                    <Dialog.Header>
                        <Dialog.Title>Add Supervisor</Dialog.Title>
                    </Dialog.Header>
                    <form
                        bind:this={userFormElement}
                        class="flex flex-col gap-4"
                        on:submit|preventDefault={formUserRoleId
                            ? updateUser
                            : addUser}
                    >
                        <div
                            class="grid grid-cols-[auto,1fr] items-center gap-4"
                        >
                            <Label for="first_name"
                                ><Required />First Name</Label
                            >
                            <div class="flex flex-col">
                                <Input
                                    name="first_name"
                                    type="text"
                                    bind:value={$userForm.first_name}
                                    required
                                />
                                {#if $userForm.errors.first_name}
                                    <ErrorText>
                                        {$userForm.errors.first_name}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="middle_name">Middle Name</Label>
                            <div class="flex flex-col">
                                <Input
                                    name="middle_name"
                                    type="text"
                                    bind:value={$userForm.middle_name}
                                />
                                {#if $userForm.errors.middle_name}
                                    <ErrorText>
                                        {$userForm.errors.middle_name}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="last_name"><Required />Last Name</Label>
                            <div class="flex flex-col">
                                <Input
                                    name="last_name"
                                    type="text"
                                    bind:value={$userForm.last_name}
                                    required
                                />
                                {#if $userForm.errors.last_name}
                                    <ErrorText>
                                        {$userForm.errors.last_name}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="email"><Required />Email</Label>
                            <div class="flex flex-col">
                                <Input
                                    name="email"
                                    type="email"
                                    bind:value={$userForm.email}
                                    required
                                />
                                {#if $userForm.errors.email}
                                    <ErrorText>
                                        {$userForm.errors.email}
                                    </ErrorText>
                                {/if}
                            </div>

                            <Label for="company">Company</Label>
                            <div class="flex flex-col">
                                <Select.Root
                                    selected={!$userForm.company_id
                                        ? { label: '-', value: '' }
                                        : {
                                              label: companies.find(
                                                  (company) =>
                                                      company.company_id ===
                                                      $userForm.company_id,
                                              ).company_name,
                                              value: $userForm.company_id,
                                          }}
                                    onSelectedChange={(v) => {
                                        v && ($userForm.company_id = v.value);
                                    }}
                                >
                                    <Select.Trigger>
                                        <Select.Value placeholder="Company" />
                                    </Select.Trigger>
                                    <Select.Content>
                                        <Select.Item value="">-</Select.Item>
                                        {#each companies as company}
                                            {@const {
                                                company_id,
                                                company_name,
                                            } = company}
                                            <Select.Item value={company_id}
                                                >{company_name}</Select.Item
                                            >
                                        {/each}
                                    </Select.Content>
                                </Select.Root>
                            </div>
                        </div>
                        <Dialog.Footer>
                            <Dialog.Close>
                                <Button variant="outline">Cancel</Button>
                            </Dialog.Close>
                            <Button type="submit"
                                >{formUserRoleId
                                    ? 'Update Supervisor'
                                    : 'Add Supervisor'}</Button
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

    <!-- List of Supervisors -->
    <Table>
        <TableRow header>
            <TableColumnHeader />
            <TableColumnHeader
                isActive={sortColumn === 'last_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('last_name')}
            >
                Last Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'first_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('first_name')}
            >
                First Name
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'company_name'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('company_name')}
            >
                Company
            </TableColumnHeader>
            <TableColumnHeader
                isActive={sortColumn === 'email'}
                isAscending={sortIsAscending}
                clickHandler={() => sortByColumn('email')}
            >
                Email
            </TableColumnHeader>
            {#each formIdNames as formIdName}
                {@const { form_name } = formIdName}
                <TableColumnHeader>{form_name}</TableColumnHeader>
            {/each}
            <TableColumnHeader>Actions</TableColumnHeader>
        </TableRow>
        {#each supervisors as supervisor (supervisor.supervisor_id)}
            {@const {
                supervisor_id,
                first_name,
                last_name,
                email,
                company_name: supervisor_company_name,
                company_id: supervisor_company_id,
                form_id_statuses,
                is_disabled,
            } = supervisor}
            <TableRow
                disabled={is_disabled}
                selected={Boolean(selected[supervisor_id])}
            >
                <TableCell
                    ><Checkbox
                        bind:checked={selected[supervisor_id]}
                    /></TableCell
                >
                <TableCell>{last_name}</TableCell>
                <TableCell>{first_name}</TableCell>
                <TableCell>
                    <Select.Root
                        selected={!supervisor_company_id
                            ? { label: '-', value: null }
                            : {
                                  label: supervisor_company_name,
                                  value: supervisor_company_id,
                              }}
                        onSelectedChange={(v) => {
                            v && setCompany(supervisor_id, v.value);
                        }}
                    >
                        <Select.Trigger>
                            <Select.Value placeholder="Company" />
                        </Select.Trigger>
                        <Select.Content>
                            <Select.Item value="">-</Select.Item>
                            {#each companies as company}
                                {@const { company_id, company_name } = company}
                                <Select.Item value={company_id}
                                    >{company_name}</Select.Item
                                >
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </TableCell>
                <TableCell>{email}</TableCell>
                {#each form_id_statuses as form_id_status}
                    {@const { form_id, status } = form_id_status}
                    <TableCell center
                        ><StatusCell
                            {isAdmin}
                            {status}
                            href="/form/{getFormFromId(form_id)
                                .short_name}/answer/{supervisor_id}"
                        />
                    </TableCell>
                {/each}
                <TableCell
                    ><div class="flex flex-row gap-2">
                        <Button
                            class={colorVariants.blue}
                            on:click={() => openUpdateForm(supervisor_id)}
                            >Edit</Button
                        >
                        {#if is_disabled}
                            <Link
                                href="/api/enable/supervisor/{supervisor_id}"
                                as="button"
                                preserveScroll
                                method="put"
                                class="grow"
                                ><Button class="w-full {colorVariants.green}"
                                    >Enable</Button
                                ></Link
                            >
                        {:else}
                            <Button
                                class="w-full grow {colorVariants.red}"
                                on:click={() => {
                                    if (
                                        confirm(
                                            'Do you really want to disable this user?',
                                        )
                                    ) {
                                        router.put(
                                            `/api/disable/supervisor/${supervisor_id}`,
                                            {},
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
