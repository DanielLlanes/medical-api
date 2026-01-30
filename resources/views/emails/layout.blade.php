<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body
    style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #718096; margin: 0; padding: 0; width: 100% !important;">
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="background-color: #edf2f7; width: 100%;">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="header" style="padding: 25px 0; text-align: center;">
                            <a href="{{ config('custom.app_url') }}"
                                style="color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
                                {{ config('app.name') }}
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="body" width="100%" style="background-color: #edf2f7; width: 100%;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                style="background-color: #ffffff; margin: 0 auto; width: 570px;">
                                <tr>
                                    <td class="content-cell" style="padding: 32px;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0"
                                style="margin: 0 auto; text-align: center; width: 570px;">
                                <tr>
                                    <td class="content-cell" align="center" style="padding: 32px;">
                                        <p style="color: #b0adc5; font-size: 12px;">
                                            © {{ date('Y') }} {{ config('app.name') }}. All rights
                                            reserved.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
