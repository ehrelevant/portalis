<script>
    import { useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    export let students = [
        {
            student_number: 202200000,
            last_name: 'last',
            first_name: 'first',
        },
        {
            student_number: 202200001,
            last_name: 'last',
            first_name: 'first',
        },
        {
            student_number: 202200002,
            last_name: 'last',
            first_name: 'first',
        },
        {
            student_number: 202200003,
            last_name: 'last',
            first_name: 'first',
        },
        {
            student_number: 202200004,
            last_name: 'last',
            first_name: 'first',
        },
    ];

    let form = useForm({
        evaluations: students,
    });

    function handleSubmit() {
        $form.post('/dashboard/during/report/submit');
    }
</script>

<div class="main-screen p-4">
    <Header txt='During Internship: Form Response'/>

    <form on:submit|preventDefault={handleSubmit}>
        <div class="flex flex-col gap-4">
            <div>
                <p>Non-Technical Criteria</p>
                {#each students as student, i}
                    {@const { last_name, first_name } = student}
                    <label class="flex flex-row">
                        {last_name}, {first_name}
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['1']}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['2']}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['3']}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['4']}
                        />
                    </label>
                {/each}
            </div>
            <div>
                <p>Technical Criteria</p>
                {#each students as student, i}
                    {@const { last_name, first_name } = student}
                    <label class="flex flex-row">
                        {last_name}, {first_name}
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['5']}
                        />
                    </label>
                {/each}
            </div>

            <div>
                <p>Number of Hours per Week</p>
                {#each students as student, i}
                    {@const { last_name, first_name } = student}
                    <label class="flex flex-row">
                        {last_name}, {first_name}
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i]['hours']}
                        />
                    </label>
                {/each}
            </div>

            <div>
                <p>Comments or Concerns</p>
                {#each students as student, i}
                    {@const { last_name, first_name } = student}
                    <label class="flex flex-row">
                        {last_name}, {first_name}
                        <textarea
                            class="border-2"
                            bind:value={$form.evaluations[i]['comments']}
                        />
                    </label>
                {/each}
            </div>
        </div>
        
        <div class="flex justify-center m-2">
            <input
                type="submit"
                value="Submit Response"
                class="text-3xl border-2 p-4 cursor-pointer w-fit bg-light-secondary text-dark-primary-text hover:opacity-90"
            />
        </div>
    </form>
</div>
