<script>
    export let type;
</script>

<div class="w-40">
    {#if type === 'For Review'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-yellow-foreground p-2 text-floating-yellow"
        >
            <div class="h-6 w-6 rounded-full bg-floating-yellow pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'None'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-gray-foreground p-2 text-floating-gray"
        >
            <div class="h-6 w-6 rounded-full bg-floating-gray pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'Accepted'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-green-foreground p-2 text-floating-green"
        >
            <div class="h-6 w-6 rounded-full bg-floating-green pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else if type === 'Returned'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-red-foreground p-2 text-floating-red"
        >
            <div class="h-6 w-6 rounded-full bg-floating-red pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {:else}
        <!-- unsure to keep this part in -->
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-blue-foreground p-2 text-floating-blue"
        >
            <div class="h-6 w-6 rounded-full bg-floating-blue pr-3"></div>
            <p class="text-md mx-2 font-medium">{type}</p>
        </div>
    {/if}
</div>
