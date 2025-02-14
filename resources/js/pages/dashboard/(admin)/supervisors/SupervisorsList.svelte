<script>
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from '@/js/shared/components/StatusCell.svelte';
    import Modal from '@/js/shared/components/Modal.svelte';
    import Required from '@/js/shared/components/Required.svelte';

    export let supervisors;
    export let form_infos;
    export let companies;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/admin/supervisors?search=${searchQuery}`);
    }

    function setCompany(evt, supervisorId) {
        const companyId = evt.target.value;

        router.put(
            `/supervisors/${supervisorId}/assign/company/${companyId}`,
            {},
            {
                preserveScroll: true,
            },
        );
    }

    let addFormElement;
    let isModalOpen;

    function openModal() {
        isModalOpen = true;
    }

    let addUserForm = useForm({
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        company_id: null,
    });

    function addUser() {
        if (!addFormElement.checkValidity()) {
            addFormElement.reportValidity();
            return;
        }
        $addUserForm.post('/dashboard/admin/supervisors/add');
        isModalOpen = false;
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
</script>

<div class="main-screen flex w-full flex-col gap-4 overflow-x-hidden p-4">
    <Header txt="Supervisor List" />

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
        <h2 slot="summary" class="text-2xl">Supervisors</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Company</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Email</th
                    >
                    {#each Object.entries(form_infos) as [_, form_info]}
                        {@const { form_name } = form_info}
                        <th scope="col" class="border-l-2 p-2 {borderColor}"
                            >{form_name}</th
                        >
                    {/each}
                    <th scope="col" class="border-l-2 p-2 {borderColor}"
                        >Actions</th
                    >
                </tr>
                {#each supervisors as supervisor}
                    {@const {
                        supervisor_id,
                        first_name,
                        last_name,
                        email,
                        company_id: supervisor_company_id,
                        form_statuses,
                    } = supervisor}
                    <tr class="border-t-2 {borderColor}">
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 text-center {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setCompany(evt, supervisor_id)}
                                >
                                    <option
                                        selected={!supervisor_company_id}
                                        value
                                    />
                                    {#each companies as company}
                                        {@const { id, company_name } = company}
                                        <option
                                            selected={id ===
                                                supervisor_company_id}
                                            value={id}>{company_name}</option
                                        >
                                    {/each}
                                </select>
                            </div>
                        </td>
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    isAdmin
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/answer/{supervisor_id}"
                                />
                            </td>
                        {/each}
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><Link
                                href="/dashboard/admin/supervisors/delete/{supervisor_id}"
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
            on:click={openModal}>Add Supervisor</button
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
        on:submit|preventDefault={addUser}
    >
        <div class="grid grid-cols-[auto,1fr] items-center gap-4">
            <label for="first name"><Required />First Name</label>
            <input
                name="first name"
                type="text"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addUserForm.first_name}
                required
            />

            <label for="middle name"><Required />Middle Name</label>
            <input
                name="middle name"
                type="text"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addUserForm.middle_name}
                required
            />

            <label for="last name"><Required />Last Name</label>
            <input
                name="last name"
                type="text"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addUserForm.last_name}
                required
            />

            <label for="email"><Required />Email</label>
            <input
                name="emai"
                type="email"
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addUserForm.email}
                required
            />

            <label for="section">Company</label>
            <select
                class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                bind:value={$addUserForm.company_id}
            >
                <option selected value />
                {#each companies as company}
                    {@const { id, company_name } = company}
                    <option value={id}>{company_name}</option>
                {/each}
            </select>

            <input
                class="cursor-pointer items-center rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
                type="submit"
                value="Add Supervisor"
            />
        </div>
    </form>
</Modal>
