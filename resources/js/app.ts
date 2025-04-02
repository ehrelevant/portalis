import Layout from '$lib/Layout.svelte';
import { createInertiaApp, type ResolvedComponent } from '@inertiajs/svelte';
import type { SvelteComponent } from 'svelte';

createInertiaApp({
    id: 'app',
    resolve: (name) => {
        const pages = import.meta.glob<ResolvedComponent>(
            './pages/**/*.svelte',
            {
                eager: true,
            },
        );
        let page = pages[`./pages/${name}.svelte`];
        return { default: page.default, layout: page.layout || Layout };
        // Uses default layout unless a different the page has its own layout defined
        // See: https://inertiajs.com/pages
    },
    setup({ el, App, props }) {
        new App({ target: el, props });
    },
});
