<script>
    import { Link } from '@inertiajs/svelte';
    import Icon from '@iconify/svelte';
    import Dashboard from '$assets/dashboard_logo.svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import { Button } from '$lib/components/ui/button';
    import { colorVariants } from '$lib/customVariants';
    import * as Accordion from '$lib/components/ui/accordion/index';
</script>

<div class="main-screen flex min-h-full w-full flex-col gap-4 p-4">
    <section>
        <Header>
            <div
                class="flex w-full flex-col-reverse items-start gap-2 md:flex-row md:items-center md:justify-between"
            >
                <h1 class="text-4xl font-bold">
                    <em>Portalis:</em> CS195 Internship Portal
                </h1>
                <!-- Temporary spot for logos-->
                <div
                    class="flex flex-row justify-center gap-4 sm:justify-start"
                >
                    <Dashboard />
                    <Dashboard />
                </div>
            </div>
        </Header>
        <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
            Welcome and thank you for diving into <strong>Portalis</strong>.
            <strong>Portalis</strong>
            is the dedicated portal for your <em>CS 195 experience</em>, granted
            to you by the
            <strong
                >University of the Philippines Diliman - Department of Computer
                Science</strong
            >.
        </p>
        <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
            This portal was created to ensure a streamlined CS195 Internship
            process for every person involved, and that includes you!
        </p>
        <h2
            class="mt-10 scroll-m-20 border-b pb-2 text-3xl font-semibold tracking-tight transition-colors first:mt-0"
        >
            Internship Phases
        </h2>
        <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
            The CS 195 experience can be informally divided into three main
            phases, <em>Pre-Internship</em>, <em>During Internship</em>, and
            <em>Post-internship</em>. The layout of the website slightly differs
            for each phase, depending on what is expected from its users.
        </p>
        <Accordion.Root class="mt-6 w-full" multiple>
            <Accordion.Item value="pre">
                <Accordion.Trigger
                    ><h3
                        class="flex scroll-m-20 flex-row items-center gap-2 text-2xl font-semibold tracking-tight"
                    >
                        <Icon icon="jam:write" class="size-8" /> Pre-Internship
                    </h3></Accordion.Trigger
                >
                <Accordion.Content>
                    <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
                        This phase, as its name suggests, describes the phase
                        before actual internships. During this phase, only
                        students are expected to submit any documents that need
                        to be processed before formally enrolling into the
                        semester. Once student documents have been approved, we
                        move on to the next phase of CS 195.
                    </p>
                </Accordion.Content>
            </Accordion.Item>
            <Accordion.Item value="during">
                <Accordion.Trigger
                    ><h3
                        class="flex scroll-m-20 flex-row items-center gap-2 text-2xl font-semibold tracking-tight"
                    >
                        <Icon icon="mi:document" class="size-8" /> During Internship
                    </h3></Accordion.Trigger
                >
                <Accordion.Content>
                    <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
                        During this phase, students should have been enrolled
                        already and have started their internship proper at
                        their respective companies. Students are expected to
                        document their weekly progress at their internships,
                        through their respective blog, following the
                        instructions of the CS195 Faculty.
                    </p>
                    <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
                        To assess student performance during their internships,
                        each intern’s supervisor is expected to submit reports
                        regarding their performance. If there are multiple
                        interns under a single supervisor, only one form needs
                        to be filled out for all students under said supervisor.
                    </p>
                </Accordion.Content>
            </Accordion.Item>
            <Accordion.Item value="post">
                <Accordion.Trigger
                    ><h3
                        class="flex scroll-m-20 flex-row items-center gap-2 text-2xl font-semibold tracking-tight"
                    >
                        <Icon icon="tabler:checkbox" class="size-8" /> Post-Internship
                    </h3></Accordion.Trigger
                >
                <Accordion.Content>
                    <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
                        Closing off CS 195, this phase is dedicated to verifying
                        and assessing student performance after their internship
                        has been completed. Students are expected to fill up a
                        self-assessment form that lists their total number of
                        hours completed while their supervisor validates the
                        information the student has filled up.
                    </p>
                </Accordion.Content>
            </Accordion.Item>
        </Accordion.Root>
        <h2
            class="mt-10 scroll-m-20 border-b pb-2 text-3xl font-semibold tracking-tight transition-colors first:mt-0"
        >
            Acknowledgements
        </h2>
        <p class="text-lg leading-7 [&:not(:first-child)]:mt-6">
            <strong>Portalis: CS 195 Portal</strong> was originally developed by
            <em>Clyde Ambroz S. Acyatan</em>,
            <em>Dominic Lawrence R. Bermudez</em>, <em>Ehren A. Castillo</em>,
            and
            <em>Eoghaine Czeriel L. Saavedra</em> for the accomplishment of
            their CS 191/192 course under <em>Prof. Ligaya Leah Figueroa</em>
            for
            <em
                >the University of the Philippines Diliman - Department of
                Computer Science</em
            >.
        </p>
    </section>

    <div class="flex flex-col items-center justify-center gap-2 md:flex-row">
        <Button
            href="https://github.com/ehrelevant/cs195-portal"
            target="_blank"
            class="flex h-16 w-full flex-row gap-2 text-xl md:w-auto"
        >
            <Icon icon="mdi:github" class="size-10" />
            Github Repository
        </Button>

        <Button
            href="https://github.com/ehrelevant/cs195-portal/issues/new"
            target="_blank"
            class="flex h-16 w-full flex-row gap-2 text-xl md:w-auto {colorVariants.red}"
        >
            <Icon icon="flowbite:bug-outline" class="size-10" />
            Bug Report
        </Button>

        <Link href="/privacy" class="w-full md:w-auto">
            <Button
                class="flex h-16 w-full flex-row gap-2 text-xl md:w-auto {colorVariants.yellow}"
            >
                <Icon icon="uil:lock" class="size-10" />
                Privacy Policy
            </Button>
        </Link>
    </div>
</div>
