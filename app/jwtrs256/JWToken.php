<?php

namespace App\JWTRS256;

use App\Http\Controllers\Controller;
use DomainException;
use Exception;
use UnexpectedValueException;

class JWToken extends Controller
{
    public static function encode($payload, $secret)
    {
        $header = array('typ' => 'JWT', 'alg' => 'RS256');

        $segments = array(
            JWToken::urlsafeB64Encode(json_encode($header)),
            JWToken::urlsafeB64Encode(json_encode($payload))
        );

        $signing_input = implode('.', $segments);

        $signature = JWToken::generateRSA($signing_input, $secret);

        $segments[] = JWToken::urlsafeB64Encode($signature);

        return implode('.', $segments);

    }

    public static function decode($jwt, $secret)
    {
        $tks = explode('.', $jwt);

        if (count($tks) != 3) {
            throw new Exception('Wrong number of segments');
        }

        list($headb64, $payloadb64, $cryptob64) = $tks;

        if (null === ($header = json_decode(JWToken::urlsafeB64Decode($headb64)))) {
            throw new Exception('Invalid segment encoding');
        }

        if (null === $payload = json_decode(JWToken::urlsafeB64Decode($payloadb64))) {
            throw new Exception('Invalid segment encoding');
        }

        $sig = JWToken::urlsafeB64Decode($cryptob64);

        if (isset($secret)) {

            if (empty($header->alg)) {
                throw new DomainException('Empty algorithm');
            }

            if (!JWToken::verify($sig, "$headb64.$payloadb64", $secret)) {
                throw new UnexpectedValueException('Signature verification failed');
            }
        }

        return $payload;

    }

    private static function urlSafeB64Encode($data)
    {
        $b64 = base64_encode($data);

        return str_replace(array('+', '/', '\r', '\n', '='),
                array('-', '_'),
                $b64);

    }

    private static function urlSafeB64Decode($b64)
    {
        $b64 = str_replace(array('-', '_'),
                array('+', '/'),
                $b64);

        return base64_decode($b64);

    }

    private static function generateRSA($input, $secret)
    {
        if (!openssl_sign($input, $signature, $secret, OPENSSL_ALGO_SHA256)) {
            throw new Exception("Unable to sign data.");
        }

        return $signature;
    }

    private static function verify($signature, $input, $secret)
    {
        return (boolean) openssl_verify($input, $signature, $secret, OPENSSL_ALGO_SHA256);
    }



}
