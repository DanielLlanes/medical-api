<?php

namespace App\Mail\Landlord;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class VerifyTenantMail extends Mailable implements ShouldQueue, NotTenantAware
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Tenant $tenant)
    {
        \Log::info('Enviando mail a: ' . $tenant);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->tenant->email,
            subject: 'Verifica tu cuenta - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
       $verificationUrl = URL::temporarySignedRoute(
            'tenant.verify',
            now()->addDays(30), // Cambiado a 30 días
            ['tenant' => $this->tenant->id]
        );

        return new Content(
            /**
             * OPCIÓN 1: Usar Markdown (Plantilla nativa de Laravel)
             */
            markdown: 'emails.landlord.tenants.welcome_verify',

            /**
             * OPCIÓN 2: Usar Vista HTML personalizada (Plantilla propia)
             * Para usarla, comenta la línea de 'markdown' arriba y descomenta la de 'view' abajo.
             */
            // view: 'emails.landlord.tenants.custom_welcome_verify',

            with: [
                'url' => $verificationUrl,
                'name' => $this->tenant->name,
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