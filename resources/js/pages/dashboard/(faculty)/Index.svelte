<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import Icon from "@iconify/svelte";
    import { Button } from "$lib/components/ui/button";
    import { Label } from "$lib/components/ui/label";
    import { Input } from '$lib/components/ui/input';
    import * as Select from "$lib/components/ui/select";
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

    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-3">
        <Link
            href="/dashboard/students"
        >
            <Button
                class="flex grow w-full h-full rounded-xl p-3 text-2xl bg-muted border-b-dark-primary"
                variant="outline"
            >
                <div
                    class="flex flex-col items-center justify-center"
                >
                    <Icon icon="ph:student" class="text-4xl"/>
                    <Label class="text-2xl">Student List</Label>
                </div>
            </Button>
        </Link>

        <Link
            href="/dashboard/supervisors"
        >
            <Button
                class="flex grow w-full h-full rounded-xl p-3 text-2xl bg-muted border-b-dark-primary"
                variant="outline"
            >
                <div
                    class="flex flex-col items-center justify-center"
                >
                    <Icon icon="mdi:account-tie-outline" class="text-4xl"/>
                    <Label class="text-2xl">Supervisor List</Label>
                </div>
            </Button>
        </Link>

        <Link
            href="/dashboard/companies"
        >
            <Button
                class="flex grow w-full h-full rounded-xl p-3 text-2xl bg-muted border-b-dark-primary"
                variant="outline"
            >
                <div
                    class="flex flex-col items-center justify-center"
                >
                    <Icon icon="mdi:company" class="text-4xl"/>
                    <Label class="text-2xl">Company List</Label>
                </div>
            </Button>
        </Link>
    </div>

    <div class="w-full rounded-xl bg-muted p-8 border border-input">
        <form
            class="flex flex-col gap-4"
            on:submit|preventDefault={saveSettings}
        >
            <div class="grid grid-cols-1 sm:grid-cols-[auto,1fr] items-center gap-2">
                    <Label class="col-span-2 text-xl font-bold border-b-2 border-b-dark-primary">Phase</Label>

                    <Label class="ml-4 text-md" for="phase">Website Phase</Label>
                    <Select.Root
                        selected={ $settingsForm.phase
                            ? { label: $settingsForm.phase + "-internship", value: $settingsForm.phase}
                            : undefined
                        }
                        onSelectedChange={(v) => {
                        v && ($settingsForm.phase = v.value);
                        }}
                    >
                        <Select.Trigger class="w-full">
                            <Select.Value placeholder="{currentPhase}-internship" />
                        </Select.Trigger>
                        <Select.Content>
                            {#each phases as phase}
                                <Select.Item value={phase}>{phase}-internship</Select.Item>
                            {/each}
                        </Select.Content>
                    </Select.Root>
                
                    <h2 class="col-span-2 mt-4 text-xl font-bold border-b-2 border-b-dark-primary">
                        Requirement Deadlines
                    </h2>
                    {#each $settingsForm.requirements as requirement, i}
                        {@const { requirement_name } = requirement}
                        <Label class="ml-4 text-md" for="{requirement_name} deadline">
                            {requirement_name}
                        </Label>
                        <Input
                            name="{requirement_name} deadline"
                            type="datetime-local"
                            step="1"
                            bind:value={$settingsForm.requirements[i].deadline}
                            class="flex flex-col p-2 justify-between"
                        />
                    {/each}

                    <h2 class="col-span-2 mt-4 text-xl font-bold border-b-2 border-b-dark-primary">
                        Form Deadlines
                    </h2>
                    {#each $settingsForm.forms as form, i}
                        {@const { form_name } = form}
                        <Label class="ml-4 text-md" for="{form_name} deadline">
                            {form_name}
                        </Label>
                        <Input
                            name="{form_name} deadline"
                            type="datetime-local"
                            step="1"
                            bind:value={$settingsForm.forms[i].deadline}
                            class="flex flex-col p-2 justify-between"
                        />
                    {/each}
            </div>
            <Button
                type="submit"
                variant="outline"
                class="w-full cursor-pointer bg-dark-primary text-white text-xl p-2"
            > Save </Button>
        </form>
    </div>
</div>
