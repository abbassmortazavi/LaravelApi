<?php

namespace App\Http\Controllers;

use App\Listening;
use App\ListeningCategory;
use App\Category;
use App\Exports\UsersExport;
use App\Exports\WordsExport;
use App\Exports\WordBackUpExport;
use App\Exports\WordCategoryExport;
use App\Exports\UserExport;
use App\Exports\CategoryExport;

use App\Imports\CategoryImport;
use App\Imports\CatsImport;
use App\Imports\TestsImport;
use App\Imports\UsersImport;
use App\Imports\WordsCategoryImport;
use App\Imports\WordsImport;
use App\Word;
use App\WordCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Shetabit\Payment\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Payment\Invoice;
use App\Transaction;
use App\User;
use App\Package;
use Yajra\DataTables\Facades\DataTables;


class ImportExcelController extends Controller
{

    public function index(Request $request)
    {

        $data = DB::table("words")->orderBy("id", "DESC")->get();

        return view("test.import_excel", compact('data'));
    }

    /* public function import(Request $request)
     {



         $this->validate($request , [
             'select_file' => 'required|mimes:xls,xlsx'
         ]);


         $path = $request->file('select_file')->getRealPath();
         Excel::import(new UsersImport, $path);
         // $data = Excel::import($request->all() , $path)->get();
         $data = Excel::import($path,$path)->get()->toArray();
         dd($data);

         if ($data->count() > 0)
         {
             foreach ($data as $key=>$value)
             {
                 foreach ($value as $row)
                 {

                     $insert_data[] = array(
                         'words'  => $row->words,
                         'category_id'  => $row['category_id'],
                         'phonetic'  => $row['phonetic'],
                         'English_Meaning'  => $row['English_Meaning'],
                         'Persian_Meaning'  => $row['Persian_Meaning'],
                         'English_Example'  => $row['English_Example'],
                         'Persian_Translation'  => $row['Persian_Translation'],
                         'choices_question'  => $row['choices_question'],
                         'correspondence_question'  => $row['correspondence_question'],
                     );
                 }
             }

             if (!empty($insert_data))
             {

                 //Word::create($insert_data);
                 DB::table('words')->insert($insert_data);
             }
         }
         return back()->with('success' , 'Excel data imported...');
     }*/

    public function importWord(Request $request)
    {
        set_time_limit(2500);
        $this->validate($request, [
            'select_file' => 'required|file|max:1024|mimes:xls,xlsx'
        ]);

        $words = Word::all();
        if ($words) {
            Word::truncate();
        }

        Excel::import(new WordsImport(), $request->file('select_file'));

        return response()->json(['message' => trans('app.import_successful')]);
    }

    public function importCat(Request $request)
    {
        /*$this->validate($request, [
            'select_file' => 'required|file|max:1024|mimes:xls,xlsx'
        ]);*/

  /*      $tess = Category::where('category_name' , '=' , null)->get();
        foreach ($tess as $tes)
        {
            $tes->delete();
        }
die();*/
        $cats = Category::all();
        if ($cats) {
            Category::truncate();
        }

        Excel::import(new CategoryImport(), $request->file('select_file'));

        return response()->json(['message' => trans('app.import_successful')]);
    }

