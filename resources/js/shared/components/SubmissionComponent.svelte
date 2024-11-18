<script>
    import Status from '@shared/components/Status.svelte';

    export let file_name;
    export let sub_status;
    export let faculty = 0;
</script>

<li>
    <div
        class="my-1 flex justify-between rounded-xl bg-white p-3 dark:bg-black"
    >
        <div class="flex flex-col justify-center">
            <div class="text-md">{file_name}</div>
        </div>
        <div class="flex items-center">
            {#if faculty == 1}
                Validate status
            {/if}
            <Status s_type={sub_status} />
        </div>
    </div>
</li>
