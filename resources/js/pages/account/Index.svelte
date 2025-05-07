<script>
    import { router } from '@inertiajs/svelte';
    import Icon from '@iconify/svelte';
    import * as Card from '$lib/components/ui/card';
    import { Button } from '$lib/components/ui/button';

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
    <Card.Root class="min-w-1/4 border-2">
        <Card.Header>
            <Card.Title
                class="flex content-end items-center gap-2 py-2 text-3xl"
            >
                <Icon icon="octicon:person-16" class="text-3xl" />
                Account Details
            </Card.Title>
        </Card.Header>

        <Card.Content class="px-8 py-4">
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
        </Card.Content>

        <Card.Footer class="flex justify-end p-3">
            <form on:submit|preventDefault={() => router.post('/logout')}>
                <Button
                    type="submit"
                    class="cursor-pointer bg-custom-primary px-8 text-xl text-white hover:bg-opacity-90"
                >
                    Logout
                </Button>
            </form>
        </Card.Footer>
    </Card.Root>
</div>
