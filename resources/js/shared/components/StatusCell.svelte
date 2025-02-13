<script>
    export let href;
    export let status;
    export let isAdmin = false;

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

{#if status !== 'unsubmitted' || isAdmin}
    <a class="flex items-center justify-center" {href} target="_blank">
        <div
            class="w-fit justify-center rounded-xl px-2 py-1 {bgColor} {textColor}"
        >
            {status}
        </div>
    </a>
{/if}
