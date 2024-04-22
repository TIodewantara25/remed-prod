<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StuffStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getStuffStock = StuffStock::with('stuff')->get();

            return ApiFormatter::sendResponse(200, true, 'SuccesFully Get All Stuff Stock Data', $getStuffStock);
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
    public function store(Request $request)
    {
        $validator = Validator::make
        ($request->all(), [
            'stuff_id' => 'required',
            'total_available' => 'required',
            'total_defect' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
              'success' => false,
              'message' => 'Semua kolom wajib disi!',
                'data' => $validator->errors()
            ],400);
        } else{
            $stock = StuffStock::updateOrCreate([
                'stuff_id' => $request->input('stuff_id')
            ],[
                'total_available' => $request->input('total_available'),
                'total_defect' => $request->input('total_defect')
            ]);


            if($stock) {
                return response()->json([
                 'success' => true,
                 'message' => 'Barang berhasil ditambahkan',
                    'data' => $stock
                ],200);
            } else{
                return response()->json([
                'success' => false,
                'message' => 'Barang gagal ditambahkan',
                ],400);
            }
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
    public function destroy($id)
    {
        try{
            $checkProses = StuffStock::where('id', $id)->delete();

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
            
            $data = StuffStock::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'succes', $data);
        }catch (\Exception $err){
            return ApiFormatter::sendResponse(200., 'bad request', $err->getMessage());
        }
    }


    public function restore($id)
    //menngembalikan data yang di hapus
    {
        try{
            $checkProses = StuffStock::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = StuffStock::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
                
            }else{
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal Mengembalikan Data!');
            }
        }catch (\Exception $err){
            return ApiFromatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }   



    public function addstock(Request $request, $id)
    {
        try{
            $getStuffStock = StuffStock::find($id);

            if (!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data stuff not found');
            }else {
                $this->validate($request, [
                    'total_avilable' => 'required',
                    'total_defec' => 'required',
                ]);
                
                $addStock = $getStuffStock->update([
                    'total_avilable' => $getStuffStock['total_avilable'] + $request->total_avilable,
                    'total_defec' => $getStuffStock['total_defec'] + $request->$total_defec,

                ]);
                if ($addStock) {
                    $getStuffStock = StuffStock::where('id', $id)->with('stuff')->first();
                
                    return ApiFormatter::sendResponse(200, true, 'Successfuly add A stock off stuff stock data', $getStuffStock);
                }
            }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400,false, $e->getMessage());
        }
    }


    public function subStock(Request $request, $id)
    {
        try {
             $getStuffStock = StuffStock::find($id);

             if (!$getStuffStock) {
                return ApiFormatter::sendResponse(400, false, 'Data Stuff Stock Not Found');
             } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defac' => 'required',
                ]);

                $isStockAvailable = $getStuffStock->update['total_available'] - $request->total_available;
                $isStockDefac = $getStuffStock->update['total_defac'] - $request->total_defac;

                if ($isStockAvailable < 0 || $isStockDefac < 0) {
                    return ApiFormatter::sendResponse(400, true, 'Substraction Stock Cant Less Than A Stock Stored');
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defac' => $isStockDefac,
                    ]);

                    if ($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return ApiFormatter::sendResponse(200, true, 'Succesfully Sub A Stock Of StuFf Stock Data', $getStockSub);
                    }
                }
             }
        }catch(\Exception $e){
            return ApiFormatter::sendResponse(400, $e->getMessage());
    }

}

}



