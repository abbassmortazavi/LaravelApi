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
use App\Version;
use Trez\RayganSms\Facades\RayganSms;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use nusoap_base;
use soapclient_nu;
use ZipArchive;

class UserController extends PackageController
{

    public function __construct(){
        ob_start("ob_gzhandler");
    }
    
    
    public function submitVerificationCodeRegister(Request $request)
    {
        $request['password'] = "topleit";
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'mobile_number' => 'required',
            'mac_address' => 'required'
        ]);
        
        $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

        if ($findMobileCode)
        {
            $created_at = $findMobileCode->created_at;
            $now = Carbon::now();

            $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
            $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
            $interval = $start_date->diff($end_date);
            $minutes = $interval->format('%i');

            if ($minutes > 2)
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
        
        //check mobile
        $userExist = User::whereMobile_number($request->mobile_number)->first();
        if ($userExist) {
            $data['msg'] = "با این شماره قبلا ثبت نام شده است!!";
            $data['status'] = 402;
            $data['success'] = 3;
            return response()->json($data);
        }

        $user = User::create([
            'password' => bcrypt($request->get('password')),
            'mobile_number' => $request->get('mobile_number'),
            'mac_address' => $request->get('mac_address'),
        ]);

        $token = JWTAuth::fromUser($user);

        User::whereId($user->id)->update(['token' => $token]);

        $data['success'] = 0;
        $data['msg'] = "اطلاعات با موفقیت ثبت شده است";
        $data['token'] = $token;
        $data['user'] = $user;
        $data['status'] = 200;
        return response()->json($data);
        
    }
    
    public function loginWithMobileRecieveCode(Request $request)
    {
        $request["password"] = "topleit";
        $validator = Validator::make($request->all(), [
            // 'password'=>'required',
            'mobile_number' => 'required',
            'mac_address' => 'required'
        ]);

        if ($validator->fails()) {
            $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
            $data['status'] = 400;
            $data['success'] = 2;
            return response()->json($data);
        }

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

            //check mac address
            if ($user->mac_address != $request->mac_address) {
                $data['msg'] = "این شماره قبلا با دستگاه دیگری ثبت نام کرده است";
                $data['status'] = 490;
                $data['success'] = 3;
                
                     
                $smsContent ="کد انتقال اطلاعات به دستگاه فعلی تاپلایت\n".$code;
                if($this->sendSMS($user->mobile_number, $smsContent)){
                return response()->json($data);
                }else{
                    $data['success'] = 0;
                    $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                    $data['status'] = 450;
                }
                
                return response()->json($data);
            }

            // User::whereId($user->id)->update(['token'=>$useToken]);
            $data['status'] = 200;
            $data['success'] = 0;
            $data['msg'] = "پیام با موفقیت ارسال شده است.";
            
            $smsContent = "کد ورود به نرم افزار تاپلایت\n".$code;
            if($this->sendSMS($user->mobile_number, $smsContent)){
                return response()->json($data);
            }else{
                $data['success'] = 0;
                $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                $data['status'] = 450;
            }

            return response()->json($data);
        }else{

            $userWithWrongPass = User::where('mobile_number', '=', $request->mobile_number)->first();

            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 401;
            $data['success'] = 2;
            return response()->json($data);
        }

    }
    
    public function submitVerificationCodeLogin(Request $request){
        
        $request['password'] = "topleit";

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

              
            // $data = gzcompress(json_encode($data), 9);
            return response()->json($data);

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 404;
            return response()->json($data);
        }
    }
    
    public function submitVerificationCodeResetMacAddressAndLogin(Request $request)
    {
        $request['password'] = "topleit";
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'code' => 'required',
            'mac_address' => 'required'
        ]);

        if ($validator->fails()) {
            $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
            $data['status'] = 400;
            $data['success'] = 2;
            return response()->json($data);
        }

        $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

        if ($findMobileCode)
        {
            $created_at =$findMobileCode->created_at;
            $now = Carbon::now();

            $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
            $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
            $interval = $start_date->diff($end_date);
            $minutes = $interval->format('%i');

            if ($minutes > 20)
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

        $user = User::whereMobile_number($request->mobile_number)->first();
        $user->update(['mac_address'=>$request->mac_address]);
        
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

    }

    
    
    public function loginSubmitCredentials(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'mobile_number' => 'required',
            'mac_address' => 'required'
        ]);

        if ($validator->fails()) {
            $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
            $data['status'] = 400;
            $data['success'] = 2;
            return response()->json($data);
        }

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

            //check mac address
            if ($user->mac_address != $request->mac_address) {
                $data['msg'] = "این شماره قبلا با دستگاه دیگری ثبت نام کرده است";
                $data['status'] = 490;
                $data['success'] = 3;
                
                     
                $smsContent ="کد انتقال اطلاعات به دستگاه فعلی تاپلایت\n".$code;
                if($this->sendSMS($user->mobile_number, $smsContent)){
                return response()->json($data);
                }else{
                    $data['success'] = 0;
                    $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                    $data['status'] = 450;
                }
                
                return response()->json($data);
            }

            // User::whereId($user->id)->update(['token'=>$useToken]);
            $data['status'] = 200;
            $data['success'] = 0;
            $data['msg'] = "پیام با موفقیت ارسال شده است.";
            
            $smsContent = "کد ورود به نرم افزار تاپلایت\n".$code;
            if($this->sendSMS($user->mobile_number, $smsContent)){
                return response()->json($data);
            }else{
                $data['success'] = 0;
                $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                $data['status'] = 450;
            }

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

              
            // $data = gzcompress(json_encode($data), 9);
            return response()->json($data);

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 404;
            return response()->json($data);
        }
    }

    public function loginWithToken(Request $request){
        $appVersion = $request->version;

        $maxVersionRow = Version::orderBy('version', 'desc')->first();
        
        if($maxVersionRow->version > $appVersion){
            $data['success'] = 0;
            $data['msg'] = "لطفا برنامه را آپدیت کنید";
            $data['downloadPage'] = $maxVersionRow->downloadPage;
            $data['status'] = 480;
            return response()->json($data);
        }
        
        $token = $request->header('Authorization');
        $user = User::whereToken($token)->first();
        if($user){
            if (!$useToken = JWTAuth::refresh($token))
            {
                $data['msg'] = "کاربری موجود میباشد. برای بازیابی رمز اقدام بفرمایید.";
                $data['status'] = 403;
                $data['success'] = 1;
                return response()->json($data);
            }

            //check mac address
            if ($user->mac_address != $request->mac_address) {
                $data['msg'] = "این شماره قبلا با دستگاه دیگری ثبت نام کرده است";
                $data['status'] = 490;
                $data['success'] = 3;
                return response()->json($data);
            }

            User::whereId($user->id)->update(['token'=>$useToken]);
            $data = $this->getInitialDataFromToken($useToken);
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 400;
        }
        
        return response()->json($data);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'password' => 'required',
            'mobile_number' => 'required',
            'mac_address' => 'required'
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

        if ($findMobileCode) {
            $user = User::create([
                'password' => bcrypt($request->get('password')),
                'mobile_number' => $request->get('mobile_number'),
                'mac_address' => $request->get('mac_address'),
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
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 409;
            return response()->json($data);
        }
    }

    /*public function sendSMS($mobile, $message)
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
            'message' => $message,
            'send_time' => strtotime('2009-09-17 15:50') // set this parameter to null if you dont want to schedule message
        ));

        if (is_array($res) && isset($res['status']) && $res['status'] === 0) {
            //echo "message successfully sent.";
            $data['success'] = 0;
            $data['msg'] = "کد فعال سازی برای شما ارسال شد.";
            $data['status'] = 200;
            return response()->json($data);
        } else {
            // echo "Error :" . @$res['status_message'];
            return false;
        };
        //print_r($res);
    }*/

    public function sendSMS($mobile, $message)
    {
        $res = RayganSms::sendAuthCode($mobile,$message , false);
        if ($res)
        {
            $data['success'] = 0;
            $data['msg'] = "کد فعال سازی برای شما ارسال شد.";
            $data['status'] = 200;
            return response()->json($data);
        }else{
            return false;
        }
    }

    public function submitMobileNumber(Request $request)
    {
        if(!$request->mobile_number){
            $data['success'] = 0;
            $data['msg'] = "اطلاعات وارد شده صحیح نمیباشد";
            $data['status'] = 400;
            return response()->json($data);
        }

        $userExists = User::where('mobile_number' , $request->mobile_number)->first();
 
        if ($userExists)
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
            
            $smsContent = "کد احراز هویت نرم افزار تاپلایت\n".$code;
            if($this->sendSMS($request->mobile_number, $smsContent)){
                return response()->json($data);
            }else{
                $data['success'] = 0;
                $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                $data['status'] = 450;
            }
        }
    }

    // public function changeMobileNumber(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'code' => 'required',
    //         'password' => 'required',
    //         'mobile_number' => 'required',
    //         'new_mobile_number'=> 'new_mobile_number',
    //         'mac_address' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
    //         $data['status'] = 400;
    //         $data['success'] = 2;
    //         return response()->json($data);
    //     }

    //     $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

    //     if ($findMobileCode)
    //     {

    //         $created_at =$findMobileCode->created_at;
    //         $now = Carbon::now();

    //         $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
    //         $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
    //         $interval = $start_date->diff($end_date);
    //         $minutes = $interval->format('%i');

    //         if ($minutes > 1)
    //         {
    //             $data['success'] = 1;
    //             $data['msg'] = "کد شما منقضی شده است";
    //             $data['status'] = 401;
    //             return response()->json($data);
    //         }

    //     }else{
    //         $data['success'] = 1;
    //         $data['msg'] = "کاربری با این مشخصات یافت نشد";
    //         $data['status'] = 404;
    //         return response()->json($data);
    //     }

    //      //check mac address
    //     $userWithMacAddress = User::whereMac_address($request->mac_address)->first();
    //     if (!$userWithMacAddress) {
    //         $data['msg'] = "مشخصات دستگاه شما در سیستم موجود نیست";
    //         $data['status'] = 491;
    //         $data['success'] = 3;
    //         return response()->json($data);
    //     }

    //     $credentials = $request->only('mobile_number' , 'password');

    //     if (!$useToken = JWTAuth::attempt($credentials))
    //     {
    //         $data['msg'] = "کاربری با این مشخصات یافت نشد";
    //         $data['status'] = 404;
    //         $data['success'] = 1;
    //         return response()->json($data);
    //     }

    //     if($userWithMacAddress->update(['mobile_number'=>$request->new_mobile_number])){
    //         $data['msg'] = "اطلاعات با موفقیت به روز گردید";
    //         $data['status'] = 200;
    //         $data['success'] = 1;
    //         return response()->json($data);
    //     }else{
    //         $data['success'] = 1;
    //         $data['msg'] = "مشکل در به روز رسانی اطلاعات";
    //         $data['status'] = 500;
    //         return response()->json($data);
    //     }

    // }

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

            if ($minutes > 2)
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
    
    public function submitVerificationCodeResetMacAddress(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'code' => 'required',
            'mac_address' => 'required'
        ]);

        if ($validator->fails()) {
            $data['msg'] = "تمامی فیلد ها باید به صورت کامل تکمیل شوند";
            $data['status'] = 400;
            $data['success'] = 2;
            return response()->json($data);
        }

        $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

        if ($findMobileCode)
        {
            $created_at =$findMobileCode->created_at;
            $now = Carbon::now();

            $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
            $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
            $interval = $start_date->diff($end_date);
            $minutes = $interval->format('%i');

            if ($minutes > 2)
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

        $findUser = User::whereMobile_number($request->mobile_number)->first();
        $findUser->update(['mac_address'=>$request->mac_address]);

        $data['success'] = 0;
        $data['msg'] = "اطلاعات با موفقیت به روز شد";
        $data['status'] = 200;
        return response()->json($data);

    }

    public function recoverPassSubmitMobile(Request $request)
    {
        $user = User::whereMobile_number($request->mobile_number)->first();

        if ($user)
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
                 
            $smsContent = "کد مورد نیاز برای تغییر رمز تاپلایت\n".$code;
            if($this->sendSMS($user->mobile_number, $smsContent)){
                return response()->json($data);
            }else{
                $data['success'] = 0;
                $data['msg'] = "اس ام اس تبلیغاتی شما غیر فعال است";
                $data['status'] = 450;
            }
            
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

        $findMobileCode = MobileVerificationCode::whereCode($request->code)->where('mobile_number',$request->mobile_number)->first();

        if ($findMobileCode)
        {

            $created_at =$findMobileCode->created_at;
            $now = Carbon::now();

            $start_date = new DateTime($created_at,new DateTimeZone('Pacific/Nauru'));
            $end_date = new DateTime($now, new DateTimeZone('Pacific/Nauru'));
            $interval = $start_date->diff($end_date);
            $minutes = $interval->format('%i');

            if ($minutes > 2)
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

                    $checkUser = User::whereMobile_number($request->mobile_number)->first();

        if ($checkUser)
        {
            $checkUser->update([
                'password' => bcrypt($request->password)
            ]);

            $data['success'] = 0;
            $data['msg'] = "پسورد با موفقیت تغییر یافت!!!";
            $data['status'] = 200;
            return response()->json($data);

        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات موجود نمیباشد";
            $data['status'] = 404;
            return response()->json($data);
        }

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
    
    public function createBakcUpFile($backupDirectory, $data){
        //Naming your file
       $path= "your path";
       $dt = date("d.M.Y");//You can use untill hours or min or seconds, if you  planning to many changes "date("F j, Y, g:i a");"  http://php.net/manual/en/function.date.php
       $dir = $path.$dt; 

       //Check if file existis or create it
       if(!is_dir($dir)){//If dir not exists, create your file
            mkdir($dir);
       }
       else{//Or replace or Update it
           $h=  opendir($dir);
           while($file = readdir($h)){
                if($file !="." && $file != ".."){
                    unlink("$path$dt/".$file);
                }
            }
            closedir($h);
       }


       $myJSONobject= "your json object";
       //Your will store your data as a 1 line string using json_encode
       $myJSONstring = json_encode($myJSONobject);

       //Now you will open the file you have created and write the string just created
       $handle = fopen($dir,"w");
       fwrite($handle,$myJSONstring);
    }

}
