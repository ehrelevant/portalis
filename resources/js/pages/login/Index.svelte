<script>
    import { router, page } from '@inertiajs/svelte';

    export let errors = {};

    let values = {
        email: null,
        pin: null,
    };

    let rateLimitTimer = 0;
    $: isRateLimited = rateLimitTimer > 0;

    function handleSubmit(evt) {
        switch (evt.submitter.name) {
            case 'send_pin':
                router.post(
                    '/login/send-pin',
                    { email: values.email },
                    {
                        onSuccess: () => {
                            rateLimitTimer = 60;
                            const countdown = setInterval(() => {
                                rateLimitTimer -= 1;
                                if (rateLimitTimer <= 0) {
                                    rateLimitTimer = 0;
                                    clearInterval(countdown);
                                }
                            }, 1000);
                        },
                    },
                );

                break;
            case 'login':
                router.post('/login', values);
                break;
        }
    }
</script>

<div class="main-screen flex w-full flex-col items-center justify-center">
    <article class="min-w-1/4">
        <div>
            <div class="flex content-end py-2 text-3xl">Account Login</div>
            <div class="rounded-xl bg-light-primary p-5 dark:bg-dark-primary">
                <form class="m-2" on:submit|preventDefault={handleSubmit}>
                    <label class="text-xl">
                        Email:
                        <input
                            class="w-full p-2 text-light-primary-text"
                            type="email"
                            placeholder="Input email address"
                            bind:value={values.email}
                        />
                    </label>
                    {#if errors.email}<div
                            class="dark:text-floating-red-dark pb-1 text-floating-red dark:text-floating-red-light"
                        >
                            {errors.email}
                        </div>
                    {:else}
                        <div class="pb-7"></div>
                    {/if}
                    <div class="flex items-end">
                        <label class="content-center text-xl">
                            PIN:
                            <input
                                class="w-full p-2 text-light-primary-text"
                                type="text"
                                placeholder="Input PIN"
                                bind:value={values.pin}
                            />
                        </label>
                        <input
                            class="ml-2 w-32 rounded-2xl border-4 p-1 text-xl transition-all ease-in enabled:cursor-pointer enabled:hover:bg-light-secondary-text dark:enabled:hover:bg-dark-secondary-text"
                            type="submit"
                            name="send_pin"
                            value={isRateLimited ? rateLimitTimer : 'Send PIN'}
                            disabled={isRateLimited}
                        />
                    </div>
                    {#if errors.pin}
                        <p
                            class="pb-4 text-floating-red dark:text-floating-red-light"
                        >
                            {errors.pin}
                        </p>
                    {:else}
                        <div class="pb-10"></div>
                    {/if}
                    <input
                        type="submit"
                        name="login"
                        value="Login"
                        class="w-full cursor-pointer rounded-full border-4 text-2xl transition-all ease-in hover:bg-light-secondary-text dark:hover:bg-dark-secondary-text"
                    />
                </form>
            </div>
        </div>
        {#if $page.props.flash.message}
            <p class="italic">(For testing) PIN: {$page.props.flash.message}</p>
        {/if}
    </article>
</div>
