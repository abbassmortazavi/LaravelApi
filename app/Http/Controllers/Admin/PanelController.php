<?php

namespace App\Http\Controllers\Admin;

use App\Word;
use App\Category;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MPCO\EnglishPersianNumber\Numbers;

class PanelController extends Controller
{
    public function index()
    {
        $words = Word::all()->count();
        $categories = Category::all()->count();
        $transactions = Transaction::all()->count();
        $users = User::all()->count();
        
        //dd($words , $categories , $transactions , $users);
        return view("Admin.panel" , compact('words' , 'categories' , 'transactions' , 'users'));
    }


    public function sms()
    {

    }

    public function convert()
    {

        $words = Word::select('persian_example')->get();

        foreach ($words as $word)
        {
            $test1 = str_replace('1' , Numbers::toPersianNumbers('1') ,$word);
            $test2 = str_replace('2' , Numbers::toPersianNumbers('2') ,$test1);
            $test3 = str_replace('3' , Numbers::toPersianNumbers('3') ,$test2);



        }

        Word::where('persian_example' , $word->id)->update([
            'persian_example' => $test3
        ]);
        die();

    }
    
    public function deleteImage(Request $request)
    {
        unlink('images/'.$request->image);
    }
}
