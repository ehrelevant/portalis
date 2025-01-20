<script>
    export let type;
</script>

<div class="w-40">
    {#if type === 'submitted'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-brown-light p-2 text-floating-brown"
        >
            <div class="h-6 w-6 rounded-full bg-floating-brown pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'unsubmitted'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-gray-light p-2 text-floating-gray"
        >
            <div class="h-6 w-6 rounded-full bg-floating-gray pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'validated'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-forest-light p-2 text-floating-forest"
        >
            <div class="h-6 w-6 rounded-full bg-floating-forest pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'rejected'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-red-light p-2 text-floating-red"
        >
            <div class="h-6 w-6 rounded-full bg-floating-red pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else}
        <!-- unsure to keep this part in -->
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-blue-light p-2 text-floating-blue"
        >
            <div class="h-6 w-6 rounded-full bg-floating-blue pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {/if}
</div>
