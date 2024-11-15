<script>
    import Header from '@shared/components/InternshipHeader.svelte';
    import Status from '@shared/components/Status.svelte';

    /** @type {string} */
    export let first_name;
    /** @type {string} */
    export let middle_name;
    /** @type {string} */
    export let last_name;
    /** @type {int} */
    export let student_number;

    let submission_intern = [
        "Internship Agreements,Pending",
        "Medical Certificate (Optional),Pending",
        "Work Plan Signed by Company Supervisor,Pending",
    ].map(item => item.split(','));

    let submission_ID = [
        "Student's ID,Pending",
        "Faculty Adviser's ID,Pending",
        "Company Supervisor's ID,Pending",
        "Parent's / Guardian's ID,Pending",
    ].map(item => item.split(','));
</script>

<div class="main-screen p-4 w-full">
    <Header txt="Pre-Internship: Student's View"/>

    <div class="p-4 bg-light-secondary dark:bg-dark-secondary text-light-secondary-text dark:text-dark-secondary-text text-xl">
        Name: {first_name} {middle_name} {last_name} <br>
        Student Number: {student_number}
    </div>

    <!-- File Submission Statuses -->
    <div>
        <p class="text-xl pt-2"> Internship Documents </p>
        <ul>
            {#each submission_intern as data}
                    <li> <div class="flex p-3 my-1 justify-between bg-white dark:bg-black rounded-xl">
                        <div class="flex flex-col justify-center">
                            <div class="text-md"> {data[0]} </div>
                        </div>
                        <div class="flex items-center">
                            <Status s_type={data[1]}/>
                        </div>
                    </div> </li>
            {/each}
        </ul>

        <p class="text-xl pt-2 "> Government IDs </p>
        <ul>
            {#each submission_ID as data}
                    <li> <div class="flex p-3 my-1 justify-between bg-white dark:bg-black rounded-xl">
                        <div class="flex flex-col justify-center">
                            <div class="text-md"> {data[0]} </div>
                        </div>
                        <div class="flex items-center">
                            <Status s_type={data[1]}/>
                        </div>
                    </div> </li>
            {/each}
        </ul>
    </div>



</div>