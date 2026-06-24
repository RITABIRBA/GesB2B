<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class RappelEvenement extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public string      $nomEvenement,
        public string      $dateEvenement,
        public string      $lieuEvenement,
        public int         $joursRestants,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->joursRestants === 1 ? 'demain' : "dans {$this->joursRestants} jours";
        return new Envelope(
            subject: "Rappel — {$this->nomEvenement} {$label} !",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rappel-evenement',
        );
    }
}