<script>
    import { Link } from '@inertiajs/svelte';

    export let student_number;
    export let requirement_id;
    export let status;

    const bgColor =
        status === 'validated'
            ? 'bg-floating-forest'
            : status === 'submitted'
              ? 'bg-floating-brown'
              : status === 'rejected'
                ? 'bg-floating-red'
                : 'bg-gray-700';
</script>

<div class="flex items-center justify-center">
    <div class="w-fit justify-center rounded-lg px-2 {bgColor}">
        {#if status !== 'unsubmitted'}
            <Link
                href="/dashboard/students/{student_number}/{requirement_id}"
                class={bgColor}>{status}</Link
            >
        {:else}
            <p class="text-base">{status}</p>
        {/if}
    </div>
</div>
