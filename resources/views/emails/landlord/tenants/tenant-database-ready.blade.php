@component('mail::message')
# ¡Todo listo, {{ $name }}!

Tu plataforma médica ya está configurada y lista para ser utilizada. Hemos creado tu instancia privada y la base de datos correspondiente de manera exitosa.

**Detalles de acceso:**
* **URL de acceso:** [{{ $domain }}]({{ $url }})
* **Estado:** Operativo

@component('mail::button', ['url' => $url])
Acceder a mi Panel
@endcomponent

Si tienes alguna duda sobre la configuración inicial o necesitas ayuda para dar tus primeros pasos, nuestro equipo de soporte está a tu disposición.

Saludos,<br>
El equipo de {{ config('app.name') }}
@endcomponent