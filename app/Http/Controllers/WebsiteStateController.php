<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Requirement;
use App\Models\WebsiteState;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebsiteStateController extends Controller
{
    public function updateWebsiteState(Request $request)
    {
        $form_values = $request->validate([
            'phase' => ['required', Rule::in(['pre','during','post'])],
        ]);

        $website_state = WebsiteState::findOrFail(1);
        $website_state->phase = $form_values['phase'];
        $website_state->save();

        return back();
    }

    public function updateDeadlines(Request $request)
    {
        $form_values = $request->validate([
            'requirements.*.id' => ['int'],
            'requirements.*.deadline' => ['date', 'nullable'],
            'forms.*.id' => ['int'],
            'forms.*.deadline' => ['date', 'nullable'],
        ]);

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
    }
}
