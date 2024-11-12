<script>
    import { page, router } from '@inertiajs/svelte';
    import Account from '@assets/account_logo.svelte';

    export let name = "Name";
    export let role = "Role";

    $: email = $page.props.auth.user?.email;
</script>

<div class="main-screen flex flex-col items-center content-center justify-center">
    <div class="w-auto h-auto">
        <div class="content-end py-2 flex text-3xl"> <Account /> <div class="ml-1"> Account Details </div> </div>
        <div class="bg-light-primary dark:bg-dark-primary p-7 rounded-xl text-md">
            Currently logged in:
            <div class="flex flex-col text-2xl">
                {name} <br> {role} <br> {email}
            </div>

            <div>
                <form on:submit|preventDefault={() => router.post('/logout')} class="flex justify-end pt-3">
                    <input type="submit" class="border-4 rounded-2xl cursor-pointer text-xl p-1 w-1/2 hover:bg-light-secondary-text  dark:hover:bg-dark-secondary-text transition-all ease-in" value="Logout" />
                </form>
            </div>
        </div>
    </div>
</div>
