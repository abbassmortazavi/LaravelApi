<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Word;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

class WordController extends Controller
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
        $destination = 'images/';
        $entries = scandir($destination);
        $attachments = array();
        foreach($entries as $entry) {
            $attachments[] = $entry;
            unset($attachments[0]);
            unset($attachments[1]);
        }

        return view('Admin.word.create' , compact('attachments'));
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
            'word' => 'required|unique:words,word',
        ]);



        $files = $request->file('image_path');
        $input = array();
        if ($files)
        {
            foreach ($files as $file)
            {
                $orginalName=$file->getClientOriginalName();

                $destination = 'images/';
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
            $input['image_path'] = "no-image.jpg";
        }



        $input['word'] = $request->word;
        $input['phonetic'] = $request->phonetic;
        $input['persian_example'] = $request->persian_example;
        $input['english_meaning'] = $request->english_meaning;
        $input['english_example'] = $request->english_example;
        $input['persian_meaning'] = $request->persian_meaning;


        Word::create($input);
        alert()->success('اطلاعات ثبت شده با موفقیت', 'Good bye!')->persistent('Close')->autoclose(6000);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Word  $word
     * @return \Illuminate\Http\Response
     */
    public function show(Word $word)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Word  $word
     * @return \Illuminate\Http\Response
     */
    public function edit(Word $word)
    {
        $destination = 'images/';
        $entries = scandir($destination);
        $attachments = array();
        foreach($entries as $entry) {
            $attachments[] = $entry;
            unset($attachments[0]);
            unset($attachments[1]);
        }
        return view('Admin.word.edit' , compact('word' , 'attachments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Word $word
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Word $word)
    {
        $this->validate($request , [
            'word' => 'required',
        ]);



        $files = $request->file('image_path');
        $input = array();
        if ($files)
        {
            foreach ($files as $file)
            {
                $orginalName=$file->getClientOriginalName();

                $destination = 'images/';
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
        }else {
            $input['image_path'] = $request->image;
        }


        //$input['image_path'] = $word->image_path;

        $input['word'] = $request->word;
        $input['phonetic'] = $request->phonetic;
        $input['persian_example'] = $request->persian_example;
        $input['english_meaning'] = $request->english_meaning;
        $input['english_example'] = $request->english_example;
        $input['persian_meaning'] = $request->persian_meaning;

        $word->update($input);
        alert()->success('اطلاعات با موفقیت ویرایش شد!!!', 'پیام سیستم!')->persistent('Close')->autoclose(6000);
        return redirect(route('words.create'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Word  $word
     * @return \Illuminate\Http\Response
     */
    public function destroy(Word $word)
    {

    }

    public function deleteWord(Request $request)
    {
        $id = $request->id;
        $findCat = Word::whereId($id)->first();
        $findCat->delete();
        return back();
    }
    
    public function uploadImage(Request $request)
    {
        $CKEditor = $request->input('CKEditor');
        $funcNum  = $request->input('CKEditorFuncNum');
        $destination = 'images/';
        $message  = $url = '';
        if (Input::hasFile('upload')) {
            $file = Input::file('upload');
            if ($file->isValid()) {
                $filename = $file->getClientOriginalName();
                $file->move(public_path().$destination, $filename);
                $url = url($destination . $filename);
            } else {
                $message = 'An error occurred while uploading the file.';
            }
        } else {
            $message = 'No file uploaded.';
        }
        return '<script>window.parent.CKEDITOR.tools.callFunction('.$funcNum.', "'.$url.'", "'.$message.'")</script>';

    }
}
