<script>
    import Dashboard from '$assets/dashboard_logo.svelte';
    import Privacy from '$assets/privacy_logo.svelte';
    import Github from '$assets/github_logo.svelte';
    import Bug from '$assets/bug_logo.svelte';
    import Pre from '$assets/pre_logo.svelte';
    import During from '$assets/during_logo.svelte';
    import Post from '$assets/post_logo.svelte';
    import Header from '$lib/components/InternshipHeader.svelte';
    import { Label } from "$lib/components/ui/label";
</script>

<div class="main-screen min-h-full w-full p-4">
    <Header>
        <div class="flex flex-row justify-center sm:justify-start pb-2">
            <!-- Temporary spot for logos-->
            <div class="pr-4">
                <Dashboard />
            </div>
            <div class="">
                <Dashboard />
            </div>
        </div>

        <Label class="text-4xl font-semibold"> <i> Portalis: </i> CS195 Internship Portal </Label>
    </Header>
    <br />
    <div class="text-md">
        Welcome and thank you for diving into Portalis. Portalis is the
        dedicated portal for your CS 195 experience, granted to you by the
        University of the Philippines Diliman - Department of Computer Science.
        <br /> <br />
        This portal was created to ensure a streamlined CS195 Internship process
        for every person involved, and that includes you!
    </div>
    <br />
    <article>
        <div class="text-xl italic">Internship Phases</div>
        <div class="text-md">
            The CS 195 experience can be informally divided into three main
            phases, Pre-Internship, During Internship, and Post-internship. The
            layout of the website slightly differs for each phase, depending on
            what is expected from its users.
        </div>
        <div
            class="flex flex-col items-start justify-around gap-4 py-4 md:flex-row"
        >
            <div class="flex flex-col items-center p-2">
                <Pre /> Pre-Internship
                <div class="text-md pt-3 text-justify">
                    This phase, as its name suggests, describes the phase before
                    actual internships. During this phase, only students are
                    expected to submit any documents that need to be processed
                    before formally enrolling into the semester. Once student
                    documents have been approved, we move on to the next phase
                    of CS 195.
                </div>
            </div>
            <div class="flex flex-col items-center p-2">
                <During /> During Internship
                <div class="text-md pt-3 text-justify">
                    During this phase, students should have been enrolled
                    already and have started their internship proper at their
                    respective companies. Students are expected to document
                    their weekly progress at their internships, through their
                    respective blog, following the instructions of the CS195
                    Faculty.
                    <br /> <br />
                    To assess student performance during their internships, each
                    intern’s supervisor is expected to submit reports regarding their
                    performance. If there are multiple interns under a single supervisor,
                    only one form needs to be filled out for all students under said
                    supervisor.
                </div>
            </div>
            <div class="flex flex-col items-center p-2">
                <Post /> Post-Internship
                <div class="text-md pt-3 text-justify">
                    Closing off CS 195, this phase is dedicated to verifying and
                    assessing student performance after their internship has
                    been completed. Students are expected to fill up a
                    self-assessment form that lists their total number of hours
                    completed while their supervisor validates the information
                    the student has filled up.
                </div>
            </div>
        </div>
    </article>
    <br />
    <article>
        <div class="text-xl italic">Acknowledgements</div>
        <div class="text-md">
            Portalis: CS 195 Portal was originally developed by Clyde Ambroz S.
            Acyatan, Dominic Lawrence R. Bermudez, Ehren A. Castillo, and
            Eoghaine Czeriel L. Saavedra as their CS 191/192 project under Prof.
            Ligaya Leah Figueroa for the University of the Philippines Diliman -
            Department of Computer Science.
        </div>
    </article>
    <br />

    <!--Some helpful buttons-->
    <div class="flex flex-col items-center justify-center text-xl sm:flex-row">
        <a
            href="https://github.com/ehrelevant/cs195-portal"
            target="_blank"
            class="m-2 flex w-fit cursor-pointer flex-row items-center rounded-2xl bg-floating-forest-light p-4 transition-all ease-linear hover:opacity-80 dark:bg-floating-forest"
        >
            <Github />
            <div class="ml-2">Github Repository</div>
        </a>

        <a
            href="https://github.com/ehrelevant/cs195-portal/issues/new"
            target="_blank"
            class="m-2 flex w-fit cursor-pointer flex-row items-center rounded-2xl bg-floating-red-light p-4 transition-all ease-linear hover:opacity-80 dark:bg-floating-red"
        >
            <Bug />
            <div class="ml-2">Bug Report</div>
        </a>

        <a
            href="/privacy"
            class="m-2 flex w-fit cursor-pointer flex-row items-center rounded-2xl bg-floating-brown-light p-4 transition-all ease-linear hover:opacity-80 dark:bg-floating-brown"
        >
            <Privacy />
            <div class="ml-2">Privacy Policy</div>
        </a>
    </div>
</div>
