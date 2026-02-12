<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Player;
use App\Models\GameTurn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Traitement de l'inscription (Register)
     */
    public function registerControl(Request $request)
    {
        // 1. Validation des données reçues du formulaire
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'age' => 'required|integer|min:18', // Exemple: âge minimum 18 ans
            'profession' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:10|unique:users,phone', // Vérifie que le numéro est unique
        ]);

        // 2. Création du nouvel utilisateur
        $user = new User();
        $user->nom = $request->nom;
        $user->prenom = $request->prenom;
        $user->age = $request->age;
        $user->profession = $request->profession;

        // On ajoute l'indicatif +225 au numéro pour le stockage
        $user->phone = '+225' . $request->phone;

        // Note : Comme votre formulaire n'a pas de mot de passe,
        // on peut définir un mot de passe par défaut ou rendre le champ nullable en base.
        // Ici, on met un mot de passe aléatoire par sécurité.
        $password = uniqid();
        $user->password = Hash::make($password);

        $user->save();

        // 3. Connexion automatique de l'utilisateur (optionnel)
        Auth::login($user);

        // 4. Redirection vers l'accueil ou le jeu
        return redirect('/success')->with('success', 'Inscription réussie ! Bienvenue.')->with('phone', $user->phone)->with('password', $password);
    }

    /**
     * Traitement de la connexion (Login)
     */
    public function loginControl(Request $request)
    {
        $credentials = $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        // On ajoute le préfixe +225 pour correspondre au format stocké en base de données
        $credentials['phone'] = '+225' . $credentials['phone'];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'phone' => 'Les identifiants fournis sont incorrects.',
        ]);
    }

    function successControl(Request $request)
    {
        return view('roulette.success');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function saveGameResult(Request $request)
    {
        $user = Auth::user();

        // On vérifie si l'utilisateur n'a pas déjà joué pour éviter la triche
        if (!$user->played_games) {
            $user->played_games = true;
            $user->price = $request->prize;
            $user->save();
        }

        return response()->json(['success' => true]);
    }

    public function registerPlayer(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:players,telephone',
            'is_accept' => 'accepted',
        ]);

        $player = new Player();
        $player->nom = $validated['nom'];
        $player->prenom = $validated['prenom'];
        $player->telephone = $validated['telephone'];
        $player->is_accept = true;
        $player->save();

        return response()->json(['success' => true, 'player' => $player]);
    }

    public function savePlayerGameResult(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        $player = Player::find($request->player_id);

        if ($player->has_played) {
            return response()->json(['success' => false, 'message' => 'Vous avez déjà joué.', 'prize' => $player->price]);
        }

        // Détermination du lot côté serveur (compteur sur game_turns)
        $winningTurns = [21, 50, 55, 60, 65];
        $turnNumber = GameTurn::count() + 1;

        $winningMap = [
            21 => "Un contrat d’assurance MonAPPUI pour deux",
            50 => "Un contrat d’assurance MonAPPUI pour deux",
            55 => "Un dîner pour deux au restaurant LE LOF",
            60 => "Une prestation d’extension de cils de chez ELIAB",
            65 => "Un somptueux bouquet de roses nature",
        ];

        $prize = $winningMap[$turnNumber] ?? "Perdu";

        $player->has_played = true;
        $player->price = $prize;
        $player->save();

        GameTurn::create([
            'player_id' => $player->id,
            'prize' => $prize,
        ]);

        return response()->json(['success' => true, 'prize' => $prize]);
    }

    public function checkPlayerStatus(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        $player = Player::find($request->player_id);

        return response()->json([
            'has_played' => $player->has_played,
            'prize' => $player->price,
            'position' => $player->has_played ? null : (GameTurn::count() + 1),
        ]);
    }
}
