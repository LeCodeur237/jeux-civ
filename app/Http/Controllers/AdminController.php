<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    // Export CSV des utilisateurs
    public function exportUsersCsv()
    {
        $users = Player::all();
        $csvFileName = 'utilisateurs_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($users) {
            $file = fopen('php://output', 'w');
            // Ajout du BOM pour compatibilité Excel (accents)
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Prénom', 'Age', 'Profession', 'Téléphone', 'Date Inscription'], ';');

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->nom,
                    $user->prenom,
                    $user->age,
                    $user->profession,
                    $user->phone,
                    $user->created_at->format('d/m/Y H:i')
                ], ';');
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

        return back()->with('success', 'Cadeau ajouté avec succès.');
    }

    public function deleteGift($id)
    {
        $gift = Gift::findOrFail($id);
        if ($gift->image && file_exists(public_path('images/' . $gift->image))) {
            @unlink(public_path('images/' . $gift->image));
        }
        $gift->delete();
        return back()->with('success', 'Cadeau supprimé.');
    }
}
