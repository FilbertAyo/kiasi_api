<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories (default + user's custom).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::forUser($request->user()->id);

        // Filter by type if provided
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $categories = $query->orderBy('type')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'type' => $category->type,
                    'is_default' => $category->is_default,
                ];
            }),
        ]);
    }
}

