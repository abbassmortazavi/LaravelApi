<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Support\Facades\File;
use App\ListeningCategory;
use App\ListeningQuestion;
use App\Listening;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use nusoap_base;
use soapclient_nu;
use ZipArchive;
use App\Version;
class PackageController extends Controller
{
    
    const LoginExpirationInterval = 24;
    const AdminAccess = ["09361411664","09385019630" , '09395567608'];
    
    public function __construct(){
        ob_start("ob_gzhandler");
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

    public function getPackageWords(Request $request)
    {
        $token = $request->header('Authorization');
        $user = User::whereToken($token)->first();
        
        if($user){
            //check mac address
            if ($request -> mac_address != $user -> mac_address)
            {
                $data['success'] = 0;
                $data['msg'] = "دستگاه کاربر مطابقت ندارد";
                $data['status'] = 480;
                return response()->json($data);
            }
            $data = $this->getInitialDataFromToken($token, $request->package_id);
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 400;
        }

        return response()->json($data);

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


        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری با این مشخصات یافت نشد";
            $data['status'] = 400;
            return response()->json($data);
        }

    }
  
    public function getPackageImages(Request $request , $package_id)
    {
        // $header = $request->header('Authorization');
        $token = $request["Authorization"];
        
        $user = User::whereToken($token)->first();
        if ($user) {

            $bookCategoryIds = $this->getBookCategoryIdsByPackageIds($request->package_id);
            //get userCategories
            $userCategories = [];
            foreach($bookCategoryIds as $bookCategoryId){
                $userCategories =  array_merge($this->getUserCategoriesByCategoryId($bookCategoryId),$userCategories);
            }
            $userCategoryIds=array_pluck($userCategories, 'id');
            //get all word ids
            $wordIds = WordCategory::whereIn('book_category_id' , $userCategoryIds)->pluck('word_id');
            //get words
            $wordImages = Word::whereIn('id', $wordIds)->where('image_path', 'like', '%.jpg%')->select('id', 'word' , 'image_path')->get()->pluck("image_path");
        
            $zip_file = $this->createZipFileForDownload('images.zip','images/','wordImages/',$wordImages);
            
            return response()->download($zip_file);

        }else{
            $data['success'] = 1;
            $data['msg'] = "اطلاعات دریافت نشد.";
            $data['status'] = 404;
            return response()->json($data);
        }

    }
    
    public function getAllPackagesImages(Request $request)
    {
        $token = $request["Authorization"];
        $user = User::whereToken($token)->first();
 
        if($user)
        {
            $allPackagesImages = array_unique(Package::all()->pluck("imagePath")->toArray());
            
            $allCategoryImages = array_unique(Category::whereNotNull('image_path')->pluck("image_path")->toArray());
            
            $allPackagesImages = array_unique(array_merge($allPackagesImages,$allCategoryImages));
            
            $userImages = array();
            if($request["images"]) $userImages = array_unique($request["images"]);
  
            // $toDownloadImages = array_diff($allPackagesImages,$userImages);
            $toDownloadImages = $allPackagesImages;
            
            $zip_file = $this->createZipFileForDownload('images.zip','packageImages/','packageImages/',$toDownloadImages);
            
            return response()->download($zip_file);
       
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری وجود ندارد";
            $data['status'] = 404;
            return response()->json($data);
        }
       
    }
    
    public function getUserWordImages(Request $request){
        $token = $request["Authorization"];
        $user = User::whereToken($token)->first();
        // $imageString = trim(preg_replace('/\s+/','', $request["images"]));
        // $imageArray = explode(',', trim($imageString, "[]"));
        if($user)
        {
            $allUserWordsImagePath = $this->getUserImages($user);
            $zip_file = $this->createZipFileForDownload('images.zip','images/','wordImages/',$allUserWordsImagePath);
            return response()->download($zip_file);
        }else{
            $data['success'] = 1;
            $data['msg'] = "کاربری وجود ندارد";
            $data['status'] = 404;
            return response()->json($data);
        }
        
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
        $root_package = Package::where('id',$package_id)->first();
        
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
                // 'is_delete' => $category->is_delete,
                // 'created_at' => $category->created_at,
                // 'updated_at' => $category->updated_at,
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
            // 'is_delete' => $root_category->is_delete,
            // 'created_at' => $root_category->created_at,
            // 'updated_at' => $root_category->updated_at,
        );
        $category_data = array_merge($root_category_data, $category_data);
        
