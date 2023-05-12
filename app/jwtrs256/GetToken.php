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

        if (!$attempt = User::where('email', $request->email)->first()) {
            return throw new Exception('Invalid Email Address');
        }

        $b64 = base64_decode(
            str_replace(array('-', '_'), array('+', '/'), $request->password)
        );

        if (!Hash::check($b64, $attempt->password)) {
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

    private static function getRandomString($n)
    {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    private static function isAdmin($status)
    {

        if ($status) {
            return date("YmdHis", strtotime('+8 hours'));
        } else {
            return date("YmdHis", strtotime('+2 hours'));
        }
    }
}
