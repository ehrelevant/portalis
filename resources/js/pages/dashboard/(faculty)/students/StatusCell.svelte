<script>
    export let student_number;
    export let requirement_id;
    export let status;

    const bgColor =
        status === 'validated'
            ? 'bg-floating-forest-light'
            : status === 'submitted'
              ? 'bg-floating-brown-light'
              : status === 'rejected'
                ? 'bg-floating-red-light'
                : 'bg-floating-gray-light';

    const textColor = 
        status === 'validated'
            ? 'text-floating-forest'
            : status === 'submitted'
            ? 'text-floating-brown'
            : status === 'rejected'
                ? 'text-floating-red'
                : 'text-floating-gray';
</script>

<div class="flex items-center justify-center">
    <div class="w-fit justify-center rounded-xl py-1 px-2 {bgColor}">
        {#if status !== 'unsubmitted'}
            <a
                href="/dashboard/students/{student_number}/{requirement_id}"
                class="{bgColor} {textColor}"
                target="_blank">{status}</a
            >
        {:else}
            <p class="text-floating-gray">{status}</p>
        {/if}
    </div>
</div>
