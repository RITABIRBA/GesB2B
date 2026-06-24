<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Traducteur;
use App\Models\RendezVous;

class TraducteurAssigne extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Traducteur $traducteur,
        public RendezVous $rendezVous,
        public string     $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ' Nouveau rendez-vous à interpréter — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.traducteur-assigne',
        );
    }
}