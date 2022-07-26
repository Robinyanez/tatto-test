<?php

namespace App\Traits\Utilities\Api;

trait ApiResponse
{

    public function successResponseAuth($message, $data, $code = 200)
    {
        return $this->successResponseJson([
            'status'        => 'Success',
            'code'          => $code,
            'message'       => $message,
            'token_type'    => $data['token_type'],
            'expires_in'    => $data['expires_in'],
            'access_token'  => $data['access_token'],
        ], $code);
    }

    public function successResponseBeneficiary($message, $data, $code = 200)
    {
        return $this->successResponseJson([
            'status'    => 'Success',
            'code'      => $code,
            'message'   => $message,
            'response'  => $data['status'],
        ], $code);
    }

    public function successResponseBenefit($message, $data, $code = 200)
    {
        return $this->successResponseJson([
            'status'    => 'Success',
            'code'      => $code,
            'message'   => $message,
            'response'  => $data['response'],
            'num_benef'  => $data['num_benef'],
        ], $code);
    }

    public function successResponseTransaction($message, $code = 200)
    {
        return $this->successResponseJson([
            'status'    => 'Success',
            'code'      => $code,
            'message'   => $message,
        ], $code);
    }

    public function successResponse($message, $data, $code = 200)
    {
        return $this->successResponseJson([
            'status'    => 'Success',
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ], $code);
    }

    public function errorResponse($message, $code = 500)
    {
        $code       = $this->validHttpCode($code);
        $message    = $this->validHttpMessage($message);

        return response()->json([
            'status'    => 'Error',
            'code'      => $code,
            'message'   => $message,
        ], $code);
    }

    public function errorResponseException($message, $data, $code = 500)
    {
        $code       = $this->validHttpCode($code);
        $message    = $this->validHttpMessage($message);

        return response()->json([
            'status'    => 'Error',
            'code'      => $code,
            'message'   => $message,
            'response'  => $data['Cod_Beneficio'],
        ], $code);
    }

    private function validHttpCode($code)
    {
        if(empty($code)) {

            $code = 500;

        }else if($code < 100 ||  $code > 599) {

            $code = 500;
        }

        return $code;
    }

    private function validHttpMessage($message)
    {
        return (empty($message)) ? 'Internal Server Error' : $message;
    }

    private function successResponseJson($data, $code)
    {
        return response()->json($data, $code);
    }
}