        //add main category to group books toghter
        $mainCategory = Category::where('id',$root_category->parent_category_id)->first();
        if($mainCategory){
            $main_category_data[] = array(
                'id' => $mainCategory->id,
                'parent_category_id' => $mainCategory->parent_category_id,
                'category_name' => $mainCategory->category_name,
                'list_type' => $mainCategory->list_type,
                'image_path' => $mainCategory->image_path,
                // 'is_delete' => $mainCategory->is_delete,
                // 'created_at' => $mainCategory->created_at,
                // 'updated_at' => $mainCategory->updated_at,
            );
            $category_data = array_merge($category_data, $main_category_data);
        }
        //end of adding main category to group books togheder
        
        return $category_data;
    }

    function getInitialDataFromToken($token, $package_id = null){

        $loggedInUser = User::whereToken($token)->first();
    
        $data['status'] = 200;
        $data['success'] = 0;
        
        if(in_array($loggedInUser->mobile_number,PackageController::AdminAccess)){
            $data['token'] = $token;
            $data['msg'] = "کاربر با موفقیت وارد شد!";
            $data['allCategories'] = Category::select('id','order_number','parent_category_id','category_type','category_name','description','list_type','image_path')->get();
            $counter = 0;
            foreach($data['allCategories'] as $category){
                $category->{"is_free"} = 1;
                $category->{"for_user"} = 1;
                $data['allCategories'][$counter] = $category;
                $counter++;
            }    
            $data['userPackages'] = [];
            $allPackages = Package::select('id','name','description','price','parent_package_id','book_category_id','imagePath')->get();
            $data['allPackages'] = $allPackages;
            $userWords = Word::select('id','word','phonetic','english_meaning','persian_meaning','english_example','persian_example','image_path')->get();
            $data['userWords'] = $userWords;
            $userWordCategories = WordCategory::select('id','word_id','category_id','book_category_id')->get();
            $data['userWordCategories'] = $userWordCategories;
            
            $listeningList = Listening::all();
            $data['listeningList'] = $listeningList;
                        
            // $wordCategoryWordIdMap = [];            
            // foreach($userWordCategories as $wordCategory){
            //     if(!isSet($wordCategoryWordIdMap[$wordCategory->word_id])){
            //         $wordCategoryWordIdMap[$wordCategory->word_id]["category_id_array"] = [];
            //         $wordCategoryWordIdMap[$wordCategory->word_id]["book_category_id_array"] = [];
            //     }
            //     array_push($wordCategoryWordIdMap[$wordCategory->word_id]["category_id_array"] , $wordCategory->category_id);
            //     array_push($wordCategoryWordIdMap[$wordCategory->word_id]["book_category_id_array"] , $wordCategory->book_category_id);
            // }        
            
            // $counter = 0;
            // foreach($userWords as $userWord){
            //     if(isset($wordCategoryWordIdMap[$userWord->id]["category_id_array"])){
            //         $userWord->{"category_id_array"} = $wordCategoryWordIdMap[$userWord->id]["category_id_array"];
            //         $userWord->{"book_category_id_array"} = $wordCategoryWordIdMap[$userWord->id]["book_category_id_array"];
            //         $userWords[$counter] = $userWord;
            //     }
            //     $counter++;
            // }
            
            // $data['userWords'] = $userWords;
            
            
            // $this->createBackupFile("cachFiles/","allCategories.json", $data);
            // $data = $this-> readCacheFile("allCategories.json");

            return $data;
        }

        if(!$package_id) 
        {
            $data['token'] = $token;
            $data['msg'] = "کاربر با موفقیت وارد شد!";
        }else{
           $data['msg'] = "اطلاعات پکیج کاربر با موفقیت دریافت شد";
        }

        $data['userPackages'] = [];
        $data['userCategories'] = [];
        $data['allCategories'] = Category::where('is_published',1)->select('id','order_number','parent_category_id','category_type','category_name','description','list_type','image_path','is_free')->get();
        
        //get free words and categories
        $freeCategoryIds = Category::where('is_free',1)->pluck('id');
        $freeWordCategories = WordCategory::whereIn('category_id' , $freeCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
        //end of free words and categories
        
        //get all packages
        if(!$package_id){
            $data['allPackages'] = [];
            $allPackages = Package::select('id','name','description','price','parent_package_id','book_category_id','imagePath')->where("is_published","1")->get();
            $data['allPackages'] = $allPackages;
        }
        //end of get all packages

        //check if user has any packages in transactions table to get user's packages and words
        if($package_id){
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->where('package_id',$package_id)->pluck('package_id');
        }else{
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->pluck('package_id');
        }
        
        if($userPackageIds){
            //get user packages
            $packages = Package::whereIn('id' , $userPackageIds)->select('id','name','description','parent_package_id','book_category_id','imagePath')->get();
            $data['userPackages'] = $packages;
            //end of get user packages

            //get user words
            $bookCategoryIds = array();
            foreach($userPackageIds as $userPackageId){
                $thisBookCategoryIds = $this->getBookCategoryIdsByPackageIds($userPackageId);
                $bookCategoryIds = array_unique(array_merge($bookCategoryIds,$thisBookCategoryIds));
            }
            
            //get userCategories
            foreach($bookCategoryIds as $bookCategoryId){
                $data['userCategories'] =  array_merge($this->getUserCategoriesByCategoryId($bookCategoryId),$data['userCategories']);
            }
            $userCategoryIds=array_pluck($data['userCategories'], 'id');
            
            //set categories is_free and for_user
            $userCategoryIds = $freeCategoryIds->merge($freeCategoryIds)->unique()->toArray();
            $counter = 0;
            foreach($data['allCategories'] as $category){
                $category->{"for_user"} = 0;
                if(in_array($category->id,$userCategoryIds)){
                    $category->{"for_user"} = 1;
                }       
                $data['allCategories'][$counter] = $category;
                $counter++;
            }    
            
            
            $userWordIds = array();
            //get all userWordCategories
            $userWordCategories = WordCategory::whereIn('book_category_id' , $userCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
            $userWordCategories = $freeWordCategories->merge($userWordCategories)->unique('id');
            $data['userWordCategories'] = $userWordCategories;
            //end of get all userWordCategories
            foreach ($userWordCategories as $userWordCategory)
            {
                array_push($userWordIds , $userWordCategory->word_id);
            }
            $userWords = Word::whereIn('id' , $userWordIds)->select('id','word','phonetic','english_meaning','persian_meaning','english_example','persian_example','image_path')->get();
            $data['userWords'] = $userWords;
            //end of get user words
            
        }
        //end of getting user bought packages and words info

        return $data;
    }

    function getUserImages($loggedInUser, $package_id = null){
        //download all images for admin
        if(in_array($loggedInUser->mobile_number,PackageController::AdminAccess)){
            return Word::whereNotNull("image_path")->pluck("image_path")->toArray();
        }
        
        //get free words and categories
        $freeCategories = Category::where('is_free',1)->select('id','parent_category_id','category_name','description','list_type','image_path')->get();
        $freeCategoryIds = $freeCategories->pluck('id');
        $freeWordCategories = WordCategory::whereIn('category_id' , $freeCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
        //end of free words and categories

        //check if user has any packages in transactions table to get user's packages and words
        if($package_id){
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->where('package_id',$package_id)->pluck('package_id');
        }else{
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->pluck('package_id');
        }
        
        if($userPackageIds){
            //get user images
            $bookCategoryIds = array();
            foreach($userPackageIds as $userPackageId){
                $thisBookCategoryIds = $this->getBookCategoryIdsByPackageIds($userPackageId);
                $bookCategoryIds = array_unique(array_merge($bookCategoryIds,$thisBookCategoryIds));
            }
            //get userCategories
            $userCategories = [];
            foreach($bookCategoryIds as $bookCategoryId){
                $userCategories =  array_merge($this->getUserCategoriesByCategoryId($bookCategoryId),$userCategories);
            }
            $userCategoryIds=array_pluck($userCategories, 'id');
            $userWordIds = array();
            //get all userWordCategories
            $userWordCategories = WordCategory::whereIn('book_category_id' , $userCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
            $userWordCategories = $freeWordCategories->merge($userWordCategories)->unique('id');
            $data['userWordCategories'] = $userWordCategories;
            //end of get all userWordCategories
            foreach ($userWordCategories as $userWordCategory)
            {
                array_push($userWordIds , $userWordCategory->word_id);
            }
            $userWordImages = array_filter(array_unique(Word::whereIn('id' , $userWordIds)->pluck('image_path')->toArray()));
            //end of get user images
        }
        //end of getting user bought packages and words info

        return $userWordImages;
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
    
    function createZipFileForDownload($zipFileName,$filePath,$subFoldersInReturnZipFile,$toDownloadFilesNameArray){
        $entries = scandir($filePath);
        unset($entries[0]);
        unset($entries[1]);
     
        $fileNamesArray = array();
        $flippedEnteries = array_flip($entries);
        foreach($toDownloadFilesNameArray as $toDownloadFileName)
        {
            if(isset($flippedEnteries[$toDownloadFileName])){
               array_push($fileNamesArray,$toDownloadFileName);
            }
            // if(array_search($toDownloadFilesNameArray, $entries)){
 
            // }
        }
        
        $zip_file = $zipFileName;
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($fileNamesArray as $fileName) {
            $imagePath = $filePath.$fileName;
            if (file_exists($imagePath)) {
                $zip->addFile($imagePath, $subFoldersInReturnZipFile.$fileName);
            }
        }

        $zip->close();
        
        return $zip_file;
    }
    
    public function checkVersion(Request $request)
    {
        $appVersion = $request->version;

        $maxVersionRow = Version::orderBy('version', 'desc')->first();
        
        if($maxVersionRow->version > $appVersion){
            $data['success'] = 0;
            $data['msg'] = "لطفا برنامه را آپدیت کنید";
            $data['downloadPage'] = $maxVersionRow->downloadPage;
            $data['status'] = 480;
        }else{
            $data['success'] = 1;
            $data['msg'] = "برنامه به روز است";
            $data['status'] = 200;
        }
        
        return response()->json($data);
    }
    
    public function readCacheFile($backupfileName){
       $str = json_decode(file_get_contents('cachFiles/'.$backupfileName),true);
       return $str;
    }
    
    public function createBackupFile($backupDirectory,$fileName, $data){

        //Check if file existis or create it
       if(!is_dir($backupDirectory)){//If dir not exists, create your file
            mkdir($backupDirectory, 0777, true);
       }
     

       //Your will store your data as a 1 line string using json_encode
       $myJSONstring = json_encode($data);

       //Now you will open the file you have created and write the string just created
       $handle = fopen($backupDirectory.$fileName,"w");
       fwrite($handle,$myJSONstring);
    }

    function getInitialDataFromTokenTest($token, $package_id = null){

        $loggedInUser = User::whereToken($token)->first();
    
        $data['status'] = 200;
        $data['success'] = 0;
        
        if(in_array($loggedInUser->mobile_number,PackageController::AdminAccess)){
            $data['token'] = $token;
            $data['msg'] = "کاربر با موفقیت وارد شد!";
            $data['allCategories'] = Category::select('id','order_number','parent_category_id','category_type','category_name','description','list_type','image_path','category_type')->get();
            $counter = 0;
            foreach($data['allCategories'] as $category){
                $category->{"is_free"} = 1;
                $category->{"for_user"} = 1;
                $data['allCategories'][$counter] = $category;
                $counter++;
            }    
            $data['userPackages'] = [];
            $allPackages = Package::select('id','name','description','price','parent_package_id','book_category_id','imagePath')->get();
            $data['allPackages'] = $allPackages;
            $userWords = Word::select('id','word','phonetic','english_meaning','persian_meaning','english_example','persian_example','image_path')->get();
            $data['userWords'] = $userWords;
            $userWordCategories = WordCategory::select('id','word_id','category_id','book_category_id')->get();
            $data['userWordCategories'] = $userWordCategories;
                        
            // $wordCategoryWordIdMap = [];            
            // foreach($userWordCategories as $wordCategory){
            //     if(!isSet($wordCategoryWordIdMap[$wordCategory->word_id])){
            //         $wordCategoryWordIdMap[$wordCategory->word_id]["category_id_array"] = [];
            //         $wordCategoryWordIdMap[$wordCategory->word_id]["book_category_id_array"] = [];
            //     }
            //     array_push($wordCategoryWordIdMap[$wordCategory->word_id]["category_id_array"] , $wordCategory->category_id);
            //     array_push($wordCategoryWordIdMap[$wordCategory->word_id]["book_category_id_array"] , $wordCategory->book_category_id);
            // }        
            
            // $counter = 0;
            // foreach($userWords as $userWord){
            //     if(isset($wordCategoryWordIdMap[$userWord->id]["category_id_array"])){
            //         $userWord->{"category_id_array"} = $wordCategoryWordIdMap[$userWord->id]["category_id_array"];
            //         $userWord->{"book_category_id_array"} = $wordCategoryWordIdMap[$userWord->id]["book_category_id_array"];
            //         $userWords[$counter] = $userWord;
            //     }
            //     $counter++;
            // }
            
            // $data['userWords'] = $userWords;
            
            
            // $this->createBackupFile("cachFiles/","allCategories.json", $data);
            // $data = $this-> readCacheFile("allCategories.json");

            return $data;
        }

        if(!$package_id) 
        {
            $data['token'] = $token;
            $data['msg'] = "کاربر با موفقیت وارد شد!";
        }else{
           $data['msg'] = "اطلاعات پکیج کاربر با موفقیت دریافت شد";
        }

        $data['userPackages'] = [];
        $data['userCategories'] = [];
        $data['allCategories'] = Category::where('is_published',1)->select('id','order_number','category_type','parent_category_id','category_name','description','list_type','image_path','is_free')->get();
        
        //get free words and categories
        $freeCategoryIds = Category::where('is_free',1)->pluck('id');
        $freeWordCategories = WordCategory::whereIn('category_id' , $freeCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
        //end of free words and categories
        
        //get all packages
        if(!$package_id){
            $data['allPackages'] = [];
            $allPackages = Package::select('id','name','description','price','parent_package_id','book_category_id','imagePath')->where("is_published","1")->get();
            $data['allPackages'] = $allPackages;
        }
        //end of get all packages

        //check if user has any packages in transactions table to get user's packages and words
        if($package_id){
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->where('package_id',$package_id)->pluck('package_id');
        }else{
            $userPackageIds = Transaction::where('user_id' , $loggedInUser->id)->where('transaction_status',100)->pluck('package_id');
        }
        
        if($userPackageIds){
            //get user packages
            $packages = Package::whereIn('id' , $userPackageIds)->select('id','name','description','parent_package_id','book_category_id','imagePath')->get();
            $data['userPackages'] = $packages;
            //end of get user packages

            //get user words
            $bookCategoryIds = array();
            foreach($userPackageIds as $userPackageId){
                $thisBookCategoryIds = $this->getBookCategoryIdsByPackageIds($userPackageId);
                $bookCategoryIds = array_unique(array_merge($bookCategoryIds,$thisBookCategoryIds));
            }
            
            //get userCategories
            foreach($bookCategoryIds as $bookCategoryId){
                $data['userCategories'] =  array_merge($this->getUserCategoriesByCategoryId($bookCategoryId),$data['userCategories']);
            }
            $userCategoryIds=array_pluck($data['userCategories'], 'id');
            
            //set categories is_free and for_user
            $userCategoryIds = $freeCategoryIds->merge($freeCategoryIds)->unique()->toArray();
            $counter = 0;
            foreach($data['allCategories'] as $category){
                $category->{"for_user"} = 0;
                if(in_array($category->id,$userCategoryIds)){
                    $category->{"for_user"} = 1;
                }       
                $data['allCategories'][$counter] = $category;
                $counter++;
            }    
            
            
            $userWordIds = array();
            //get all userWordCategories
            $userWordCategories = WordCategory::whereIn('book_category_id' , $userCategoryIds)->select('id','word_id','category_id','book_category_id')->get();
            $userWordCategories = $freeWordCategories->merge($userWordCategories)->unique('id');
            $data['userWordCategories'] = $userWordCategories;
            //end of get all userWordCategories
            foreach ($userWordCategories as $userWordCategory)
            {
                array_push($userWordIds , $userWordCategory->word_id);
            }
            $userWords = Word::whereIn('id' , $userWordIds)->select('id','word','phonetic','english_meaning','persian_meaning','english_example','persian_example','image_path')->get();
            $data['userWords'] = $userWords;
            //end of get user words
            
        }
        //end of getting user bought packages and words info

        return $data;
    }
    
    function test(){
        $data['listeningCategories'] = [];
        $data['listeningList'] = [];
        
        $listeningCategories = Category::where("category_type",1)->select('id' , 'parent_category_id','category_name' , 'description', 'list_type' , 'image_path' , 'is_free')->get();
        $data['listeningCategories'] = $listeningCategories;
        
        $listeningList = Listening::all();
        $data['listeningList'] = $listeningList;
        
        $counter = 0;
        foreach($listeningList as $listening){
            $decodedQuestions = json_decode($listening->questions);
            $listeningList[$counter]->questions = $decodedQuestions; 
            $counter++;
        }
         
        return response()->json($data);
    }
    
//   public function getListeningCategorySoundClip(Request $request)
//     {
//         $token = $request->header('Authorization');
//         $user = User::whereToken($token)->first();
//         $catId = $request->listeningCategoryId;

//         if($user)
//         {
//             $zip = new ZipArchive;
//             $fileName = 'ListeningSound.zip';
//             if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
//             {
//                 $files = File::files(public_path('/listening'));
//                 foreach ($files as $key => $value) {
//                     $soundClipName = basename($value);
//                     $listenings = Listening::whereCategory_id($catId)->get();
//                     foreach ($listenings as $listening)
//                     {
//                         $filePath = $listening->file_path;
//                         if ($filePath == $soundClipName)
//                         {
//                             $zip->addFile($value, $soundClipName);
//                         }
//                     }
//                 }

//                 $zip->close();
//             }

//             return response()->download(public_path($fileName));
//         }else{
//             $data['success'] = 1;
//             $data['msg'] = "کاربری وجود ندارد";
//             $data['status'] = 404;
//             return response()->json($data);
//         }
//     }

  public function getListeningCategorySoundClip(Request $request)
    {
        // $token = $request["Authorization"];
        // $user = User::whereToken($token)->first();
        // // $imageString = trim(preg_replace('/\s+/','', $request["images"]));
        // // $imageArray = explode(',', trim($imageString, "[]"));
        // if($user)
        // {
            $allUserCategorySoundClipPath = Listening::whereCategory_id($request["category_id"])->pluck("file_path");
            $zip_file = $this->createZipFileForDownload('listening.zip','listening/','listening/',$allUserCategorySoundClipPath);
            return response()->download($zip_file);
        // }else{
        //     $data['success'] = 1;
        //     $data['msg'] = "کاربری وجود ندارد";
        //     $data['status'] = 404;
        //     return response()->json($data);
        // }
    }

}
