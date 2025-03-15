<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Header from '$lib/components/InternshipHeader.svelte';

    import * as Dialog from '$lib/components/ui/dialog/index';
    import { Label } from '$lib/components/ui/label/index';
    import { Button } from '$lib/components/ui/button/index';
    import { colorVariants } from '$lib/customVariants';
    import { Textarea } from '$lib/components/ui/textarea';

    export let studentId;
    export let studentName;
    export let requirementId;
    export let requirementName;
    export let status;
    export let isAdmin = false;

    let isReturnFormOpen = false;
    const returnForm = useForm({
        remarks: null,
    });

    function returnRequirementSubmission() {
        $returnForm.post(
            `/requirement/${requirementId}/view/${studentId}/reject`,
            {
                preserveScroll: true,
                onSuccess: () => {
                    $returnForm.reset();
                    isReturnFormOpen = false;
                },
            },
        );
    }
</script>

<div class="main-screen flex w-full flex-col gap-4 p-4">
    <Header
        txt="{studentName.last_name}, {studentName.first_name} — {requirementName}"
    />
    <div class="flex h-full w-full flex-col gap-4">
        {#if status !== 'None'}
            <iframe
                src="/file/submission/{studentId}/{requirementId}"
                title={requirementName}
                class="h-full grow"
            >
                This browser does not support PDFs.
            </iframe>
        {/if}
        <div class="flex flex-row justify-center gap-2">
            {#if ['Accepted'].includes(status)}
                <Link
                    as="button"
                    href="/requirement/{requirementId}/view/{studentId}/invalidate"
                    method="post"
                    ><Button variant="destructive">Invalidate</Button></Link
                >
            {:else if ['For Review'].includes(status)}
                <Link
                    as="button"
                    href="/requirement/{requirementId}/view/{studentId}/validate"
                    method="post"
                    ><Button class={colorVariants.green}>Accept</Button></Link
                >
                <Dialog.Root bind:open={isReturnFormOpen}>
                    <Dialog.Trigger>
                        <Button variant="destructive">Return to Student</Button>
                    </Dialog.Trigger>
                    <Dialog.Content class="sm:max-w-[425px]">
                        <Dialog.Header>
                            <Dialog.Title>Return to Student</Dialog.Title>
                            <Dialog.Description>
                                Return {requirementName} submission to {studentName.last_name},
                                {studentName.first_name}.
                            </Dialog.Description>
                        </Dialog.Header>
                        <form
                            on:submit|preventDefault={returnRequirementSubmission}
                            class="flex flex-col gap-4"
                        >
                            <Label for="remarks">Remarks</Label>
                            <Textarea
                                id="remarks"
                                bind:value={$returnForm.remarks}
                            />
                            <Dialog.Footer>
                                <Dialog.Close>
                                    <Button variant="outline">Cancel</Button>
                                </Dialog.Close>
                                <Button variant="destructive" type="submit"
                                    >Return to Student</Button
                                >
                            </Dialog.Footer>
                        </form>
                    </Dialog.Content>
                </Dialog.Root>
            {/if}
            {#if isAdmin}
                <Link href="/requirement/{requirementId}/upload/{studentId}"
                    ><Button class={colorVariants.blue}>Upload Document</Button
                    ></Link
                >
            {/if}
        </div>
    </div>
</div>
