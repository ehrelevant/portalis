<script>
    import { router, page, Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Pending from '@assets/pending_logo.svelte';
    import Submitted from '@assets/submitted_logo.svelte';
    import Validated from '@assets/validated_logo.svelte';
    import Status from '@shared/components/Status.svelte';

    let totalStatus = "Pending";
    let date = "mm/dd/yyyy";

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

<div class="main-screen px-4 py-2 w-full">
    <Header txt="Pre-Internship Phase"/>

    {#if totalStatus == "Pending"}
        <div class="flex flex-row content-center w-stretch bg-floating-brown-light text-floating-brown min-h-24 max-h-fit">
            <div class="bg-floating-brown w-3 h-stretch"></div>
            <div class="px-5 content-center"> <Pending /> </div>
            <div class="py-5 flex flex-col justify-center">
                <p class="text-4xl font-semibold"> Pending Files </p>
                <div class="flex flex-row"> <p class="text-2xl font-medium"> Please update/upload ALL pending documents before <i>{date}</i>.</p></div>
            </div>
        </div>
    {/if}

    {#if totalStatus == "Submitted"}
        <div class="flex flex-row content-center w-stretch bg-floating-forest-light text-floating-forest min-h-24 max-h-fit">
            <div class="bg-floating-forest w-3 h-stretch"></div>
            <div class="px-5 content-center"> <Submitted /> </div>
            <div class="py-5 flex flex-col justify-center">
                <p class="text-4xl font-semibold"> Submitted All Documents </p>
                <div class="flex flex-row"> <p class="text-2xl font-medium"> Please wait for your faculty advisor to validate your documents. </p> </div>
            </div>
        </div>
    {/if}

    {#if totalStatus == "Validated"}
        <div class="flex flex-row content-center w-stretch bg-floating-blue-light text-floating-blue min-h-24 max-h-fit">
            <div class="bg-floating-blue w-3 h-stretch"></div>
            <div class="px-5 content-center"> <Validated /> </div>
            <div class="py-5 flex flex-col justify-center">
                <p class="text-4xl font-semibold"> Validated All Documents </p>
                <div class="flex flex-row"> <p class="text-2xl font-medium"> Congratulations! Good luck on your internship! </p> </div>
            </div>
        </div>
    {/if}
    
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

    <!-- Link to Submission Bin -->
    <div class="flex w-stretch p-4 justify-center">
        <Link href="/dashboard/pre/upload">
            <div class="bg-light-secondary text-3xl p-4 text-light-secondary-text hover:opacity-90 border-2"> Submit Documents </div>
        </Link>
    </div>
</div>

