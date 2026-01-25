@component('mail::message')
    # ¡Buenas noticias, Dr. {{ $name }}!

    Nos alegra informarle que el entorno digital para **{{ $company }}** ha sido configurado exitosamente.

    Ya puede acceder a su plataforma médica y comenzar a gestionar sus expedientes.

    @component('mail::button', ['url' => $url])
        Ir al Panel de Control
    @endcomponent

    ### Información de su entorno:
    * **Acceso directo:** [{{ $domain }}]({{ $url }})
    * **Usuario:** Su correo electrónico registrado.

    Atentamente,<br>
    El equipo de {{ config('app.name') }}
@endcomponent
