@component('mail::message')
# ¡Bienvenido a {{ config('app.name') }} 🎉

Hola **{{ $name }}**,  
gracias por registrarte en nuestro sistema.

Estamos muy contentos de tenerte con nosotros.  
Tu cuenta ha sido creada correctamente y ya estamos preparando todo para que empieces a usar la plataforma.

---

## 🚀 ¿Qué sigue ahora?

Para comenzar tu **periodo de prueba**, necesitamos que confirmes tu correo electrónico.

@component('mail::button', ['url' => $url])
Activar mi cuenta
@endcomponent

> ⚠️ **Nota importante:** Tienes **30 días** para activar tu cuenta. Si no realizas la confirmación en este periodo, los datos del registro y tu base de datos provisional serán eliminados automáticamente por seguridad.

---

## 🧪 Periodo de prueba
Una vez activada tu cuenta:
- Podrás acceder a todas las funcionalidades
- Tu periodo de prueba comenzará automáticamente
- No se te cobrará nada durante este tiempo

---

Si no realizaste este registro, puedes ignorar este correo con tranquilidad.

Gracias por confiar en nosotros,  
**El equipo de {{ config('app.name') }}**

@endcomponent