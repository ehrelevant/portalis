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
    export let week = 1;

    const evaluations = students.map((student) => {
        return {
            student_number: student.student_number,
            last_name: student.last_name,
            first_name: student.first_name,
            week: week,
            ratings: {
                1: null,
                2: null,
                3: null,
                4: null,
                5: null,
            },
            hours: null,
        };
    });

    let form = useForm({
        week: week,
        evaluations: evaluations,
    });

    function handleSubmit() {
        $form.post('/dashboard/during/report/submit');
    }
</script>

<div class="main-screen p-4">
    <Header txt="During Internship: Form Response" />

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
                            bind:value={$form.evaluations[i].ratings[1]}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i].ratings[2]}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i].ratings[3]}
                        />
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i].ratings[4]}
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
                            bind:value={$form.evaluations[i].ratings[5]}
                        />
                    </label>
                {/each}
            </div>

            <div>
                <p>Number of Hours</p>
                {#each students as student, i}
                    {@const { last_name, first_name } = student}
                    <label class="flex flex-row">
                        {last_name}, {first_name}
                        <input
                            class="border-2"
                            type="number"
                            bind:value={$form.evaluations[i].hours}
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

        <div class="m-2 flex justify-center">
            <input
                type="submit"
                value="Submit Response"
                class="w-fit cursor-pointer border-2 bg-light-secondary p-4 text-3xl text-dark-primary-text hover:opacity-90"
            />
        </div>
    </form>
</div>
