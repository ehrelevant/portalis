<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Requirement;
use App\Models\WebsiteState;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WebsiteStateController extends Controller
{
    public function updateSettings(Request $request)
    {
        try {
            $form_values = $request->validate([
                'phase' => ['required', Rule::in(['pre', 'during', 'post'])],
                'requirements.*.id' => ['int'],
                'requirements.*.deadline' => ['date', 'nullable'],
                'forms.*.id' => ['int'],
                'forms.*.deadline' => ['date', 'nullable'],
            ]);

            $website_state = WebsiteState::findOrFail(1);
            $website_state->phase = $form_values['phase'];
            $website_state->save();

            $new_requirements = $form_values['requirements'];
            $new_forms = $form_values['forms'];

            foreach ($new_requirements as $new_requirement) {
                ['id' => $id, 'deadline' => $deadline] = $new_requirement;
                $requirement = Requirement::find($id);
                $requirement->deadline = $deadline;
                $requirement->save();
            }
            foreach ($new_forms as $new_form) {
                ['id' => $id, 'deadline' => $deadline] = $new_form;
                $form = Form::find($id);
                $form->deadline = $deadline;
                $form->save();
            }

            return back()->with('success', 'Successfully saved settings.');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to save settings.');
        }
    }
}
