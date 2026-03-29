<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function show(string $token, Request $request)
    {
        $ticket = Ticket::where('ticket_reply_token', $token)
            ->where('is_support_ticket', true)
            ->firstOrFail();

        // Pre-select rating if passed via query string (quick-rate from email)
        $preRating = (int) $request->query('rating', 0);
        if ($preRating >= 1 && $preRating <= 5 && ! $ticket->survey_responded_at) {
            $preRating = $preRating;
        } else {
            $preRating = 0;
        }

        return view('survey.index', compact('ticket', 'preRating'));
    }

    public function submit(string $token, Request $request)
    {
        $ticket = Ticket::where('ticket_reply_token', $token)
            ->where('is_support_ticket', true)
            ->firstOrFail();

        if ($ticket->survey_responded_at) {
            return redirect()->route('survey.show', $token)->with('already', true);
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'survey_rating'       => $validated['rating'],
            'survey_comment'      => $validated['comment'] ?? null,
            'survey_responded_at' => now(),
        ]);

        return redirect()->route('survey.show', $token)->with('submitted', true);
    }
}
