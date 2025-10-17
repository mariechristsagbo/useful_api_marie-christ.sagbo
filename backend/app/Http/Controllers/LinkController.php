<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LinkController extends Controller
{
   
    public function shorten(Request $request)
    {
        $validated = $request->validate([
            'original_url' => 'required|url',
            'custom_code' => [
                'nullable',
                'max:10',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('links', 'code'),
            ],
        ]);

        $code = $validated['custom_code'] ?? $this->generateUniqueCode();

        if (isset($validated['custom_code']) && Link::where('code', $validated['custom_code'])->exists()) {
            return response()->json(['message' => 'This code is already used'], 422);
        }

        $link = Link::create([
            'user_id' => $request->user()->id,
            'original_url' => $validated['original_url'],
            'code' => $code,
        ]);

        return response()->json([
            'id' => $link->id,
            'user_id' => $link->user_id,
            'original_url' => $link->original_url,
            'code' => $link->code,
            'created_at' => $link->created_at,
    ], 201);
    }

   
    public function redirect($code)
    {
        $link = Link::where('code', $code)->first();

        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->increment('clicks');

        return redirect($link->original_url, 302);
    }

   
    public function index(Request $request)
    {
        $links = $request->user()->links()->get();

        return response()->json($links, 200);
    }

    
    public function destroy(Request $request, $id)
    {
        $link = Link::where('id', $id)
                    ->where('user_id', $request->user()->id)
                    ->first();

        if (!$link) {
            return response()->json(['message' => 'Link not found'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Link deleted successfully'], 200);
    }

    
    protected function generateUniqueCode()
    {
        do {
            $code = Str::random(7);
        } while (Link::where('code', $code)->exists());

        return $code;
    }
}
