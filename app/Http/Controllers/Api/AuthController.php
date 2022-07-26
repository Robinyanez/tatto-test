<?php

namespace App\Http\Controllers\Api;

use Auth;
use Config;
use Carbon\Carbon;
use App\Models\ApiLog;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\Api\ApiResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller implements HttpCodeInterface
{
    use ApiResponse, AuthorizesRequests;

    public function login(AuthRequest $request)
    {
        $url = Config::get('values.userUrl');

        try {

            if(!$this->attemptLogin($request)) {

                $error = new ApiLog;
                $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
                $error->input_json = json_encode($request->all());
                $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
                $error->response_json = json_encode($this->errorResponse('Credenciales incorrectas.', self::FORBIDDEN));
                $error->response_code = self::FORBIDDEN;
                $error->url = $url.'/auth/hcb/login';
                $error->save();

                return $this->errorResponse('Credenciales incorrectas.', self::FORBIDDEN);
            }

            $user_id = Auth::user()->id;

            $country = DetailUser::where('user_id',$user_id)->first();

            if (empty($country) || is_null($country)) {

                $error = new ApiLog;
                $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
                $error->input_json = json_encode($request->all());
                $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
                $error->response_json = json_encode($this->errorResponse('No puede acceder a esta parte del sistema..', self::FORBIDDEN));
                $error->response_code = self::FORBIDDEN;
                $error->url = $url.'/auth/hcb/login';
                $error->save();

                return $this->errorResponse('No puede acceder a esta parte del sistema.', self::FORBIDDEN);

            } else {

                if ($country->country == 'Ecuador' || $country->country == 'Chile' || $country->country == 'Colombia' || $country->country == 'Perú') {

                    $tokenResult = auth()->user()->createToken('TATTO-TEST');

                    $expiresIn = new Carbon($tokenResult->token->expires_at);

                    $response = array();

                    $response = [
                        'token_type' => 'Bearer',
                        'expires_in' => $expiresIn->diffInSeconds(Carbon::now()).' seconds',
                        'access_token' => $tokenResult->accessToken,
                    ];

                    $message = 'Autenticación realizado con éxito';

                    $success = new ApiLog;
                    $success->input_date = Carbon::now()->format('Y-m-d H:i:s');
                    $success->input_json = json_encode($request->all());
                    $success->response_date = Carbon::now()->format('Y-m-d H:i:s');
                    $success->response_json = json_encode($this->successResponseAuth($message, $response));
                    $success->response_code = self::OK;
                    $success->url = $url.'/auth/hcb/login';
                    $success->save();

                    return $this->successResponseAuth($message, $response);

                } else {

                    $error = new ApiLog;
                    $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
                    $error->input_json = json_encode($request->all());
                    $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
                    $error->response_json = json_encode($this->errorResponse('No puede acceder a esta parte del sistema.', self::FORBIDDEN));
                    $error->response_code = self::FORBIDDEN;
                    $error->url = $url.'/auth/hcb/login';
                    $error->save();

                    return $this->errorResponse('No puede acceder a esta parte del sistema.', self::FORBIDDEN);
                }
            }

        } catch (\Exception | \QueryException | \Error $e) {

            $error = new ApiLog;
            $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->input_json = json_encode($request->all());
            $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->response_json = $e->getMessage();
            $error->response_code = $e->getCode();
            $error->url = $url.'/auth/hcb/login';
            $error->save();

            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param LoginRequest $request
     * @return bool
     */
    protected function attemptLogin(AuthRequest $request): bool
    {
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  LoginRequest  $request
     * @return array
     */
    protected function credentials(AuthRequest $request): array
    {
        $field = filter_var($request->get($this->email()), FILTER_VALIDATE_EMAIL)
            ? $this->email()
            : null;

        return [
            $field      => $request->get($this->email()),
            'password'  => $request->input('password'),
        ];
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function email(): string
    {
        return 'email';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard(): StatefulGuard
    {
        return Auth::guard();
    }
}
