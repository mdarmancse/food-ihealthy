<?php
defined('BASEPATH') or exit('No direct script access allowed');
//error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Bkash extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this
            ->load
            ->model('v1/api_model');
        $this
            ->load
            ->library('form_validation');
        $this->current_lang = "en";
    }
    //bKAsh
    public function agreementExecute($paymentid)
    {
        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $execute_url = $array['execute_url'];
        $token = $this->getAuthbypayment($paymentid);
        $request_data = array(
            'paymentID' => $paymentid

        );
        $url = curl_init($execute_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-App-Key:' . $key,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        curl_close($url);
        $result = json_decode($content, true);
        //$this->response(['status' => 1, 'msg' => $result, ], REST_Controller::HTTP_OK);
        $statusMessage = $result['statusMessage'] ? $result['statusMessage'] : null;
        $transactionStatus = $result['agreementStatus'] ? $result['agreementStatus'] : null;
        $agreementId = $result['agreementID'] ? $result['agreementID'] : null;
        $customerMsisdn = $result['customerMsisdn'] ? $result['customerMsisdn'] : null;
        if ($statusMessage == "Successful" && $transactionStatus == "Completed") {
            echo '
        <html>
            <script>
                const data = {
                    "payment_id" : "' . $paymentid . '",
                    "agreement_id" : "' . $agreementId . '",
                     "customerMsisdn" : "' . $customerMsisdn . '",
                    "message":"' . $statusMessage . '",
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
                    "message" : "' . $statusMessage . '",
                    "errorCode" : "Error3",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }
    }
    public function AgreementSuccess_get()
    {

        $x = $_GET["paymentID"];
        $y = $_GET["status"];
        if ($x && $y == "success") {
            $this->agreementExecute($x);
        } else if ($y == "failure") {
            echo '
        <html>
            <script>
                const data = {
                    "message":"failure",
                    "errorCode" : "Error4",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        } else if ($y == "cancel") {
            echo '
        <html>
            <script>
                const data = {
                    "message":"cancel",
                    "errorCode" : "Error5",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }
    }
    public function createAgreement_post()
    {
        $post_data = json_decode($this->input->raw_input_stream, true);
        $total_amount = $post_data['total_amount'];
        $user_id = $post_data['user_Id'];
        // $total_amount = 11;
        // $user_id=1234;
        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $create_url = $array['create_url'];
        $user = $array['Username'];
        $pass = $array['Password'];
        $callbackURL = $array['callback_agreement'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $this->saveToken($user_id, $result['id_token']);
            $request_data = array(
                'mode' => '0000',
                'payerReference' => '01XXXXXXXXX',
                'callbackURL' => $callbackURL

            );
            $url = curl_init($create_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            curl_close($url);
            $result = json_decode($content, true);
            if ($result['bkashURL']) {
                $id_init = $result['paymentID'];
                $this->savePaymentid($user_id, $id_init);

                echo "<meta http-equiv='refresh' content='0;url=" . $result['bkashURL'] . "'>";
            } else {
                echo '
        <html>
            <script>
                data = {
                    "message" : "Payment failed",
                    "errorCode" : "Error1",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html
      ';
            }
        } else {
            echo '
        <html>
            <script>
                data = {
                    "message" : "Payment failed",
                    "errorCode" : "Error2",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html
      ';
        }
    }
    public function grant_post()
    {
        $post_data = json_decode($this->input->raw_input_stream, true);
        $total_amount = $post_data['total_amount'];
        $user_id = $post_data['user_Id'];
        $agreementID = $post_data['agreementID'];
        //$total_amount = 11;
        // $user_id=1234;
        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $create_url = $array['create_url'];
        $user = $array['Username'];
        $pass = $array['Password'];
        $callbackURL = $array['callback'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $this->saveToken($user_id, $result['id_token']);
            $request_data = array(
                'mode' => '0001',
                'amount' => $total_amount,
                'currency' => 'BDT',
                'intent' => 'sale',
                'payerReference' => '01XXXXXXXXX',
                'agreementID' => $agreementID,
                'merchantInvoiceNumber' => 'commonPayment001',
                'callbackURL' => $callbackURL

            );
            $url = curl_init($create_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            curl_close($url);
            $result = json_decode($content, true);
            if ($result['bkashURL']) {
                $id_init = $result['paymentID'];
                $this->savePaymentid($user_id, $id_init);

                echo "<meta http-equiv='refresh' content='0;url=" . $result['bkashURL'] . "'>";
            } else {
                echo '
        <html>
            <script>
                data = {
                    "message" : "Payment failed",
                    "errorCode" : "Error1",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html
      ';
            }
        } else {
            echo '
        <html>
            <script>
                data = {
                    "message" : "Payment failed",
                    "errorCode" : "Error2",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html
      ';
        }
    }

    //end bKash
    public function execute($paymentid)
    {
        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $execute_url = $array['execute_url'];
        $token = $this->getAuthbypayment($paymentid);
        $request_data = array(
            'paymentID' => $paymentid

        );
        $url = curl_init($execute_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-App-Key:' . $key,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        curl_close($url);
        $result = json_decode($content, true);
        //$this->response(['status' => 1, 'msg' => $result, ], REST_Controller::HTTP_OK);
        $statusMessage = $result['statusMessage'] ? $result['statusMessage'] : null;
        $transactionStatus = $result['transactionStatus'] ? $result['transactionStatus'] : null;
        $trxID = $result['trxID'] ? $result['trxID'] : null;
        if ($statusMessage == "Successful" && $transactionStatus == "Completed") {
            echo '
        <html>
            <script>
                const data = {
                    "trxID" : "' . $paymentid . '",
                    "invoice" : "' . $trxID . '",
                    "message":"Payment Successfull",
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
                    "message" : "' . $statusMessage . '",
                    "errorCode" : "Error31"
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }
    }
    public function bKashSuccess_get()
    {

        $x = $_GET["paymentID"];
        $y = $_GET["status"];
        if ($x && $y == "success") {
            $this->execute($x);
        } else if ($y == "failure") {
            echo '
        <html>
            <script>
                const data = {
                    "message":"failure",
                    "errorCode" : "Error4",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        } else if ($y == "cancel") {
            echo '
        <html>
            <script>
                const data = {
                    "message":"cancel",
                    "errorCode" : "Error5",
                }
                window.ReactNativeWebView.postMessage(JSON.stringify(data));
            </script>
        </html>
      ';
        }
    }

    public function saveToken($userid, $token)
    {
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     $sql = "update cart_amount set id_token='" . $token . "'  where user_id = '" . $userid . "'";

        //     $result = $conn->query($sql);
        // }

        $this->db->set('id_token', $token);
        $this->db->where('user_id', $userid);
        $this->db->update('cart_amount');
    }
    public function savePaymentid($userid, $token)
    {
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     $sql = "update cart_amount set payment_id='" . $token . "'  where user_id = '" . $userid . "'";

        //     $result = $conn->query($sql);
        // }

        $this->db->set('payment_id', $token);
        $this->db->where('user_id', $userid);
        $this->db->update('cart_amount');
    }
    public function saveagreement_post()
    {
        $user_id = $this->post('user_id');
        $msidn = $this->post('msidn');
        $agreementid = $this->post('agreementid');
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     // $sql = "select id_token from cart_amount  where payment_id = '" . $paymentid . "'";
        //     $sql = "INSERT INTO `agreement_info` (`user_id`, `customerMsisdn`, `paymentID`) VALUES ('" . $user_id . "', '" . $msidn . "', '" . $agreementid . "')";

        //     $result = $conn->query($sql);
        //     if ($result) {
        //         $sql2 = "SELECT customerMsisdn,paymentID FROM agreement_info WHERE user_id = " . $user_id . "";
        //         $this->response(['status' => 1, 'msg' => $result,], REST_Controller::HTTP_OK);
        //     }
        // }

        $data = array(
            'user_id' => $user_id,
            'customerMsisdn' => $msidn,
            'paymentID' => $agreementid,
        );

        $result = $this->db->insert('agreement_info', $data);

        if ($result) {
            $this->response(['status' => 1, 'msg' => $result,], REST_Controller::HTTP_OK);
        }
    }
    public function getAuthbypayment($paymentid)
    {
        // $token_id = "";
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     $sql = "select id_token from cart_amount  where payment_id = '" . $paymentid . "'";

        //     $result = $conn->query($sql);
        //     while ($row = $result->fetch_array()) {
        //         $token_id = $row['id_token'];
        //     }
        //     return $token_id;
        // }
        $this->db->select('id_token');
        $this->db->where('payment_id', $paymentid);
        $res = $this->db->get('cart_amount')->first_row();

        if ($res)
            return $res->id_token;
    }
    public function getToken($userid)
    {
        // $token_id = "";
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     $sql = "select id_token from cart_amount  where user_id = '" . $userid . "'";

        //     $result = $conn->query($sql);
        //     while ($row = $result->fetch_array()) {
        //         $token_id = $row['id_token'];
        //     }
        //     return $token_id;
        // }

        $this->db->select('id_token');
        $this->db->where('user_id', $userid);
        $res = $this->db->get('cart_amount')->first_row();

        if ($res)
            return $res->id_token;
    }
    public function test_post()
    {
        $content = $this->saveToken(123456789123, 'aefsfg,vnsjklfnvlskj');
        $this->response(['status' => 1, 'msg' => $content,], REST_Controller::HTTP_OK);
    }
    public function refund_post()
    {
        $PAYMENT_ID = $this->post('payment_id');
        $TRX_ID = $this->post('trxID');
        $TOTAL = $this->post('total');
        $reason = $this->post('reason');
        //$TOTAL="12";
        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $query_url = $array['refund'];
        $user = $array['Username'];
        $pass = $array['Password'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        //echo file_put_contents("testx.txt",".$content.");
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $request_data = array(
                'paymentID' => $PAYMENT_ID,
                'amount' => $TOTAL,
                'trxID' => $TRX_ID,
                'sku' => "sale",
                'reason' => $reason

            );
            $url = curl_init($query_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            //echo file_put_contents("testy.txt",".$content.");
            curl_close($url);
            $result = json_decode($content, true);
            $code = $result['statusCode'];
            $MSG = $result['statusMessage'];
            if ($code = "0000" && $MSG == "Successful") {
                $this->refundStatus($PAYMENT_ID, $TRX_ID);
            } else {
                $this->response(['status' => -1, 'msg' => "Something went wrong", 'ms1' => $request_data], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(['status' => -2, 'msg' => "Something Went wrong",], REST_Controller::HTTP_OK);
        }
    }
    public function refundStatus($x, $y)
    {
        $PAYMENT_ID = $x;
        $TRX_ID = $y;


        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $query_url = $array['refund'];
        $user = $array['Username'];
        $pass = $array['Password'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        //echo file_put_contents("testg.txt",".$content.");
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $request_data = array(
                'paymentID' => $PAYMENT_ID,
                'trxID' => $TRX_ID,

            );
            $url = curl_init($query_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            //echo file_put_contents("testj.txt",".$content.");
            curl_close($url);
            $result = json_decode($content, true);
            $this->response(['status' => 1, 'msg' => $result,], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => -1, 'msg' => "Something Went wrong",], REST_Controller::HTTP_OK);
        }
    }
    public function CancelAgreement_post()
    {
        $agreementID = $this->post('agreementID');


        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $query_url = $array['cancel_agreement'];
        $user = $array['Username'];
        $pass = $array['Password'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        //echo file_put_contents("testg.txt",".$content.");
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $request_data = array(
                'agreementID' => $agreementID,

            );
            $url = curl_init($query_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            //echo file_put_contents("testj.txt",".$content.");
            curl_close($url);
            $result = json_decode($content, true);
            if ($result) {
                $this->deleteAgreement($agreementID);
            }
            $this->response(['status' => 1, 'msg' => $result,], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => -1, 'msg' => "Something Went wrong",], REST_Controller::HTTP_OK);
        }
    }
    public function CheckrefundStatus_post()
    {
        $PAYMENT_ID = $this->post('payment_id');
        $TRX_ID = $this->post('trx_id');


        $strJsonFileContents = file_get_contents("https://foodibd.com/bkashconfog.json");
        $array = json_decode($strJsonFileContents, true);
        $key = $array['app_key'];
        $secret = $array['app_secret'];
        $grant_url = $array['grant_url'];
        $query_url = $array['refund'];
        $user = $array['Username'];
        $pass = $array['Password'];

        $request_data = array(
            'app_key' => $key,
            'app_secret' => $secret
        );
        $url = curl_init($grant_url);
        $request_data_json = json_encode($request_data);
        $header = array(
            'Content-Type:application/json',
            'username:' . $user,
            'password:' . $pass,
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $content = curl_exec($url);
        //echo file_put_contents("testg.txt",".$content.");
        curl_close($url);
        $result = json_decode($content, true);

        //create payment
        if ($result['id_token']) {
            $request_data = array(
                'paymentID' => $PAYMENT_ID,
                'trxID' => $TRX_ID,

            );
            $url = curl_init($query_url);
            $request_data_json = json_encode($request_data);
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $result['id_token'],
                'X-App-Key:' . $key,
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $content = curl_exec($url);
            //echo file_put_contents("testj.txt",".$content.");
            curl_close($url);
            $result = json_decode($content, true);
            $this->response(['status' => 1, 'msg' => $result,], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => -1, 'msg' => "Something Went wrong",], REST_Controller::HTTP_OK);
        }
    }
    public function deleteAgreement($id)
    {
        // $conn = new mysqli('34.143.203.49', $this
        //     ->db->username, $this
        //     ->db->password, $this
        //     ->db
        //     ->database);
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // } else {

        //     //$sql = "select session_id from users  where entity_id = '" . $userid . "'";
        //     $sql = "update agreement_info set status= 0  where paymentID = '" . $id . "'";

        //     $result = $conn->query($sql);
        // }

        $this->db->set('status', 0);
        $this->db->where('paymentID', $id);
        $this->db->update('agreement_info');
    }
}
