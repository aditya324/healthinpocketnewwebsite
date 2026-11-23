<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingDraftRequest;
use App\Http\Requests\OnboardingRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OnboardingController extends Controller
{
    public function edit(Request $request, Order $order): View|RedirectResponse
    {
        $this->visibleTo($request, $order);
        $order->load('items');

        abort_unless($order->hasOnboarding(), 404);

        if (! $order->needsOnboarding()) {
            return redirect()->route('orders.show', $order);
        }

        return view('orders.onboarding', [
            'order' => $order,
        ]);
    }

    public function update(OnboardingRequest $request, Order $order): RedirectResponse
    {
        $order->load('items');

        abort_unless($order->needsOnboarding(), 404);

        $this->writeAnswers($order, $request->validated('items') ?? [], true);

        return redirect()
            ->route('dashboard')
            ->with('onboarding_complete', true);
    }

    public function draft(OnboardingDraftRequest $request, Order $order): JsonResponse
    {
        $order->load('items');

        abort_unless($order->needsOnboarding(), 404);

        $this->writeAnswers($order, $request->validated('items') ?? [], false);

        return response()->json(['saved' => true]);
    }

    /**
     * @param  array<int|string, mixed>  $items
     */
    private function writeAnswers(Order $order, array $items, bool $complete): void
    {
        foreach ($order->items as $item) {
            if (! $item->needsOnboarding()) {
                continue;
            }

            $onboarding = $item->onboarding;

            foreach ($item->onboardingQuestions() as $index => $question) {
                $answer = trim((string) data_get($items, $item->id.'.answers.'.$index));
                $onboarding['questions'][$index]['answer'] = $answer !== '' ? $answer : null;
            }

            if ($complete) {
                $onboarding['completed_at'] = now()->toIso8601String();
            }

            $item->update(['onboarding' => $onboarding]);
        }
    }

    public function storeAudio(Request $request, Order $order, OrderItem $item, int $index): JsonResponse
    {
        $this->visibleTo($request, $order);
        abort_unless($item->needsOnboarding(), 404);
        $this->question($order, $item, $index);

        $request->validate([
            'audio' => ['required', 'file', 'max:8192'],
        ]);

        $file = $request->file('audio');
        $extension = $file instanceof UploadedFile ? $this->audioExtension($file) : null;

        if ($extension === null) {
            throw ValidationException::withMessages([
                'audio' => 'Record a voice answer.',
            ]);
        }

        $path = $file->storeAs(
            'onboarding/'.$order->uuid,
            $item->uuid.'-'.$index.'-'.Str::lower(Str::random(8)).'.'.$extension,
            'local',
        );

        $onboarding = $item->onboarding;
        $previous = $onboarding['questions'][$index]['audio'] ?? null;

        if (is_string($previous) && $previous !== $path && str_starts_with($previous, 'onboarding/')) {
            Storage::disk('local')->delete($previous);
        }

        $onboarding['questions'][$index]['audio'] = $path;
        $item->update(['onboarding' => $onboarding]);

        return response()->json([
            'url' => route('orders.onboarding.audio', [$order, $item, $index]),
        ]);
    }

    public function audio(Request $request, Order $order, OrderItem $item, int $index): StreamedResponse
    {
        $this->visibleToListener($request, $order);
        $question = $this->question($order, $item, $index);
        $path = $question['audio'] ?? null;

        abort_unless(is_string($path) && str_starts_with($path, 'onboarding/') && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }

    /**
     * @return array<string, mixed>
     */
    private function question(Order $order, OrderItem $item, int $index): array
    {
        abort_unless($item->order_id === $order->id, 404);

        $question = $item->onboardingQuestions()[$index] ?? null;

        abort_unless(is_array($question), 404);

        return $question;
    }

    private function audioExtension(UploadedFile $file): ?string
    {
        $mime = strtolower((string) $file->getMimeType());

        if ($mime === '' || $mime === 'application/octet-stream') {
            $mime = strtolower((string) $file->getClientMimeType());
        }

        return match ($mime) {
            'audio/webm', 'video/webm' => 'webm',
            'audio/ogg', 'application/ogg' => 'ogg',
            'audio/mpeg', 'audio/mp3' => 'mp3',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/mp4', 'audio/m4a', 'audio/x-m4a' => 'm4a',
            default => null,
        };
    }

    private function visibleToListener(Request $request, Order $order): void
    {
        if (Auth::guard('admin')->check() && in_array($order->status, Order::settledStatuses(), true)) {
            return;
        }

        $this->visibleTo($request, $order);
    }

    private function visibleTo(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()?->id && in_array($order->status, Order::settledStatuses(), true), 404);
    }
}
