<?php

namespace App\Http\Controllers;

use App\Models\WebsiteState;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebsiteStateController extends Controller
{
    public function updateWebsiteState(Request $request)
    {
        $form_values = $request->validate([
            'phase' => ['required', Rule::in(['pre','during','post'])]
        ]);

        $website_state = WebsiteState::findOrFail(1);
        $website_state->phase = $form_values['phase'];
        $website_state->save();

        return back();
    }
}
