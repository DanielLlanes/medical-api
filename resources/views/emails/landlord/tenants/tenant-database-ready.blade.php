@component('mail::message')
    # ¡Buenas noticias, Dr. {{ $name }}!

    Nos alegra informarle que el entorno digital para **{{ $company }}** ha sido configurado exitosamente.

    Ya puede acceder a su plataforma médica y comenzar a gestionar sus expedientes de forma segura.

    @component('mail::button', ['url' => $url])
        Ir al Panel de Control
    @endcomponent

    ### Detalles de acceso:
    * **Dominio:** [{{ $domain }}]({{ $url }})
    * **Usuario:** Su correo electrónico registrado.

    @if (isset($tempPassword))
        * **Contraseña temporal:** `{{ $tempPassword }}`
    @endif

    Atentamente,<br>
    El equipo de **{{ config('app.name') }}**
@endcomponent
