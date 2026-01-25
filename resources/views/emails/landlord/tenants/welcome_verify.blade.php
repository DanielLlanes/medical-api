@component('mail::message')
# ¡Hola, {{ $name }}!

@if($type === 'welcome')
## ¡Bienvenido a la plataforma!
Gracias por registrar tu clínica **{{ $tenant->company }}**. Estamos muy emocionados de tenerte a bordo.

Para comenzar, por favor verifica tu correo electrónico:
@else
## ¡Tu entorno médico está listo!
Hemos terminado de configurar la base de datos y los módulos para **{{ $tenant->company }}**.

Ya puedes acceder y activar tu cuenta aquí:
@endif

@component('mail::button', ['url' => $url])
{{ $type === 'welcome' ? 'Verificar mi cuenta' : 'Acceder a mi Clínica' }}
@endcomponent

Si tienes problemas con el botón, copia y pega esta dirección:
[{{ $url }}]({{ $url }})

Atentamente,<br>
El equipo de {{ config('app.name') }}
@endcomponent
