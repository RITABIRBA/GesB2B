<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgePublicController extends Controller
{
    public function show($qr_code)
    {
        $badge = Badge::with([
            'participant',
            'participant.entreprise',
            'typeBadge',
        ])->where('qr_code', $qr_code)->first();

        if (!$badge) {
            abort(404, 'Badge introuvable');
        }

        return view('badge-public', compact('badge'));
    }
}