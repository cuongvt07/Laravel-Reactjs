<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponseWithHttpSTatus
{
    protected function apiResponse(string $message,$data=null,int $code = Response::HTTP_OK,bool $status = true, $errors = null){
        $responseData = [
            'status' => $status,
            'message' => $message,
            'headerCode' => $code,
            'data' => $data,
        ];
        if (!empty($errors) && $status !== true) {
            $responseData['error'] = $errors;
        }
        return response($responseData, $code);
    }

}
