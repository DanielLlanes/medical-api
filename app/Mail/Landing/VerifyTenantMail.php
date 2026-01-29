<?php

namespace App\Mail\Landing;

use App\Models\Landlord\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Illuminate\Support\Facades\URL;

class VerifyTenantMail extends Mailable implements ShouldQueue, NotTenantAware
{
    use Queueable, SerializesModels;

    /**
     * @param Tenant $tenant
     * @param string $type ('welcome' para registro nuevo, 'activation' para cuando la DB está lista)
     */
    public function __construct(
        public Tenant $tenant,
        public string $type = 'welcome'
    ) {
        \Log::info("Enviando mail tipo [{$type}] a: " . $tenant->email);
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'welcome'    => '¡Bienvenido! Verifica tu cuenta - ' . config('app.name'),
            'activation' => 'Tu entorno médico está listo - ' . config('app.name'),
        ];

        return new Envelope(
            to: $this->tenant->email,
            subject: $subjects[$this->type] ?? $subjects['welcome'],
        );
    }

    public function content(): Content
    {
        $verificationUrl = URL::temporarySignedRoute(
            'public.tenants.verify',
            now()->addDays(30),
            ['tenant' => $this->tenant->id]
        );

        return new Content(
            markdown: 'emails.landlord.tenants.welcome_verify',
            with: [
                'url'  => $verificationUrl,
                'name' => $this->tenant->name,
                'type' => $this->type, // Pasamos el tipo a la vista
            ],
        );
    }
}
