<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

//  return App\User::create([
//   'name'=>'abbass',
//   'email'=>'abbassmortazavi123@gmail.com',
//   'password'=>bcrypt('55555555')
// ]);
 
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any' , '.*');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('import_excel' , 'ImportExcelController@index');
//Route::get('export' , 'ImportExcelController@export');

Route::get('export' , 'ImportExcelController@export')->name('export');
Route::post('export' , 'ImportExcelController@exportTable')->name('exportTable');

Route::post('import_excel/importWord' , 'ImportExcelController@importWord');
Route::post('import_excel/importCat' , 'ImportExcelController@importCat');

$this->any('pay' , 'ImportExcelController@pay');
$this->get('paymentVerification' , 'ImportExcelController@paymentVerification');

$this->get('paymentErrorPage' , 'ImportExcelController@paymentErrorPage');
$this->get('paymentSuccessPage' , 'ImportExcelController@paymentSuccessPage');

Route::post('import_excel/importWordCat' , 'ImportExcelController@importWordCat');
 Route::get('uploadImage' , 'ImportExcelController@uploadImage')->name('upload.image');
  Route::post('uploadImageInDirectory' , 'ImportExcelController@uploadImageInDirectory')->name('uploadImageInDirectory');


//ajax load data in dataTable
Route::any('word', 'ImportExcelController@word');
Route::any('category1', 'ImportExcelController@category1');
Route::any('wordCategoryTable', 'ImportExcelController@wordCategoryTable');
Route::any('listeningDataTable', 'ImportExcelController@listeningDataTable');
Route::any('listeningCategoryDataTable', 'ImportExcelController@listeningCategoryDataTable');

Route::group(['namespace'=>'Admin' , 'prefix'=>'admin' , 'middleware'=>'auth'] , function (){

    Route::resource('categories' , 'CategoryController');
    Route::resource('words' , 'WordController');
    Route::resource('wordCategories' , 'WordCategoryController');
    Route::resource('listenings' , 'ListeningController');
    Route::resource('listeningCategories' , 'ListeningCategoryController');
    Route::get('wordListening' , 'CategoryController@wordListening')->name('wordListening');
    Route::post('wordListeningAdd' , 'ListeningCategoryController@wordListeningAdd')->name('wordListeningAdd');


    Route::get('panel' , 'PanelController@index');
    Route::get('convert' , 'PanelController@convert');
   

    //ajax
    Route::post('loadSubCat' , 'CategoryController@loadSubCat')->name('loadSubCat.ajax');
    Route::post('categorytype' , 'CategoryController@categorytype')->name('categorytype.ajax');
    Route::post('word' , 'CategoryController@word')->name('word.ajax');
    Route::post('wordCategory' , 'WordCategoryController@wordCategory')->name('wordCategory.ajax');
    Route::post('getCategoryWords' , 'WordCategoryController@getCategoryWords')->name('getCategoryWords.ajax');
    

    //delete with ajax
    Route::post('deleteCat' , 'CategoryController@deleteCat')->name('deleteCat.ajax');
    Route::post('deleteWord' , 'WordController@deleteWord')->name('deleteWord.ajax');
    Route::post('deleteImage' , 'PanelController@deleteImage')->name('deleteImage.ajax');
    Route::post('deleteWordCategory' , 'WordCategoryController@deleteWordCategory')->name('deleteWordCategory.ajax');
    Route::post('updateWordCat/{id}' , 'WordCategoryController@updateWordCat')->name('updateWordCat.ajax');
    Route::post('deleteListeningCategory' , 'ListeningCategoryController@deleteListeningCategory')->name('deleteListeningCategory.ajax');
    Route::post('deleteListening' , 'ListeningController@deleteListening')->name('deleteListening.ajax');

    //ckEditor uploadImage
    Route::post('uploadImage' , 'WordController@uploadImage')->name('upload');;
});

Auth::routes([
    'register' => false, // Registration Routes...
]);



Route::get('/home', 'HomeController@index')->name('home');
