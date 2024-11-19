<script>
    import Status from '@shared/components/Status.svelte';

    export let file_name;
    export let sub_status;
    export let faculty = 0;
    export let href;
</script>

<li>
    <div
        class="my-1 flex justify-between rounded-xl bg-white p-3 dark:bg-black"
    >
        <div class="flex flex-col justify-center">
            <div class="text-md">{file_name}</div>
        </div>
        <div class="flex items-center gap-2">
            {#if faculty == 1}
                Validate status
            {/if}
            {#if href && sub_status !== 'pending'}
                <a
                    {href}
                    class="rounded-xl bg-light-primary p-2 transition-colors hover:bg-light-secondary hover:text-light-secondary-text dark:bg-dark-primary dark:text-dark-primary-text dark:hover:bg-dark-secondary dark:hover:text-dark-secondary-text"
                    >View</a
                >
            {/if}
            <Status s_type={sub_status} />
        </div>
    </div>
</li>
