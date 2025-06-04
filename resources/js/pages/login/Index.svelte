<script>
    import { router, page } from '@inertiajs/svelte';
    import * as Card from '$lib/components/ui/card';
    import { Input } from '$lib/components/ui/input';
    import { Button } from '$lib/components/ui/button';
    import { Label } from '$lib/components/ui/label';

    export let errors = {};

    let values = {
        email: null,
        pin: null,
    };

    let rateLimitTimer = 0;
    $: isRateLimited = rateLimitTimer > 0;
    let isInputPin = false;

    function handleSubmit(evt) {
        switch (evt.submitter.name) {
            case 'send_pin':
                router.post(
                    '/login/send-pin',
                    { email: values.email },
                    // For Rate Limiting the sending of PIN
                    // {
                    //     onSuccess: () => {
                    //         rateLimitTimer = 60;
                    //         const countdown = setInterval(() => {
                    //             rateLimitTimer -= 1;
                    //             if (rateLimitTimer <= 0) {
                    //                 rateLimitTimer = 0;
                    //                 clearInterval(countdown);
                    //             }
                    //         }, 1000);
                    //     },
                    // },
                );
                if (values.email == null || values.email == ""){
                    isInputPin = false; 
                } else {
                    isInputPin = true;
                };
                break;
            case 'login':
                router.post('/login', values);
                break;
            case 'back':
                isInputPin = false;
                break;
        }
    }
</script>

<div class="main-screen flex w-full flex-col items-center justify-center">
    <Card.Root class="w-4/5 border-2 sm:w-96">
        <Card.Header>
            <Card.Title class="flex content-end py-2 text-3xl"
                >Account Login</Card.Title
            >
        </Card.Header>

        <form on:submit|preventDefault={handleSubmit}>
            {#if !isInputPin}
                <!-- Login with email -->
                <Card.Content>
                    <div class="w-fullitems-center grid gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <Label for="email">Email:</Label>
                            <Input
                                type="email"
                                placeholder="Input email address"
                                bind:value={values.email}
                            />

                            {#if errors.email}
                                <Label
                                    class="text-md break-words text-floating-red"
                                >
                                    {errors.email}
                                </Label>
                            {/if}
                        </div>
                    </div>
                </Card.Content>

                <Card.Footer>
                    <Button
                        type="submit"
                        name="send_pin"
                        class="text-md w-full cursor-pointer"
                    >
                        Login
                    </Button>
                </Card.Footer>

            {:else}
                <!-- Verify with PIN -->
                <Card.Content>
                    <div class="w-fullitems-center grid gap-4">
                        <div class="flex flex-col space-y-1.5">
                            <Label for="pin">PIN:</Label>
                            <div class="flex items-end space-x-1.5">
                                <Input
                                    type="text"
                                    placeholder="Input PIN"
                                    bind:value={values.pin}
                                />
                                <Button
                                    class="text-md w-32 p-1"
                                    type="submit"
                                    name="send_pin"
                                    disabled={isRateLimited}
                                >
                                    {isRateLimited ? rateLimitTimer : 'Resend PIN'}
                                </Button>
                            </div>

                            {#if errors.pin}
                                <p class="text-sm break-words text-floating-red">
                                    {errors.pin}
                                </p>
                            {/if}
                        </div>
                    </div>
                </Card.Content>

                <Card.Footer>
                    <div class="flex flex-col gap-2 w-full">
                        <Button
                            type="submit"
                            name="login"
                            class="text-md cursor-pointer"
                        >
                            Confirm Login
                        </Button>
                        <Button
                            type="submit"
                            name="back"
                            class="text-sm cursor-pointer"
                            variant="outline"
                        >
                            Change email address
                        </Button>
                    </div>
                </Card.Footer>
            {/if}
        </form>
    </Card.Root>
</div>
