<script>
    export let href;
    export let status;
    export let isAdmin = false;

    const bgColor =
        status === 'Accepted'
            ? 'bg-floating-forest-light'
            : status === 'For Review'
              ? 'bg-floating-brown-light'
              : status === 'Returned'
                ? 'bg-floating-red-light'
                : 'bg-floating-gray-light';

    const textColor =
        status === 'Accepted'
            ? 'text-floating-forest'
            : status === 'For Review'
              ? 'text-floating-brown'
              : status === 'Returned'
                ? 'text-floating-red'
                : 'text-floating-gray';
</script>

{#if status !== 'None' || isAdmin}
    <a class="flex items-center justify-center" {href} target="_blank">
        <div
            class="w-fit justify-center rounded-xl px-2 py-1 {bgColor} {textColor}"
        >
            {status}
        </div>
    </a>
{/if}
