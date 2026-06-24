<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use App\Models\User;

class NouvellePreinscriptionCdd extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public User        $cdd,
        public string      $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle préinscription à valider — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouvelle-preinscription-cdd',
        );
    }
}