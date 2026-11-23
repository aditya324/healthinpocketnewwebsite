<?php

namespace App\Support;

use App\Models\Service;

class WhatsApp
{
    public static function digits(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) config('services.whatsapp.number')) ?? '';

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 10) {
            return '91'.$digits;
        }

        return $digits;
    }

    public static function url(?string $message = null): ?string
    {
        $digits = self::digits();

        if ($digits === null) {
            return null;
        }

        $url = 'https://wa.me/'.$digits;
        $message = trim((string) $message);

        if ($message !== '') {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    public static function enquiryMessage(?Service $service = null): string
    {
        if ($service === null) {
            return 'Hi Sunrise, I would like to know more about your digital services.';
        }

        $price = $service->money($service->price).($service->billingLabel() ?? '');

        return "Hi Sunrise, I am interested in {$service->name} ({$price}). Please share more details.\n".route('services.show', $service);
    }

    public static function enquiryUrl(?Service $service = null): ?string
    {
        return self::url(self::enquiryMessage($service));
    }
}
