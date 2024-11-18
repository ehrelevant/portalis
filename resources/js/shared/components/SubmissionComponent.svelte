<script>
    import Status from '@shared/components/Status.svelte';
    import Valid from '@assets/validated_faculty_logo.svelte';

    export let file_name;
    export let sub_status;
    export let faculty = 0;
    
    let enabled = sub_status === "submitted";
    let enable_style = enabled ? "cursor-pointer" : "cursor-not-allowed"
    let validity = sub_status === "validated";
    let iconColor = "";

    function notEnabled() {
        // Validation is not enabled due to pending.
    }

    function handleValidity(){
        validity = !validity;
        toggleValidity();
    }

    function toggleValidity(){
        if (validity) {
            //change validity in backend here
        } else {
            //change validity in backend here
        }
    }
</script>

<li> <div class="flex flex-col sm:flex-row p-3 my-1 justify-between bg-white dark:bg-black rounded-xl">
    <div class="flex flex-col items-center justify-center sm:items-start">
        <div class="text-md"> {file_name} </div>
    </div>
    <div class="flex items-center content-center justify-center">
        {#if faculty == 1}
        <button class="w-36 w-stretch flex flex-row items-center justify-between rounded-full bg-light-primary dark:bg-dark-primary p-2 mx-3 hover:opacity-90 {enable_style}"
            on:click={enabled ? handleValidity : notEnabled}
        >
            <p class="text-md mx-2 font-medium"> Validate? </p>
            <div class={validity ? "text-light-secondary dark:text-light-primary" : "text-light-primary dark:text-dark-primary"}> <Valid /> </div>
        </button>
        {/if}
        <Status s_type={sub_status}/>
    </div>
</div> </li>