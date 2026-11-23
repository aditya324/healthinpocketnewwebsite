<?php

namespace App\Support;

class WeeklyReportFields
{
    /**
     * @return array<string, string>
     */
    public static function facebookMetrics(): array
    {
        return [
            'views' => 'Views',
            'three_second_views' => '3-second views',
            'content_interactions' => 'Content interactions',
            'watch_time' => 'Watch time',
            'viewers' => 'Viewers',
            'organic_views' => 'Organic views',
            'ad_views' => 'Ad views',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function facebookContent(): array
    {
        return [
            'reels' => 'Reels',
            'stories' => 'Stories',
            'photos' => 'Photos',
            'total' => 'Total content',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function facebookViewsByFormat(): array
    {
        return [
            'reels' => 'Reels',
            'photos' => 'Photos',
            'links' => 'Links',
            'stories' => 'Stories',
            'multi_photo' => 'Multi-photo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function facebookInteractionsByFormat(): array
    {
        return [
            'photos' => 'Photos',
            'reels' => 'Reels',
            'stories' => 'Stories',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function instagramMetrics(): array
    {
        return [
            'total_views' => 'Total views',
            'instagram_views' => 'Instagram views',
            'facebook_views' => 'Facebook views',
            'reach' => 'Reach',
            'content_interactions' => 'Content interactions',
            'organic_views' => 'Organic views',
            'ad_views' => 'Ad views',
            'posts_published' => 'Posts published',
            'stories_published' => 'Stories published',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function youtubeMetrics(): array
    {
        return [
            'views' => 'Views',
            'engaged_views' => 'Engaged views',
            'unique_viewers' => 'Unique viewers',
            'new_viewers' => 'New viewers',
            'stayed_to_watch' => 'Stayed to watch',
            'likes' => 'Likes',
            'post_likes' => 'Post likes',
            'subscribers_gained' => 'Subscribers gained',
            'subscribers_lost' => 'Subscribers lost',
            'watch_time' => 'Watch time',
            'subscribers' => 'Subscribers',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function narrativeSections(): array
    {
        return [
            'working' => 'What is working?',
            'not_working' => 'What is not working?',
            'comparison' => 'Comparison with last week',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function narrativePlatforms(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
        ];
    }

    /**
     * @param  array<string, string>  $fields
     * @return array<string, array<int, string>>
     */
    public static function metricRules(string $prefix, array $fields, bool $changes = false): array
    {
        $rules = [];

        foreach (array_keys($fields) as $key) {
            $rules[$prefix.'.'.$key] = ['nullable', 'string', 'max:80'];

            if ($changes) {
                $rules[$prefix.'.'.$key.'_change'] = ['nullable', 'string', 'max:40'];
            }
        }

        return $rules;
    }
}
