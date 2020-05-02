<?php

namespace App\Http\Controllers\Admin;

use stdClass;
use App\Category;
use App\Listening;
use App\ListeningCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use function GuzzleHttp\Psr7\mimetype_from_filename;

class ListeningController extends Controller
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
        $parents = Category::where('parent_category_id' , '0')->where('category_type' , '0')->get();
        $categories = Category::whereCategory_type(1)->get();
        return view('Admin.listening.create' , compact('categories' , 'parents'));
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

        /* $this->validate($request,[
            'title' => 'required',
            'category_id' => 'required',
        ]); */

        if ($request->title == "")
        {
            //return back()->withErrors('دسته بندی مورد نظر خود را انتخاب نکرده اید');
            return 3;
        }


        $input = $request->all();
        
        
        if ($request->image){
            $image = $request->image;  // your base64 encoded
            $destination = 'listening/';
            $image_parts = explode(";base64,", $image);
            $image_type_aux = explode("audio/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $file = uniqid() . '.mp3';
            file_put_contents($destination.$file, $image_base64);
            //File::put(storage_path(). '/' . $imageName, base64_decode($image));
            $input['file_path'] = $file;
        }else{
            $input['file_path'] = "";
        }
        
        
        if ($request->book_category_id == 0)
        {
            //return back()->withErrors('دسته بندی مورد نظر خود را انتخاب نکرده اید');
            return 1;
        }

        $checkChild = Category::where('parent_category_id' , $request->category_id)->count();
        if ($checkChild > 0)
        {
            //return back()->withErrors(' یکی از دسته بندی هارو انتخاب کنید');
            return 2;
        }
        

       
        $input['category_id'] = $request->category_id;
        $input['book_category_id'] = $request->book_category_id;
        
        
        
        $input['title'] = $request->title;
        $input['description'] = $request->description;
        $input['questions'] = $request->questions;

       
        
        
        
        
        
        //dd($input);
        Listening::create($input);
        return 0;
        //alert()->success('اطلاعات باموفقیت ثبت شده است', 'Good bye!')->persistent('Close')->autoclose(6000);
        //return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listening $listening
     * @return void
     */
    public function show(Listening $listening)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Listening $listening
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Listening $listening)
    {
        $questions = json_decode($listening->questions);
      //dd($questions);
        $categories = ListeningCategory::get();
        $parents = Category::where('parent_category_id' , '0')->where('category_type' , '0')->get();
        $categories = Category::whereCategory_type(1)->get();
        return view('Admin.listening.edit' , compact('listening' , 'categories' , 'questions' , 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Listening $listening
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Listening $listening)
    {
        $this->validate($request,[
            'title' => 'required',
            'category_id' => 'required',
        ]);
        $input = $request->all();
        $files = $request->file('file_path');

        if ($files){
            $size = $request->file('file_path')->getSize();
            $orginalName = $files->getClientOriginalName();
            $mimeType = $files->getMimeType();
            $supportedTypes = ['audio/mpeg', 'audio/mpeg3', 'audio/mp3'];
            $destination = 'listening/';
            $msg="";
            $check=true;
            if (! in_array($mimeType, $supportedTypes)) {
                $check=false;
                $msg.='فرمتهای قابل قبول برای آپلود فایل = ';
                $msg.=implode(' , ',$supportedTypes);
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
                //return back()->withErrors($msg);
            }
            if($size > 30000000){
                $check=false;
                $msg.="حجم فایل نباید بیشتر از 9 مگابایت باشد .";
                alert()->error($msg, 'پیام سیستم')->autoclose(6000);
                return back();
            }

            $files->move($destination, $orginalName);
            //$path = $request->file('file_path')->store('listening/Audio');
            $input['file_path'] = $orginalName;
        }else{
            $input['file_path'] = $listening->file_path;
        }
        if ($request->title)
        {
            $input['title'] = $request->title;
        }else{
            $input['title'] = $listening->title;
        }
        if ($request->description)
        {
            $input['description'] = $request->description;
        }else{
            $input['description'] = $listening->description;
        }
        if ($request->category_id)
        {
            $input['category_id'] = $request->category_id;
        }else{
            $input['category_id'] = $listening->category_id;
        }
        
        
        
         
        if ($request->questions)
        {
            $questions = json_decode($request->questions);
            foreach($questions as $question)
            {
                foreach($question->choices as $choice){
                    if($choice->isCorrect == "on")
                    {
                        $choice->isCorrect = 1;
                    }
                } 
            }
            $input['questions'] = json_encode($questions);
        }else{
            $input['questions'] = $listening->questions;
        }
        
        
        if ($request->book_category_id)
        {
            $input['book_category_id'] = $request->book_category_id;
        }else{
            $input['book_category_id'] = $listening->book_category_id;
        }
    
        
        
        
        
        //dd($input);
        $listening->update($input);
        return redirect(route('listenings.create'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Listening $listening
     * @return void
     */
    public function destroy(Listening $listening)
    {
        //
    }
    public function deleteListening(Request $request)
    {
        $id = $request->id;
        $findListening = Listening::whereId($id)->first();
        if ($findListening->file_path){
            unlink('listening/'.$findListening->file_path);
        }
        $findListening->delete();
        //return back();
    }
}
