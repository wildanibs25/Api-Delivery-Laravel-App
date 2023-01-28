<?php

namespace App\JWTRS256;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class GetToken extends Controller
{
    public static function Auth($request)
    {
        if(! $attempt = User::where('email', $request->email)->first()){

            return throw new Exception('Invalid Email Address');

        }

        if(! Hash::check($request->password, $attempt->password)){

            return throw new Exception('Invalid Password');

        }

        return GetToken::encodeData(GetToken::payload($attempt));

    }

    private static function encodeData($payload)
    {

        return JWToken::encode($payload, file_get_contents('../app/jwtrs256/keys/private.key'));

    }

    private static function payload($attempt)
    {
        return array(
            'iss'   => request()->fullUrl(),
            'iat'   => date("YmdHis"),
            'exp'   => GetToken::isAdmin($attempt->is_admin),
            'sub'   => $attempt->id_user,
            'jti'   => GetToken::getRandomString(32),
            'admin' => $attempt->is_admin
        );


    }

    private static function getRandomString($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    private static function isAdmin($attempt)
    {
        if($attempt){

            return date("YmdHis",strtotime('+4 hours'));

        }else{

            return date("YmdHis",strtotime('+2 hours'));

        }
    }
}
