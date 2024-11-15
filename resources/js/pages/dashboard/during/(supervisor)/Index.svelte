<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Status from '@shared/components/Status.svelte'

    let week_container = "flex flex-row justify-between p-4 bg-white dark:bg-black rounded-xl hover:opacity-90";

    let company = "c_name";
    let week_status = ["1,Pending", "2,Pending", "3,Pending"].map(item => item.split(','));
</script>

<div class="main-screen w-full p-4">
    <Header txt="During Internship Phase" />

    <div class="text-2xl pb-4"> 
        Weekly Performance Evaluation <br>
        Company: {company}
    </div>

    <div class="flex flex-col gap-2">
        {#each week_status as week}
            <Link href="/dashboard/during/report/{week[0]}" class={week_container}>
                <div class="flex items-center"> Week {week[0]} </div> 
                <Status s_type={week[1]} /> 
            </Link>
        {/each}
    </div>
</div>
