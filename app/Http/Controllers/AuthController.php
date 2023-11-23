<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facedes\Redirect;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    //
    public function index() {
        // kita ambil data user lalu simpan pada variabel $user 
        $user = Auth::user();
        // kondisi jika user nya ada 
        if($user){
            $username = $this->decryptMyszkowskiTranspositionCipher($user->username, 'udinus');
            $password = $this->decryptMyszkowskiTranspositionCipher($user->password, 'udinus');

             // Debugging statements
        // dd($username, $password);
            // simpan username dan password ke dalam session
            session()->put('username', $username);
            session()->put('password', $password);
            // reflash session data
            session()->reflash();
            // jika level nya admin maka akan diarahkan ke halaman admin
            if($user->level == 'admin'){
                // return redirect()->action([AdminController::class, 'index']);
            }
            // jika level nya user maka akan diarahkan ke halaman user
            else if($user->level == 'user'){
                return redirect()->action([UserController::class, 'index']);
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
    
        // enkripsi username dan password
        $username = $this->encryptMyszkowskiTranspositionCipher($request->input('username'), 'udinus');
        $password = $this->encryptMyszkowskiTranspositionCipher($request->input('password'), 'udinus');
    
        // cari user dengan username yang diberikan
        $user = User::where('username', $username)->first();
    
        // cek jika user ditemukan dan password cocok
        if ($user && $user->password == $password) {
            // kalau berhasil simpan data user yang di variabel $user
            Auth::login($user);
    
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
                // jika berhasil isi level & enkripsi username dan password dengan kunci udinus
                    $request['level']='user';
                    $request['username'] = $this->encryptMyszkowskiTranspositionCipher($request['username'], 'udinus');
                    $request['password'] = $this->encryptMyszkowskiTranspositionCipher($request['password'], 'udinus');
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

        // aksi enkripsi
        public function encryptMyszkowskiTranspositionCipher($text, $key) {
            // Ganti spasi dengan simbol #
            $text = str_replace(' ', '#', $text);
            $keyLength = strlen($key);
            $textLength = strlen($text);
            $numRows = ceil($textLength / $keyLength);
        
            // Buat array untuk menyimpan teks yang diacak
            $scrambledText = array_fill(0, $numRows, array_fill(0, $keyLength, '@'));
        
            // Isi array dengan teks
            for ($i = 0; $i < $textLength; $i++) {
                $row = floor($i / $keyLength);
                $col = $i % $keyLength;
                $scrambledText[$row][$col] = $text[$i];
            }
        
            // Urutkan kolom berdasarkan kunci
            $order = array();
            for ($i = 0; $i < $keyLength; $i++) {
                $order[$i] = array(ord(strtolower($key[$i])), $i);
            }
            sort($order);
        
            // Buat teks terenkripsi
            $encryptedText = "";
            for ($i = 0; $i < $keyLength; $i++) {
                $col = $order[$i][1];
                for ($row = 0; $row < $numRows; $row++) {
                    $encryptedText .= $scrambledText[$row][$col];
                }
            }
        
            return $encryptedText;
        }
        
        public function decryptMyszkowskiTranspositionCipher($encryptedText, $key) {
            $keyLength = strlen($key);
            $textLength = strlen($encryptedText);
            $numRows = ceil($textLength / $keyLength);
        
            // Buat array untuk menyimpan teks yang diacak
            $scrambledText = array_fill(0, $numRows, array_fill(0, $keyLength, '@'));
        
            // Isi array dengan teks terenkripsi
            $index = 0;
            for ($i = 0; $i < $keyLength; $i++) {
                for ($row = 0; $row < $numRows; $row++) {
                    if ($index < $textLength) {
                        $scrambledText[$row][$i] = $encryptedText[$index];
                        $index++;
                    }
                }
            }
        
            // Urutkan kolom berdasarkan kunci
            $order = array();
            for ($i = 0; $i < $keyLength; $i++) {
                $order[$i] = array(ord(strtolower($key[$i])), $i);
            }
            sort($order);
        
            // Buat array untuk menyimpan teks asli
            $text = array_fill(0, $numRows, array_fill(0, $keyLength, '@'));
        
            // Susun ulang kolom berdasarkan urutan asli
            for ($i = 0; $i < $keyLength; $i++) {
                $col = $order[$i][1];
                for ($row = 0; $row < $numRows; $row++) {
                    $text[$row][$col] = $scrambledText[$row][$i];
                }
            }
        
            // Buat teks asli
            $decryptedText = "";
            for ($row = 0; $row < $numRows; $row++) {
                for ($col = 0; $col < $keyLength; $col++) {
                    $decryptedText .= $text[$row][$col];
                }
            }
        
            // Hapus karakter dummy dan ganti simbol # dengan spasi
            $decryptedText = str_replace('@', '', $decryptedText);
            $decryptedText = str_replace('#', ' ', $decryptedText);
        
            return $decryptedText;
        }

}
