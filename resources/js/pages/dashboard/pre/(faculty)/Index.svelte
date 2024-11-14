<script>
    import { router, page } from '@inertiajs/svelte';

    import Header from '@shared/components/InternshipHeader.svelte';
    import Search from '@assets/search_logo.svelte'

    let student_data = ['fn1,mn1,ln1,sn1,Validated', 'fn2,mn2,ln2,sn2,Pending', 'fn3,mn3,ln3,sn3,Submitted'].map(item => item.split(','));

</script>

<div class="main-screen p-4 w-full">
    <Header txt="Pre-Internship Phase"/>

    <!-- Search Function -->
    <div class="flex flex-row content-center justify-center">
        <div class="flex px-2 items-center">
            <Search />
        </div>
        <input
            class="w-full p-2 text-light-primary-text text-md sm:text-xl rounded-md"
            type="email"
            placeholder="Search by Name or Student Number"
        />
    </div>

    <!-- List of Students -->
    <div class="py-4">
        <ul>
            {#each student_data as data}
                <a href="{$page.url}/students/{data[3]}">
                    <li> <div class="flex p-4 my-2 justify-between bg-white dark:bg-black rounded-xl hover:opacity-80">
                        <div class="flex flex-col">
                            <div> {data[2]}, {data[0]} {data[1]} </div>
                            <div> {data[3]} </div>
                        </div>
                        <div class="flex items-center">
                            --- {data[4]}
                        </div>
                    </div> </li>
                </a>
            {/each}
        </ul>
    </div>
</div>
