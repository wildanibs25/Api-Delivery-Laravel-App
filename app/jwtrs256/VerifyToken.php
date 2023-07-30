<?php

namespace App\JWTRS256;

use App\Http\Controllers\Controller;
use Exception;

class VerifyToken extends Controller
{
    public static function AuthCheck()
    {

        $data = VerifyToken::decodeData(request()->bearerToken());

        if (strtotime(date("YmdHis")) >= strtotime($data->exp)) {
            throw new Exception('Token is Expired');
        }

        $fileDecode = array();

        if ($my_file = file_get_contents('../public/logout/ListLogout.json')) {
            $fileDecode = json_decode($my_file);
        }

        if (count((array)$fileDecode) > 0) {
            if (VerifyToken::filterToList($fileDecode)) {
                throw new Exception("You've been logged out");
            }
        }

        return $data;
    }

    private static function filterToList($fileDecode)
    {

        return array_filter(
            $fileDecode,
            function ($obj) {
                return $obj->token === request()->bearerToken();
            }
        );
    }

    private static function decodeData($token)
    {

        return JWToken::decode($token, file_get_contents('../app/JWTRS256/keys/public.key'));
    }

    public static function codeCheck($code)
    {

        $user = VerifyToken::decodeData(request()->bearerToken());
        $data = VerifyToken::decodeData($code);

        if (strtotime(date("YmdHis")) >= strtotime($data->exp)) {
            throw new Exception('Code is Expired');
        }

        if ($user->sub !== $data->sub) {
            throw new Exception("The email verification is failed");
        }

        return $data;
    }
}
