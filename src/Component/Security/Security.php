<?php


namespace App\Component\Security;


use Symfony\Component\HttpFoundation\Request;

class Security
{
    public static function isGranted(Request $request)
    {
        $hash = $request->headers->get('MC3_IMPORTER_SECURITY_HASH');
        if (!$hash) {
            return false;
        }
        
        $data = $request->getContent();

        if (!$verified = self::verifySignature($data, $hash))
        {
            // no log for test env
            if ($_SERVER['APP_ENV'] !==  'test') {
                error_log('Webhook verified: '.var_export($verified, true)); //check error.log to see the result
            }
            return false;
        }

        return true;
    }

    public static function verifySignature($data, string $hash)
    {
        // we must be able to find the same encoding result with our security key
        $localHash = base64_encode(hash_hmac('sha256', $data, $_ENV['MC3_IMPORTER_SECURITY_KEY'], true));

        return hash_equals($hash, $localHash);
    }
}