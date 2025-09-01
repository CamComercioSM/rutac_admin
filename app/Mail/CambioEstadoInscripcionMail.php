<?php

namespace App\Mail;

use App\Models\Inscripciones\ConvocatoriaInscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CambioEstadoInscripcionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inscripcion;
    public $unidadProductiva;

    /**
     * Create a new message instance.
     */
    public function __construct(ConvocatoriaInscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
        $this->unidadProductiva = $inscripcion->unidadProductiva;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $estado = $this->inscripcion->estado->inscripcionEstadoNOMBRE ?? 'Estado Actualizado';
        $unidad = $this->unidadProductiva->business_name ?? 'Unidad Productiva';
        
        return new Envelope(
            subject: "Cambio de Estado - Inscripción: {$unidad}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.cambio-estado-inscripcion',
            with: [
                'inscripcion' => $this->inscripcion,
                'unidadProductiva' => $this->unidadProductiva,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Si hay un archivo adjunto en la inscripción, lo incluimos
        if ($this->inscripcion->archivo && Storage::disk('public')->exists($this->inscripcion->archivo)) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->inscripcion->archivo)
                ->as('archivo_inscripcion_' . $this->inscripcion->inscripcion_id . '.' . pathinfo($this->inscripcion->archivo, PATHINFO_EXTENSION))
                ->withMime(Storage::disk('public')->mimeType($this->inscripcion->archivo));
        }

        return $attachments;
    }
}
