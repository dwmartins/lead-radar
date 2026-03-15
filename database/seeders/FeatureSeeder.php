<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::insert([
            [
                'name' => 'Nome',
                'key' => 'name',
                'google_field' => 'displayName'
            ],
            [
                'name' => 'Endereço',
                'key' => 'address',
                'google_field' => 'formattedAddress'
            ],
            [
                'name' => 'Telefone',
                'key' => 'phone',
                'google_field' => 'nationalPhoneNumber'
            ],
            [
                'name' => 'Website',
                'key' => 'website',
                'google_field' => 'websiteUri'
            ],
            [
                'name' => 'Categoria',
                'key' => 'category',
                'google_field' => 'primaryType'
            ],
            [
                'name' => 'Rating',
                'key' => 'rating',
                'google_field' => 'rating'
            ],
            [
                'name' => 'Latitude',
                'key' => 'latitude',
                'google_field' => 'location.latitude'
            ],
            [
                'name' => 'Longitude',
                'key' => 'longitude',
                'google_field' => 'location.longitude'
            ],
        ]);
    }
}
