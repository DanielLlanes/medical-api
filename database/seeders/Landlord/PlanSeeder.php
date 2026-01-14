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
        ['name' => 'Básico', 'slug' => 'basico', 'price' => 29.99, 'is_active' => true],
        ['name' => 'Premium', 'slug' => 'premium', 'price' => 59.99, 'is_active' => true],
        ['name' => 'Empresarial', 'slug' => 'empresarial', 'price' => 99.99, 'is_active' => true],
        ['name' => 'Startup', 'slug' => 'startup', 'price' => 19.99, 'is_active' => true],
        ['name' => 'Enterprise', 'slug' => 'enterprise', 'price' => 199.99, 'is_active' => true],
        ['name' => 'Personal', 'slug' => 'personal', 'price' => 0.00, 'is_active' => true],
        ];

        foreach ($planes as $planData) {
            $plan = new Plan();
            $plan->name = $planData['name'];
            $plan->slug = $planData['slug'];
            $plan->price = $planData['price'];
            $plan->is_active = $planData['is_active'];
            $plan->code = 'PLN-' . strtoupper(Str::random(18));
            $plan->save();
        }
    }
}
