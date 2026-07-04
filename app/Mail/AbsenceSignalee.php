<?php

namespace App\Mail;

use App\Models\Participant;
use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbsenceSignalee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant  $destinataire,
        public Participant  $absent,
        public string       $dateRdv,
        public ?string      $heureDebut,
        public ?string      $heureFin,
        public ?string      $salle,
        public ?int         $table,
        public string       $nomEvenement,
        public Collection   $remplacants,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Absence signalée — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.absence-signalee',
            // ✅ Passer explicitement les variables à la vue
            with: [
                'destinataire' => $this->destinataire,
                'absent'       => $this->absent->load('entreprise'),
                'dateRdv'      => $this->dateRdv,
                'heureDebut'   => $this->heureDebut,
                'heureFin'     => $this->heureFin,
                'salle'        => $this->salle,
                'table'        => $this->table,
                'nomEvenement' => $this->nomEvenement,
                'remplacants'  => $this->remplacants,
            ],
        );
    }
}