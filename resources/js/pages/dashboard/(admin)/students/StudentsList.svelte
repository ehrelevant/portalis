<script>
    import { router, Link, useForm } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte';
    import Accordion from '@/js/shared/components/Accordion.svelte';
    import StatusCell from '@/js/shared/components/StatusCell.svelte';
    import Required from '@/js/shared/components/Required.svelte';
    import Modal from '@/js/shared/components/Modal.svelte';
    import ErrorText from '@/js/shared/components/ErrorText.svelte';

    export let students;
    export let requirements;
    export let sections;
    export let form_infos;
    export let companies;
    export let companySupervisors;

    /** @type {string} */
    let searchQuery = '';

    function search() {
        router.get(`/dashboard/admin/students?search=${searchQuery}`);
    }

    function setSection(evt, studentNumber) {
        const sectionName = evt.target.value;

        router.put(
            `/students/${studentNumber}/assign/section/${sectionName}`,
            {},
            {
                preserveScroll: true,
            },
        );
    }

    function setSupervisor(evt, studentNumber) {
        const supervisorId = evt.target.value;

        router.put(
            `/students/${studentNumber}/assign/supervisor/${supervisorId}`,
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
        student_number: null,
        first_name: null,
        middle_name: null,
        last_name: null,
        email: null,
        section: null,
        supervisor_id: null,
        wordpress_name: null,
        wordpress_email: null,
    });

    function addUser() {
        if (!addFormElement.checkValidity()) {
            addFormElement.reportValidity();
            return;
        }
        $addUserForm.post(
            '/dashboard/admin/students/add',
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    isModalOpen = false;
                },
            },
        );
    }

    /** @type {string} */
    let borderColor = 'border-black dark:border-white';
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
    <Accordion open>
        <h2 slot="summary" class="text-2xl">Students</h2>

        <div class="w-full overflow-x-auto rounded-xl">
            <table
                class="w-full border-collapse overflow-x-scroll rounded-xl bg-white dark:bg-black"
            >
                <tr class="border-b-2 {borderColor}">
                    <th scope="col" class="border-r-2 p-2 {borderColor}">SN</th>
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Section</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Supervisor Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Company Interned</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Email</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Wordpress Name</th
                    >
                    <th scope="col" class="border-r-2 p-2 {borderColor}"
                        >Wordpress Email</th
                    >
                    {#each requirements as requirement}
                        {@const { requirement_name } = requirement}
                        <th scope="col" class="border-l-2 p-2 {borderColor}"
                            >{requirement_name}</th
                        >
                    {/each}
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
                {#each students as student}
                    {@const {
                        student_number,
                        first_name,
                        last_name,
                        section: student_section,
                        supervisor_id,
                        company,
                        email,
                        wordpress_name,
                        wordpress_email,
                        form_statuses,
                        has_dropped,
                        submissions,
                    } = student}
                    <tr class="border-t-2 {borderColor}">
                        <th class="border-r-2 p-2 {borderColor}"
                            >{student_number}</th
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{last_name}, {first_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSection(evt, student_number)}
                                >
                                    <option
                                        selected={!has_dropped &&
                                            student_section}
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
                        <td class="border-r-2 p-2 {borderColor}">
                            <div class="flex items-center justify-center">
                                <select
                                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                                    on:change={(evt) =>
                                        setSupervisor(evt, student_number)}
                                >
                                    <option selected={!supervisor_id} value />
                                    {#each companies as company}
                                        {@const {
                                            id: company_id,
                                            company_name,
                                        } = company}
                                        <optgroup label={company_name}>
                                            {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                                {@const {
                                                    id,
                                                    first_name,
                                                    last_name,
                                                } = companySupervisor}
                                                <option
                                                    value={companySupervisorId}
                                                    selected={id ===
                                                        supervisor_id}
                                                    >{last_name}, {first_name}</option
                                                >
                                            {/each}
                                        </optgroup>
                                    {/each}
                                    <optgroup label="No Company">
                                        {#each Object.entries(companySupervisors[0]) as [companySupervisorId, companySupervisor]}
                                            {@const {
                                                id,
                                                first_name,
                                                last_name,
                                            } = companySupervisor}
                                            <option
                                                value={companySupervisorId}
                                                selected={id === supervisor_id}
                                                >{last_name}, {first_name}</option
                                            >
                                        {/each}
                                    </optgroup>
                                </select>
                            </div>
                        </td>
                        <td class="border-r-2 p-2 text-center {borderColor}"
                            >{company ?? ''}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}">{email}</td>
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_name}</td
                        >
                        <td class="border-r-2 p-2 {borderColor}"
                            >{wordpress_email}</td
                        >
                        {#each submissions as submission}
                            {@const { requirement_id, status } = submission}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    isAdmin
                                    href="/requirement/{requirement_id}/view/{student_number}"
                                    {status}
                                />
                            </td>
                        {/each}
                        {#each Object.entries(form_statuses) as [form_id, form_status]}
                            <td class="border-l-2 p-2 text-center {borderColor}"
                                ><StatusCell
                                    isAdmin
                                    status={form_status}
                                    href="/form/{form_infos[form_id]
                                        .short_name}/answer/{student_number}"
                                />
                            </td>
                        {/each}
                        <td class="border-l-2 p-2 text-center {borderColor}"
                            ><Link
                                href="/dashboard/admin/students/delete/{student_number}"
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
            on:click={openModal}>Add Student</button
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
            <label for="student_number"><Required />Student Number</label>
            <div class="flex flex-col">
                <input
                    name="student_number"
                    type="number"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.student_number}
                    required
                />
                {#if $addUserForm.errors.student_number}
                    <ErrorText>
                        {$addUserForm.errors.student_number}
                    </ErrorText>
                {/if}
            </div>

            <label for="first_name"><Required />First Name</label>
            <div class="flex flex-col">
                <input
                    name="first_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.first_name}
                    required
                />
                {#if $addUserForm.errors.first_name}
                    <ErrorText>
                        {$addUserForm.errors.first_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="middle_name"><Required />Middle Name</label>
            <div class="flex flex-col">
                <input
                    name="middle_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.middle_name}
                    required
                />
                {#if $addUserForm.errors.middle_name}
                    <ErrorText>
                        {$addUserForm.errors.middle_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="last_name"><Required />Last Name</label>
            <div class="flex flex-col">
                <input
                    name="last_name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.last_name}
                    required
                />
                {#if $addUserForm.errors.last_name}
                    <ErrorText>
                        {$addUserForm.errors.last_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="email"><Required />Email</label>
            <div class="flex flex-col">
                <input
                    name="email"
                    type="email"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.email}
                    required
                />
                {#if $addUserForm.errors.email}
                    <ErrorText>
                        {$addUserForm.errors.email}
                    </ErrorText>
                {/if}
            </div>

            <label for="section">Section</label>
            <div class="flex flex-col">
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.section}
                >
                    <option selected value />
                    {#each sections as section}
                        <option value={section}>{section}</option>
                    {/each}
                </select>
                {#if $addUserForm.errors.section}
                    <ErrorText>
                        {$addUserForm.errors.section}
                    </ErrorText>
                {/if}
            </div>

            <label for="supervisor">Supervisor</label>
            <div class="flex flex-col">
                <select
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.supervisor_id}
                >
                    <option selected value />
                    {#each companies as company}
                        {@const { id: company_id, company_name } = company}
                        <optgroup label={company_name}>
                            {#each Object.entries(companySupervisors[company_id]) as [companySupervisorId, companySupervisor]}
                                {@const { first_name, last_name } =
                                    companySupervisor}
                                <option value={companySupervisorId}
                                    >{last_name}, {first_name}</option
                                >
                            {/each}
                        </optgroup>
                    {/each}
                    <optgroup label="No Company">
                        {#each Object.entries(companySupervisors[0]) as [companySupervisorId, companySupervisor]}
                            {@const { first_name, last_name } =
                                companySupervisor}
                            <option value={companySupervisorId}
                                >{last_name}, {first_name}</option
                            >
                        {/each}
                    </optgroup>
                </select>
                {#if $addUserForm.errors.supervisor_id}
                    <ErrorText>
                        {$addUserForm.errors.supervisor_id}
                    </ErrorText>
                {/if}
            </div>

            <label for="wordpress name"><Required />Wordpress Username</label>
            <div class="flex flex-col">
                <input
                    name="wordpress name"
                    type="text"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.wordpress_name}
                    required
                />
                {#if $addUserForm.errors.wordpress_name}
                    <ErrorText>
                        {$addUserForm.errors.wordpress_name}
                    </ErrorText>
                {/if}
            </div>

            <label for="wordpress email"><Required />Wordpress Email</label>
            <div class="flex flex-col">
                <input
                    name="wordpress email"
                    type="email"
                    class="bg-white p-2 text-light-primary-text dark:bg-dark-background dark:text-dark-primary-text"
                    bind:value={$addUserForm.wordpress_email}
                    required
                />
                {#if $addUserForm.errors.wordpress_email}
                    <ErrorText>
                        {$addUserForm.errors.wordpress_email}
                    </ErrorText>
                {/if}
            </div>
        </div>

        <input
            class="cursor-pointer rounded-full bg-light-primary p-2 px-4 hover:opacity-90 dark:bg-dark-primary"
            type="submit"
            value="Add Student"
        />
    </form>
</Modal>