    public function importWordCat(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|file|max:1024|mimes:xls,xlsx'
        ]);

        $wordCategory = WordCategory::all();
        if ($wordCategory) {
            WordCategory::truncate();
        }

        Excel::import(new WordsCategoryImport(), $request->file('select_file'));

        return response()->json(['message' => trans('app.import_successful')]);
    }


    //get data : collection
    /* public function import(Request $request)
     {
         $path = $request->file('select_file')->getRealPath();

         $imports = new UsersImport();

         Excel::import($imports, $path);
          //dd($imports->sheetData);
         foreach ($imports->sheetData as $import)
         {
            foreach ($import as $item)
            {
                dd($item);
            }
         }
     }*/


   /* public function export()
    {
        return Excel::download(new WordsExport(), 'users.xlsx');
        //Excel::store(new UsersExport, 'users.csv');

    }*/
    
    public function export()
    {
        //return Excel::download(new WordsExport(), 'users.xlsx');
        //Excel::store(new UsersExport, 'users.csv');

        return view('Admin.export.create');
    }

    public function exportTable(Request $request)
    {
        switch ($request->export){
            case "1":
                return Excel::download(new CategoryExport(), 'categories.xlsx');
                break;
            case "2":
                return Excel::download(new UserExport(), 'users.xlsx');
                break;
            case "3":
                return Excel::download(new WordsExport(), 'words.xlsx');
                break;
            case "4":
                return Excel::download(new WordBackUpExport(), 'wordBackUps.xlsx');
                break;
            case "5":
                return Excel::download(new WordCategoryExport(), 'wordCategory.xlsx');
                break;
            default :
                return Excel::download(new WordsExport(), 'words.xlsx');
                break;
        }
        return back();
    }

    public function pay(Request $request)
    {
        $token = $request->token;
        $package_id = $request->package_id;
        $user = User::whereToken($token)->first();
        if($user)
        {
            $user_id = $user->id;

            //check if user buy package in the last
            $checkPackageUser = Transaction::whereUser_id($user_id)->wherePackage_id($package_id)->whereTransaction_status('100')->first();

            if($checkPackageUser)
            {
                $data['message'] = "شما قبلاً این پکیج را خریداری کرده اید";
                $data['state'] = 'error';
                return view('test.paymentVerification' , compact('data'));
                //ridirect to paymentVerification to show the error message
                //then to paymentErrorPage
            }

            $amount = Package::whereId($package_id)->first()->price;

            return Payment::purchase(
                (new Invoice)->amount($amount),
                function($driver, $transactionId) use($package_id,$user_id,$amount) {
                    // store transactionId in database.
                    // we need the transactionId to verify payment in future

                    Transaction::create([
                        'package_id' => $package_id,
                        'user_id' => $user_id,
                        'payed_amount' =>$amount,
                        'transaction_id' => $transactionId,
                        'transaction_status' => null
                    ]);

                }
            )->pay();
        }else{;
            $data['message'] = "کاربری وجود ندارد";
            $data['state'] = 'error';
            return view('test.paymentVerification' , compact('data'));
            //ridirect to paymentVerification to show the error message
            //then to paymentErrorPage
        }
    }

    public function paymentVerification(Request $request)
    {

        $transaction_id = $request->Authority;
        $status = $request->Status;

        try {
            $transaction = Transaction::whereTransaction_id($transaction_id)->first();
            $amount = $transaction->payed_amount;
            Payment::amount($amount)->transactionId($transaction_id)->verify();
            $transaction->update(['transaction_status' => 100]);

            $data['message'] = "پرداخت با موفقیت انجام شد";
            $data['state'] = 'success';
            return view('test.paymentVerification' , compact('data'));
            //ridirect to paymentVerification to show the success message
            //then to paymentSuccessPage

        } catch (InvalidPaymentException $exception) {
            $transaction = Transaction::whereTransaction_id($transaction_id)->where('transaction_status' , '!=' , 100)->first();
            if($transaction){
                $transaction->update(['transaction_status' => 400]);
            }
            $data['message'] = $exception->getMessage();
            $data['state'] = 'error';
            return view('test.paymentVerification' , compact('data'));
            //ridirect to paymentVerification to show the error message
            //then to paymentErrorPage
        }
    }

    public function paymentErrorPage()
    {
        return view('test.paymentErrorPage');
    }

    public function paymentSuccessPage()
    {
        return view('test.paymentSuccessPage');
    }

    public function word(Request $request)
    {
        /*$words = Word::select(['id', 'word', 'phonetic', 'english_meaning',
            'persian_meaning' ,'english_example', 'persian_example' , 'image_path'])->orderBy('id' , 'ASC');*/
            
            
            
        $word1 = $request->has('word1')?$request->input('word1',''):'' ;


        if ($word1)
        {
            $words = Word::select(['id', 'word', 'phonetic', 'english_meaning',
                'persian_meaning' ,'english_example', 'persian_example' , 'image_path'])->where('word' , '=' , $word1)->get();
        }else{
            $words = Word::select(['id', 'word', 'phonetic', 'english_meaning',
                'persian_meaning' ,'english_example', 'persian_example' , 'image_path']);
        }
            
            

        $dt=Datatables::of($words);


        $dt->addColumn('edit', function ($word) {
            return '<a href="'.route('words.edit' , $word->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        });

        $dt->addColumn('delete', function ($word) {
            return '<a id="delete" data-id="'.$word->id.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-edit"></i> Delete</a>';
        });

        $dt->editColumn('image_path', function ($word) {
            if ($word->image_path != "" )
            {
                return '<img class="img-fluid" with=100 height=100 src="'.asset('images/'.$word->image_path).'">';
            }
        });

        return $dt->escapeColumns(null)->make(true);
    }

    public function category1(Request $request)
    {
        

        $parent_category_id = $request->parent_category_id;
 

        $subCats = Category::where('id', $parent_category_id)->get();
        //return $subCats;

        $cats = Category::select(['id', 'category_name', 'description', 'parent_category_id' , 'image_path' , 'list_type' , 'is_free' , 'is_published'])->orderBy('id' , 'ASC');
        
       if ($parent_category_id)
        {
            $categories = $subCats;
        }else{
            $categories = $cats;
        }

    
        $dt=Datatables::of($categories);

        $dt->addColumn('edit', function ($category) {
            return '<a href="'.route('categories.edit' , $category->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        });

        $dt->addColumn('delete', function ($category) {
            return '<a id="delete" data-id="'.$category->id.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-edit"></i> Delete</a>';

        });

        $dt->editColumn('is_free', function ($category) {
            if ($category->is_free == 1)
            {
                return '<span class="btn btn-info font-small text-info">Free</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">Cash</span>';
        });

        $dt->editColumn('list_type', function ($category) {
            switch ($category->list_type){
                case 1:
                    return '<span class="btn btn-info font-small text-info">کتاب</span>';
                    break;
                case 2:
                    return '<span class="btn btn-info font-small text-info">سر فصل</span>';
                    break;
                case 3:
                    return '<span class="btn btn-info font-small text-info">کلمه</span>';
                    break;
                default:
                    return '<span class="btn btn-info font-small text-info">کتاب</span>';
            }


        });

        $dt->editColumn('is_published', function ($category) {
            if ($category->is_published == 1)
            {
                return '<span class="btn btn-info font-small text-info">منتشر شد</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">منتشر نشد</span>';
        });

        $dt->editColumn('parent_category_id', function ($category) {
            $name = Category::where('id' , $category->parent_category_id)->first();
            if ($name)
            {
                return $name->category_name;
            }
        });

        $dt->editColumn('image_path', function ($word) {
            if ($word->image_path != "" )
            {
                return '<img class="img-fluid" with=100 height=100 src="'.asset('images/'.$word->image_path).'">';
            }
        });

        return $dt->escapeColumns(null)->make(true);
    }

    public function wordCategoryTable(Request $request)
    {


        $word = $request->has('word_id')?$request->input('word_id',''):'' ;
        $book_category_id = $request->has('book_category_id')?$request->input('book_category_id',''):'' ;



        $wordCategories = DB::table('word_categories')
            ->join('words', 'words.id', '=', 'word_categories.word_id')
            ->join('categories', 'categories.id', '=', 'word_categories.book_category_id')
            ->orWhere(function ($q) use($word){
                if($word !== "0")
                    return $q->where('word_categories.word_id','=', $word);
                return 1;
            })

            ->orWhere(function ($q) use($book_category_id){
                if($book_category_id !== "0")
                    return $q->where('word_categories.book_category_id','=', $book_category_id);
                return 1;
            })

            ->orderby('word_categories.id','ASC')
            ->select('word_categories.*' , 'categories.category_name as CatName','categories.image_path as image' , 'words.word as wordName');




        /*$wordCategories = WordCategory::select(['id', 'word_id', 'category_id', 'book_category_id'])->latest();*/


        $dt = Datatables::of($wordCategories);

        $dt->addColumn('edit', function ($wordCategory) {
            return '<a href="'.route('wordCategories.edit' , $wordCategory->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        });

        $dt->addColumn('delete', function ($wordCategory) {
            return '<a id="delete" data-id="'.$wordCategory->id.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-edit"></i> Delete</a>';
        });

        $dt->editColumn('category_id', function ($wordCategory) {
            return Category::where('id' , $wordCategory->category_id)->first()->category_name;
        });

        $dt->editColumn('book_category_id', function ($wordCategory) {
            return Category::where('id' , $wordCategory->book_category_id)->first()->category_name;
        });

        $dt->editColumn('word_id', function ($wordCategory) {
            return Word::where('id' , $wordCategory->word_id)->first()->word;
        });

        return $dt->escapeColumns(null)->make(true);
    }
    
        public function listeningDataTable(Request $request)
    {

        $listenings = Listening::get();

        $dt=Datatables::of($listenings);

        $dt->addColumn('edit', function ($listening) {
            return '<a href="'.route('listenings.edit' , $listening->id).'" class="btn btn-sm btn-block btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        });

        $dt->addColumn('delete', function ($listening) {
            return '<a id="delete" data-id="'.$listening->id.'" class="btn btn-sm btn-block btn-danger"><span class="fa fa-trash ml-3" style="margin-left: 3px;"></span>Delete</a>';

        });

        $dt->editColumn('is_free', function ($listening) {
            if ($listening->is_free == 1)
            {
                return '<span class="btn btn-info font-small text-info">Free</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">Cash</span>';
        });

        $dt->editColumn('category_id', function ($listening) {
            if ($listening->category_id)
            {
                $name = Category::where('id' , $listening->category_id)->first();
                if($name){
                    return $name->category_name;
                }
                return '--';
            }
        });


        $dt->editColumn('is_published', function ($listening) {
            if ($listening->is_published == 1)
            {
                return '<span class="btn btn-info font-small text-info">منتشر شد</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">منتشر نشد</span>';
        });



        /*$dt->editColumn('image_path', function ($word) {
            if ($word->image_path != "" )
            {
                return '<img class="img-fluid" with=100 height=100 src="'.asset('images/'.$word->image_path).'">';
            }
        });*/

        return $dt->escapeColumns(null)->make(true);
    }

    public function listeningCategoryDataTable(Request $request)
    {
        $categories = Category::whereCategory_type(1)->get();

        $dt=Datatables::of($categories);

        $dt->addColumn('edit', function ($categories) {
            return '<a href="'.route('listeningCategories.edit' , $categories->id).'" class="btn btn-sm btn-block btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
        });

        $dt->addColumn('delete', function ($categories) {
            return '<a id="delete" data-id="'.$categories->id.'" class="btn btn-sm btn-block btn-danger"><span class="fa fa-trash ml-3" style="margin-left: 3px;"></span>Delete</a>';

        });

        $dt->editColumn('is_free', function ($categories) {
            if ($categories->is_free == 1)
            {
                return '<span class="btn btn-info font-small text-info">Free</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">Cash</span>';
        });


        $dt->editColumn('is_published', function ($categories) {
            if ($categories->is_published == 1)
            {
                return '<span class="btn btn-info font-small text-info">منتشر شد</span>';
            }
            return '<span class="btn btn-warning font-small text-danger">منتشر نشد</span>';
        });



        $dt->editColumn('image_path', function ($categories) {
            if ($categories->image_path != "" )
            {
                return '<img class="img-fluid" with=100 height=100 src="'.asset('category/'.$categories->image_path).'">';
            }
        });

        return $dt->escapeColumns(null)->make(true);
    }
    
    public function uploadImage()
    {
        $destination = 'images/';
        $entries = scandir($destination);
        $attachments = array();
        foreach($entries as $entry) {
            $attachments[] = $entry;
            unset($attachments[0]);
            unset($attachments[1]);
        }
        return view('Admin.uploadImage.create',compact('attachments'));
    }
    
    public function uploadImageInDirectory(Request $request)
    {
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
         alert()->error($msg, 'پیام سیستم')->autoclose(6000);
        return back();
    }

}
