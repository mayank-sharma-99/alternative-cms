<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('children')->whereNull('parent_id')->get();
        return response()->json($pages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:pages,id',
            'slug' => 'required|string',
            'title' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $page = Page::create($validated);
        return response()->json($page, 201);
    }

    public function show($slug, Request $request)
    {
        $slugParts = explode('/', $slug);
        $page = null;

        foreach ($slugParts as $part) {
            $page = Page::where('slug', $part)
                ->where('parent_id', optional($page)->id)
                ->first();

            if (!$page) {
                return response()->json(['error' => 'Page not found'], 404);
            }
        }

        $page->load('children');
        return response()->json($page);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:pages,id',
            'slug' => 'required|string',
            'title' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $page->update($validated);
        return response()->json($page);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->noContent();
    }
}
