<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import Icon from '@iconify/svelte';
    import { Button } from '$lib/components/ui/button';
    import { Label } from '$lib/components/ui/label';
    import { Input } from '$lib/components/ui/input';
    import * as Select from '$lib/components/ui/select';

    const phases = ['pre', 'during', 'post'];

    export let currentPhase;
    export let requirements;
    export let forms;

    let settingsForm = useForm({
        phase: currentPhase,
        requirements: [...requirements],
        forms: [...forms],
    });

    function saveSettings() {
        const isConfirmed = confirm('Do you really want to save your changes?');

        if (isConfirmed) {
            $settingsForm.put('/globals/settings/update');
        }
    }
</script>

<div class="main-screen flex w-full flex-col gap-2 p-4">
    <Header txt="Admin Dashboard" />

    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
        <Link href="/dashboard/students">
            <Button
                class="flex h-full w-full grow rounded-xl border-b-dark-primary bg-muted p-3 text-2xl"
                variant="outline"
            >
                <div class="flex flex-col items-center justify-center">
                    <Icon icon="ph:student" class="text-4xl" />
                    <p class="text-2xl">Student List</p>
                </div>
            </Button>
        </Link>

        <Link href="/dashboard/supervisors">
            <Button
                class="flex h-full w-full grow rounded-xl border-b-dark-primary bg-muted p-3 text-2xl"
                variant="outline"
            >
                <div class="flex flex-col items-center justify-center">
                    <Icon icon="mdi:account-tie-outline" class="text-4xl" />
                    <p class="text-2xl">Supervisor List</p>
                </div>
            </Button>
        </Link>

        <Link href="/dashboard/faculties">
            <Button
                class="flex h-full w-full grow rounded-xl border-b-dark-primary bg-muted p-3 text-2xl"
                variant="outline"
            >
                <div class="flex flex-col items-center justify-center">
                    <Icon icon="mdi:teach" class="text-4xl" />
                    <p class="text-2xl">Faculty List</p>
                </div>
            </Button>
        </Link>

        <Link href="/dashboard/companies">
            <Button
                class="flex h-full w-full grow rounded-xl border-b-dark-primary bg-muted p-3 text-2xl"
                variant="outline"
            >
                <div class="flex flex-col items-center justify-center">
                    <Icon icon="mdi:company" class="text-4xl" />
                    <p class="text-2xl">Company List</p>
                </div>
            </Button>
        </Link>
    </div>

    <div class="w-full rounded-xl border border-input bg-muted p-8">
        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveSettings}
        >
            <div
                class="grid grid-cols-1 items-start gap-2 xl:grid-cols-[1fr,1fr,1fr]"
            >
                <div
                    class="grid grid-cols-1 items-center gap-2 sm:grid-cols-2 sm:gap-y-0 xl:grid-cols-[max-content,1fr]"
                >
                    <Label
                        class="col-span-2 mb-4 border-b-2 border-b-dark-primary text-xl font-bold"
                        >Phase</Label
                    >

                    <Label class="text-md col-span-2 sm:col-span-1" for="phase"
                        >Website Phase</Label
                    >
                    <Select.Root
                        selected={$settingsForm.phase
                            ? {
                                  label: $settingsForm.phase + '-internship',
                                  value: $settingsForm.phase,
                              }
                            : undefined}
                        onSelectedChange={(v) => {
                            v && ($settingsForm.phase = v.value);
                        }}
                    >
                        <Select.Trigger class="col-span-2 w-full sm:col-span-1">
                            <Select.Value
                                class="px-2"
                                placeholder="{currentPhase}-internship"
                            />
                        </Select.Trigger>
                        <Select.Content>
                            {#each phases as phase}
                                <Select.Item value={phase}
                                    >{phase}-internship</Select.Item
                                >
                            {/each}
                        </Select.Content>
                    </Select.Root>
                </div>

                <div
                    class="grid grid-cols-1 items-center gap-2 sm:grid-cols-2 sm:gap-y-0 xl:grid-cols-[max-content,1fr]"
                >
                    <Label
                        class="mb-4 border-b-2 border-b-dark-primary text-xl font-bold sm:col-span-2"
                    >
                        Requirement Deadlines
                    </Label>
                    {#each $settingsForm.requirements as requirement, i}
                        {@const { requirement_name } = requirement}
                        <Label
                            class="text-md"
                            for="{requirement_name} deadline"
                        >
                            {requirement_name}
                        </Label>
                        <Input
                            name="{requirement_name} deadline"
                            type="datetime-local"
                            step="1"
                            bind:value={$settingsForm.requirements[i].deadline}
                            class="mb-2 flex flex-col justify-between p-2 sm:m-0"
                        />
                    {/each}
                </div>

                <div
                    class="grid grid-cols-1 items-center gap-2 sm:grid-cols-2 sm:gap-y-0 xl:grid-cols-[max-content,1fr]"
                >
                    <Label
                        class="mb-4 border-b-2 border-b-dark-primary text-xl font-bold sm:col-span-2"
                    >
                        Form Deadlines
                    </Label>
                    {#each $settingsForm.forms as form, i}
                        {@const { form_name } = form}
                        <Label class="text-md" for="{form_name} deadline">
                            {form_name}
                        </Label>
                        <Input
                            name="{form_name} deadline"
                            type="datetime-local"
                            step="1"
                            bind:value={$settingsForm.forms[i].deadline}
                            class="mb-2 flex flex-col justify-between p-2 sm:m-0"
                        />
                    {/each}
                </div>
            </div>
            <Button
                type="submit"
                class="w-full cursor-pointer bg-dark-primary p-2 text-xl text-white hover:bg-opacity-90"
            >
                Save
            </Button>
        </form>
    </div>
</div>
