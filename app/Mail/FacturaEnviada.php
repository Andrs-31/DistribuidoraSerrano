<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class FacturaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $pdfContent;
    public $filename;

    public function __construct($factura, $pdfContent, $filename)
    {
        $this->factura = $factura;
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
    }

    /**
     * Asunto del correo
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura N° ' . str_pad($this->factura->num_factura, 6, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Vista del correo
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.factura', // IMPORTANTE: crea esta vista
        );
    }

    /**
     * Adjuntos
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfContent,
                $this->filename
            )->withMime('application/pdf'),
        ];
    }
}