<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\League;
use App\Models\Team;

class ContentController extends Controller
{
    //
    public function index()
    {
        $contents = Content::with('options')->latest()->paginate(20);
        return view('admin.contents.index', compact('contents'));
    }


    public function create()
    {
        return view('admin.contents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:poll,quiz,trivia',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'league_id' => 'nullable|exists:leagues,id',
            'team_id' => 'nullable|exists:teams,id',
            'published_at' => 'nullable|date',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        $content = Content::create($request->except('options'));

        foreach ($request->options as $opt) {
            ContentOption::create([
                'content_id' => $content->id,
                'option_text' => $opt['text'],
                'is_correct' => $opt['is_correct'] ?? false,
            ]);
        }

        //  return response()->json($content->load('options'), 201);
        return redirect()->route('admin.contents.index')->with('success', 'Content created.');
    }


    public function update(Request $request, Content $content)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:poll,quiz,trivia',
        'difficulty' => 'nullable|in:easy,medium,hard',
        'league_id' => 'nullable|exists:leagues,id',
        'team_id' => 'nullable|exists:teams,id',
        'published_at' => 'nullable|date',
        'options' => 'required|array|min:2',
        'options.*.text' => 'required|string',
        'options.*.is_correct' => 'nullable|boolean',
    ]);

    // Update main content fields
    $content->update($request->except('options'));

    // Delete existing options
    $content->options()->delete();

    // Re-create new options
    foreach ($request->options as $opt) {
        $content->options()->create([
            'option_text' => $opt['text'],
            'is_correct' => $opt['is_correct'] ?? false,
        ]);
    }

        //  return response()->json($content->load('options'), 201);
    return redirect()->route('admin.contents.index')->with('success', 'Content updated.');
}



public function suggestContentPlacement()
{
    // Get leagues with less than a threshold amount of content
    $suggestedLeagues = League::withCount('contents')
        ->having('contents_count', '<', 5) // Threshold of 5 contents per league
        ->get();

    // Get teams with less than a threshold amount of content
    $suggestedTeams = Team::withCount('contents')
        ->having('contents_count', '<', 3) // Threshold of 3 contents per team
        ->get();

    // If no leagues or teams are found with low content, return all available leagues and teams
    if ($suggestedLeagues->isEmpty()) {
        $suggestedLeagues = League::all();
    }
    if ($suggestedTeams->isEmpty()) {
        $suggestedTeams = Team::all();
    }

    return response()->json([
        'suggested_leagues' => $suggestedLeagues,
        'suggested_teams' => $suggestedTeams,
    ]);
}
}
