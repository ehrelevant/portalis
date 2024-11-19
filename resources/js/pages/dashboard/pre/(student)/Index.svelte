<script>
    import { Link } from '@inertiajs/svelte';
    import Header from '@shared/components/InternshipHeader.svelte';
    import Pending from '@assets/pending_logo.svelte';
    import Submitted from '@assets/submitted_logo.svelte';
    import Validated from '@assets/validated_logo.svelte';
    import Submission from '@shared/components/SubmissionComponent.svelte';

    let date = 'mm/dd/yyyy';

    export let student_number;
    export let submissions;
    export let total_status;
</script>

<div class="main-screen w-full px-4 py-2">
    <Header txt="Pre-Internship Phase" />

    {#if total_status == 'pending'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-brown-light text-floating-brown"
        >
            <div class="h-stretch w-3 bg-floating-brown"></div>
            <div class="content-center px-5"><Pending /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Pending Files</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Please update/upload ALL pending documents before <i
                            >{date}</i
                        >.
                    </p>
                </div>
            </div>
        </div>
    {/if}

    {#if total_status == 'submitted'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-forest-light text-floating-forest"
        >
            <div class="h-stretch w-3 bg-floating-forest"></div>
            <div class="content-center px-5"><Submitted /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Submitted All Documents</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Please wait for your faculty advisor to validate your
                        documents.
                    </p>
                </div>
            </div>
        </div>
    {/if}

    {#if total_status == 'validated'}
        <div
            class="w-stretch flex max-h-fit min-h-24 flex-row content-center bg-floating-blue-light text-floating-blue"
        >
            <div class="h-stretch w-3 bg-floating-blue"></div>
            <div class="content-center px-5"><Validated /></div>
            <div class="flex flex-col justify-center py-5">
                <p class="text-4xl font-semibold">Validated All Documents</p>
                <div class="flex flex-row">
                    <p class="text-2xl font-medium">
                        Congratulations! Good luck on your internship!
                    </p>
                </div>
            </div>
        </div>
    {/if}

    <!-- File Submission Statuses -->
    <div>
        <p class="pt-2 text-xl">Internship Documents</p>
        <ul>
            {#each submissions.slice(0, 3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    href="/file/student/{student_number}/{requirement_id}"
                />
            {/each}
        </ul>

        <p class="pt-2 text-xl">Government IDs</p>
        <ul>
            {#each submissions.slice(3) as submission}
                {@const { requirement_id, requirement_name, status } =
                    submission}
                <Submission
                    file_name={requirement_name}
                    sub_status={status}
                    href="/file/student/{student_number}/{requirement_id}"
                />
            {/each}
        </ul>
    </div>

    <!-- Link to Submission Bin -->
    <div class="w-stretch flex justify-center p-4">
        <Link href="/dashboard/pre/upload">
            <div
                class="border-2 bg-light-secondary p-4 text-3xl text-light-secondary-text hover:opacity-90"
            >
                Submit Documents
            </div>
        </Link>
    </div>
</div>
