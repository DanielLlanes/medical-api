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

    public function __construct(public Tenant $tenant)
    {
        // Usamos el nombre de la clínica en el log para mayor claridad
        \Log::info("Enviando mail de Entorno Médico Listo a: {$this->tenant->email} (Clínica: {$this->tenant->company})");
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->tenant->email,
            // Cambiamos $tenant->name por $tenant->company para el asunto
            subject: "🚀 Tu clínica '{$this->tenant->company}' ya está operativa",
        );
    }

    public function content(): Content
    {
        // Detectamos el protocolo (http o https) basado en el entorno
        $protocol = app()->environment('local') ? 'http://' : 'https://';
        $loginUrl = $protocol . $this->tenant->domain . '/login';

        return new Content(
            markdown: 'emails.landlord.tenants.tenant-database-ready',
            with: [
                'url' => $loginUrl,
                'name' => $this->tenant->name,
                'company' => $this->tenant->company, // Agregamos la compañía a la vista
                'domain' => $this->tenant->domain,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
