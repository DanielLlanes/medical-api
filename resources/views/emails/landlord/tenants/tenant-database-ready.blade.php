@extends('emails.layout')

@section('content')
    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;">
        ¡Buenas noticias, Dr. {{ $name }}!
    </h1>

    <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
        Nos alegra informarle que el entorno digital para <strong>{{ $company }}</strong> ha sido configurado
        exitosamente.
    </p>

    <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
        Ya puede acceder a su plataforma médica y comenzar a gestionar sus <strong>expedientes</strong> de forma segura
    </p>

    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0"
        style="margin: 30px auto; text-align: center; width: 100%;">
        <tr>
            <td align="center">
                <a href="{{ $url }}"
                    style="border-radius: 4px; color: #fff; display: inline-block; text-decoration: none; background-color: #2d3748; border: 10px solid #2d3748; padding: 0 10px;">
                    Ir al Panel de Control
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size: 14px; border-top: 1px solid #e8e5ef; padding-top: 25px;">
        Si tienes problemas con el botón, copia y pega esta URL: <br>
        <a href="{{ $url }}" style="color: #3869d4; word-break: break-all;">{{ $url }}</a>
    </p>
@endsection
