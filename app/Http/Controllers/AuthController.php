<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facedes\Redirect;

class AuthController extends Controller
{
    //
    public function index() {
        // kita ambil data user lalu simpan pada variabel $user 
        $user = Auth::user();
        // kondisi jika user nya ada 
        if($user){
            // jika level nya admin maka akan diarahkan ke halaman admin
            if($user->level == 'admin'){
                return redirect()->intended('admin');
            }
            // jika level nya user maka akan diarahkan ke halaman user
            else if($user->level == 'user'){
                return redirect()->intended('user');
            }
        }
        return view('login');
    }
    //
    public function proses_login(Request $request){
        // kita buat validasi pada saat tombol login di klik
        // validasinya username & password wajib diisi 
        $request->validate([
            'username'=>'required',
            'password'=>'required'
        ]);
        
        // ambil data request username & password saja 
        $credential = $request->only('username','password');

        // cek jika data username dan valid (sesuai) dengan data
        if(Auth::attempt($credential)){
            // kalau berhasil simpan data user yang di variabel $user
            $user = Auth::user();
            // cek level user jika level nya admin maka akan diarahkan ke halaman admin
            if($user->level == 'admin'){
                return redirect()->intended('admin');
            }
            // cek level user jika level nya user maka akan diarahkan ke halaman user
            else if($user->level == 'user'){
                return redirect()->intended('user');
            }
            // jika belum ada role maka ke halaman /
            return redirect()->intended('/');            
        }
        // jika ga ada data user yang valid maka kembalikan lagi ke halaman login
            // pastikan kirim pesan error juga kalau login gagal ya
            return redirect('login')
            ->withInput()
            ->withErrors(['login_gagal'=>'These credentials do not match our records.']);
    }
    
    public function register(){
        // tampilkan view register
        return view('register');
    }

    // aksi form register
        public function proses_register(Request $request){
            // kita buat validasi nih buat proses register 
            // validasinya yaitu semua field wajib diisi
            // validasi username itu harus unique atau tidak boleh duplicate username ya
                $validator = Validator::make($request->all(),[
                    'name'=>'required',
                    'username'=>'required|unique:users',
                    'email'=>'required|email',
                    'password'=>'required',
                ]);
                // jika validasi gagal maka kembali ke halaman register
                if($validator->fails()){
                    return redirect('/register')
                    ->withErrors($validator)
                    ->withInput();
                }
                // jika berhasil isi level & hash passwordnya ya biar secure
                    $request['level']='user';
                    $request['password']=Hash::make($request['password']);
                // masukkkan semua data pada request ke table user
                    User::create($request->all());

                    // kalo berhasil arahkan ke halaman login
                    return redirect()->route('login');
        }

        // aksi logout
        public function logout(Request $request){
            // logout itu harus menghapus session nya ya
            $request->session()->flush();
            // logout user
            Auth::logout();
            // kembalikan ke halaman login
            return redirect()->route('login');
        }

}
