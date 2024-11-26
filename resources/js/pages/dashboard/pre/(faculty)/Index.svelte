<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Account from '@assets/account_logo.svelte';
</script>

<div class="main-screen w-full p-4">
    <Header txt="Faculty Dashboard" />

    <div class="flex flex-col sm:flex-row w-full">
        <Link
            href="/dashboard/pre/students"
            class="w-full flex flex-col items-center justify-center rounded-xl bg-white p-3 m-2 text-2xl dark:bg-black hover:opacity-80"
        >
        <Account />
        <p>Student List</p>
        </Link>

        <Link
            href="/dashboard/pre/supervisors"
            class="w-full flex flex-col items-center justify-center rounded-xl bg-white p-3 m-2 text-2xl dark:bg-black hover:opacity-80"
        >
        <Account />
        <p>Supervisor List</p>
        </Link>
    </div>
</div>
