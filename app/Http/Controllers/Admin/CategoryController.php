<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Word;
use foo\bar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
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
     * @throws \Exception
     */
    public function create()
    {
        $parents = Category::where('parent_category_id' , '0')->where('category_type' , '0')->get();
        return view('Admin.category.create' , compact('parents'));
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

        $this->validate($request , [
            'category_name' => 'required',
            'parent_category_id' => 'required',
        ]);

        /*if ($request->is_published == 1 && $request->parent_category_id11 !=0)
        {
            $childrens = $this->getCategoryChildrenByCategoryId($request->parent_category_id11);


            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                $findChild->update(['is_published'=>1]);
            }

        }*/

        $files = $request->file('image_path');
        $input = array();
        if ($files)
        {
            foreach ($files as $file)
            {
                $orginalName=$file->getClientOriginalName();

                $destination = 'packageImages/';
                $msg="";
                $check=true;
                $size=$file->getSize();

                $orginalName=$file->getClientOriginalName();

                $fileSuffix = strtolower($file->getClientOriginalExtension());
                if(!in_array($fileSuffix,['jpg'])){
                    $check=false;
                    $msg.='فرمتهای قابل قبول برای آپلود تصویر = ';
                    $msg.=implode(' , ',['jpg']);
                    //return back()->withErrors($msg);
                    alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                    return back();
                }

                if($size/1024 > 1024){
                    $check=false;
                    $msg.="حجم تصاویر نباید بیشتر از 1 مگابایت باشد .";
                    alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                    return back();
                }

                $fileName = $orginalName;
                if($check){
                    $move=$file->move($destination, $fileName);
                    if(!$move){
                        $check=false;
                        $msg.='این فایل آپلود نشده - '.$file->getClientOriginalName();
                        alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                        //return back()->withErrors($msg);
                        return back();
                    }
                }
                else{
                    $msg.='عکس شما با موفقیت آپلود شده است';
                    alert()->error($msg, 'پیام سیستم')->persistent('Close')->autoclose(6000);
                    //return back()->withErrors($msg);
                    return back();
                }

            }
            $input['image_path'] = $orginalName;
        }


        $input['parent_category_id'] = $request->parent_category_id;
        $input['category_name'] = $request->category_name;
        $input['category_type'] = $request->category_type;
        $input['description'] = $request->description;
        /*$input['descriptionEn'] = $request->descriptionEn;
        $input['descriptionFa'] = $request->descriptionFa;*/
        $input['list_type'] = $request->list_type;
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
        
        //dd($input);
        
        Category::create($input);
        alert()->success('اطلاعات ثبت شده با موفقیت', 'Good bye!')->persistent('Close')->autoclose(6000);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $parents = Category::where('parent_category_id' , '0')->get();
        return view('Admin.category.edit' , compact('category' , 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Category $category
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request , [
            'category_name' => 'required',
            'parent_category_id' => 'required',
        ]);
        
        
        //check category with own
        if ($category->id == $request->parent_category_id)
        {
             alert()->error('شمانمیتوانید خود دسته رو زیر دسته خودش کنید!!', 'پیام سیستم')->persistent('Close')->autoclose(6000);
            //return back()->withErrors($msg);
            return back();
        }

        $files = $request->file('image_path');
        $input = array();
        if ($files)
        {
            foreach ($files as $file)
            {
                $orginalName=$file->getClientOriginalName();

                $destination = 'packageImages/';
                $msg="";
                $check=true;
                $size=$file->getSize();

                $orginalName=$file->getClientOriginalName();

                $fileSuffix = strtolower($file->getClientOriginalExtension());
                if(!in_array($fileSuffix,['jpg'])){
                    $check=false;
                    $msg.='فرمتهای قابل قبول برای آپلود تصویر = ';
                    $msg.=implode(' , ',['jpg']);
                    //return back()->withErrors($msg);
                    alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                    return back();
                }

                if($size/1024 > 1024){
                    $check=false;
                    $msg.="حجم تصاویر نباید بیشتر از 1 مگابایت باشد .";
                    alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                    return back();
                }

                $fileName = $orginalName;
                if($check){
                    $move=$file->move($destination, $fileName);
                    if(!$move){
                        $check=false;
                        $msg.='این فایل آپلود نشده - '.$file->getClientOriginalName();
                        alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                        //return back()->withErrors($msg);
                        return back();
                    }
                }
                else{
                    $msg.='عکس شما با موفقیت آپلود شده است';
                    alert()->error($msg, 'پیام سیستم')->persistent('Close')->autoclose(6000);
                    //return back()->withErrors($msg);
                    return back();
                }

            }
            $input['image_path'] = $orginalName;
        }else{
            $input['image_path'] = $category->image_path;
        }

        if ($request->parent_category_id !== $category->parent_category_id && $request->parent_category_id !==0)
        {
            $input['parent_category_id'] = $request->parent_category_id;
        }

        if ($request->parent_category_id == 0)
        {
            $input['parent_category_id'] = $category->parent_category_id;
        }
        if ($request->category_type)
        {
            $input['category_type'] = $request->category_type;
        }else{
            $input['category_type'] = $category->category_type;
        }



        $input['category_name'] = $request->category_name;
       //$input['descriptionEn'] = $request->descriptionEn;
        //$input['descriptionFa'] = $request->descriptionFa;
        $input['description'] = $request->description;
        $input['is_free'] = $request->is_free;
        $input['is_published'] = $request->is_published;
        $input['list_type'] = $request->list_type;
        
        
        if ($request->is_published == 0)
        {
            $childrens = $this->getCategoryChildrenByCategoryId($category->id);
            //dd($childrens);

            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                $findChild->update(['is_published'=>0]);
            }

        }
        if ($request->is_published == 1)
        {
            $childrens = $this->getCategoryChildrenByCategoryId($category->id);
            //dd($childrens);

            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                $findChild->update(['is_published'=>1]);
            }
        }
        
        
        
        if ($request->is_free == 0)
        {
            $childrens = $this->getCategoryChildrenByCategoryId($category->id);
            //dd($childrens);

            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                $findChild->update(['is_free'=>0]);
            }

        }
        if ($request->is_free == 1)
        {
            $childrens = $this->getCategoryChildrenByCategoryId($category->id);
            //dd($childrens);

            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                $findChild->update(['is_free'=>1]);
            }
        }
        
        //dd($input);
        $category->update($input);
        alert()->success('اطلاعات با موفقیت آپدیت شد', 'Good bye!')->persistent('Close')->autoclose(6000);
        return redirect(route('categories.create'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category $category
     * @return void
     */
    public function destroy(Category $category)
    {
        dd($category);
    }

    public function loadSubCat(Request $request)
    {
        $id = $request->id;

        $subCats = Category::where('parent_category_id', $id)->get();
        return $subCats;
    }

    public function deleteCat(Request $request)
    {
        $id = $request->id;


        $findCat = Category::whereId($id)->first();

        $childrens = $this->getCategoryChildrenByCategoryId($id);
        if ($childrens)
        {
            foreach ($childrens as $children)
            {
                $findChild = Category::whereId($children['id'])->first();
                if ($findChild['image_path']){
                    unlink('packageImages/'.$findChild['image_path']);
                }

                $findChild->delete();
            }
        }
        $findCat->delete();
        return back();
    }

    public function getCategoryChildrenByCategoryId($category_id) {
        $category_data = array();

        $category_query = DB::select('select * from categories where parent_category_id = ?', [$category_id]);
        $t = Category::where('parent_category_id' , $category_id)->get();


        foreach ($t as $category) {
            $category_data[] = array(
                'id' => $category->id,
                'parent_category_id' => $category->parent_category_id,

            );

            $children = $this->getCategoryChildrenByCategoryId($category->id);

            if ($children) {
                $category_data = array_merge($children, $category_data);
            }
        }

        return $category_data;
    }

    public function wordListening()
    {
        $words = Word::all();
        $parents = Category::where('category_type' , '1')->get();
        return view('Admin.category.listeningWord' , compact('words' , 'parents'));
    }
    
    public function categorytype(Request $request)
    {
  
        $id = $request->id;
        $categories = Category::whereCategory_type($id)->whereParent_category_id(0)->get();
        return $categories;
    }



}
