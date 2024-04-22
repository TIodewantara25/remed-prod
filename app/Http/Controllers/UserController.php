<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            //ambil data yang mau ditampilkan
            $data = User::all()->toArray();

            return ApiFormatter::sendResponse(200,'success',$data);
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
            'username' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required',
            'role' => 'required',
            ]);

            $password = substr($request->email, 0, 3) . substr($request->username, 0, 3);
            $data = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $data = User::where('id' ,$id)->first();
        //$data: variabel yang digunakan untuk menampung data yang diambil.
        // Stuff: nama model yang mewakili tabel stuff.
        // where: metode Eloquent (nanipulasi data )untuk memfilter data berdasarkan kondisi.
        // id: kolom dalam tabel stuff yang digunakan sebagai filter.
        // $id: nilai id yang digunakan untuk filter.
        // first: metode Eloquent untuk mengambil satu baris data pertama yang
            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request','Data not found!');
            }else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
        //unutk mengeck $data nul ata tidak 

        }catch (Exceptiion $err ){
            return ApiFormatter::sendResponse(400, 'bad request',$err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $this->validate($request, [
                'username' => 'required|min:3',
                'email' => 'required|email:dns',
                'role' => 'required',
            ]);


            $checkProses = User::where('id',$id)->update([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if ($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            }else {
                return ApiFormatter::sendResponse(400, 'bad requst', 'gagal mengunduh data! ');                
            }
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad requst', $err->getMessage());   
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response    
     */
    public function destroy($id)
    {
        {
            try{
                $checkProses = User::where('id', $id)->delete();
    
                return ApiFormatter::senResponse(200, 'success','Data User Berhasil dihapus!');
            }
            catch(\Exception $err){
                return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
            }
        }
    }

    public function trash()

    {
        try {
            //onlyTrashed : mencari data yang deletes_at nya BUKAN null
            
            $data = User::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(200., 'bad request', $err->getMessage());
        }
    }


    public function restore($id)
    //menngembalikan data yang di hapus
    {
        try{
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();//where=>mencari berdasarkan kolo spesifik yamh ingin di cari

            if ($checkProses) {
                $data = User::find($id);//find=>mecari berdasarkan kolom primary key
                return ApiFormatter::sendResponse(200, 'success', $data);
                
            }else{
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal Mengembalikan Data!');
            }
        }catch (\Exception $err){
            return ApiFromatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }   


    public function deletePermanent($id)

    {
        try{
            $checkProses = User::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus permanen data!');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }



    public function login(Request $request)
    {
        try{
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ],[
                'email.required ' => 'Email harus diisi',
                'password.required' => 'Password harus diisi',
                'password.min' => 'Password minimal 8 karakter'
            ]);

            $user = User::where('email', $request->email)->first();//mencari dan mendapatkan data user berdasarkan email yang digunakan untuk login

            if (!$user) {
                //jika email tidak terdapat maka akan dikembangkan  respon error
                return ApiFormatter::sendResponse(400, false , 'Login Failed! User Doesnt Exists');
            }else { 
                //jika email terdaftar, selanjutnya mencocokan password yang di input 
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    return ApiFormatter::sendResponse(400, false, 'Login Failed! Passwprd Doesnt Match');
                }else {
                    //jika password sesuai selanjutnya akan membuat token 
                    //bin2x digunakan untuk mengonversi string karakter ASCII  menjadi nilai heksadesimal
                    $generateToken = bin2hex(random_bytes(40));
                    //Token inilah nanti yang digunakan pada proses authentication user yang ining login
                    $user ->update([
                        'token' => $generateToken
                        //update kolom token dengan value hasi dari generateToken di row user yang ingin login
                    ]);
                    return ApiFormatter::sendResponse(200, 'Login Successfuly', $user);
                }
            }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, false, $e->getMessage());
        }
    }
    public function logout(Request $request)

    {
        try{
            $this->validate($request, [
                'email' => 'required',
                
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                
                return ApiFormater::sendResponse(400,  'Login Failed! User Doesnt Exists');
            }else {
                if (!$user->token) {
                    return ApiFormatter::sendResponse(400,  'Login Failed! User Doesnt Login Scine');
                }else {
                    $logout = $user->update(['token' => null]);
                 
                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Succesfully');
                    }

                }
            }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, false,  $e->getMessage());
        }
    }

}
