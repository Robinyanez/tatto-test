<?php

namespace App\Http\Controllers\Api;

use Hash;
use Config;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ApiLog;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\Api\ApiResponse;

class UserController extends Controller implements HttpCodeInterface
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $url = Config::get('values.userUrl');

        try {

            $user = new User;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'),);
            $user->save();

            $detailUser = new DetailUser;
            $detailUser->user_id = $user->id;
            $detailUser->country = $request->get('country');
            $detailUser->save();

            $data = array();

            $message = 'Usuario realizado con éxito';

            $success = new ApiLog;
            $success->input_date = Carbon::now()->format('Y-m-d H:i:s');
            $success->input_json = json_encode($request->all());
            $success->response_date = Carbon::now()->format('Y-m-d H:i:s');
            $success->response_json = json_encode($this->successResponse($message, $data));
            $success->response_code = self::OK;
            $success->url = $url.'/opcion/tatto/create/user';
            $success->save();

            return $this->successResponse($message, $data);

        } catch (\Exception | \QueryException | \Error $e) {

            $error = new ApiLog;
            $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->input_json = json_encode($request->all());
            $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->response_json = $e->getMessage();
            $error->response_code = $e->getCode();
            $error->url = $url.'/opcion/tatto/create/user';
            $error->save();

            return $this->errorResponse($e->getMessage(), $e->getCode());
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
        //
    }
}
