<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\GameTurn;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function normalizeIvorianPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '225') && strlen($digits) === 13) {
            $digits = substr($digits, 3);
        }

        if (!preg_match('/^(01|05|07)[0-9]{8}$/', $digits)) {
            throw ValidationException::withMessages([
                'phone' => 'Le numéro doit être un numéro ivoirien valide à 10 chiffres.',
            ]);
        }

        return '+225' . $digits;
    }

    private function rouletteSegments()
    {
        $segments = Gift::query()
            ->get(['name', 'image'])
            ->map(function ($gift) {
                return [
                    'name' => $gift->name,
                    'image' => $gift->image ? asset('images/' . $gift->image) : null,
                ];
            })
            ->values();

        if ($segments->isEmpty()) {
            $segments = collect([
                ['name' => 'T-shirt', 'image' => null],
                ['name' => 'Casquette', 'image' => null],
                ['name' => 'Panier', 'image' => null],
                ['name' => 'Bloc note', 'image' => null],
                ['name' => 'Bol', 'image' => null],
            ]);
        }

        return $segments->push(['name' => 'Perdu', 'image' => null]);
    }

    /**
     * Traitement de l'inscription (Register)
     */
    public function registerControl(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'age' => 'required|integer|min:18',
            'profession' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(?:\+225)?(?:01|05|07)[0-9]{8}$/'],
            'firebase_verified' => 'accepted',
            'firebase_phone' => ['required', 'string'],
        ]);

        $plainPassword = Str::random(12);
        $normalizedPhone = $this->normalizeIvorianPhone($validated['phone']);
        $firebasePhone = $this->normalizeIvorianPhone($validated['firebase_phone']);

        if ($firebasePhone !== $normalizedPhone) {
            throw ValidationException::withMessages([
                'phone' => 'Le numéro vérifié par Firebase ne correspond pas au numéro saisi.',
            ]);
        }

        if (User::where('phone', $normalizedPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'Ce numéro est déjà utilisé.',
            ]);
        }

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'age' => $validated['age'],
            'profession' => $validated['profession'],
            'phone' => $normalizedPhone,
            'password' => $plainPassword,
        ]);

        Auth::login($user);

        return redirect('/success')
            ->with('success', 'Inscription réussie ! Bienvenue.')
            ->with('phone', $user->phone)
            ->with('password', $plainPassword);
    }

    /**
     * Traitement de la connexion (Login)
     */
    public function loginControl(Request $request)
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(?:\+225)?(?:01|05|07)[0-9]{8}$/'],
            'password' => 'required',
        ]);

        $credentials['phone'] = $this->normalizeIvorianPhone($credentials['phone']);

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

    public function successControl(Request $request)
    {
        return view('roulette.success');
    }

    public function rouletteResultControl(Request $request)
    {
        $user = Auth::user();

        return view('roulette.result', [
            'hasPlayed' => (bool) $user->played_games,
            'prize' => $user->price,
            'isWinner' => $user->played_games && $user->price && $user->price !== 'Perdu',
        ]);
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

        if ($user->played_games) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà joué.',
                'prize' => $user->price,
            ], 409);
        }

        $winningSegment = $this->rouletteSegments()->random();

        $user->forceFill([
            'played_games' => true,
            'price' => $winningSegment['name'],
        ])->save();

        return response()->json([
            'success' => true,
            'prize' => $winningSegment['name'],
            'image' => $winningSegment['image'],
            'redirect' => route('roulette.result'),
        ]);
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

        return DB::transaction(function () use ($request) {
            $player = Player::whereKey($request->player_id)->lockForUpdate()->firstOrFail();

            if ($player->has_played) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà joué.',
                    'prize' => $player->price,
                ]);
            }

            $turnNumber = GameTurn::lockForUpdate()->count() + 1;

            $winningMap = [
                21 => "Un contrat d’assurance MonAPPUI pour deux",
                50 => "Un contrat d’assurance MonAPPUI pour deux",
                55 => "Un dîner pour deux au restaurant LE LOF",
                60 => "Une prestation d’extension de cils de chez ELIAB",
                65 => "Un somptueux bouquet de roses nature",
                75 => "Un contrat d’assurance MonAPPUI pour deux",
                80 => "Un dîner pour deux au restaurant LE LOF",
            ];

            $prize = $winningMap[$turnNumber] ?? 'Perdu';

            $player->forceFill([
                'has_played' => true,
                'price' => $prize,
            ])->save();

            GameTurn::create([
                'player_id' => $player->id,
                'prize' => $prize,
            ]);

            return response()->json(['success' => true, 'prize' => $prize]);
        });
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
