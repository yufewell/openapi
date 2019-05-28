<?php
/**
 * File: Util.php.
 * User: yufewell
 * Date: 2019/3/7
 * Time: 16:52
 */

class Util
{
    private static $secretKey = '';

    /**
     * curl
     *
     * @param $url
     * @param null $postFields
     * @param array $headers
     * @param int $timeout
     * @return mixed
     */
    public static function curl($url, $postFields = null, $headers = [], $timeout = 10) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if (!empty($postFields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        if (is_array($headers) && 0 < count($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::warning('Util::curl error: '.json_encode(curl_getinfo($ch), JSON_UNESCAPED_UNICODE));
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                Log::warning('Util::curl error: '.json_encode(curl_getinfo($ch), JSON_UNESCAPED_UNICODE));
            }
        }

        curl_close($ch);
        return $reponse;
    }

    /**
     * 分转元
     *
     * @param $money_fen int
     * @return string
     */
    public static function fentoyuan($money_fen) {
        return sprintf("%.2f", $money_fen / 100);
    }

    /**
     * 元转分
     *
     * @param $money_yuan
     * @return string
     */
    public static function yuantofen($money_yuan) {
        return strval($money_yuan * 100);
    }
}