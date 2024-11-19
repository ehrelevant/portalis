<script>
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
</div>
