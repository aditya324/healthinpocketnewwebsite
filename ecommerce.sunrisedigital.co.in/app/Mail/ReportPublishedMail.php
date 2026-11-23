<?php

namespace App\Mail;

use App\Models\WeeklyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReportPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WeeklyReport $report) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your '.$this->report->kindLabel().' report for '.$this->report->currentPeriodLabel().' is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.report-published',
        );
    }

    public static function deliver(WeeklyReport $report): bool
    {
        $report->loadMissing('client');
        $email = $report->client?->email;

        if (! is_string($email) || $email === '') {
            return false;
        }

        try {
            Mail::to($email)->send(new self($report));

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
