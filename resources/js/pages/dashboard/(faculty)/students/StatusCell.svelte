<script>
    import { Link } from '@inertiajs/svelte';

    export let student_number;
    export let requirement_id;
    export let status;
</script>

{#if status === 'validated'}
    <div>
        <Link
            href="/dashboard/students/{student_number}/{requirement_id}"
            class="bg-floating-blue">{status}</Link
        >
    </div>
{:else if status === 'submitted'}
    <div>
        <Link
            href="/dashboard/students/{student_number}/{requirement_id}"
            class="bg-floating-forest">{status}</Link
        >
    </div>
{:else}
    <div class="bg-floating-brown"><p>{status}</p></div>
{/if}
