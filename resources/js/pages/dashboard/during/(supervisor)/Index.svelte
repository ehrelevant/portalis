<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    export let weeks = 5;
</script>

<div class="main-screen w-full p-4">
    <Header txt="During Internship Phase" />

    <div class="flex flex-col">
        {#each [...Array(weeks).keys()] as week}
            <Link href="/dashboard/during/report/{week + 1}"
                >Week {week + 1}</Link
            >
        {/each}
    </div>
</div>
