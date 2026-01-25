<?php

namespace Database\Seeders\Landlord;


use Illuminate\Support\Str;
use App\Models\Landlord\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $planes = [
            [
                'name' => 'Básico',
                'slug' => 'plan_basico_01',
                'price' => 199.00,
                'limit_users' => 1,
                'has_custom_domain' => false,
                'trial_days' => 14,
                'is_recommended' => false,
                'features' => [
                    'Expediente NOM-024 Básico',
                    'Agenda y Citas (hasta 200/mes)',
                    'Recordatorios por Email',
                    'Soporte por Chat'
                ]
            ],
            [
                'name' => 'Consultorio',
                'slug' => 'plan_consultorio_01',
                'price' => 499.00,
                'limit_users' => 3,
                'has_custom_domain' => false,
                'trial_days' => 14,
                'is_recommended' => false,
                'features' => [
                    'Expediente NOM-024 Completo',
                    'Agenda y Citas Ilimitadas',
                    'Recordatorios SMS + Email',
                    'Telemedicina Básica',
                    'Firma Electrónica Simple'
                ]
            ],
            [
                'name' => 'Clínica Pro',
                'slug' => 'plan_clinica_pro_01',
                'price' => 1299.00,
                'limit_users' => 10,
                'has_custom_domain' => true,
                'trial_days' => 14,
                'is_recommended' => true,
                'features' => [
                    'HL7 / FHIR Integrado',
                    'Facturación CFDI 4.0 Completa',
                    'Telemedicina Avanzada',
                    'Control de Inventario y Farmacia',
                    'Reportes Analíticos + Dashboards'
                ]
            ],
            [
                'name' => 'Hospital',
                'slug' => 'plan_hospital_01',
                'price' => 2499.00,
                'limit_users' => 999, // Ilimitados
                'has_custom_domain' => true,
                'trial_days' => 14,
                'is_recommended' => false,
                'features' => [
                    'Médicos y Usuarios Ilimitados',
                    'API de Integración Avanzada',
                    'Gestión Multi-Sucursal',
                    'Integración con Dispositivos Médicos',
                    'Soporte 24/7 Dedicado'
                ]
            ],
        ];

        foreach ($planes as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                [
                    'name' => $planData['name'],
                    'price' => $planData['price'],
                    'limit_users' => $planData['limit_users'],
                    'has_custom_domain' => $planData['has_custom_domain'],
                    'is_recommended' => $planData['is_recommended'],
                    'trial_days' => $planData['trial_days'],
                    'features' => $planData['features'],
                    'is_active' => true,
                    'status' => 'active',
                    'code' => 'PLN-' . strtoupper(Str::random(12)),
                ]
            );
        }
    }
}
