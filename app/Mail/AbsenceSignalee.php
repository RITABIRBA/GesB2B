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
            subject: 'Absence signalee — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.absence-signalee',
        );
    }
}