<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Carbon\Carbon;
use App\Models\ApiLog;
use App\Models\DetailUser;
use Illuminate\Http\Request;
use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\Api\ApiResponse;

class ValidateCountry implements HttpCodeInterface
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user_id = Auth::user()->id;

        $country = DetailUser::where('user_id',$user_id)->first();

        if ($country == 'Ecuador' || $country == 'Chile' || $country == 'Colombia' || $country == 'Perú') {

            return $next($request);

        } else {

            $error = new ApiLog;
            $error->input_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->input_json = json_encode($request->all());
            $error->response_date = Carbon::now()->format('Y-m-d H:i:s');
            $error->response_json = json_encode($this->errorResponse('Cuenta inactiva, contacte al administrador del sistema.', self::NOT_ACCEPTABLE));
            $error->response_code = self::FORBIDDEN;
            $error->url = $url.'/auth/hcb/login';
            $error->save();
        }
    }
}
