<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
</script>

<div class="main-screen w-full p-4">
    <Header txt="Faculty Dashboard" />

    <div class="flex flex-col">
        <Link
            href="/dashboard/pre/students"
            class="my-1 flex justify-between rounded-xl bg-white p-3 dark:bg-black"
            >Student List</Link
        >
        <Link
            href="/dashboard/pre/supervisors"
            class="my-1 flex justify-between rounded-xl bg-white p-3 dark:bg-black"
            >Supervisor List</Link
        >
    </div>
</div>
