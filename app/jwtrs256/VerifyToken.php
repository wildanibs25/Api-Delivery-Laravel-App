<?php

namespace App\JWTRS256;

use App\Http\Controllers\Controller;
use Exception;

class VerifyToken extends Controller
{
    public static function AuthCheck()
    {

        $data = VerifyToken::decodeData(request()->bearerToken());

        if(date("YmdHis") >= $data->exp){

            throw new Exception('Token is Expired');

        }

        if(VerifyToken::filterToList()){

            throw new Exception("You've been logged out");
        }

        return $data;

    }

    private static function filterToList(){
        $my_file = json_decode(file_get_contents('../app/jwtrs256/logout/ListLogout.json'));

       return array_filter(
            $my_file,
            function($obj){
                return $obj->token === request()->bearerToken();;
            });
    }

    private static function decodeData($token)
    {

        return JWToken::decode($token, file_get_contents('../app/jwtrs256/keys/public.key'));

    }

}
