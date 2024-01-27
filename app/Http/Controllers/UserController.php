<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\User; // Pastikan model User sudah di-import


class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::all();

        // Decrypt usernames and passwords
        $decryptedUsers = $this->decryptUserCredentials($users);

        return view('dashboard')->with('users', $decryptedUsers);
    }

    private function decryptUserCredentials($users)
    {
        foreach ($users as $user) {
            $user->encrypted_email = $this->decryptMyszkowskiTranspositionCipher($user->email, 'udinus');
            $user->encrypted_name = $this->decryptMyszkowskiTranspositionCipher($user->name, 'udinus');
            $user->encrypted_username = $this->decryptMyszkowskiTranspositionCipher($user->username, 'udinus');
            $user->encrypted_password = $this->decryptMyszkowskiTranspositionCipher($user->password, 'udinus');
        }

        return $users;
    }
    public function edit($id)
    {
        // Ambil data user berdasarkan ID
        $user = User::find($id);

         // Decrypt username
        $user->name = $this->decryptMyszkowskiTranspositionCipher($user->name, 'udinus');
        $user->email = $this->decryptMyszkowskiTranspositionCipher($user->email, 'udinus');
        $user->username = $this->decryptMyszkowskiTranspositionCipher($user->username, 'udinus');
        $user->password = $this->decryptMyszkowskiTranspositionCipher($user->password, 'udinus');
    
        // Kirim data user ke view edit
        return view('edit')->with('user', $user);
    }
    
    public function update(Request $request, $id)
    {
        // Validasi data yang diupdate
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Ambil data user berdasarkan ID
        $user = User::find($id);
    
        // Update data user
        $user->update([
            'name' => $this->encryptMyszkowskiTranspositionCipher($request->input('name'), 'udinus'),
            'username' => $this->encryptMyszkowskiTranspositionCipher($request->input('username'), 'udinus'),
            'email' => $this->encryptMyszkowskiTranspositionCipher($request->input('email'), 'udinus'),
            'password' => $this->encryptMyszkowskiTranspositionCipher($request->input('password'), 'udinus'),
        ]);
    
        // Redirect ke halaman user dengan pesan sukses
        return redirect()->action([UserController::class, 'index'])->with('success', 'User berhasil disimpan.');
    }

    

    public function destroy($id)
    {
        // Hapus data user berdasarkan ID
        User::destroy($id);

        // Redirect ke halaman user dengan pesan sukses
        return redirect()->action([UserController::class, 'index'])->with('success', 'User berhasil dihapus.');
    }

            // aksi enkripsi
            public function encryptMyszkowskiTranspositionCipher($text, $key) {
                // Ganti spasi dengan simbol #
                $text = str_replace(' ', '#', $text);
                $keyLength = strlen($key);
                $textLength = strlen($text);
                $numRows = ceil($textLength / $keyLength);
            
                // Buat array untuk menyimpan teks yang diacak
                $scrambledText = array_fill(0, $numRows, array_fill(0, $keyLength, '/'));
            
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
                $scrambledText = array_fill(0, $numRows, array_fill(0, $keyLength, '/'));
            
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
                $text = array_fill(0, $numRows, array_fill(0, $keyLength, '/'));
            
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
                $decryptedText = str_replace('/', '', $decryptedText);
                $decryptedText = str_replace('#', ' ', $decryptedText);
            
                return $decryptedText;
            }

            public function storeUser(Request $request)
            {
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
                    return redirect('/user/create')
                    ->withErrors($validator)
                    ->withInput();
                }
                // jika berhasil isi level & enkripsi username dan password dengan kunci udinus
                    $request['level']='user';
                    $request['username'] = $this->encryptMyszkowskiTranspositionCipher($request['username'], 'udinus');
                    $request['name'] = $this->encryptMyszkowskiTranspositionCipher($request['name'], 'udinus');
                    $request['password'] = $this->encryptMyszkowskiTranspositionCipher($request['password'], 'udinus');
                    $request['email'] = $this->encryptMyszkowskiTranspositionCipher($request['email'], 'udinus');
                // masukkkan semua data pada request ke table user
                    User::create($request->all());

                    
                    return redirect()->action([UserController::class, 'index'])->with('success', 'User berhasil disimpan.');
            }

            public function createUser()
            {
            // Return the view for creating a new user
            return view('create');
            }

}
