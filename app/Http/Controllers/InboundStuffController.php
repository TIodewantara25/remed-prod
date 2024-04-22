<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\InboundStuff; 

use App\Models\Stuff;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class InboundStuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getInboundStuff = InboundStuff::with('stuff')->get();

            return ApiFormatter::sendResponse(200, true, 'SuccesFully Get All Stuff Stock Data',  $getInboundStuff);
        } catch (\Expetion $e) {
            return ApiFormatter::sendRsponse(400, false, $e-getMessage());
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
    public function store(Request $request)//proof file di sesuikan
    {
        try{
            $this->validate($request, [
                'stuff_id'=>'required',
                'total'=>'required',
                'date'=>'required',
                'proff_file'=>'required|mimes:jpeg,png,pdf|max:2048',
            ]);

            if ($request->hasFile('proff_file')) {//chek ada file atu ngak
                $proof =$request->file('proff_file');//get filenya
                $destinationPath = 'proof/';
                //2024038102130
                $proofName = date('YmdHis'). "."  . $proof->getClientOriginalExtension();//modifikasi nama file, tahunbulantanggalmenitdetik.extentiom
                $proof->move($destinationPath, $proofName);//file yang sudah di get diatas dipindahkan ke folfer/proof dengan nama sesuai yang ada divariabel proofName
            }
            $createStock = InboundStuff::create([
                'stuff_id'=>$request->stuff_id,
                'total'=>$request-> total,
                'date'=>$request-> date,
                'proff_file'=>$proofName,
            ]);

            if ($createStock){
                $getStuff = Stuff::where('id',$request->stuff_id)->first();
                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();


                if (!$getStuffStock) {
                    $updateStock = StuffStock::create([
                        'stuff_id'=>$request->stuff_id,
                        'total_available'=>$request-> total,
                        'total_defec' => 0, 
                    ]);
                }else{
                    $updateStock = $getStuffStock->update([
                        'stuff_id'=>$request->stuff_id,
                        'total_available'=>$getStuffStock['total_available'] + $request->total,
                        'total_defec' => $getStuffStock['total_defec'], 
                    ]);

                }
                
                if($updateStock){
                    $getStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                    $stuff =[
                        'stuff'=> $getStuff,
                        'inboundStuff'=> $createStock,
                        'stuffStock'=>$getStock,
                    ];  

                    return ApiFormatter::sendResponse(200, 'Succesfully Create A Inbound Stiff Data', $stuff);
                }else{
                    
                    return ApiFormatter::sendResponse(400,  'Failed To Update A Inbound Stiff Data');
                }
            }else{
                return ApiFormatter::sendResponse(400,  'Failed To Create A Inbound Stiff Data');
            }
        }catch (\Exeption $e){
            return ApiFormatter::sendResponse(400,  $e->getMessage());
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function destroy(InboundStuff $inboundStuff)
    {
        try {
            $checkProses = InboundStuff::where('id', $id)->first();

            if ($checkProses) {
                $stuffId = $checkProses->stuff_id;
                $totalInbound = $checkProses->total;
                $checkProses->delete();

                $dataStock = StuffStock::where('stuff_id', $checkProses->stuff_id)->first();

                if ($dataStock) {
                    $total_available = (int)$dataStock->total_available - (int)$totalInbound;
                    $minusTotalStock = $dataStock->update(['total_available' => $total_available]);

                    if ($minusTotalStock) {
                        $updateStufAndInbound = Stuff::where('id', $stuffId)->with('inboundStuffs', 'stuffStock')->first();
                        return ApiFormatter::sendResponse(200, 'success', $updateStufAndInbound);
                    }
                } else {
                    // Tangani jika data stok tidak ditemukan
                    return ApiFormatter::sendResponse(404, 'not found', 'Data stok stuff tidak ditemukan');
                }
            } else {
                // Tangani jika data InboundStuff tidak ditemukan
                return ApiFormatter::sendResponse(404, 'not found', 'Data InboundStuff tidak ditemukan');
            }
        } catch (\Exception $err) {
            // Tangani kesalahan
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try{
            $data= InboundStuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore(InboundStuff $inboundStuff, $id)
    {
        try {
            // Memulihkan data dari tabel 'inbound_stuffs'
            $checkProses = InboundStuff::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                // Mendapatkan data yang dipulihkan
                $restoredData = InboundStuff::find($id);

                // Mengambil total dari data yang dipulihkan
                $totalRestored = $restoredData->total;

                // Mendapatkan stuff_id dari data yang dipulihkan
                $stuffId = $restoredData->stuff_id;

                // Memperbarui total_available di tabel 'stuff_stocks'
                $stuffStock = StuffStock::where('stuff_id', $stuffId)->first();

                if ($stuffStock) {
                    // Menambahkan total yang dipulihkan ke total_available
                    $stuffStock->total_available += $totalRestored;

                    // Menyimpan perubahan pada stuff_stocks
                    $stuffStock->save();
                }

                return ApiFormatter::sendResponse(200, 'success', $restoredData);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
    
    public function deletePermanent(InboundStuff $inboundStuff, Request $request, $id)
    {
        try {
            $getInbound = InboundStuff::onlyTrashed()->where('id',$id)->first();

            unlink(base_path('public/proff/'.$getInbound->proff_file));
            // Menghapus data dari database
            $checkProses = InboundStuff::where('id', $id)->forceDelete();

            // Memberikan respons sukses
            return ApiFormatter::sendResponse(200, 'success', 'Data inbound-stuff berhasil dihapus permanen');
        } catch(\Exception $err) {
            // Memberikan respons error jika terjadi kesalahan
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    

private function deleteAssociatedFile(InboundStuff $inboundStuff)
    {
        // Mendapatkan jalur lengkap ke direktori public
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/proff';


        // Menggabungkan jalur file dengan jalur direktori public
         $filePath = public_path('proff/'.$inboundStuff->proff_file);

        // Periksa apakah file ada
        if (file_exists($filePath)) {
            // Hapus file jika ada
            unlink(base_path($filePath));
        }
    }
}



