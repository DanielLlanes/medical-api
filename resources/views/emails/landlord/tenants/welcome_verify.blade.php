@extends('emails.layout')

@section('content')
    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;">
        ¡Hola, {{ $name }}!
    </h1>

    @if ($type === 'welcome')
        <h2 style="color: #2d3748; font-size: 16px; font-weight: bold;">¡Bienvenido a la plataforma!</h2>
        <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
            Gracias por registrar tu clínica <strong>{{ $tenant->company }}</strong>. Estamos muy emocionados de tenerte a
            bordo.
        </p>
        <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
            Para comenzar, por favor verifica tu correo electrónico:
        </p>
    @else
        <h2 style="color: #2d3748; font-size: 16px; font-weight: bold;">¡Tu entorno médico está listo!</h2>
        <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
            Hemos terminado de configurar la base de datos y los módulos para <strong>{{ $tenant->company }}</strong>.
        </p>
        <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
            Ya puedes acceder y gestionar tus <strong>expedientes</strong> aquí
        </p>
    @endif

    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0"
        style="margin: 30px auto; text-align: center; width: 100%;">
        <tr>
            <td align="center">
                <a href="{{ $url }}"
                    style="box-sizing: border-box; border-radius: 4px; color: #fff; display: inline-block; text-decoration: none; background-color: #2d3748; border: 10px solid #2d3748; padding: 0 10px; font-weight: bold;">
                    {{ $type === 'welcome' ? 'Verificar mi cuenta' : 'Acceder a mi Clínica' }}
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 12px; line-height: 1.5em; border-top: 1px solid #e8e5ef; padding-top: 20px; color: #718096;">
        Si tienes problemas con el botón, copia y pega esta dirección en tu navegador:<br>
        <a href="{{ $url }}" style="color: #3869d4; word-break: break-all;">{{ $url }}</a>
    </p>

    <p style="font-size: 16px; line-height: 1.5em; margin-top: 15px; text-align: left;">
        Atentamente,<br>
        El equipo de <strong>{{ config('app.name') }}</strong> [cite: 2026-01-29]
    </p>
@endsection
