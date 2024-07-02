<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paste; // Import the Paste model
use Illuminate\Support\Str; // Import the Str class

class PasteController extends Controller
{
    public function create()
    {
        return view('pastes.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required',
            'expiration' => 'in:1day,1week,1month,1year,never',
        ]);

        $truncatedContent = Str::limit($validatedData['content'], 65000, ''); 

        //unique slug
        $slug = Str::random(8);
        while (Paste::where('slug', $slug)->exists()) {
            $slug = Str::random(8);
        }

        $expiration = $validatedData['expiration'];
        $expiredAt = match ($expiration) {
            '1day' => now()->addDay(),
            '1week' => now()->addWeek(),
            '1month' => now()->addMonth(),
            '1year' => now()->addYear(),
            'never' => null,
        };

        $paste = new Paste();
        $paste->content = $truncatedContent;
        $paste->slug = $slug;
        $paste->expired_at = $expiredAt;
        $paste->save();

        return redirect()->route('pastes.show', $paste->slug);
    }

    public function show($slug)
    {
        $paste = Paste::where('slug', $slug)->firstOrFail();
        if ($paste->expired_at && $paste->expired_at->isPast()) {
            abort(404, 'Paste has expired');
        }
        return response($paste->content)
            ->header('Content-Type', 'text/plain');
    }
}
