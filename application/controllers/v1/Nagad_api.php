<?php

defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Nagad_api extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        $this->current_lang = 'en';
    }

    public function init_post()
    {
        $post_data = json_decode($this->input->raw_input_stream, true);
        $total_amount = $post_data['total_amount'];
        if (true) {
            date_default_timezone_set('Asia/Dhaka');

            $MerchantID = "683228193975399";
            $DateTime = Date('YmdHis');
             $amount = $total_amount;
            //$amount = 10;
            $OrderId = 'FOODI' . strtotime("now") . rand(1000, 10000);
            $random = $this->generateRandomString();

            $PostURL = "https://api.mynagad.com/api/dfs/check-out/initialize/" . $MerchantID . "/" . $OrderId;

            $_SESSION['orderId'] = $OrderId;

            $merchantCallbackURL = base_url(ADMIN_URL . '/Nagad');

            $SensitiveData = array(
                'merchantId' => $MerchantID,
                'datetime' => $DateTime,
                'orderId' => $OrderId,
                'challenge' => $random
            );

            $PostData = array(
                'accountNumber' => '01322819397', //Replace with Merchant Number (not mandatory)
                'dateTime' => $DateTime,
                'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveData)),
                'signature' => $this->SignatureGenerate(json_encode($SensitiveData))
            );
            $Result_Data = $this->HttpPostMethod($PostURL, $PostData);


            if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
                if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                    $PlainResponse = json_decode($this->DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);


                    if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {


                        $paymentReferenceId = $PlainResponse['paymentReferenceId'];


                        $randomServer = $PlainResponse['challenge'];

                        $SensitiveDataOrder = array(
                            'merchantId' => $MerchantID,
                            'orderId' => $OrderId,
                            'currencyCode' => '050',
                            'amount' => $amount,
                            'challenge' => $randomServer
                        );


                        $logo = base_url('assets/admin/img/logo.png');

                        $merchantAdditionalInfo = '{"serviceName":"Foodi", "serviceLogoURL": "' . $logo . '", "additionalFieldNameEN": "Type", "additionalFieldNameBN": "টাইপ","additionalFieldValue": "Payment"}';

                        $PostDataOrder = array(
                            'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                            'signature' => $this->SignatureGenerate(json_encode($SensitiveDataOrder)),
                            'merchantCallbackURL' => $merchantCallbackURL,
                            'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
                        );


                        $OrderSubmitUrl = "https://api.mynagad.com/api/dfs/check-out/complete/" . $paymentReferenceId;
                        $Result_Data_Order = $this->HttpPostMethod($OrderSubmitUrl, $PostDataOrder);

                        if ($Result_Data_Order['status'] == "Success") {
                            $status = 1;
                            $url = $Result_Data_Order['callBackUrl'];
                            $data['url'] = $url;
                            // echo "<script>window.open($url, '_self')</script>";
                        } else {
                            $status = 0;
                            $data['result_Data_Order'] = $Result_Data_Order;
                        }
                    } else {
                        $status = 0;
                        $data['plain_response'] = $PlainResponse;
                    }
                }
            }
            if ($data['url']) {
                echo "<meta http-equiv='refresh' content='0;url=" . $data['url'] . "'>";
            } else {
                echo "Payment can not be initiated";
            }
            // $this->response(['status' => $status, 'data' => $data['url']], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('validation')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    private function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function EncryptDataWithPublicKey($data)
    {
        $pgPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiCWvxDZZesS1g1lQfilVt8l3X5aMbXg5WOCYdG7q5C+Qevw0upm3tyYiKIwzXbqexnPNTHwRU7Ul7t8jP6nNVS/jLm35WFy6G9qRyXqMc1dHlwjpYwRNovLc12iTn1C5lCqIfiT+B/O/py1eIwNXgqQf39GDMJ3SesonowWioMJNXm3o80wscLMwjeezYGsyHcrnyYI2LnwfIMTSVN4T92Yy77SmE8xPydcdkgUaFxhK16qCGXMV3mF/VFx67LpZm8Sw3v135hxYX8wG1tCBKlL4psJF4+9vSy4W+8R5ieeqhrvRH+2MKLiKbDnewzKonFLbn2aKNrJefXYY7klaawIDAQAB";
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";

        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $cryptText, $key_resource);
        return base64_encode($cryptText);
    }



    private function SignatureGenerate($data)
    {
        $merchantPrivateKey = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCC3RFHVY7+RpECmGd18lI3suNKSaa/CmM6OaQE5T12PUSXhAXjAdvt7obPQ5PL+tdbJrNgnMGh9Zih4Y4meCf7F6BpsnNzH1EsNC+Oo3OoUnIcWdU8g0v9n9TLM6aA8alPu5hZVChjuFY2WOUrlUJmlLuoKyRUwyNTNb1mcSaRfkzzpivWZTRwQQjIvfs/4sHEbcd+ZTaUdWry2FR9ZYjjXNI5HdGZ21zrBB7MiiS4RWcuxYnxPrWgBBWuvl2NQpgycQCF1QIili8RZmd20ZXKs/gEpq8I258MxObMxSlG6FoGJ7wZHTToa5KJI6QGH1HoQap7T7KqxdX0Ro+TxPMLAgMBAAECggEAYWnQn+pHrG65KVZrxbfhjgbC/RzAXHuOC9y2hNJkoyzOb39epnJO1dn2Tjk+vtv2DatMYgGufjKFMRPnLinJkTcwOR4WpL7OPPqH4EU6JjVhLkuM2SPfoGenDrBfJKM/5tN9gBmOi1TAEGqyBXRxXk0fN/sNa29rT1i0qZpXHHvxrsxof4zLYppsXyeX5nd6LntUtgaR2koVKEAnjMaJJ3kPRqMtR29mMcjBRqNCoYdYogDYQ77xLR3HICsoMg3gpy5U79oC8miTX67mPHReoVaB0pEIilmZIt/dd0qLFX1s7miKYjtgsy7MySb+osce7qugP2H+uOjcJmzZBa0sSQKBgQDlbTqJ4coQUm4yaENkO2E+ffvFElpoN00skkS3aVlPCUvKeNRxfRu6FbRmrxLyrDp1WaGOFkWRIviX+N6/wGa0mW58HzrdYCokn/RzD5rI2ndQh+WRJUgc/HP8fIOCqgTXZuxk9GzfR8bnlPO2xx0Tin9WE45BGp1cykKkaxfNFwKBgQCSBVNrRs/iWkFFgB3G3o8kdpq2orQ/vhA6i3kxgodKgzwaawnSvwz3I4v/QOuVfmmWhiRYMAIS/4j6lfjtsqGorvyLfpVimWZDsTIdJfVCNh9jVQwTsj+z5ENf+kqnUjK60YmI4r1CbIW1Jmg2cz0R3Pi/MooQ54ayhfD3q+UKLQKBgDGOYnKd/tN/uqXQt53S5bJl7BgpWrXgHB9giM9FRjE4RNK7Psg0yeRsA8eaUXFxmj722VqjnOs4rpHFA/hPSt//tEnDRSfEOdYnZtnjqP9xkQwoDoJHl0gLj9Id4xo6N0l/xdgYo9um2WP0XKBCahlqdQ7WsoeFhDAi0DpLl5yJAoGBAIrtOEXbtJPmTS2jzDDWtRf7JA3Z1WbEHUqmQmNhjfjNzZRQ/KKfFOXZ0yvDB6FPCYSmEdQ5sO+EYj4QDCuTBg2olyzc/aI20r5ay5RB9gvN1KU8WGYw9DziU1vwlbCGGy/1hkZPe8PD3p7QcNxFXfgt5hh7LDMTJOubVf0+dYbtAoGALG3KI4ycyzK4xti+s4lD2tjY5/wobZrcZHQsVazg1d4jW+Nkn962IoQd9NQLVGlm/qJYZ91awv9Dg1615Fz7SwL9SdAQLSsdnaF1a0R0WUi67E2ZrX657m2gTBM/0qoFgrjeqDBv+8zRMsl/7rwNOySwKHI1q0BeKZpitUH18XY=";
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";

        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }



    private function HttpPostMethod($PostURL, $PostData)
    {
        $url = curl_init($PostURL);
        $postToken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . $this->get_client_ip(),
            'X-KM-Client-Type:MOBILE_APP'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($url, CURLOPT_HEADER, 1);

        $resultData = curl_exec($url);
        $ResultArray = json_decode($resultData, true);
        $header_size = curl_getinfo($url, CURLINFO_HEADER_SIZE);
        curl_close($url);
        // $headers = substr($resultData, 0, $header_size);
        // $body = substr($resultData, $header_size);
        // print_r($body);
        // print_r($headers);
        return $ResultArray;
    }

    private function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    private function DecryptDataWithPrivateKey($cryptText)
    {
        $merchantPrivateKey = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCC3RFHVY7+RpECmGd18lI3suNKSaa/CmM6OaQE5T12PUSXhAXjAdvt7obPQ5PL+tdbJrNgnMGh9Zih4Y4meCf7F6BpsnNzH1EsNC+Oo3OoUnIcWdU8g0v9n9TLM6aA8alPu5hZVChjuFY2WOUrlUJmlLuoKyRUwyNTNb1mcSaRfkzzpivWZTRwQQjIvfs/4sHEbcd+ZTaUdWry2FR9ZYjjXNI5HdGZ21zrBB7MiiS4RWcuxYnxPrWgBBWuvl2NQpgycQCF1QIili8RZmd20ZXKs/gEpq8I258MxObMxSlG6FoGJ7wZHTToa5KJI6QGH1HoQap7T7KqxdX0Ro+TxPMLAgMBAAECggEAYWnQn+pHrG65KVZrxbfhjgbC/RzAXHuOC9y2hNJkoyzOb39epnJO1dn2Tjk+vtv2DatMYgGufjKFMRPnLinJkTcwOR4WpL7OPPqH4EU6JjVhLkuM2SPfoGenDrBfJKM/5tN9gBmOi1TAEGqyBXRxXk0fN/sNa29rT1i0qZpXHHvxrsxof4zLYppsXyeX5nd6LntUtgaR2koVKEAnjMaJJ3kPRqMtR29mMcjBRqNCoYdYogDYQ77xLR3HICsoMg3gpy5U79oC8miTX67mPHReoVaB0pEIilmZIt/dd0qLFX1s7miKYjtgsy7MySb+osce7qugP2H+uOjcJmzZBa0sSQKBgQDlbTqJ4coQUm4yaENkO2E+ffvFElpoN00skkS3aVlPCUvKeNRxfRu6FbRmrxLyrDp1WaGOFkWRIviX+N6/wGa0mW58HzrdYCokn/RzD5rI2ndQh+WRJUgc/HP8fIOCqgTXZuxk9GzfR8bnlPO2xx0Tin9WE45BGp1cykKkaxfNFwKBgQCSBVNrRs/iWkFFgB3G3o8kdpq2orQ/vhA6i3kxgodKgzwaawnSvwz3I4v/QOuVfmmWhiRYMAIS/4j6lfjtsqGorvyLfpVimWZDsTIdJfVCNh9jVQwTsj+z5ENf+kqnUjK60YmI4r1CbIW1Jmg2cz0R3Pi/MooQ54ayhfD3q+UKLQKBgDGOYnKd/tN/uqXQt53S5bJl7BgpWrXgHB9giM9FRjE4RNK7Psg0yeRsA8eaUXFxmj722VqjnOs4rpHFA/hPSt//tEnDRSfEOdYnZtnjqP9xkQwoDoJHl0gLj9Id4xo6N0l/xdgYo9um2WP0XKBCahlqdQ7WsoeFhDAi0DpLl5yJAoGBAIrtOEXbtJPmTS2jzDDWtRf7JA3Z1WbEHUqmQmNhjfjNzZRQ/KKfFOXZ0yvDB6FPCYSmEdQ5sO+EYj4QDCuTBg2olyzc/aI20r5ay5RB9gvN1KU8WGYw9DziU1vwlbCGGy/1hkZPe8PD3p7QcNxFXfgt5hh7LDMTJOubVf0+dYbtAoGALG3KI4ycyzK4xti+s4lD2tjY5/wobZrcZHQsVazg1d4jW+Nkn962IoQd9NQLVGlm/qJYZ91awv9Dg1615Fz7SwL9SdAQLSsdnaF1a0R0WUi67E2ZrX657m2gTBM/0qoFgrjeqDBv+8zRMsl/7rwNOySwKHI1q0BeKZpitUH18XY=";
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
        return $plain_text;
    }
}
