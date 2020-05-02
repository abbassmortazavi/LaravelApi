<?php

namespace App\Http\Controllers\Admin;

use App\ListeningCategory;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Word;
use App\WordCategory;
class ListeningCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
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
        return view('Admin.listening-category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'image_path' => 'required|mimes:jpg,jpeg,png|max:4096',//size:8654659
       ]);
//        dd($request->all());
        $input = $request->all();
        $files = $request->file('image_path');

        if ($files){
            $size = $request->file('image_path')->getSize();
            $originalName = $files->getClientOriginalName();
            $mimeType = $files->getMimeType();
            $supportedTypes = ['image/jpeg', 'image/png'];
            $destination = 'category/';
            $msg="";
            $check=true;
            if (! in_array($mimeType, $supportedTypes)) {
                $check=false;
                $msg.='فرمتهای قابل قبول برای آپلود فایل = ';
                $msg.=implode(' , ',$supportedTypes);
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
            }
            if($size > 400000){
                $check=false;
                $msg.="حجم فایل نباید بیشتر از 4 مگابایت باشد .";
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
            }

            $files->move($destination, $originalName);
            $input['image_path'] = $originalName;
        }else{
            $input['image_path'] = "";
        }

        if ($request->is_free)
        {
            $input['is_free'] = $request->is_free;
        }else{
            $input['is_free'] = 0;
        }

        if ($request->is_published)
        {
            $input['is_published'] = $request->is_published;
        }else{
            $input['is_published'] = 0;
        }
        $input['description'] = $request->description;
        
        $input['parent_category_id'] = 0;
        $input['category_type'] = 1;
        $input['category_name'] = $request->category_name;
        $input['list_type'] = $request->list_type;
        

        //dd($input);
        Category::create($input);
        alert()->success('اطلاعات باموفقیت ثبت شده است', 'Good bye!')->persistent('Close')->autoclose(6000);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ListeningCategory  $listeningCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ListeningCategory $listeningCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ListeningCategory  $listeningCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category , $id)
    {
        
        $category = Category::find($id);
        return view('Admin.listening-category.edit' , compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\ListeningCategory $listeningCategory
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request , $id)
    {
        
        $category = Category::find($id);
        
        /*$this->validate($request,[
            'image_path' => 'required|mimes:jpg,jpeg,png|max:4096',//size:8654659
        ]);*/
//        dd($request->all());
        $input = $request->all();
        $files = $request->file('image_path');

        if ($files){
            $size = $request->file('image_path')->getSize();
            $originalName = $files->getClientOriginalName();
            $mimeType = $files->getMimeType();
            $supportedTypes = ['image/jpeg', 'image/png'];
            $destination = 'category/';
            $msg="";
            $check=true;
            if (! in_array($mimeType, $supportedTypes)) {
                $check=false;
                $msg.='فرمتهای قابل قبول برای آپلود فایل = ';
                $msg.=implode(' , ',$supportedTypes);
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
            }
            if($size > 9000000){
                $check=false;
                $msg.="حجم فایل نباید بیشتر از 4 مگابایت باشد .";
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
            }

            $files->move($destination, $originalName);
            $input['image_path'] = $originalName;
        }else{
            $input['image_path'] = $category->image_path;
        }

        if ($request->is_free)
        {
            $input['is_free'] = 1;
        }else{
            $input['is_free'] = 0;
        }

        if ($request->is_published)
        {
            $input['is_published'] = 1;
        }else{
            $input['is_published'] = 0;
        }
        if ($request->description)
        {
            $input['description'] = $request->description;
        }else{
            $input['description'] = $category->description;
        }

        if ($request->category_name)
        {
            $input['category_name'] = $request->category_name;
        }else{
            $input['category_name'] = $category->category_name;
        }
        
        if ($request->list_type)
        {
            $input['list_type'] = $request->list_type;
        }else{
            $input['list_type'] = $category->list_type;
        }
        
        $input['category_type'] = 1;
        $input['parent_category_id'] = 0;
        

       
       $category->update($input);
     
        alert()->success('اطلاعات باموفقیت آپدیت شده است', 'Good bye!')->persistent('Close')->autoclose(6000);
        return redirect(route('listeningCategories.create'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ListeningCategory  $listeningCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListeningCategory $listeningCategory)
    {
        //
    }

    public function deleteListeningCategory(Request $request)
    {
        $id = $request->id;
        $findCategory = Category::whereId($id)->first();
        if ($findCategory->file_path){
            unlink('category/'.$findCategory->file_path);
        }
        $findCategory->delete();
        return redirect(route('categories.create'));
    }
    
    public function wordListening()
    {
        $words = Word::all();
        $parents = Category::where('category_type' , '1')->get();
        return view('Admin.listening-category.listeningWord' , compact('words' , 'parents'));
    }
    
    public function wordListeningAdd(Request $request)
    {
        //return $request->all();
        // if ($request->book_category_id == 0 or sizeof($request->wordIdsArray)==0)
        // {
        //     return '1';
        // }

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
            //return $input;
            $t = WordCategory::create($input);
        }

        return '4';
    }
}
