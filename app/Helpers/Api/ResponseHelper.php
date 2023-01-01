<?php

namespace App\Helpers\Api;

use Response;

class ResponseHelper
{
    /*
        Function name : error
        Description : Common function to return a error response.
        Developed by : Pratik Prajapati
        Date : 05/08/2022
    */

    public function error($errorMessage, $statusCode = 400, $success = false, $data = [])
    {
        return Response::json([
            'status' => $statusCode,
            "message" => $errorMessage,
            "success" => $success,
            "data" => $data
        ], $statusCode);
    }

    /*
        Function name : success
        Description : Common function to return a success response.
        Developed by : Pratik Prajapati
        Date : 05/08/2022
    */

    public function success($successMessage, $data = [], $statusCode = 200, $success = true)
    {
        if (!empty($data)) {
            array_walk_recursive($data, function (&$item) {
                $item = strval($item);
            });
        }
        return Response::json([
            'status' => $statusCode,
            "message" => $successMessage,
            "success" => $success,
            "data" => $data
        ], $statusCode);
    }
}
