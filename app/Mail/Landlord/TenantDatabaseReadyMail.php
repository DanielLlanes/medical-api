<?php

namespace App\Mail\Landlord;

use App\Models\Landlord\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class TenantDatabaseReadyMail extends Mailable implements ShouldQueue, NotTenantAware
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Tenant $tenant)
    {
        \Log::info('Enviando mail de Base de Datos Lista a: ' . $this->tenant->email);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->tenant->email,
            subject: "🚀 Tu clínica '{$this->tenant->name}' ya está operativa",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Construimos la URL de acceso
        $loginUrl = "http://{$this->tenant->domain}/login";

        return new Content(
            markdown: 'emails.landlord.tenants.tenant-database-ready',
            with: [
                'url' => $loginUrl,
                'name' => $this->tenant->name,
                'domain' => $this->tenant->domain,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}