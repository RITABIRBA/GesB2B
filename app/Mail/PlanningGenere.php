<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PlanningGenere extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $destinataire,
        public string      $nomEvenement,
        public string      $dateEvenement,
        public Collection  $rendezVous,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre planning B2B — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.planning-genere',
            // ✅ Passer explicitement les variables à la vue
            with: [
                'destinataire' => $this->destinataire,
                'nomEvenement' => $this->nomEvenement,
                'dateEvenement'=> $this->dateEvenement,
                'rendezVous'   => $this->rendezVous,
            ],
        );
    }
}