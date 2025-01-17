<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Account from '@assets/account_logo.svelte';
</script>

<div class="main-screen w-full p-4">
    <Header txt="Faculty Dashboard" />

    <div class="flex w-full flex-col sm:flex-row">
        <Link
            href="/dashboard/students"
            class="m-2 flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Student List</p>
        </Link>

        <Link
            href="/dashboard/supervisors"
            class="m-2 flex w-full flex-col items-center justify-center rounded-xl bg-white p-3 text-2xl hover:opacity-80 dark:bg-black"
        >
            <Account />
            <p>Supervisor List</p>
        </Link>
    </div>
</div>
