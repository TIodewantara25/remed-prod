<?php
namespace App\Helpers;
//namaspace : menentukan lokasi folder dari file ini

//nama class == nama file 

class ApiFormatter{
    //varibel struktur data yang akan ditampilkan di respoon postman
    protected static $response = [
        "status" => NULL,
        "message" => NULL,
        "data" => NULL,
    ];

    public static function sendResponse($status = NULL, $message = NULL, $data = [])
    {
        self::$response['status'] = $status;
        self::$response['message'] = $message;
        self::$response['data'] = $data;
        return response()->json(self::$response, self::$response ['status']);
        //status : http staus code (200, 400, 500)
        //massage : desc http  status code ('succes', 'bad request', 'server error')
        //data : hasil yang diambil dari db   
    }
}
?>