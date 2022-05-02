<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{   

    public function index(Request $request)
    {
        // kiểm tra đăng nhập
        if(!Auth::check() && $request->path() != '/login'){
            return redirect('/login'); // chưa đăng nhập -> chuyển sang trang login
        }
        if(!Auth::check() && $request->path() == '/login' ){
            return view('index'); //chưa đăng nhập -> chuyển sang trang chủ
        }
        // đã đăng nhập -> xác thực thông tin người dùng
        $user = Auth::user();
        

        // kiểm tra level của ueser
        if($user->role->isAdmin == 0){
            return redirect('/'); 
            // chuyển về trang login
        }
        if($request->path() == 'login'){
            return redirect('/');
        }
        
        return view('index');
    }


    public function auth ()
    {   
        if(Auth::check()){
            $user = Auth::user();
            $role = Auth::user()->role;
            return response()->json(['user'=> $user,'role' => $role]);
        }
        else{
            return response()->json(['user'=> false, 'msg'=>'User is not authenticated']);
        }
    }

        // func đăng nhập
    public function login(Request $request){
                // điều kiện 
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();

            // kiểm tra level user
            if($user->role->isAdmin == 0){
                // Auth::logout();
                return response()->json([
                    'redirect' => '/', 
                    'msg' => 'User, You are logged in', 
                    'user' => $user,
                    'role' => $user->role
                ]);
            }
            return response()->json([
                'redirect' => '/app', 
                'msg' => 'Admin, You are logged in', 
                'user' => $user,
                'role' => $user->role
            ]);
        }else{
            return response()->json([
                'msg' => 'Incorrect login details', 
            ], 401);
        }
    }

    public function signup(Request $request)    
    {
        // validate request 
        $this->validate($request, [
            'name' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'role_id' => 3
        ]);
        
        return response()->json([
            'redirect' => '/login', 
            'msg' => 'Sign up success', 
            'user' => $user,
            'role' => 3
        ]);
    }
    

    public function logout(){
        Auth::logout();
        return response()->json([
            'msg' => 'Logout succesfull', 
        ], 200);
        return redirect('/');
    }

}
