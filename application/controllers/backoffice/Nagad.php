<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Nagad extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
        $payment_ref_id = substr($Query_String[2], 15);
        $url = "https://api.mynagad.com/api/dfs/verify/payment/" . $payment_ref_id;
        $json = $this->HttpGet($url);
        $arr = json_decode($json, true);

        if ($arr['status'] == "Success") {
            echo '
        <html>
            <script>
                const data = {
                    "invoice" : "' . $arr['paymentRefId'] . '",
                    "status" : "' . $arr['status'] . '",
                    "message":"Payment successful",
                    "errorCode" : null,
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        } else {
            echo '
        <html>
            <script>
                const data = {
                    "status" : "' . $arr['status'] . '",
                    "message":"Payment Failed",
                    "errorCode" : "Error",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }

        // file_put_contents('nagad.txt', $json, FILE_APPEND);
    }

    private function HttpGet($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }
}
