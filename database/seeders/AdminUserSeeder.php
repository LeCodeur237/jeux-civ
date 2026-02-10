<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phone = '+2250102030405';
        $password = 'password';

        // Vérifie si l'administrateur existe déjà pour ne pas le dupliquer
        if (!User::where('phone', $phone)->exists()) {
            User::create([
                'nom'        => 'Admin',
                'prenom'     => 'Super',
                'age'        => 30,
                'profession' => 'Administrateur',
                'phone'      => $phone, // Numéro de téléphone de l'admin
                'password'   => Hash::make($password), // Mot de passe de l'admin, changez-le !
                'is_admin'   => true,
                'played_games' => true,
            ]);

            $this->command->info("Admin créé avec succès !");
            $this->command->info("Téléphone : $phone");
            $this->command->info("Mot de passe : $password");
        }
    }
}
