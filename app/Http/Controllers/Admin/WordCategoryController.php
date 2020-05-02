<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Word;
use App\WordCategory;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WordCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $words = Word::all();
        $parents = Category::where('parent_category_id' , '0')->where('category_type' , '0')->get();
        return view('Admin.wordCategory.create' , compact('words' , 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        /* $this->validate($request , [
             'category_id' => 'required',
         ]);*/
         
        // return $request->wordIdsArray;
         
        if ($request->book_category_id == 0 or sizeof($request->wordIdsArray)==0)
        {
            return '1';
        }

        $checkChild = Category::where('parent_category_id' , $request->category_id)->count();
        //return $checkChild;
        if ($checkChild > 0)
        {
            return '3';
        }
        

        $wordIdsArray = $request->wordIdsArray;
        $categoryId = $request->category_id;
        $bookCategoryId = $request->book_category_id;
        
        WordCategory::where('category_id',$categoryId)->delete();

        foreach ($wordIdsArray as $wordId)
        {
            $input['word_id'] = $wordId;
            $input['category_id'] = $categoryId;
            $input['book_category_id'] = $bookCategoryId;
            $t = WordCategory::create($input);
        }

        return '4';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\WordCategory  $wordCategory
     * @return \Illuminate\Http\Response
     */
    public function show(WordCategory $wordCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WordCategory  $wordCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(WordCategory $wordCategory)
    {
        $words = Word::all();
        $parents = Category::where('parent_category_id' , '0')->get();
        return view('Admin.wordCategory.edit' , compact('wordCategory' , 'words' , 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\WordCategory $wordCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WordCategory $wordCategory)
    {
        /*$this->validate($request , [
            'category_id' => 'required',
        ]);*/


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WordCategory  $wordCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(WordCategory $wordCategory)
    {
        //
    }

    public function deleteWordCategory(Request $request)
    {

        $id = $request->id;
        $findCat = WordCategory::whereId($id)->first();
        $findCat->delete();
        return back();
    }

    public function updateWordCat(Request $request  , $id)
    {
        
       $ok = 0;
        $input = array();
        $input['category_id'] = $request->category_id;
        $input['book_category_id'] = $request->book_category_id;
        $wordIds = $request->word_ids;
        
        if ($request->book_category_id == 0 or $request->word_ids == 0)
        {
            return '1';
        }

       

        $checkChild = Category::where('parent_category_id' , $request->category_id)->count();
        //return $checkChild;
        if ($checkChild > 0)
        {
            return '3';
        }
        

        $wordCategory = WordCategory::where('id' , $id)->first();
        
        foreach ($wordIds as $wordId)
        {
            $checkExist = WordCategory::where('category_id' , $request->category_id)->where('word_id' , $wordId)->first();
            if ($checkExist)
            {
                return '2';
            }
            $input['word_id'] = $wordId;
            $wordCategory->update($input);
        }
        alert()->success('اطلاعات با موفقیت ویرایش شد', 'پیام سیستم!')->persistent('Close')->autoclose(6000);
        return back();
      
    }
    
    public function getCategoryWords(Request $request)
    {
        $categoryId = $request->categoryId;
    
        $results = DB::table('word_categories')
             ->select(['words.id', 'words.word'])
             ->join('words', 'words.id', '=', 'word_categories.word_id')
             ->where('word_categories.category_id' , $categoryId)
             ->orderBy('word_categories.id','ASC')
             ->get();
        return $results;
    }
}
