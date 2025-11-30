<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticContent;
use Illuminate\Http\Request;

class StaticContentController extends Controller
{
    /**
     * Display all static content.
     */
    public function index()
    {
        $contents = StaticContent::orderBy('type')
            ->orderBy('language')
            ->get()
            ->groupBy('type');

        return view('admin.content.index', compact('contents'));
    }

    /**
     * Show form to create new content.
     */
    public function create()
    {
        $types = ['terms', 'privacy', 'about'];
        $languages = ['sw' => 'Kiswahili', 'en' => 'English'];
        
        return view('admin.content.create', compact('types', 'languages'));
    }

    /**
     * Store new content.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:terms,privacy,about',
            'language' => 'required|in:sw,en',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'nullable|string|max:20',
            'effective_date' => 'nullable|date',
        ]);

        // Check if content already exists for this type/language
        $existing = StaticContent::where('type', $validated['type'])
            ->where('language', $validated['language'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Content already exists for this type and language. Please edit the existing one.');
        }

        $validated['is_active'] = true;

        StaticContent::create($validated);

        return redirect()->route('admin.content.index')
            ->with('success', 'Content created successfully.');
    }

    /**
     * Show form to edit content.
     */
    public function edit(StaticContent $content)
    {
        return view('admin.content.edit', compact('content'));
    }

    /**
     * Update content.
     */
    public function update(Request $request, StaticContent $content)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'nullable|string|max:20',
            'effective_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Auto-increment version if content changed significantly
        if ($request->has('increment_version') && $content->version) {
            $parts = explode('.', $content->version);
            if (count($parts) >= 2) {
                $parts[1] = (int)$parts[1] + 1;
                $validated['version'] = implode('.', $parts);
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $content->update($validated);

        return redirect()->route('admin.content.index')
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Delete content.
     */
    public function destroy(StaticContent $content)
    {
        $content->delete();

        return redirect()->route('admin.content.index')
            ->with('success', 'Content deleted successfully.');
    }
}

