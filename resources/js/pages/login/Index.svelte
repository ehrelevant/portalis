<script>
    import { router } from '@inertiajs/svelte';

    export let errors = {};

    let values = {
        email: null,
        pin: null,
    };

    function handleSubmit(evt) {
        switch (evt.submitter.name) {
            case 'send_pin':
                router.post('/login/send_pin', { email: values.email });
                break;
            case 'login':
                router.post('/login', values);
                break;
        }
    }
</script>

<article class="main-screen flex flex-col items-center justify-center">
    Login Form

    <form on:submit|preventDefault={handleSubmit}>
        <label>
            Email:
            <input
                type="email"
                placeholder="Input email address"
                bind:value={values.email}
            />
        </label>
        {#if errors.email}<div>{errors.email}</div>{/if}
        <div>
            <label>
                PIN:
                <input
                    type="text"
                    placeholder="Input PIN"
                    bind:value={values.pin}
                />
            </label>
            <input
                type="submit"
                name="send_pin"
                value="Send PIN"
                class="border-4"
            />
        </div>
        {#if errors.pin}<div>{errors.pin}</div>{/if}
        <input type="submit" name="login" value="Login" class="border-4" />
    </form>
</article>
