<?php

namespace App\Http\Controllers\Api\v2;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function allCategories(Request $request)
    {
       
        $categories = Category::where('parent_category_id' , '0')->get();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Get All Category!',
                'data' => $categories
            ]);
        }
    }

    public function subCategory(Request $request)
    {
        $id = $request->id;
        $subCats = Category::where('parent_category_id', $id)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Get All SubCategory!',
                'data' => $subCats
            ]);
        }
    }
}
