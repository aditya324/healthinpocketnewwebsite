<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('onboarding')->nullable();
        });

        foreach ($this->packages() as $slug => $questions) {
            $service = DB::table('services')->where('slug', $slug)->first();

            if ($service === null) {
                continue;
            }

            $details = json_decode((string) $service->details, true);
            $details = is_array($details) ? $details : [];
            $details['questions'] = $questions;

            DB::table('services')->where('id', $service->id)->update([
                'details' => json_encode($details),
            ]);
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->packages()) as $slug) {
            $service = DB::table('services')->where('slug', $slug)->first();

            if ($service === null) {
                continue;
            }

            $details = json_decode((string) $service->details, true);
            $details = is_array($details) ? $details : [];
            unset($details['questions']);

            DB::table('services')->where('id', $service->id)->update([
                'details' => json_encode($details),
            ]);
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('onboarding');
        });
    }

    /**
     * @return array<string, list<array{prompt: string, help: ?string, required: bool}>>
     */
    private function packages(): array
    {
        $shared = [
            ['prompt' => 'What is the business name, and what do you sell?', 'help' => null, 'required' => true],
            ['prompt' => 'What are the Instagram and Facebook links?', 'help' => null, 'required' => true],
            ['prompt' => 'Who do you want to reach?', 'help' => null, 'required' => true],
            ['prompt' => 'What should someone do after they see a post?', 'help' => 'For example, call, visit, or send a message.', 'required' => true],
            ['prompt' => 'What are the brand colors, logo, and anything we should never say?', 'help' => null, 'required' => true],
            ['prompt' => 'Who can give us Facebook, Instagram, and Google Business access?', 'help' => null, 'required' => true],
        ];

        $growth = [
            ['prompt' => 'What counts as a good enquiry?', 'help' => null, 'required' => true],
            ['prompt' => 'What are two campaign goals for this month?', 'help' => null, 'required' => true],
            ['prompt' => 'What Meta ads budget can you spend apart from the package fee?', 'help' => 'This is the ad spend, separate from the package fee.', 'required' => true],
            ['prompt' => 'Which competitors should we watch?', 'help' => null, 'required' => false],
            ['prompt' => 'Where should leads go?', 'help' => 'WhatsApp, a form, or a phone number.', 'required' => true],
        ];

        $performance = [
            ['prompt' => 'What is the website or landing page we should use?', 'help' => null, 'required' => true],
            ['prompt' => 'Who can give Google Ads access, and what is the monthly Google budget?', 'help' => null, 'required' => true],
            ['prompt' => 'What is the LinkedIn page link?', 'help' => 'Leave this blank if you do not have a page yet.', 'required' => false],
            ['prompt' => 'Which outcomes do you want from ads?', 'help' => 'Up to four, such as calls, visits, leads, or sales.', 'required' => true],
            ['prompt' => 'Which locations do you serve?', 'help' => null, 'required' => true],
        ];

        $strategic = [
            ['prompt' => 'Which CRM or sheet do you use for leads today?', 'help' => null, 'required' => true],
            ['prompt' => 'What is the WhatsApp Business number?', 'help' => null, 'required' => true],
            ['prompt' => 'Who should join the weekly check-in?', 'help' => null, 'required' => true],
            ['prompt' => 'What result do you want by the end of the quarter?', 'help' => null, 'required' => true],
        ];

        return [
            'digital-presence-partner' => [
                ...$shared,
                ['prompt' => 'What is your Google Business Profile link?', 'help' => 'Leave this blank if you do not have a profile yet.', 'required' => false],
            ],
            'growth-marketing-partner' => [...$shared, ...$growth],
            'performance-growth-partner' => [...$shared, ...$growth, ...$performance],
            'strategic-growth-partner' => [...$shared, ...$growth, ...$performance, ...$strategic],
        ];
    }
};
