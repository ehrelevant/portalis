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

<div class="main-screen flex flex-col items-center justify-center">
<article class="min-w-1/4">
    <div>
        <div class="content-end py-2 flex text-3xl"> Account Login </div>
        <div class="bg-light-primary dark:bg-dark-primary p-5 rounded-xl">
            <form
                class="m-2"
                on:submit|preventDefault={handleSubmit}
            >
                <label class="text-xl">
                    Email:
                    <input
                        class="w-full text-light-primary-text p-2"
                        type="email"
                        placeholder="Input email address"
                        bind:value={values.email}
                    />
                </label>
                {#if errors.email}<div class="text-floating-red dark:text-floating-red-dark pb-1">{errors.email}</div> {:else} <div class="pb-7"></div> {/if}
                <div class="flex items-end">
                    <label class="text-xl content-center">
                        PIN:
                        <input
                            class="text-light-primary-text p-2 w-full"
                            type="text"
                            placeholder="Input PIN"
                            bind:value={values.pin}
                        />
                    </label>
                    <input
                        class="border-4 rounded-2xl cursor-pointer text-xl ml-2 p-1 w-1/2 hover:bg-light-secondary-text  dark:hover:bg-dark-secondary-text transition-all ease-in"
                        type="submit"
                        name="send_pin"
                        value="Send PIN"
                    />
                </div>
                {#if errors.pin}<div class="text-floating-red dark:text-floating-red-dark pb-4">{errors.pin}</div> {:else} <div class="pb-10"></div> {/if}
                <input type="submit" name="login" value="Login" class="border-4 cursor-pointer w-full text-2xl rounded-full hover:bg-light-secondary-text  dark:hover:bg-dark-secondary-text transition-all ease-in"/>
            </form>
        </div>
    </div>
</article>
</div>
