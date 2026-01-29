<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // ── CATEGORÍA: VENTA (Para el Pricing Page) ──
            [
                'question' => '¿Cuánto tiempo dura el periodo de prueba (trial)?',
                'answer' => 'Ofrecemos 14 días de prueba completamente gratis en el plan que elijas, sin necesidad de tarjeta de crédito. Puedes operar con todas las funcionalidades activas durante este periodo.',
                'category' => 'venta',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => '¿Qué pasa si contrato mi plan antes de que terminen los 14 días?',
                'answer' => 'Respetamos tu tiempo. Si decides contratar en el día 5, el cobro se procesará, pero tu periodo pagado comenzará a contar hasta que finalicen tus 14 días de prueba originales.',
                'category' => 'venta',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => '¿Puedo agregar más médicos o usuarios después? ¿Cómo se factura?',
                'answer' => 'Sí, puedes comprar "sillas" (usuarios extra) en cualquier momento. El costo se prorratea automáticamente por los días restantes de tu mes y se verá reflejado en tu próxima factura.',
                'category' => 'venta',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => '¿Cuál es el límite de almacenamiento?',
                'answer' => 'Cada plan incluye una base de almacenamiento segura en la nube (S3). Si excedes el límite de tu plan por un alto volumen de estudios médicos, podrás seguir subiendo archivos por una tarifa mínima por cada GB adicional.',
                'category' => 'venta',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => '¿Hay descuento por pago anual?',
                'answer' => 'Sí, al elegir el pago anual obtienes un ahorro equivalente a 2 meses gratis (aprox. 17% de descuento) comparado con el pago mensual.',
                'category' => 'venta',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => '¿Los precios incluyen IVA?',
                'answer' => 'Los precios mostrados son netos. Al ser un servicio digital en México, se aplica el 16% de IVA y emitimos factura CFDI 4.0 deducible automáticamente.',
                'category' => 'venta',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => '¿Puedo cancelar mi suscripción cuando quiera?',
                'answer' => 'Sí, puedes cancelar desde tu panel de administración sin penalizaciones. Mantendrás acceso a tus datos hasta el final del periodo que ya hayas pagado.',
                'category' => 'venta',
                'order' => 7,
                'is_active' => true,
            ],

            // ── CATEGORÍA: OPERATIVA (Para el Help Center / Página Aparte) ──
            [
                'question' => '¿La información de los expedientes me pertenece?',
                'answer' => 'Totalmente. La información clínica es propiedad del médico o institución. Puedes exportar tu base de datos y expedientes en formatos estándares en cualquier momento.',
                'category' => 'operativa',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => '¿Cómo se protegen los datos de salud de mis pacientes?',
                'answer' => 'Cumplimos con la NOM-024-SSA3-2012. Los datos están cifrados con AES-256 y alojados en la nube con certificaciones de grado médico para garantizar la máxima privacidad.',
                'category' => 'operativa',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => '¿El sistema permite compartir información con otros médicos?',
                'answer' => 'Sí, el sistema utiliza estándares HL7 y FHIR para la interoperabilidad, permitiendo compartir información clínica de forma segura entre consultorios o sucursales autorizadas.',
                'category' => 'operativa',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => '¿Puedo usar el sistema en mi iPad o Mac?',
                'answer' => 'Sí, al ser una plataforma 100% web, es compatible con cualquier dispositivo (Windows, Mac, iOS, Android) a través de navegadores modernos como Chrome o Safari.',
                'category' => 'operativa',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => '¿Incluye recordatorios por WhatsApp?',
                'answer' => 'Sí, utilizamos la API oficial de WhatsApp Business para enviar recordatorios de citas, confirmaciones y recetas digitales, mejorando la asistencia de tus pacientes.',
                'category' => 'operativa',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'question' => '¿Puedo tener mi propio dominio (ej. consulta.miapellido.com)?',
                'answer' => 'Sí, ofrecemos el servicio de dominio personalizado como un adicional para que tus pacientes accedan a través de una URL profesional y propia.',
                'category' => 'operativa',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => '¿Qué pasa si no tengo conexión a internet?',
                'answer' => 'El sistema cuenta con un modo de contingencia que permite visualizar la agenda del día y tomar notas básicas que se sincronizarán en cuanto recuperes la conexión.',
                'category' => 'operativa',
                'order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                // Buscamos por la pregunta para evitar duplicados
                ['question' => $faq['question']],
                [
                    'answer'    => $faq['answer'],
                    'category'  => $faq['category'],
                    'order'     => $faq['order'],
                    'is_active' => $faq['is_active'],
                    'code' => Faq::generateReferenceCode('FAQ'),
                ]
            );
        }
    }
}
