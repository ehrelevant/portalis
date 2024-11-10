<script>
    import { page, router } from '@inertiajs/svelte';

    $: email = $page.props.auth.user?.email;
</script>

<div class="main-screen">
    <p>Currently logged into: {email}</p>
    <form on:submit|preventDefault={() => router.post('/logout')}>
        <input type="submit" class="border-2" value="logout" />
    </form>
</div>
