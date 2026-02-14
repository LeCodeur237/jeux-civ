<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Gift;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.users');
    }

    // Gestion des utilisateurs
    public function users()
    {
        $players = Player::orderBy('created_at', 'desc')->paginate(10, ['*'], 'players_page');
        $playedCount = Player::where('has_played', true)->count();
        return view('admin.users', compact('players', 'playedCount'));
    }

    // Export CSV des joueurs (table players)
    public function exportUsersCsv()
    {
        $players = Player::orderBy('created_at', 'desc')->get();
        $csvFileName = 'players_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($players) {
            $file = fopen('php://output', 'w');
            // Ajout du BOM pour compatibilite Excel (accents)
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Prenom', 'Telephone', 'RGPD', 'Statut', 'Lot', 'Date Inscription'], ';' );

            foreach ($players as $player) {
                $status = 'Pas encore joue';
                if ($player->has_played) {
                    $status = ($player->price && $player->price !== 'Perdu') ? 'Gagne' : 'Perdu';
                }

                fputcsv($file, [
                    $player->id,
                    $player->nom,
                    $player->prenom,
                    $player->telephone,
                    $player->is_accept ? 'Oui' : 'Non',
                    $status,
                    $player->price ?? '',
                    $player->created_at->format('d/m/Y H:i')
                ], ';' );
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Gestion des cadeaux
    public function gifts()
    {
        $gifts = Gift::all();
        return view('admin.gifts', compact('gifts'));
    }

    public function storeGift(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $input = $request->all();

        if ($image = $request->file('image')) {
            $destinationPath = public_path('images');
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = $profileImage;
        }

        Gift::create($input);

        return back()->with('success', 'Cadeau ajoutÃ© avec succÃ¨s.');
    }

    public function deleteGift($id)
    {
        $gift = Gift::findOrFail($id);
        if ($gift->image && file_exists(public_path('images/' . $gift->image))) {
            @unlink(public_path('images/' . $gift->image));
        }
        $gift->delete();
        return back()->with('success', 'Cadeau supprimÃ©.');
    }
}
