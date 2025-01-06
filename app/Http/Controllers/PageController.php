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

    public function show($slugPath)
    {
        $slugs = explode('/', $slugPath);
        $page = null;
        $parentId = null;

        foreach ($slugs as $slug) {
            $page = Page::bySlug($slug, $parentId)->firstOrFail();
            $parentId = $page->id;
        }

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
