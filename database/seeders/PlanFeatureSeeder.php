<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basic = Plan::where('name', 'Basic')->first();
        $professional = Plan::where('name', 'Professional')->first();
        $enterprise = Plan::where('name', 'Enterprise')->first();

        $features = Feature::pluck('id', 'key');

        $basic->features()->sync([
            $features['name'],
            $features['address'],
            $features['phone'],
            $features['export_csv'],
        ]);

        $professional->features()->sync([
            $features['name'],
            $features['address'],
            $features['phone'],
            $features['website'],
            $features['category'],
            $features['rating'],
            $features['export_csv'],
            $features['export_excel'],
        ]);

        $enterprise->features()->sync([
            $features['name'],
            $features['address'],
            $features['phone'],
            $features['website'],
            $features['category'],
            $features['rating'],
            $features['latitude'],
            $features['longitude'],
            $features['export_csv'],
            $features['export_excel'],
        ]);
    }
}
