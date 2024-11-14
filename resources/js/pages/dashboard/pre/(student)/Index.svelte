<script>
    import { router, page } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Pending from '@assets/pending_logo.svelte';
    import Submitted from '@assets/submitted_logo.svelte';
    import Validated from '@assets/validated_logo.svelte';

    let totalStatus = "Pending";
    let date = "mm/dd/yyyy";
</script>

<div class="main-screen p-4 w-full">
    <Header txt="Pre-Internship Phase"/>

    {#if totalStatus == "Pending"}
        <div class="flex flex-row content-center w-stretch bg-floating-brown-light text-floating-brown min-h-24 max-h-fit">
            <div class="bg-floating-brown w-3 h-stretch"></div>
            <div class="px-5 content-center"> <Pending /> </div>
            <div class="py-5 flex flex-col justify-center">
                <p class="text-4xl font-semibold"> Pending Files </p>
                <div class="flex flex-row"> <p class="text-2xl font-medium"> Please update/upload ALL pending documents before </p> <p class="text-2xl font-semibold pl-2">{date}.</p> </div>
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
                <div class="flex flex-row"> <p class="text-2xl font-medium"> Congratulations! Good luck on your internship! </p> <p class="text-2xl font-semibold pl-2">{date}</p> </div>
            </div>
        </div>
    {/if}
</div>

