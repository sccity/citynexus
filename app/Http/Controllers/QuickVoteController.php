<?php

namespace App\Http\Controllers;

use App\Models\QuickVote;
use App\Models\QuickVoteResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QuickVoteController extends Controller
{
    public function index()
    {
        $votes = QuickVote::where('created_by', auth()->id())
            ->with('responses')
            ->latest()
            ->get()
            ->map(function ($vote) {
                return [
                    'id' => $vote->id,
                    'question' => $vote->question,
                    'is_active' => $vote->is_active,
                    'created_at' => $vote->created_at,
                    'results' => $vote->results,
                    'access_code' => $vote->access_code,
                ];
            });

        return Inertia::render('QuickVote/Index', [
            'votes' => $votes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
        ]);

        $vote = QuickVote::create([
            'question' => $validated['question'],
            'created_by' => auth()->id(),
            'access_code' => Str::random(8),
            'is_active' => true,
        ]);

        // Generate QR code for the vote URL
        $voteUrl = url("/vote/{$vote->access_code}");
        $qrCode = base64_encode(QrCode::format('svg')->size(300)->generate($voteUrl));

        return response()->json([
            'vote' => $vote,
            'qr_code' => $qrCode,
            'vote_url' => $voteUrl,
        ]);
    }

    public function show($accessCode)
    {
        $vote = QuickVote::where('access_code', $accessCode)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('QuickVote/Vote', [
            'vote' => [
                'id' => $vote->id,
                'question' => $vote->question,
                'access_code' => $vote->access_code,
            ]
        ]);
    }

    public function submitVote(Request $request, $accessCode)
    {
        $vote = QuickVote::where('access_code', $accessCode)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'voter_name' => 'required|string|max:255',
            'response' => 'required|in:yea,nay,abstain',
        ]);

        $response = QuickVoteResponse::create([
            'quick_vote_id' => $vote->id,
            'voter_name' => $validated['voter_name'],
            'response' => $validated['response'],
        ]);

        return response()->json(['message' => 'Vote recorded successfully']);
    }

    public function getResults($id)
    {
        $vote = QuickVote::findOrFail($id);
        
        return response()->json([
            'results' => $vote->results,
            'responses' => $vote->responses()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function toggleActive($id)
    {
        $vote = QuickVote::findOrFail($id);
        $vote->update(['is_active' => !$vote->is_active]);
        
        return response()->json(['is_active' => $vote->is_active]);
    }

    public function getQrCode($accessCode)
    {
        $vote = QuickVote::where('access_code', $accessCode)
            ->where('is_active', true)
            ->firstOrFail();

        $voteUrl = url("/vote/{$vote->access_code}");
        $qrCode = base64_encode(QrCode::format('svg')->size(300)->generate($voteUrl));

        return response()->json([
            'qr_code' => $qrCode,
            'vote_url' => $voteUrl,
        ]);
    }
} 