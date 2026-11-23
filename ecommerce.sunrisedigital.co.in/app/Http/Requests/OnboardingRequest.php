<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class OnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        $user = $this->user();

        abort_unless(
            $order instanceof Order
                && $user !== null
                && $order->user_id === $user->id
                && in_array($order->status, Order::settledStatuses(), true),
            404,
        );

        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.answers' => ['nullable', 'array'],
            'items.*.answers.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $order = $this->route('order');

            if (! $order instanceof Order) {
                return;
            }

            $order->loadMissing('items');

            foreach ($order->items as $item) {
                if (! $item->needsOnboarding()) {
                    continue;
                }

                foreach ($item->onboardingQuestions() as $index => $question) {
                    if (! ($question['required'] ?? false)) {
                        continue;
                    }

                    $answer = trim((string) data_get($this->input('items'), $item->id.'.answers.'.$index));
                    $audio = $question['audio'] ?? null;

                    if ($answer === '' && ! (is_string($audio) && $audio !== '')) {
                        $validator->errors()->add('items.'.$item->id.'.answers.'.$index, 'Answer this question in writing or with the microphone.');
                    }
                }
            }
        });
    }
}
