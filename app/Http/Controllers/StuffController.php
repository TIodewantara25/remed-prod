<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Stuff;
use Illuminate\Http\Request;

class StuffController extends Controller
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
            $data = Stuff::all()->toArray();

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
   // Ini adalah objek dari kelas Request
    //mengirmkan data
    {
        try{
            //validasi
            $this->validate($request,[
                'name' => 'required',
                'category' =>'required',
            ]);

            //proses tambah data
            //namaModel :: create(['column' =>$request->name_or_key, ])
            $data = Stuff::create([
                'name' => $request->name,
                'category' =>$request->category,
            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
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
            $data = Stuff::where('id' ,$id)->first();
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
                'name' => 'required',
                'category' => 'required',
            ]);


            $checkProses = Stuff::where('id',$id)->update([
                'name' => $request->name,
                'category' => $request->category,
            ]);

            if ($checkProses) {
                $data = Stuff::find($id);
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
    //menghapus data sesui id
    {
        try{
            $checkProses = Stuff::where('id', $id)->delete();

            return ApiFormatter::senResponse(200, 'success','Data Stuff Berhasil dihapus!');
        }
        catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    public function trash()

    {
        try {
            //onlyTrashed : mencari data yang deletes_at nya BUKAN null
            
            $data = Stuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(200., 'bad request', $err->getMessage());
        }
    }


    public function restore($id)
    //menngembalikan data yang di hapus
    {
        try{
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = Stuff::find($id);
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
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus permanen data!');
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
}


