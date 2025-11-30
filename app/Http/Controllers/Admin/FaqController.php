<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display FAQ categories and questions.
     */
    public function index()
    {
        $categories = FaqCategory::withCount('questions')
            ->orderBy('display_order')
            ->get();

        return view('admin.faq.index', compact('categories'));
    }

    /**
     * Show questions for a category.
     */
    public function showCategory(FaqCategory $category)
    {
        $questions = $category->questions()
            ->orderBy('language')
            ->orderBy('display_order')
            ->get()
            ->groupBy('language');

        return view('admin.faq.category', compact('category', 'questions'));
    }

    /**
     * Show form to create new category.
     */
    public function createCategory()
    {
        return view('admin.faq.create-category');
    }

    /**
     * Store new category.
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:50|unique:faq_categories,slug',
            'name_en' => 'required|string|max:100',
            'name_sw' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = true;
        $validated['display_order'] = $validated['display_order'] ?? FaqCategory::max('display_order') + 1;

        FaqCategory::create($validated);

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ category created successfully.');
    }

    /**
     * Show form to edit category.
     */
    public function editCategory(FaqCategory $category)
    {
        return view('admin.faq.edit-category', compact('category'));
    }

    /**
     * Update category.
     */
    public function updateCategory(Request $request, FaqCategory $category)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:100',
            'name_sw' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ category updated successfully.');
    }

    /**
     * Delete category.
     */
    public function destroyCategory(FaqCategory $category)
    {
        // Delete all questions first
        $category->questions()->delete();
        $category->delete();

        return redirect()->route('admin.faq.index')
            ->with('success', 'FAQ category and all its questions deleted successfully.');
    }

    /**
     * Show form to create new question.
     */
    public function createQuestion(FaqCategory $category)
    {
        $languages = ['sw' => 'Kiswahili', 'en' => 'English'];
        return view('admin.faq.create-question', compact('category', 'languages'));
    }

    /**
     * Store new question.
     */
    public function storeQuestion(Request $request, FaqCategory $category)
    {
        $validated = $request->validate([
            'language' => 'required|in:sw,en',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $validated['category_id'] = $category->id;
        $validated['is_active'] = true;
        $validated['display_order'] = $validated['display_order'] ?? 
            $category->questions()->where('language', $validated['language'])->max('display_order') + 1;

        FaqQuestion::create($validated);

        return redirect()->route('admin.faq.category', $category)
            ->with('success', 'FAQ question created successfully.');
    }

    /**
     * Show form to edit question.
     */
    public function editQuestion(FaqQuestion $question)
    {
        return view('admin.faq.edit-question', compact('question'));
    }

    /**
     * Update question.
     */
    public function updateQuestion(Request $request, FaqQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $question->update($validated);

        return redirect()->route('admin.faq.category', $question->category)
            ->with('success', 'FAQ question updated successfully.');
    }

    /**
     * Delete question.
     */
    public function destroyQuestion(FaqQuestion $question)
    {
        $category = $question->category;
        $question->delete();

        return redirect()->route('admin.faq.category', $category)
            ->with('success', 'FAQ question deleted successfully.');
    }
}

