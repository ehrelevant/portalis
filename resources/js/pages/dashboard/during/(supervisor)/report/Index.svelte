<script>
    import { useForm } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';

    export let students;
    export let week;

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
            open_ended: {
                1: null,
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
                <h2 class="my-4 text-2xl">Non-Technical Criteria</h2>
                <div
                    class="grid grid-cols-[auto,repeat(4,1fr)] items-center justify-center gap-x-4 gap-y-2"
                >
                    <p class="col-start-2 text-center">Work Ethic (10)</p>
                    <p class="text-center">Attitude and Personality (10)</p>
                    <p class="text-center">Attendance and Punctuality (10)</p>
                    <p class="text-center">Respect for Authority (10)</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[1]}
                        />
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[2]}
                        />
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[3]}
                        />
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            max="10"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[4]}
                        />
                    {/each}
                </div>
            </div>

            <div>
                <h2 class="my-4 text-2xl">Technical Criteria</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    <p class="col-start-2 text-center">Work Output (60)</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            max="60"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].ratings[5]}
                        />
                    {/each}
                </div>
            </div>

            <div>
                <h2 class="my-4 text-2xl">Number of Hours</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    <p class="col-start-2 text-center">Total Hours</p>
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <input
                            class="border-2 p-2 text-light-primary-text"
                            type="number"
                            min="0"
                            required
                            bind:value={$form.evaluations[i].hours}
                        />
                    {/each}
                </div>
            </div>

            <div>
                <h2 class="my-4 text-2xl">Comments or Concerns</h2>
                <div
                    class="grid grid-cols-[auto,1fr] items-center justify-center gap-x-4 gap-y-2"
                >
                    {#each students as student, i}
                        {@const { last_name, first_name } = student}
                        <p>{last_name}, {first_name}</p>
                        <textarea
                            class="w-full border-2 p-2 text-light-primary-text"
                            bind:value={$form.evaluations[i]['open_ended'][1]}
                        />
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
        </div>
    </form>
</div>
