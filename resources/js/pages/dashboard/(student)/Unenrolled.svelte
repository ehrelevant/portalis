<script>
    import Header from '$lib/components/InternshipHeader.svelte';
    import { Label } from '$lib/components/ui/label';
</script>

<div class="main-screen w-full p-4">
    <Header txt="You are currently NOT ENROLLED" />

    <Label class="text-2xl">
        You are currently registered as NOT ENROLLED to this course. This is
        likely due to a missing requirement. To resolve this, please contact the
        faculty or admin.
    </Label>
</div>
