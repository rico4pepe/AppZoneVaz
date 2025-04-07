<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\ContentOption;

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

        return redirect()->route('admin.contents.index')->with('success', 'Content created.');
    }
}
