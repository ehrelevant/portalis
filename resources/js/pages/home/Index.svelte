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

    const dropdown = "flex flex-row justify-between hover:bg-light-primary/80 dark:hover:bg-dark-primary/80 w-full p-3 my-1 rounded-xl"
</script>

<div class="main-screen p-4 min-h-full">
    <div class = "flex flex-row justify-center sm:justify-start"> 
        <!-- Temporary spot for logos-->
        <div class="p-2">
            <Dashboard />
        </div>
        <div class="p-2">
            <Dashboard />
        </div>
    </div>
    <div class="text-4xl font-semibold"> <i> Portalis: </i> CS195 Internship Portal </div>
    <br>
    <div class="text-md">
        Welcome and thank you for diving into Portalis. Portalis is the dedicated portal for your CS 195 experience, granted to you by the University of the Philippines Diliman - Department of Computer Science. 
        <br> <br>
        This portal was created to ensure a streamlined CS195 Internship process for every person involved, and that includes you!
    </div>
    <br>
    <article>
        <div class="text-xl italic"> Internship Phases </div>
        <div class="text-md"> 
            Lorem ipsum dolor sit amet, 
            consectetur adipiscing elit. Duis consectetur cursus libero, 
            sed pretium erat venenatis ut. Vivamus iaculis justo in velit facilisis posuere. 
            Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
        </div>
        <div class="flex flex-col md:flex-row justify-around items-center py-4 gap-4">
            <div class="flex flex-col items-center p-2"> 
                <Pre /> Pre-Internship
                <div class="pt-3 text-md text-justify"> 
                    Lorem ipsum dolor sit amet, 
                    consectetur adipiscing elit. Duis consectetur cursus libero, 
                    sed pretium erat venenatis ut. Vivamus iaculis justo in velit facilisis posuere. 
                    Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                </div>
            </div>
            <div class="flex flex-col items-center p-2"> 
                <During /> During Internship
                <div class="pt-3 text-md text-justify"> 
                    Lorem ipsum dolor sit amet, 
                    consectetur adipiscing elit. Duis consectetur cursus libero, 
                    sed pretium erat venenatis ut. Vivamus iaculis justo in velit facilisis posuere. 
                    Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                </div>
            </div>
            <div class="flex flex-col items-center p-2"> 
                <Post /> Post-Internship
                <div class="pt-3 text-md text-justify"> 
                    Lorem ipsum dolor sit amet, 
                    consectetur adipiscing elit. Duis consectetur cursus libero, 
                    sed pretium erat venenatis ut. Vivamus iaculis justo in velit facilisis posuere. 
                    Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
                </div>
            </div>
        </div>
    </article>
    <br>
    <article>
        <div class="text-xl italic"> Acknowledgements </div>
        <div class="text-md"> 
            Lorem ipsum dolor sit amet, 
            consectetur adipiscing elit. Duis consectetur cursus libero, 
            sed pretium erat venenatis ut. Vivamus iaculis justo in velit facilisis posuere. 
            Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
        </div>
    </article>
    <br>

    <!--Some helpful buttons-->
    <div class="flex flex-col sm:flex-row justify-center items-center text-xl"> 
        <a href="https://github.com/ehrelevant/cs195-portal" target="_blank" class="flex flex-row w-fit items-center p-4 m-2 rounded-2xl cursor-pointer bg-floating-forest-light dark:bg-floating-forest hover:opacity-80 transition-all ease-linear">
            <Github />
            <div class="ml-2"> Github Repository </div>
        </a>

        <a href="https://github.com/ehrelevant/cs195-portal/issues/new" target="_blank" class="flex flex-row w-fit items-center p-4 m-2 rounded-2xl cursor-pointer bg-floating-red-light dark:bg-floating-red hover:opacity-80 transition-all ease-linear">
            <Bug />
            <div class="ml-2"> Bug Report </div>
        </a>

        <a href="/privacy" class="flex flex-row w-fit items-center p-4 m-2 rounded-2xl cursor-pointer bg-floating-brown-light dark:bg-floating-brown hover:opacity-80 transition-all ease-linear">
            <Privacy />
            <div class="ml-2"> Privacy Policy </div>
        </a>
    </div>
</div>
