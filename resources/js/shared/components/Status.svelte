<script>
    import { toTitleCase } from '@shared/helpers';

    export let s_type;

    $: statusTypeCapitalized = toTitleCase(s_type);
</script>

<div class="w-36">
    {#if statusTypeCapitalized === 'Pending'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-brown-light p-2 text-floating-brown"
        >
            <div class="h-6 w-6 rounded-full bg-floating-brown pr-3"></div>
            <p class="text-md mx-2 font-medium">{s_type}</p>
        </div>
    {:else if statusTypeCapitalized === 'Submitted'}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-forest-light p-2 text-floating-forest"
        >
            <div class="h-6 w-6 rounded-full bg-floating-forest pr-3"></div>
            <p class="text-md mx-2 font-medium">{s_type}</p>
        </div>
    {:else}
        <div
            class="w-stretch flex flex-row items-center justify-between rounded-full bg-floating-blue-light p-2 text-floating-blue"
        >
            <div class="h-6 w-6 rounded-full bg-floating-blue pr-3"></div>
            <p class="text-md mx-2 font-medium">{s_type}</p>
        </div>
    {/if}
</div>
