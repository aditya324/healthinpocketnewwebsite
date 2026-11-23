<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->packages() as $slug => $package) {
            $service = DB::table('services')->where('slug', $slug)->first();

            if ($service === null) {
                continue;
            }

            $details = json_decode((string) $service->details, true);
            $details = is_array($details) ? $details : [];
            $details['package_label'] = $package['label'];
            $details['reasons'] = $package['reasons'];
            $details['questions'] = $this->questions($package['questions']);

            if ($package['price_note'] !== null) {
                $details['price_note'] = $package['price_note'];
            }

            DB::table('services')->where('id', $service->id)->update([
                'short_description' => $package['summary'],
                'description' => $package['summary'],
                'details' => json_encode($details),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->previous() as $slug => $package) {
            $service = DB::table('services')->where('slug', $slug)->first();

            if ($service === null) {
                continue;
            }

            $details = json_decode((string) $service->details, true);
            $details = is_array($details) ? $details : [];
            unset($details['package_label'], $details['reasons'], $details['price_note']);
            $details['questions'] = $package['questions'];

            DB::table('services')->where('id', $service->id)->update([
                'short_description' => $package['summary'],
                'description' => $package['summary'],
                'details' => json_encode($details),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * @param  list<string>  $prompts
     * @return list<array{prompt: string, help: null, required: true}>
     */
    private function questions(array $prompts): array
    {
        return array_map(fn (string $prompt): array => [
            'prompt' => $prompt,
            'help' => null,
            'required' => true,
        ], $prompts);
    }

    /**
     * @return array<string, array{label: string, summary: string, price_note: ?string, questions: list<string>, reasons: list<array{title: string, body: string}>}>
     */
    private function packages(): array
    {
        return [
            'digital-presence-partner' => [
                'label' => 'Starter Package',
                'summary' => 'Build a professional, consistent and engaging digital presence for your business. Strengthen your brand identity, connect with your audience and maintain an active presence across essential digital platforms.',
                'price_note' => null,
                'questions' => [
                    'What is your business name, industry and primary product or service?',
                    'Who is your target audience, and which locations do you want to reach?',
                    'Do you currently have Facebook and Instagram business accounts?',
                    'Do you have an existing brand logo, brand colours and other branding materials?',
                    'What type of social media content would you like us to create for your business?',
                    'Do you have product images, business photographs or videos available for content creation?',
                    'What are your primary expectations from social media marketing: brand awareness, engagement or online visibility?',
                    'Are there any specific competitors or reference brands whose social media presence you would like us to consider?',
                ],
                'reasons' => [
                    ['title' => 'Professional Digital Presence', 'body' => 'Build a professional and consistent digital identity that helps your business establish credibility and make a strong first impression online.'],
                    ['title' => 'Consistent Content Creation', 'body' => 'Keep your audience engaged with 8 creative posts, 2 professionally edited reels and 12 social media stories every month.'],
                    ['title' => 'Strategic Content Planning', 'body' => 'Follow a structured monthly content calendar with relevant captions and hashtags to ensure consistent and purposeful communication.'],
                    ['title' => 'Complete Social Media Management', 'body' => 'Strengthen your online presence through Facebook, Instagram and Google Business Profile management, along with community engagement monitoring.'],
                    ['title' => 'Track Your Digital Growth', 'body' => 'Understand your online performance through monthly performance reports and a dedicated review meeting to identify opportunities for improvement.'],
                ],
            ],
            'growth-marketing-partner' => [
                'label' => 'Most Popular Package',
                'summary' => 'Increase your brand visibility, reach the right audience and generate consistent business enquiries through strategic content marketing and targeted advertising. Build stronger customer connections and turn your digital presence into a growth opportunity.',
                'price_note' => null,
                'questions' => [
                    'What are your primary business growth and marketing objectives?',
                    'Which products or services would you like to promote through digital marketing?',
                    'Which locations, customer segments or demographics do you want to target?',
                    'Are you currently running Meta Ads or any other paid advertising campaigns?',
                    'What monthly advertising budget are you planning to allocate separately?',
                    'What is your preferred method of receiving customer enquiries: WhatsApp, phone calls, website forms or landing pages?',
                    'Do you have an existing sales team to handle and follow up with generated leads?',
                    'What results would you consider successful from your digital marketing campaigns?',
                ],
                'reasons' => [
                    ['title' => 'Strategic Marketing Approach', 'body' => 'Build a stronger digital presence with detailed business and competitor analysis, supported by a monthly digital marketing strategy designed around your business goals.'],
                    ['title' => 'Consistent Brand Visibility', 'body' => 'Keep your brand active and engaging with 12 social media creatives, 4 professionally edited reels and 15–20 social media stories every month.'],
                    ['title' => 'Targeted Advertising Campaigns', 'body' => 'Reach potential customers through professionally managed Meta Ads campaigns, audience research and targeted marketing strategies with up to 2 active campaign objectives.'],
                    ['title' => 'Focus on Lead Generation', 'body' => 'Develop a structured lead generation strategy to attract potential customers, track enquiries and understand which marketing channels bring business opportunities.'],
                    ['title' => 'Performance Tracking and Optimisation', 'body' => 'Monitor campaign effectiveness, cost per lead and enquiry sources through a monthly performance dashboard and dedicated growth review meeting.'],
                ],
            ],
            'performance-growth-partner' => [
                'label' => 'Performance Package',
                'summary' => 'Accelerate your business growth with a comprehensive digital marketing strategy focused on customer acquisition, targeted advertising and measurable performance. Strengthen your brand across multiple platforms, attract quality leads and optimise your marketing efforts for better business results.',
                'price_note' => null,
                'questions' => [
                    'What are your current customer acquisition goals and monthly business targets?',
                    'Which marketing channels are you currently using to generate leads and sales?',
                    'Are you currently using Meta Ads, Google Ads or both?',
                    'What monthly advertising investment are you planning for paid campaigns?',
                    'Which geographical locations and customer segments do you want to target?',
                    'Do you have an existing website, landing pages and conversion tracking setup?',
                    'Which keywords or search terms would you like your business to rank for on Google?',
                    'How do you currently track, manage and evaluate the quality of incoming leads?',
                ],
                'reasons' => [
                    ['title' => 'Comprehensive Digital Marketing Strategy', 'body' => 'Strengthen your marketing efforts with a complete digital marketing strategy backed by detailed market research and competitor analysis to identify growth opportunities.'],
                    ['title' => 'Multi-Platform Brand Visibility', 'body' => 'Expand your brand reach across Facebook, Instagram and LinkedIn with 16 social media creatives, 6 professionally edited reels and 20–25 social media stories every month.'],
                    ['title' => 'Advanced Advertising Campaigns', 'body' => 'Reach potential customers through professionally managed Meta Ads and Google Ads campaigns, supported by audience research, targeting and up to 4 active campaign objectives.'],
                    ['title' => 'Lead Generation and Search Visibility', 'body' => 'Build effective lead generation funnels while improving your online discoverability through local SEO optimisation, keyword research and on-page SEO.'],
                    ['title' => 'Performance Monitoring and Growth Optimisation', 'body' => 'Track lead quality, conversion performance and marketing effectiveness through biweekly updates, monthly performance analysis and a dedicated account manager.'],
                ],
            ],
            'strategic-growth-partner' => [
                'label' => 'Premium Package',
                'summary' => 'Transform your business with an integrated marketing, technology and business growth partnership. Combine advanced digital marketing, automation, lead management and performance analytics to strengthen customer acquisition, improve conversions and build a scalable growth ecosystem.',
                'price_note' => '₹90,000 a month onwards, inclusive of all taxes.',
                'questions' => [
                    'What are your long-term business goals and digital growth expectations?',
                    'Which areas of your business do you want to improve: marketing, customer acquisition, lead management, retention or automation?',
                    'Do you currently use a CRM or any customer management software?',
                    'Are you looking to integrate WhatsApp automation and automated lead follow-up workflows?',
                    'Do you have an existing website, customer database and digital infrastructure?',
                    'What are your monthly marketing and advertising investment plans?',
                    'Which business processes would you like to automate using technology?',
                    'What key business metrics would you like to monitor through a centralised performance dashboard?',
                ],
                'reasons' => [
                    ['title' => 'Complete Business Growth Strategy', 'body' => 'Build a comprehensive digital growth roadmap supported by detailed market research and competitor intelligence to align your marketing activities with long-term business objectives.'],
                    ['title' => 'Premium Multi-Platform Brand Presence', 'body' => 'Strengthen your brand visibility across Facebook, Instagram and LinkedIn with 20 social media creatives, 8–10 professionally edited reels and 25–30 social media stories every month.'],
                    ['title' => 'Integrated Marketing and Advertising', 'body' => 'Accelerate customer acquisition through professionally managed Meta and Google Ads campaigns, advanced SEO strategies and up to 6 active campaign objectives.'],
                    ['title' => 'Smart Lead Management and Automation', 'body' => 'Streamline your customer acquisition process with CRM integration, structured lead management workflows, WhatsApp enquiry automation setup and marketing automation planning.'],
                    ['title' => 'Advanced Performance and ROI Tracking', 'body' => 'Make informed business decisions with advanced performance dashboards, customer acquisition analysis, conversion tracking, marketing ROI analysis, weekly performance monitoring and quarterly strategic growth planning.'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{summary: string, questions: list<array{prompt: string, help: ?string, required: bool}>}>
     */
    private function previous(): array
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
                'summary' => 'Build a professional and consistent digital presence.',
                'questions' => [
                    ...$shared,
                    ['prompt' => 'What is your Google Business Profile link?', 'help' => 'Leave this blank if you do not have a profile yet.', 'required' => false],
                ],
            ],
            'growth-marketing-partner' => [
                'summary' => 'Increase brand visibility and generate consistent business enquiries.',
                'questions' => [...$shared, ...$growth],
            ],
            'performance-growth-partner' => [
                'summary' => 'Improve customer acquisition and marketing performance.',
                'questions' => [...$shared, ...$growth, ...$performance],
            ],
            'strategic-growth-partner' => [
                'summary' => 'An integrated marketing, technology and business growth partnership, from ₹90,000 a month.',
                'questions' => [...$shared, ...$growth, ...$performance, ...$strategic],
            ],
        ];
    }
};
