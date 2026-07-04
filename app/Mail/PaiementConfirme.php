<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Paiement;
use App\Models\Participant;

class PaiementConfirme extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public Paiement    $paiement,
        public string      $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Paiement confirmé — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.paiement-confirme',
            //  Passer explicitement les variables à la vue
            with: [
                'participant'  => $this->participant,
                'paiement'     => $this->paiement,
                'nomEvenement' => $this->nomEvenement,
            ],
        );
    }
}