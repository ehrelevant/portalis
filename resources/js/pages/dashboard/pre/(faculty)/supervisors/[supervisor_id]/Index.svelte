<script>
    import { Link } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';

    export let supervisor;
    $: ({ first_name, middle_name, last_name } = supervisor);
</script>

<div class="main-screen w-full p-4">
    <Header txt="Supervisor's View" />

    <div
        class="bg-light-secondary p-4 text-xl text-light-secondary-text dark:bg-dark-secondary dark:text-dark-secondary-text"
    >
        Name: {first_name}
        {middle_name}
        {last_name}
    </div>

    <!-- Back to Supervisor List -->
    <div class="w-stretch flex justify-center p-4">
        <Link href="/dashboard/pre/supervisors">
            <div
                class="hover:opacit y-90 border-2 bg-light-secondary p-4 text-center text-3xl text-light-secondary-text"
            >
                Back to Supervisor List
            </div>
        </Link>
    </div>
</div>
