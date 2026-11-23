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
        $categoryId = DB::table('categories')->where('slug', 'social-media')->value('id');
        $now = now();

        foreach ($this->packages() as $index => $package) {
            DB::table('services')->insert([
                'category_id' => $categoryId,
                'name' => $package['name'],
                'slug' => $package['slug'],
                'group_name' => null,
                'short_description' => $package['summary'],
                'description' => $package['summary'],
                'price' => $package['price'],
                'price_suffix' => null,
                'compare_price' => null,
                'billing_type' => 'monthly',
                'delivery_label' => 'Ongoing Monthly',
                'rating' => 0,
                'review_count' => 0,
                'is_deal' => false,
                'is_best_seller' => false,
                'is_listed' => true,
                'is_active' => true,
                'badge' => null,
                'discount_label' => null,
                'image' => $package['slug'].'.jpg',
                'video_url' => null,
                'sort_order' => $index + 1,
                'deal_ends_at' => null,
                'details' => json_encode(['deliverables' => $package['deliverables']]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('services')->where('slug', 'social-media-starter-package')->delete();
        DB::table('plan_items')->delete();
        DB::table('plans')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * The removed homepage plans and the Social Media Starter Package are not restored.
     */
    public function down(): void
    {
        DB::table('services')->whereIn('slug', array_column($this->packages(), 'slug'))->delete();
    }

    /**
     * @return list<array{name: string, slug: string, summary: string, price: float, deliverables: list<string>}>
     */
    private function packages(): array
    {
        return [
            [
                'name' => 'Digital Presence Partner',
                'slug' => 'digital-presence-partner',
                'summary' => 'Build a professional and consistent digital presence.',
                'price' => 10000,
                'deliverables' => [
                    'Digital presence audit',
                    'Monthly content strategy',
                    '8 social media creatives',
                    '2 professionally edited reels',
                    '12 social media stories',
                    'Monthly content calendar',
                    'Caption and hashtag strategy',
                    'Facebook and Instagram management',
                    'Google Business Profile management',
                    'Community engagement monitoring',
                    'Monthly performance report',
                    'One monthly review meeting',
                ],
            ],
            [
                'name' => 'Growth Marketing Partner',
                'slug' => 'growth-marketing-partner',
                'summary' => 'Increase brand visibility and generate consistent business enquiries.',
                'price' => 35000,
                'deliverables' => [
                    'Digital presence and competitor audit',
                    'Monthly digital marketing strategy',
                    '12 social media creatives',
                    '4 professionally edited reels',
                    '15–20 social media stories',
                    'Content calendar and campaign planning',
                    'Facebook and Instagram management',
                    'Google Business Profile management',
                    'Meta Ads campaign management',
                    'Audience research and targeting',
                    'Lead generation strategy',
                    'Up to 2 active campaign objectives',
                    'Lead tracking and source analysis',
                    'Cost per lead monitoring',
                    'Monthly performance dashboard',
                    'Monthly growth review meeting',
                ],
            ],
            [
                'name' => 'Performance Growth Partner',
                'slug' => 'performance-growth-partner',
                'summary' => 'Improve customer acquisition and marketing performance.',
                'price' => 60000,
                'deliverables' => [
                    'Complete digital marketing strategy',
                    'Competitor and market analysis',
                    '16 social media creatives',
                    '6 professionally edited reels',
                    '20–25 social media stories',
                    'Facebook, Instagram and LinkedIn management',
                    'Google Business Profile management',
                    'Meta Ads campaign management',
                    'Google Ads campaign management',
                    'Up to 4 active campaign objectives',
                    'Audience research and targeting',
                    'Lead generation funnel strategy',
                    'Local SEO optimisation',
                    'Keyword research and on-page SEO',
                    'Landing page optimisation recommendations',
                    'Lead source and quality analysis',
                    'Conversion tracking and monitoring',
                    'Monthly performance and competitor analysis',
                    'Biweekly performance updates',
                    'Dedicated account manager',
                    'Monthly strategic growth review',
                ],
            ],
            [
                'name' => 'Strategic Growth Partner',
                'slug' => 'strategic-growth-partner',
                'summary' => 'An integrated marketing, technology and business growth partnership, from ₹90,000 a month.',
                'price' => 90000,
                'deliverables' => [
                    'Comprehensive digital growth strategy',
                    'Detailed market and competitor intelligence',
                    '20 social media creatives',
                    '8–10 professionally edited reels',
                    '25–30 social media stories',
                    'Facebook, Instagram and LinkedIn management',
                    'Google Business Profile management',
                    'Meta and Google Ads management',
                    'Up to 6 active campaign objectives',
                    'Advanced SEO strategy and optimisation',
                    'Landing page and conversion optimisation',
                    'Lead generation and nurturing strategy',
                    'CRM integration and lead management workflow',
                    'WhatsApp enquiry automation setup',
                    'Marketing automation planning',
                    'Advanced business performance dashboard',
                    'Customer acquisition and conversion analysis',
                    'Marketing ROI analysis',
                    'Weekly performance monitoring',
                    'Dedicated strategic account manager',
                    'Monthly management review',
                    'Quarterly strategic growth planning',
                ],
            ],
        ];
    }
};
