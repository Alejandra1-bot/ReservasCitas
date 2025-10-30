<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaConfirmacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $citaData;

    /**
     * Create a new message instance.
     */
    public function __construct($citaData)
    {
        $this->citaData = $citaData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $tipo = $this->citaData['tipo'] ?? null;

        $subject = match($tipo) {
            'cambio_estado' => 'Actualización de Estado de Cita Médica - MediCare',
            'cambio_detalles' => 'Actualización de Detalles de Cita Médica - MediCare',
            default => 'Confirmación de Cita Médica - MediCare',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cita_confirmacion',
            with: [
                'citaData' => $this->citaData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
