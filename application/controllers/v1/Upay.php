<?php
defined('BASEPATH') or exit('No direct script access allowed');
//error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Upay extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/api_model');
        $this->load->library('form_validation');
        $this->current_lang = "en";
    }
    public function upayAuth_post()
    {
        $post_data = json_decode($this->input->raw_input_stream, true);
        $total_amount = $post_data['total_amount'];
        $url = "https://pg.upaysystem.com/payment/merchant-auth/";
        $post_data = array();
        $post_data['merchant_id'] = "1150101050005345";
        $post_data['merchant_key'] = "9Gt8aptAtSISnArQ1NTlToRv4edhRghB";
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);
        $respdata = json_decode($content, true);
        $token = $respdata['data']['token'];
        $this->PaymentInit($token, $total_amount);
        // is->response([
        //      'status' => 1,
        //      'msg' =>  $content,$c$conte
        //  ], REST_Controller::HTTP_OK);
    }
    public function sslCancel_get()
    {

        $x = $_GET["invoice_id"];
        $y = $_GET["status"];
        if ($y == "successful") {
            echo '
        <html>
            <script>
                const data = {
                    "invoice" : "' . $x . '",
                    "status" : "' . $y . '",
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
                    "invoice" : "' . $x . '",
                    "status" : "' . $y . '",
                    "message":"Payment Failed",
                    "errorCode" : "Error",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }
    }
    public function PaymentInit($token, $total_amount)
    {
        $url = "https://pg.upaysystem.com/payment/merchant-payment-init/";
        $DateTime = Date('Y-m-d');
        $post_data = array();
        $post_data['date'] = $DateTime;
        $post_data['txn_id'] = rand(1234, 10000);
        $post_data['invoice_id'] = rand(12345, 1000000);
        $post_data['amount'] = $total_amount;
        $post_data['merchant_id'] = "1150101050005345";
        $post_data['merchant_name'] = "FOODI EXPRESS LIMITED";
        $post_data['merchant_code'] = "5262";
        $post_data['merchant_country_code'] = "BD";
        $post_data['merchant_city'] = "Dhaka";
        $post_data['merchant_category_code'] = "5262";
        $post_data['merchant_mobile'] = "01322819397";
        $post_data['transaction_currency_code'] = "BDT";
        $post_data['redirect_url'] = "https://foodibd.com/v1/Upay/sslCancel";
        $headers = array(
            "Authorization:UPAY " . $token,
        );
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);
        $respdata = json_decode($content, true);
        $gateway_url = $respdata['data']['gateway_url'];
        if ($gateway_url) {

            echo "<meta http-equiv='refresh' content='0;url=" . $gateway_url . "'>";
        } else {
            echo "payment initialization failed";
        }
        // $this->response([
        //         'status' => 1,
        //         'msg' =>  $token,
        //         "pd"=>$post_data,
        //         "respdata"=>$respdata['data']['gateway_url']
        //     ], REST_Controller::HTTP_OK);

    }
    public function upaySuccess_get()
    {
        //$Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
        $Query_String = 1;
        echo '
        <html>
            <script>
                data = {
                    "message" : $Query_String,
                    "errorCode" : "Error",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html
      ';
    }
    public function test_post()
    {
        $this->response([
            'status' => 1,
            'msg' =>  $content,
        ], REST_Controller::HTTP_OK);
    }
}
