<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class OnboardingDraftRequest extends OnboardingRequest
{
    public function withValidator(Validator $validator): void
    {
        // A draft may be unfinished. Required answers are checked when onboarding is saved.
    }
}
