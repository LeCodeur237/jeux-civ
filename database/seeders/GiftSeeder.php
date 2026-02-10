<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gift;

class GiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gifts = [
            ['name' => 'T-shirt', 'game_name' => 'tshirt'],
            ['name' => 'Casquette', 'game_name' => 'casquette'],
            ['name' => 'Panier', 'game_name' => 'panier'],
            ['name' => 'Bloc note', 'game_name' => 'bloc_note'],
            ['name' => 'Bol', 'game_name' => 'bol'],
        ];

        foreach ($gifts as $gift) {
            Gift::firstOrCreate(['name' => $gift['name']], [
                'game_name' => $gift['game_name'],
                'image' => null // Vous pourrez ajouter les images via l'admin ou ici (ex: 'tshirt.png')
            ]);
        }
    }
}
