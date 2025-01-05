<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use App\Mail\ForgetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Brand;
use App\Models\Product;

class DashboardController extends Controller
{
    public function admin_login(Request $request)
    {

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => 'required',
                'password' => 'required',
            ], [
                'email.required' => 'Please Write Your Email',
                'password.required' => 'Please Write Your Password',
            ]);
            $email = $request->email;
            $password = $request->password;
            $user = User::where('email', '=', $email)->first();
            if (isset($user) && $user->hasRole('admin')) {

                if (Auth::attempt(['email' => $email, 'password' => $password])) {
                    $request->session()->regenerate();
                    return redirect()->intended('dashboard');
                } else {

                    return redirect()->back()->with('msg', 'Password Not Correct');
                }
            } else {

                return redirect()->back()->with('msg', 'Can\'t Access To This Page');
            }
        } else {
            return redirect()->route('login');
        }
    } //End Method

    public function admin_forget_password(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            
        ]);
        if ($request->isMethod('post')) {

            $check = User::where('email', '=', $request->email)->first();
            
            if (isset($check) && $check->hasRole('admin')) {

                $data = Mail::to($check->email)->send(new ForgetPassword($check->id));
                return redirect()->back()->with('msg','Rest Password Link Sent To Your Mail');
            } else {

                return redirect()->back()->with('msg', 'Sorry Email Not Found');
            }
            
        } else {

            return redirect()->route('login');
        }
    } //End Method

    public function admin_reset_password($id){
        return view('auth.reset-password',compact('id'));
    }//End Method

    public function admin_update_password(Request $request){
        
        $validated = $request->validate([
            'password' => 'required',
            
        ]);
        $id = $request->id;
        $password = Hash::make($request->password);
        $user = User::where('id', '=', $id)->update([
            'password' => $password
        ]);
        return redirect()->back()->with('msg','Your Password is Updated Successfully');
    }//End Method

    public function dashboard(){
        $Brand = Brand::count('id');
        $productInStock = Product::where('available','=',1)->count('id');
        $productOutStock = Product::where('available','=',0)->count('id');
        return view('dashboard',compact('Brand','productInStock','productOutStock'));
    }//End Method
}
