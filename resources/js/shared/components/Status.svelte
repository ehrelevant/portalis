<script>
    export let s_type;
</script>

<div class="w-36">
{#if s_type == 'Pending'}
    <div class="flex flex-row w-stretch p-2 justify-between rounded-full bg-floating-brown-light text-floating-brown items-center">
        <div class="rounded-full w-6 h-6 pr-3 bg-floating-brown"></div>
        <p class="mx-2 font-medium text-md"> {s_type} </p>
    </div>
{/if}

{#if s_type == 'Submitted'}
    <div class="flex flex-row w-stretch p-2 justify-between rounded-full bg-floating-forest-light text-floating-forest items-center">
        <div class="rounded-full w-6 h-6 pr-3 bg-floating-forest"></div>
        <p class="mx-2 font-medium text-md"> {s_type} </p>
    </div>
{/if}

{#if s_type == 'Validated'}
    <div class="flex flex-row w-stretch p-2 justify-between rounded-full bg-floating-blue-light text-floating-blue items-center">
        <div class="rounded-full w-6 h-6 pr-3 bg-floating-blue"></div>
        <p class="mx-2 font-medium text-md"> {s_type} </p>
    </div>
{/if}
</div>