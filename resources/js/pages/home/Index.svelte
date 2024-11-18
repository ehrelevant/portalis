<script>
    import Dashboard from '@assets/dashboard_logo.svelte';
    import Privacy from '@assets/privacy_logo.svelte';
    import Github from '@assets/github_logo.svelte';
    import Bug from '@assets/bug_logo.svelte';
    import Pre from '@assets/pre_logo.svelte';
    import During from '@assets/during_logo.svelte';
    import Post from '@assets/post_logo.svelte';
    import Dropdown from '@assets/dropdown_logo.svelte';
    import Dropup from '@assets/dropup_logo.svelte';

    const dropdown =
        'flex flex-row justify-between hover:bg-light-primary/80 dark:hover:bg-dark-primary/80 w-full p-3 my-1 rounded-xl';
</script>

<div class="main-screen min-h-full w-full p-4">
    <div class="flex flex-row justify-center sm:justify-start">
        <!-- Temporary spot for logos-->
        <div class="p-2">
            <Dashboard />
        </div>
        <div class="p-2">
            <Dashboard />
        </div>
    </div>
    <div class="text-4xl font-semibold">
        <i> Portalis: </i> CS195 Internship Portal
    </div>
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
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis
            consectetur cursus libero, sed pretium erat venenatis ut. Vivamus
            iaculis justo in velit facilisis posuere. Pellentesque habitant
            morbi tristique senectus et netus et malesuada fames ac turpis
            egestas.
        </div>
        <div
            class="flex flex-col items-center justify-around gap-4 py-4 md:flex-row"
        >
            <div class="flex flex-col items-center p-2">
                <Pre /> Pre-Internship
                <div class="text-md pt-3 text-justify">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Duis consectetur cursus libero, sed pretium erat venenatis
                    ut. Vivamus iaculis justo in velit facilisis posuere.
                    Pellentesque habitant morbi tristique senectus et netus et
                    malesuada fames ac turpis egestas.
                </div>
            </div>
            <div class="flex flex-col items-center p-2">
                <During /> During Internship
                <div class="text-md pt-3 text-justify">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Duis consectetur cursus libero, sed pretium erat venenatis
                    ut. Vivamus iaculis justo in velit facilisis posuere.
                    Pellentesque habitant morbi tristique senectus et netus et
                    malesuada fames ac turpis egestas.
                </div>
            </div>
            <div class="flex flex-col items-center p-2">
                <Post /> Post-Internship
                <div class="text-md pt-3 text-justify">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Duis consectetur cursus libero, sed pretium erat venenatis
                    ut. Vivamus iaculis justo in velit facilisis posuere.
                    Pellentesque habitant morbi tristique senectus et netus et
                    malesuada fames ac turpis egestas.
                </div>
            </div>
        </div>
    </article>
    <br />
    <article>
        <div class="text-xl italic">Acknowledgements</div>
        <div class="text-md">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis
            consectetur cursus libero, sed pretium erat venenatis ut. Vivamus
            iaculis justo in velit facilisis posuere. Pellentesque habitant
            morbi tristique senectus et netus et malesuada fames ac turpis
            egestas.
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
