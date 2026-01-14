<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Multitenancy\Models\Tenant;
use Database\Seeders\Landlord\PlanSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Tenant::checkCurrent()
           ? $this->runTenantSpecificSeeders()
           : $this->runLandlordSpecificSeeders();
    }

    public function runTenantSpecificSeeders()
    {
        // run tenant specific seeders
    }

    public function runLandlordSpecificSeeders()
    {
        // $this->call([
        //     PlanSeeder::class,
        //     // Aquí puedes agregar otros seeders del landlord en el futuro
        //     // UserLandlordSeeder::class,
        // ]);

        $this->call(PlanSeeder::class);
    }
}
