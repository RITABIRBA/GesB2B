<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreinscriptionValidee extends Mailable
{
    use Queueable, SerializesModels;

    public Participant $participant;
    public ?string $password;

    public function __construct(Participant $participant, ?string $password = null)
    {
        $this->participant = $participant;
        $this->password    = $password;
    }

    public function build()
    {
        return $this->subject('✅ Votre préinscription a été validée — Business Forum CCI-BF')
            ->view('emails.preinscription-validee')
            ->with([
                'participant' => $this->participant,
                'password'    => $this->password,
            ]);
    }
}