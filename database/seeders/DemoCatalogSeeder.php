<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Nomenclature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $brandGuid = 'd0000000-0000-4000-8000-000000000001';
            Brand::upsert([
                ['guid' => $brandGuid, 'name' => 'Nord Tools', 'main_brand_guid' => $brandGuid],
            ], ['guid'], ['name']);

            Nomenclature::upsert([
                [
                    'guid' => 'd0000000-0000-4000-8000-000000000002',
                    'code' => 'DEMO-001',
                    'name' => 'Precision screwdriver',
                    'full_name' => 'Nord Tools precision screwdriver',
                    'set_number' => 'NT-100',
                    'brand_guid' => $brandGuid,
                    'price' => '490.50',
                ],
                [
                    'guid' => 'd0000000-0000-4000-8000-000000000003',
                    'code' => 'DEMO-002',
                    'name' => 'Tape measure',
                    'full_name' => 'Nord Tools tape measure, 5 m',
                    'set_number' => 'NT-200',
                    'brand_guid' => $brandGuid,
                    'price' => '320.00',
                ],
            ], ['guid'], ['code', 'name', 'full_name', 'set_number', 'brand_guid', 'price']);
        });
    }
}
