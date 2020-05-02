<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' =>'v1' , 'namespace' =>'Api\v1'] , function (){

   
    //UserController
    $this->post('loginSubmitCredentials' , 'UserController@loginSubmitCredentials');
    $this->post('loginReceiveToken' , 'UserController@loginReceiveToken');
    $this->post('loginWithToken' , 'UserController@loginWithToken');
    $this->get('sms' , 'UserController@sms');
    $this->post('submitMobileNumber' , 'UserController@submitMobileNumber');
    $this->post('register' , 'UserController@register');
    // $this->post('changeMobileNumber' , 'UserController@changeMobileNumber');
    $this->post('submitVerificationCode' , 'UserController@submitVerificationCode');
    
    $this->post('loginWithMobileRecieveCode' , 'UserController@loginWithMobileRecieveCode');
    $this->post('submitVerificationCodeRegister' , 'UserController@submitVerificationCodeRegister');
    $this->post('submitVerificationCodeLogin' , 'UserController@submitVerificationCodeLogin');
    $this->post('submitVerificationCodeResetMacAddressAndLogin' , 'UserController@submitVerificationCodeResetMacAddressAndLogin');
    
    $this->post('recoverPassSubmitMobile' , 'UserController@recoverPassSubmitMobile');
    $this->post('recoverPassSubmitPassword' , 'UserController@recoverPassSubmitPassword');
    $this->get('getUserWords' , 'UserController@getUserWords');
     $this->post('submitVerificationCodeResetMacAddress' , 'UserController@submitVerificationCodeResetMacAddress');
     $this->post('sms1' , 'UserController@sendSMS');
    /********************************/


    //PackageController
    $this->get('getAllPackages' , 'PackageController@getAllPackages');
    $this->post('getPackageWords' , 'PackageController@getPackageWords');
    $this->get('getUserPackages' , 'PackageController@getUserPackages');
    $this->get('getPackageImages/{package_id}' , 'PackageController@getPackageImages');
    $this->get('getAllPackagesImages' , 'PackageController@getAllPackagesImages');
    $this->get('getUserWordImages' , 'PackageController@getUserWordImages');
    
    $this->get('test' , 'PackageController@test');
    $this->get('getListeningCategorySoundClip/{category_id}' , 'PackageController@getListeningCategorySoundClip');

    //PanelController
    $this->post('upload' , 'PanelController@upload');
    $this->post('submitTransaction' , 'PanelController@submitTransaction');
    $this->post('backupUserWords' , 'PanelController@backupUserWords');
    $this->get('backupGetUserWords' , 'PanelController@backupGetUserWords');
    $this->get('getImages' , 'PanelController@getImages');
    $this->get('getImage/{image}' , 'PanelController@getImage');
    
    $this->post('checkVersion' , 'PackageController@checkVersion');

});

Route::group(['middleware' => 'api','prefix' =>'v2' , 'namespace' =>'Api\v2'], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');
    Route::post('checkUserExpireTime', 'AuthController@checkUserExpireTime');

});
