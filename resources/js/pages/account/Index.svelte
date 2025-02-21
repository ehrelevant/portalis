<script>
    import { router } from '@inertiajs/svelte';
    import Account from '$assets/account_logo.svelte';

    /** @type {string} */
    export let first_name;
    /** @type {string} */
    export let middle_name;
    /** @type {string} */
    export let last_name;
    /** @type {string} */
    export let role;
    /** @type {string} */
    export let email;

    export let wordpress_name;
    export let wordpress_email;
</script>

<div
    class="main-screen flex flex-col content-center items-center justify-center"
>
    <div class="h-auto w-auto">
        <div class="flex content-end py-2 text-3xl">
            <Account />
            <div class="ml-1">Account Details</div>
        </div>
        <div
            class="text-md rounded-xl bg-light-primary p-7 dark:bg-dark-primary"
        >
            Currently logged in:
            <div class="flex flex-col text-2xl">
                <p class="italic">({role})</p>
                <p>
                    Name: {last_name}, {first_name}
                    {middle_name}
                </p>
                <p>Email: {email}</p>
                {#if wordpress_name && wordpress_email}
                    <p>Wordpress Name: {wordpress_name}</p>
                    <p>Wordpress Email: {wordpress_email}</p>
                {/if}
            </div>

            <div>
                <form
                    on:submit|preventDefault={() => router.post('/logout')}
                    class="flex justify-end pt-3"
                >
                    <input
                        type="submit"
                        class="w-1/2 cursor-pointer rounded-2xl border-4 p-1 text-xl transition-all ease-in hover:bg-light-secondary-text dark:hover:bg-dark-secondary-text"
                        value="Logout"
                    />
                </form>
            </div>
        </div>
    </div>
</div>
