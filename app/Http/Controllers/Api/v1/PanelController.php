<?php

namespace App\Http\Controllers\Api\v1;

use App\Category;
use App\MobileVerificationCode;
use App\MobileVertificationCode;
use App\Package;
use App\PackageBooks;
use App\Transaction;
use App\User;
use App\Word;
use App\WordBackUp;
use App\WordCategory;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use nusoap_client;
use Shetabit\Payment\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;
use Shetabit\Payment\Invoice;
use Shetabit\Payment\PaymentManager;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use nusoap_base;
use soapclient_nu;
use ZipArchive;

class PanelController extends Controller
{
    protected $MerchantID = 'e208bc00-b292-11e9-87cb-000c29344814'; //Required
    protected $Amount = '100'; //Required
    const LoginExpirationInterval = 24;
    /*
     * UPDATE `word_categories` SET `book_category_id` = REPLACE(`book_category_id`, '0', '1') WHERE  `book_category_id` LIKE '%0%';
     * */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login']]);
    }


    public function loginSubmitCredentials(Request $request)
    {

        $user = User::where('mobile_number', '=', $request->mobile_number)->first();
        $credentials = $request->only('mobile_number' , 'password');
        if($user)
        {
            if (!$useToken = JWTAuth::attempt($credentials))
            {
                $data['msg'] = "کاربری موجود میباشد. برای بازیابی رمز اقدام بفرمایید.";
                $data['status'] = 403;
                $data['success'] = 1;
                return response()->json($data);
            }


            $code = rand(11111, 99999);

            $checkCodes = MobileVerificationCode::whereMobile_number($request->mobile_number)->get();
            if ($checkCodes) {
                foreach ($checkCodes as $checkCode) {
                    $checkCode->delete();
                }
            }

            MobileVerificationCode::create([
                'mobile_number' => $request->mobile_number,
                'code' => $code
            ]);


            // User::whereId($user->id)->update(['token'=>$useToken]);
            $data['status'] = 200;
            $data['success'] = 0;
            $data['msg'] = "پیام با موفقیت ارسال شده است.";
            $data['code'] = $code;

            return response()->json($data);
        }else{

            $userWithWrongPass = User::where('mobile_number', '=', $request->mobile_number)->first();

            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 401;
            $data['success'] = 2;
            return response()->json($data);
        }


    }

    public function loginReceiveToken(Request $request){
        $findMobileCode = MobileVerificationCode::whereCode($request->code)->whereMobile_number($request->mobile_number)->first();

        if ($findMobileCode)
        {
            $user = User::where('mobile_number', '=', $request->mobile_number)->first();
            $credentials = $request->only('mobile_number' , 'password');

            if (!$useToken = JWTAuth::attempt($credentials))
            {
                $data['msg'] = "کاربری موجود میباشد. برای بازیابی رمز اقدام بفرمایید.";
                $data['status'] = 403;
                $data['success'] = 1;
                return response()->json($data);
            }

            User::whereId($user->id)->update(['token'=>$useToken]);
            $data = $this->getInitialDataFromToken($useToken);

            return response()->json($data);

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 404;
            return response()->json($data);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'name' => 'required',
            'code' => 'required',
            'password' => 'required',
            //'confirmPass' => 'required',
            'mobile_number' => 'required'
        ]);


        if ($validator->fails()) {
            $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
            $data['status'] = 400;
            $data['success'] = 2;
            return response()->json($data);
        }


        //check mobile
        $userExist = User::whereMobile_number($request->mobile_number)->first();
        if ($userExist) {
            $data['msg'] = "با این شماره قبلا ثبت نام شده است!!";
            $data['status'] = 402;
            $data['success'] = 3;
            return response()->json($data);
        }


        $findMobileCode = MobileVerificationCode::whereCode($request->code)->whereMobile_number($request->mobile_number)->first();
        //$findMobileCode = MobileVerificationCode::where('mobile_number' , '=' , $request->mobile_number)->where('code' ,'=' , $request->code)->first();

        //dd($findMobileCode);

        if ($findMobileCode) {
            $user = User::create([
                //'name'=>$request->get('name'),
                //'email'=>$request->get('email'),
                'password' => bcrypt($request->get('password')),
                'mobile_number' => $request->get('mobile_number')
            ]);
            //$user = User::first();
            $token = JWTAuth::fromUser($user);

            User::whereId($user->id)->update(['token' => $token]);

            $data['success'] = 0;
            $data['msg'] = "اطلاعات با موفقیت ثبت شده است";
            $data['token'] = $token;
            $data['user'] = $user;
            $data['status'] = 200;
            return response()->json($data);
        } else {
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیاباشد";
            $data['status'] = 409;
            return response()->json($data);
        }


    }

    public function sms($mobile, $code)
    {
        $sms_username = 'amiri';
        $sms_password = 'Ty@12Tn';
        $sms_number = '30007654322427';

        require_once('nusoap.php');

        $client = new nusoap_client('http://mihansmscenter.com/webservice/?wsdl', 'wsdl');
        $client->decodeUTF8(false);

        //send a message to a number
        $res = $client->call('send', array(
            'username' => $sms_username,
            'password' => $sms_password,
            'to' => $mobile,
            'from' => $sms_number,
            'message' => 'تایید کد ارسالی :' . $code,
            'send_time' => strtotime('2009-09-17 15:50') // set this parameter to null if you dont want to schedule message
        ));

        if (is_array($res) && isset($res['status']) && $res['status'] === 0) {
            //echo "message successfully sent.";
            $data['success'] = 0;
            $data['msg'] = "کد فعال سازی برای شما ارسال شد.";
            $data['status'] = 200;
            return response()->json($data);
        } else echo "Error :" . @$res['status_message'];
        //print_r($res);
    }

    public function submitMobileNumber(Request $request)
    {
        if(!$request->mobile_number){
            $data['success'] = 0;
            $data['msg'] = "اطلاعات وارد شده صحیح نمیباشد";
            $data['status'] = 400;
            return response()->json($data);
        }

        $check = User::where('mobile_number' , $request->mobile_number)->first();

        if ($check)
        {
            $data['success'] = 0;
            $data['msg'] = "کاربری با این مشخصات از قبل در سیستم موجود میباشد";
            $data['status'] = 409;
            return response()->json($data);
        }else{
            $code = rand(11111,99999);
            //$this->sms($request->mobileNumber , $code);

            $checkCodes = MobileVerificationCode::where('mobile_number',$request->mobile_number)->get();
            if ($checkCodes)
            {
                foreach ($checkCodes as $checkCode)
                {
                    $checkCode->delete();
                }
            }

            MobileVerificationCode::create([
                'mobile_number' => $request->mobile_number,
                'code' => $code
            ]);

            $data['success'] = 0;
            $data['msg'] = "پیام با موفقیت ارسال شده است.";
            $data['status'] = 200;
            $data['code'] = $code;
            return response()->json($data);

        }
    }


    public function submitVerificationCode(Request $request)
    {
        $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

        if ($findMobileCode)
        {

            $created_at =$findMobileCode->created_at;
            $now = Carbon::now();

            $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
            $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
            $interval = $start_date->diff($end_date);
            $minutes = $interval->format('%i');

            if ($minutes > 1)
            {
                $data['success'] = 1;
                $data['msg'] = "کد شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 404;
            return response()->json($data);
        }


        $data['success'] = 0;
        $data['msg'] = "اطلاعات صحیح میباشد";
        $data['status'] = 200;
        return response()->json($data);

    }

    public function upload(Request $request)
    {
        /*if ($request->file('image'))
        {
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();

            $filePath = "images/";

            $file->move($filePath , $fileName);
        }*/

        $name = $request->name;

        DB::table('test')->insert(
            ['name' => $name]
        );

        $data['msg'] = "Upload Ok!";
        $data['success'] = 0;
        return response()->json($data);
    }

    public function getAllPackages(Request $request)
    {

        $header = $request->header('Authorization');

        $user = User::whereToken($header)->first();

        if ($user){

            if(!$this->tokenIsValid($user)){
                $data['success'] = 1;
                $data['msg'] = "توکن شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

            $allPackages = Package::all();

            $data['success'] = 0;
            $data['msg'] = "اطلاعات دریافت شد";
            $data['allPackages'] = $allPackages;
            // $data['user'] = $user;
            $data['status'] = 200;
            return response()->json($data);
        }else
            $data['success'] = 1;
        $data['msg'] = "کاربری با این مشخصات یافت نشد";
        //$data['user'] = $user;
        $data['status'] = 401;
        return response()->json($data);

    }


    public function submitTransaction(Request $request)
    {
        $header = $request->header('Authorization');

        $user = User::whereToken($header)->first();

        if ($user){

            if(!$this->tokenIsValid($user)){
                $data['success'] = 1;
                $data['msg'] = "توکن شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

            $check = Transaction::where('user_id' , $user->id)->where('package_id' , $request->package_id)->first();
            if ($check)
            {
                $data['success'] = 1;
                $data['msg'] = "کاربر قبلا این پکیج را خریداری کرده است!";
                $data['status'] = 409;
                return response()->json($data);

            }else{
                Transaction::create([
                    'user_id' => $user->id,
                    'package_id' => $request->package_id,
                    'payed_amount' => $request->payed_amount
                ]);
                $data['success'] = 0;
                $data['msg'] = "اطلاعات ثبت شد";
                $data['status'] = 200;
                return response()->json($data);
            }
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری یافت نشد";
            $data['status'] = 401;
            return response()->json($data);
        }
    }


    public function getPackageWords(Request $request)
    {
        $header = $request->header('Authorization');

        $user = User::whereToken($header)->first();

        if ($user)
        {

            if(!$this->tokenIsValid($user)){
                $data['success'] = 1;
                $data['msg'] = "توکن شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

            $check = Transaction::where('user_id' , $user->id)->where('package_id' , $request->package_id)->first();
        

            if ($check)
            {
                $bookCategoryIds = $this->getBookCategoryIdsByPackageIds($request->package_id);

                $wordCategoryIds = array();
                $wordCategorys = WordCategory::whereIn('book_category_id' , $bookCategoryIds)->get();

                foreach ($wordCategorys as $category)
                {
                    array_push($wordCategoryIds , $category->word_id);
                }

                $words = Word::whereIn('id' , $wordCategoryIds)->get();

                $data['success'] = 1;
                $data['msg'] = "لیست کلمات با موفقیت دریافت شد";
                $data['words'] = $words;
                $data['status'] = 200;
                return response()->json($data);

            }else{

                $data['success'] = 1;
                $data['msg'] = "پکیج مورد نظر موجود نمیابشد";
                $data['status'] = 404;
                return response()->json($data);

            }

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری یافت نشد";
            $data['status'] = 401;
            return response()->json($data);
        }
    }


    public function getUserPackages(Request $request)
    {
        $token = $request->header('Authorization');

        $user = User::whereToken($token)->first();

        if ($user){

            if(!$this->tokenIsValid($user)){
                $data['success'] = 1;
                $data['msg'] = "توکن شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

            $userPackageIds = Transaction::where('user_id' , $user->id)->pluck('package_id');
            
            if($userPackageIds){
                $packages = Package::whereIn('id' , $userPackageIds)->get();
                $data['success'] = 0;
                $data['msg'] = "لیست پکیج ها با موفقیت دریافت شد";
                $data['packages'] = $packages;
                $data['status'] = 200;
                return response()->json($data);
            }else{
                $data['success'] = 1;
                $data['msg'] = "کاربری با این مشخصات یافت نشد";
                $data['status'] = 400;
                return response()->json($data);
            }


        }else
            $data['success'] = 1;
        $data['msg'] = "کاربری با این مشخصات یافت نشد";
        $data['status'] = 400;
        return response()->json($data);

    }
    
    public function getUserWords(Request $request)
    {
        $header = $request->header('Authorization');

        $user = User::whereToken($header)->first();


        if ($user){

            if(!$this->tokenIsValid($user)){
                $data['success'] = 1;
                $data['msg'] = "توکن شما منقضی شده است";
                $data['status'] = 401;
                return response()->json($data);
            }

            $userPackageIds = Transaction::where('user_id' , $user->id)->pluck('package_id');
            
            if($userPackageIds){
                
                $bookCategoryIds = array();
                
                foreach($userPackageIds as $userPackageId){
                    $thisBookCategoryIds = $this->getBookCategoryIdsByPackageIds($userPackageId);
                    $bookCategoryIds = array_merge($bookCategoryIds,$thisBookCategoryIds);
                }
                
                $wordCategoryIds = array();
                $wordCategorys = WordCategory::whereIn('book_category_id' , $bookCategoryIds)->get();

                foreach ($wordCategorys as $category)
                {
                    array_push($wordCategoryIds , $category->word_id);
                }

                $words = Word::whereIn('id' , $wordCategoryIds)->get();
                
                $data['success'] = 0;
                $data['msg'] = "لیست کلمات با موفقیت دریافت شد";
                $data['words'] = $words;
                $data['status'] = 200;
                return response()->json($data);
                
            }else{
                $data['success'] = 1;
                $data['msg'] = "کاربری با این مشخصات یافت نشد";
                $data['status'] = 400;
                return response()->json($data);
            }


        }else
            $data['success'] = 1;
        $data['msg'] = "کاربری با این مشخصات یافت نشد";
        $data['status'] = 400;
        return response()->json($data);
    }


    public function recoverPassSubmitMobile(Request $request)
    {
        $check = User::whereMobile_number($request->mobile_number)->first();

        if ($check)
        {
            $code = rand(11111,99999);
            //$this->sms($request->mobileNumber , $code);

            $checkCodes = MobileVerificationCode::whereMobile_number($request->mobile_number)->get();
            if ($checkCodes)
            {
                foreach ($checkCodes as $checkCode)
                {
                    $checkCode->delete();
                }
            }

            MobileVerificationCode::create([
                'mobile_number' => $request->mobile_number,
                'code' => $code
            ]);

            $data['success'] = 0;
            $data['msg'] = "پیام با موفقیت ارسال شده است.";
            $data['status'] = 200;
            $data['code'] = $code;
            return response()->json($data);

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری یافت نشد!!!";
            $data['status'] = 404;
            return response()->json($data);
        }
    }

    public function recoverPassSubmitPassword(Request $request)
    {
        $findMobileCode = MobileVerificationCode::whereCode($request->code)->whereMobile_number($request->mobile_number)->first();

        if ($findMobileCode)
        {
            $checkUser = User::whereMobile_number($request->mobile_number)->first();

            if ($checkUser)
            {
                $checkUser->update([
                    'password' => bcrypt($request->password)
                ]);

                $data['success'] = 0;
                $data['msg'] = "پس ورد با موفقیت تغییر یافت!!!";
                $data['status'] = 200;
                return response()->json($data);

            }else{
                $data['success'] = 1;
                $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
                $data['status'] = 404;
                return response()->json($data);
            }

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 404;
            return response()->json($data);
        }
    }

    public function backupUserWords(Request $request)
    {
        $user = User::whereToken($request->header('Authorization'))->first();

        if($user){
            $checkBackUps = WordBackUp::whereUser_id($user->id)->get();

            if ($checkBackUps)
            {
                foreach ($checkBackUps as $checkBackUp)
                {
                    $checkBackUp->delete();
                }
            }
            
            $backupWordsArray = $request->leitnerWordList;
            if(!is_array($backupWordsArray))  $backupWordsArray = json_decode($request->leitnerWordList,true);
            
            foreach($backupWordsArray as $backupWord){
                WordBackUp::create([
                    'word_id' => $backupWord['word_id'],
                    'user_id' => $user->id,
                    'user_note' => $backupWord['user_note'],
                    'leitner_level' => $backupWord['leitner_level'],
                    'last_leitner_date' => $backupWord['last_leitner_date'],
                ]);
            }

            $data['success'] = 0;
            $data['msg'] = "اطلاعات با موفقیت ثبت شده است.";
            $data['status'] = 200;
            return response()->json($data);
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری یافت نشد.";
            $data['status'] = 404;
            return response()->json($data);
        }

    }

    public function backupGetUserWords(Request $request)
    {
        $user = User::whereToken($request->header('Authorization'))->first();

        if ($user)
        {
            $backUpWords = DB::table('word_back_ups')
                // ->join('words' , 'words.id' , '=' , 'word_back_ups.word_id')
                // ->select('words.*' , 'word_back_ups.leitner_level as leitner_level' , 'word_back_ups.user_note as user_note')
                ->where('user_id' , $user->id)->get();

            $data['success'] = 0;
            $data['backUpWords'] = $backUpWords;
            $data['msg'] = "اطلاعات دریافت شد.";
            $data['status'] = 200;
            return response()->json($data);
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری یافت نشد.";
            $data['status'] = 404;
            return response()->json($data);
        }


    }

    public function getImages()
    {
       
        $destination = 'images/';
         $entries = scandir($destination);
       
         $attachments = array();
         foreach($entries as $entry) {
             //if (strpos($entry, $id."_") === 0) {
                
                 $attachments[] = $entry;
             unset($attachments[0]);
             unset($attachments[1]);
             //}
         }
         
            $t = array();
     /*    foreach($attachments as $attachment) {
            
             $test = explode('.' , $attachment);
            
           
                $words = Word::all();
                foreach($words as $word)
                {
                    if($test[0] == $word->image_path)
                    {
                        $t = Word::where('image_path' , $word->image_path)->first();
                        
                        $t->update([
                             'image_path' => '/images/'.$attachment
                            
                            ]);
                        
                    }
                    
                }
         
            
         }*/
         
         
         
        /*$zip_file = 'invoices.zip';
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $path = public_path('images/');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {

            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = 'images/' . substr($filePath, strlen($path));

                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return response()->download($zip_file);*/


    }

    public function getImage($image)
    {
        $filepath = public_path('images/') . $image;
        return Response::download($filepath);
    }

    public function getPackageImages(Request $request, $package_id)
    {
        $header = $request->header('Authorization');

        $user = User::whereToken($header)->first();

        if ($user) {
            $package_parents = Package::where('parent_package_id' , $package_id)->get();

            foreach ($package_parents as $package_parent)
            {
                $package_childes = Package::where('parent_package_id' , $package_parent->id)->get();
                $bookCategoryIds = array();
                foreach ($package_childes as $child)
                {
                    if ($child->book_category_id != 0)
                    {
                        array_push($bookCategoryIds , $child->book_category_id);
                    }

                }

                //get all word ids
                $wordIds = WordCategory::whereIn('book_category_id' , $bookCategoryIds)->pluck('word_id');
                //get words
                $words = Word::whereIn('id', $wordIds)->select('id', 'word')->get();

                $data['success'] = 0;
                $data['words'] = $words;
                $data['msg'] = "اطلاعات دریافت شد.";
                $data['status'] = 200;
                return response()->json($data);
                
                
                $redis = Redis::connection();
                $redis->set('test', json_encode($words));

                if ($redis->get('test')) {
                return json_decode($redis->get('test'));
                }
                
                
                
            }


        }else{
            $data['success'] = 1;
            $data['msg'] = "اطلاعات دریافت نشد.";
            $data['status'] = 404;
            return response()->json($data);
        }

    }

    public function tokenIsValid(User $user)
    {
        $updated_at =$user->updated_at;
        $now = Carbon::now();

        $start_date = new DateTime($updated_at,new DateTimeZone('Pacific/Nauru'));
        $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
        $interval = $start_date->diff($end_date);
        $hours = $interval->format('%h');

        if($hours > PanelController::LoginExpirationInterval)
            return false;

        return true;

    }
    
    function getPackageChildrenByPackageId($package_id) {
    $book_category_data = array();
    
    $package_query = DB::select('select * from packages where parent_package_id = ?', [$package_id]);

    foreach ($package_query as $package) {
        $book_category_data[] = array(
            'id' => $package->id,
            'name' => $package->name,
            'description' => $package->description,
            'price' => $package->price,
            'parent_package_id' => $package->parent_package_id,
            'book_category_id' => $package->book_category_id,
            'imagePath' => $package->imagePath,
            'created_at' => $package->created_at,
            'updated_at' => $package->updated_at,
        );

        $children = $this->getPackageChildrenByPackageId($package->id);

        if ($children) {
            $book_category_data = array_merge($children, $book_category_data);
        }           
    }

    return $book_category_data;
    }
    
    function getBookCategoryIdsByPackageIds($package_id){
        $bookCategoryIds = array();
        $root_package = Package::where('id'  , $package_id)->first();
        
        if($root_package->book_category_id != 0){
            array_push($bookCategoryIds , $root_package->book_category_id);
        }else{
            $book_category_array = $this->getPackageChildrenByPackageId($package_id);
            foreach($book_category_array as $book_category){
                if($book_category['book_category_id']!= 0){
                    array_push($bookCategoryIds , $book_category['book_category_id']); 
                }
            }
        }
        return $bookCategoryIds;
    }
    
    function getCategoryChildrenByCategoryId($category_id) {
        $category_data = array();
        
        $category_query = DB::select('select * from categories where parent_category_id = ?', [$category_id]);
    
        foreach ($category_query as $category) {
            $category_data[] = array(
                'id' => $category->id,
                'parent_category_id' => $category->parent_category_id,
                'category_name' => $category->category_name,
                'list_type' => $category->list_type,
                'image_path' => $category->image_path,
                'is_delete' => $category->is_delete,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            );
    
            $children = $this->getCategoryChildrenByCategoryId($category->id);
    
            if ($children) {
                $category_data = array_merge($children, $category_data);
            }           
        }
    
        return $category_data;
    }
    
    function getUserCategoriesByCategoryId($category_id){
        $category_data = $this->getCategoryChildrenByCategoryId($category_id);
        $root_category = Category::Where('id',$category_id)->first();
        $root_category_data[] = array(
                'id' => $root_category->id,
                'parent_category_id' => $root_category->parent_category_id,
                'category_name' => $root_category->category_name,
                'list_type' => $root_category->list_type,
                'image_path' => $root_category->image_path,
                'is_delete' => $root_category->is_delete,
                'created_at' => $root_category->created_at,
                'updated_at' => $root_category->updated_at,
            );
        $category_data = array_merge($root_category_data, $category_data);
        return $category_data;
    }
    
    function getInitialDataFromToken($token){
        
        $loggedInUser = User::whereToken($token)->first();
        
        $data['status'] = 200;
        $data['success'] = 0;
        $data['msg'] = "کاربر با موفقیت وارد شد!";
        $data['token'] = $token;        
        $data['userPackages'] = [];
        $data['userWords'] = [];
        $data['allPackages'] = [];
        $data['userWordCategories'] = [];
        $data['userCategories'] = [];
        
        //get all packages
        $allPackages = Package::all();
        $data['allPackages'] = $allPackages;
        //end of get all packages
        
                
        //check if user has any packages in transactions table to get user's packages and words 
        $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->pluck('package_id');
        if($userPackageIds){
            
            //get user packages
            $packages = Package::whereIn('id' , $userPackageIds)->get();
            $data['userPackages'] = $packages;
            //end of get user packages
            
            //get user words
            $bookCategoryIds = array();
            foreach($userPackageIds as $userPackageId){
                $thisBookCategoryIds = $this->getBookCategoryIdsByPackageIds($userPackageId);
                $bookCategoryIds = array_merge($bookCategoryIds,$thisBookCategoryIds);
            }
            $userWordIds = array();
            //get all userWordCategories
            $userWordCategories = WordCategory::whereIn('book_category_id' , $bookCategoryIds)->get();
             $data['userWordCategories'] = $userWordCategories;
            //end of get all userWordCategories
            foreach ($userWordCategories as $userWordCategory)
            {
                array_push($userWordIds , $userWordCategory->word_id);
            }
            $userWords = Word::whereIn('id' , $userWordIds)->get();
            $data['userWords'] = $userWords;
            //end of get user words
            
            //get userCategories
            foreach($bookCategoryIds as $bookCategoryId){
                $data['userCategories'] = array_merge($this->getUserCategoriesByCategoryId($bookCategoryId),$data['userCategories']);
            }
            //end of get userCategories
            
        }
        //end of getting user bought packages and words info
        
        return $data;
    }
    

    
}
