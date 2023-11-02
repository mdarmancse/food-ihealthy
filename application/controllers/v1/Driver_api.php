<?php

defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Driver_api extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        $this->current_lang = 'en';
        $this->load->model('v1/driver_api_model');
        $this->load->model('v1/api_model');
        $this->load->library('form_validation');
    }
    //common lang fucntion
    public function getLang()
    {
        $this->current_lang = ($this->post('language_slug')) ? $this->post('language_slug') : $this->current_lang;
        $languages = $this->driver_api_model->getLanguages($this->current_lang);
        $this->lang->load('messages_lang', $languages->language_directory);
    }
    //bkash
    public function bksh_post()
    {
        $ux = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant";
        $header = array(
            'Content-Type:application/json',
            'username:sandboxTokenizedUser01',
            'password:sandboxTokenizedUser12345'
        );
        $body = array();
        $body['app_key'] = '7epj60ddf7id0chhcm3vkejtab';
        $body['app_secret'] = '18mvi27h9l38dtdv110rq5g603blk0fhh5hg46gfb27cp2rbs66f';
        $url = curl_init();
        curl_setopt($url, CURLOPT_URL, 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);

        $resultdata = curl_exec($url);
        $resultdata2 = json_decode($resultdata, true);
        curl_close($url);
        $this->response([
            'status' => 1,
            'id_token' => $resultdata2["id_token"]
        ], REST_Controller::HTTP_OK);
    }
    public function create_post()
    {

        $token = $this->post('id_token');
        $amount = $this->post('amount') ? $this->post('amount') : 0;
        $data = array(
            'device_id' => $token
        );

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $invoice = $randomString; // must be unique
        $intent = "sale";
        $proxy = "";
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $createpaybody = array('amount' => $amount, 'currency' => 'BDT', 'merchantInvoiceNumber' => $invoice, 'intent' => $intent);
        $url = curl_init("https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/create");

        $createpaybodyx = json_encode($createpaybody);

        $header = array(
            'Content-Type:application/json',
            'authorization:' . $token,
            'x-app-key:7epj60ddf7id0chhcm3vkejtab'
        );
        echo $_GET["token"];
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);

        $resultdata = curl_exec($url);
        curl_close($url);
        $this->response([

            'resultdata' => $resultdata
        ], REST_Controller::HTTP_OK);
    }
    public function execute_post()
    {

        $token = $this->post('token');
        $paymentID = $this->post('payment_id');
        $intent = "sale";
        $proxy = "";
        $urlLink = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/execute/" . $paymentID;
        $url = curl_init($urlLink);
        $header = array(
            'Content-Type:application/json',
            'authorization:' . $token,
            'x-app-key:7epj60ddf7id0chhcm3vkejtab'
        );
        echo $_GET["token"];
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);

        $resultdata = curl_exec($url);
        curl_close($url);
        $this->response([

            'resultdata' => $resultdata
        ], REST_Controller::HTTP_OK);
    }
    public function query_post()
    {

        $token = $this->post('token');
        $paymentID = $this->post('payment_id');
        $intent = "sale";
        $proxy = "";
        $urlLink = "https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/query/" . $paymentID;
        $url = curl_init($urlLink);
        $header = array(
            'Content-Type:application/json',
            'authorization:' . $token,
            'x-app-key:7epj60ddf7id0chhcm3vkejtab'
        );
        echo $_GET["token"];
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);

        $resultdata = curl_exec($url);
        curl_close($url);
        $this->response([

            'resultdata' => $resultdata
        ], REST_Controller::HTTP_OK);
    }
    //bkash
    // Login API
    public function login_post()
    {
        $this->getLang();
        $login = $this->driver_api_model->getLogin($this->post('PhoneNumber'), $this->post('Password'));
        if (!empty($login)) {
            $data = array('device_id' => $this->post('firebase_token'));
            if ($login->status == 1) {
                // update device
                $image = ($login->image) ? image_url . $login->image : '';
                $traking_data = array(
                    'latitude' => $this->post('latitude'),
                    'longitude' => $this->post('longitude'),
                    'driver_id' => $login->entity_id,

                );
                $this->driver_api_model->addRecord('driver_traking_map', $traking_data);

                $this->driver_api_model->updateUser('users', $data, 'entity_id', $login->entity_id);
                $login_detail = array(
                    'FirstName' => $login->first_name,
                    'image' => $image,
                    'PhoneNumber' => $login->mobile_number,
                    'UserID' => $login->entity_id,
                    'rider_types' => "Biker",
                    'vehicle_type'  => $login->vehicle_name
                );
                $this->response(['login' => $login_detail, 'status' => 1, 'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else if ($login->status == 0) {
                $adminEmail = $this->driver_api_model->getSystemOptoin('Admin_Email_Address');
                $this->response(['status' => 2, 'message' => $this->lang->line('login_deactive'), 'email' => $adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else {
            $emailexist = $this->driver_api_model->getRecord('users', 'mobile_number', $this->post('PhoneNumber'));
            if ($emailexist) {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('pass_validation')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    // Forgot Password
    public function forgotpassword_post()
    {
        $this->getLang();
        $checkRecord = $this->driver_api_model->getRecordMultipleWhere('users', array('mobile_number' => $this->post('mobile_number'), 'status' => 1));
        if (!empty($checkRecord)) {
            $activecode = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $password = random_string('alnum', 8);
            $data = array('active_code' => $activecode, 'password' => md5(SALT . $password));
            $this->driver_api_model->updateUser('users', $data, 'mobile_number', $this->post('mobile_number'));
            $this->response(['status' => 1, 'password' => $password, 'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('user_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //add review
    public function addReview_post()
    {
        $this->getLang();
        if ($this->post('rating') != '' && $this->post('review') != '') {
            $add_data = array(
                'rating' => trim($this->post('rating')),
                'review' => trim($this->post('review')),
                'order_user_id' => $this->post('order_user_id'),
                'user_id' => $this->post('user_id'),
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->driver_api_model->addRecord('review', $add_data);
            $this->response(['status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('validation')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function editProfile_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenusr = $this->driver_api_model->checkToken($token, $user_id);
        if ($tokenusr) {
            $add_data = array(
                'first_name' => $this->post('first_name'),
            );
            if (!empty($_FILES['image']['name'])) {
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/profile';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/profile')) {
                    @mkdir('./uploads/profile', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('image')) {
                    $img = $this->upload_cloud->data();
                    $add_data['image'] = "profile/" . $img['file_name'];
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }
            $this->driver_api_model->updateUser('users', $add_data, 'entity_id', $this->post('user_id'));
            $token = $this->driver_api_model->checkToken($token, $user_id);
            $image = ($token->image) ? image_url . $token->image : '';
            $login_detail = array('FirstName' => $token->first_name, 'image' => $image, 'PhoneNumber' => $token->mobile_number, 'UserID' => $token->entity_id);
            $this->response(['profile' => $login_detail, 'status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //change address
    public function changePassword_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        if ($tokenres) {
            if (md5(SALT . $this->post('old_password')) == $tokenres->password) {
                if ($this->post('confirm_password') == $this->post('password')) {
                    $this->db->set('password', md5(SALT . $this->post('password')));
                    $this->db->where('entity_id', $user_id);
                    $this->db->update('users');
                    $this->response(['status' => 1, 'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('confirm_password')], REST_Controller::HTTP_OK); // OK
                }
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('old_password')], REST_Controller::HTTP_OK); // OK
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //accept order
    public function acceptOrder_post()
    {
        $this->getLang();
        $check = 0;
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        $checkAccept = $this->driver_api_model->checkAccept($this->post('order_id'));
        if ($tokenres) {
            $order_id = $this->post('order_id');
            $driver_map_id = $this->post('driver_map_id');
            if ($order_id) {
                $details = $this->driver_api_model->getRecordMultipleWhere('order_driver_map', array('driver_map_id' => $driver_map_id));
                if (!empty($details) && $this->driver_api_model->isOrderAvailable($order_id, $user_id) == true) {
                    if ($this->post('order_status') == 'cancel') {
                        if ($this->post('cancel_reason') != '') {
                            $add_data = array('cancel_reason' => $this->post('cancel_reason'), 'cancel' => 1);
                            $this->driver_api_model->updateUser('order_driver_map', $add_data, 'driver_map_id', $driver_map_id);
                            $this->driver_api_model->updateEngage($user_id);

                            $this->api_model->updateDriver($order_id);


                            // $data = array('order_id'=>$order_id,'order_status'=>'placed','time'=>date('Y-m-d H:i:s'),'status_created_by'=>'Driver');
                            // $this->driver_api_model->addRecord('order_status',$data);

                            // $this->db->set('order_status','placed')->where('entity_id', $order_id)->update('order_master');
                            //  $this->db->set('status','0')->where('entity_id', $order_id)->update('order_master');
                            //get user of order
                            // $userData = $this->driver_api_model->getUserofOrder($order_id);
                            // // load language
                            // $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$userData->language_slug))->first_row();
                            // $this->lang->load('messages_lang', $languages->language_directory);

                            // if(!empty($userData) && $userData->device_id){
                            //     #prep the bundle
                            //     $fields = array();
                            //     $message = $this->lang->line('push_order_cancel');
                            //     $fields['to'] = $userData->device_id; // only one user to send push notification
                            //     $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                            //     $fields['data'] = array ('screenType'=>'order');

                            //     $headers = array (
                            //         'Authorization: key=' . Driver_FCM_KEY,
                            //         'Content-Type: application/json'
                            //     );
                            //     #Send Reponse To FireBase Server
                            //     $ch = curl_init();
                            //     curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                            //     curl_setopt( $ch,CURLOPT_POST, true );
                            //     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                            //     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                            //     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                            //     curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                            //     $result = curl_exec($ch);
                            //     curl_close($ch);
                            // }
                        }
                        // else{
                        //     $this->driver_api_model->deleteRecord('order_driver_map','driver_map_id',$driver_map_id);
                        // }
                        // adding notification for website
                        // $order_detail = $this->driver_api_model->getRecord('order_master','entity_id',$order_id);
                        // $notification = array(
                        //     'order_id' => $order_id,
                        //     'user_id' => $order_detail->user_id,
                        //     'notification_slug' => 'order_canceled',
                        //     'view_status' => 0,
                        //     'datetime' => date("Y-m-d H:i:s"),
                        // );
                        // $this->driver_api_model->addRecord('user_order_notification',$notification);

                        // $this->response(['status'=>1,'message' => $this->lang->line('order_cancel')], REST_Controller::HTTP_OK); // OK */
                    } else {
                        if ($checkAccept->status == 0) {
                            $this->response(['status' => 1, 'status2' => 0, 'message' => "wait"], REST_Controller::HTTP_OK); // OK */
                        } else if ($this->driver_api_model->isOrderAvailable($order_id, $user_id) == false) {
                            //  $x=$this->driver_api_model->isOrderAvailable($order_id,$user_id) ;
                            $this->response(['status' => 1, 'status2' => 0, 'status3' => $x, 'message' => "passed"], REST_Controller::HTTP_OK); // OK */
                        } else {
                            $add_data = array(
                                'order_id' => $order_id, 'order_status' => 'preparing', 'time' => date('Y-m-d H:i:s'), 'status_created_by' => 'Driver',
                                'updated_by' => $user_id

                            );
                            $this->driver_api_model->addRecord('order_status', $add_data);
                            $detail = $this->driver_api_model->acceptOrder($order_id, $driver_map_id, $user_id);
                            // adding notification for website
                            $order_detail = $this->driver_api_model->getRecord('order_master', 'entity_id', $order_id);
                            $notification = array(
                                'order_id' => $order_id,
                                'user_id' => $order_detail->user_id,
                                'notification_slug' => 'order_preparing',
                                'view_status' => 0,
                                'datetime' => date("Y-m-d H:i:s"),
                            );
                            $this->driver_api_model->addRecord('user_order_notification', $notification);
                            //unlink($order_id . '.json');
                            $this->response(['user_detail' => $detail, 'status' => 1, 'status2' => 1, 'message' => $checkAccept->status], REST_Controller::HTTP_OK); // OK */
                        }
                    }
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('order_accepted')], REST_Controller::HTTP_OK); // OK */
                }
            } else {

                if ($checkAccept->status != 1) {
                    $response = 'Order did not accepted by restaurant';
                    $st = 10;
                } else {
                    $response = $this->lang->line('not_found');
                }

                $this->response([
                    'status' => $st == 10 ? 1 : 0,
                    'message' => $response
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get order of driver
    //get order of driver
    public function getallOrder_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $commissionget = $this->driver_api_model->getSystemOptoin($this->post('type'));
            $commission = $commissionget->OptionValue;
            $detail = $this->driver_api_model->getallOrder($user_id);
            $this->response(['order_list' => $detail, 'status' => 1, 'rider_commission' => $commission, 'message' => $this->lang->line('record_found'), 'suspend' => $tokenres->suspend], REST_Controller::HTTP_OK); // OK */
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //change status after delivery
    public function deliveredOrder_post()
    {
        $avail = $this->driver_api_model->isOrderAvailable($this->post('order_id'), $this->post('user_id'));
        if ($avail == true) {

            $this->getLang();
            $HC = $this->post('hand_cash');
            $token = $this->post('token');
            $user_id = $this->post('user_id');
            $weeklyBonus = 0;
            $TotalBonus = 0;
            $customer_pay = $this->post('customer_pay');
            $tokenres = $this->driver_api_model->checkToken($token, $user_id);
            if ($tokenres) {
                $order_id = $this->post('order_id');
                $status = $this->post('status');
                $subtotal = $this->post('subtotal');
                $add_data = array('order_id' => $order_id, 'order_status' => $status, 'time' => date('Y-m-d H:i:s'), 'status_created_by' => 'Driver');
                $this->driver_api_model->addRecord('order_status', $add_data);
                $detail = $this->driver_api_model->deliveredOrder($order_id, $user_id, $status, $subtotal);
                // adding notification for website
                $order_detail = $this->driver_api_model->getRecord('order_master', 'entity_id', $order_id);
                $notification = array(
                    'order_id' => $order_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => 'order_delivered',
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->driver_api_model->addRecord('user_order_notification', $notification);
                if (!empty($customer_pay)) {
                    $lastdata = $this->driver_api_model->getLastRecord('riders_earning', 'rider_id', $user_id);
                    $weeklyOrders = $this->driver_api_model->getWeeklyOrders($this->post('user_id'));
                    $questOrder = $this->driver_api_model->getSystemMultiOptoin("questride", $this->post('type'));
                    if ($weeklyOrders == $questOrder->OptionValue) {
                        $bonus = $this->driver_api_model->getSystemMultiOptoin("questbonus", $this->post('type'));
                        $weeklyBonus = $bonus->OptionValue;
                        $TotalBonus = $lastdata->total_bonus ? ($lastdata->total_bonus + $weeklyBonus) : $weeklyBonus;
                    } else {
                        $TotalBonus = $lastdata->total_bonus;
                    }
                    $earn_total = $lastdata->total_earn;
                    $hand_total = $lastdata->total_hand;
                    $commissionget = $this->driver_api_model->getSystemOptoin($this->post('type'));
                    $commission = $commissionget->OptionValue;
                    $tot_earn = $earn_total != null ? $earn_total + $commission : $commission;
                    $tot_hand = $hand_total != null ? $HC + $hand_total : $HC;
                    //$hand_cash= ($this->post('rider_earn')) - $commission ;
                    $data = array(
                        'rider_id' => $this->post('user_id'),
                        'order_id' => $this->post('order_id'),
                        'customer_pay' => $this->post('customer_pay'),
                        'restaurant_pay' => $this->post('rest_pay'),
                        'hand_cash' => $this->post('hand_cash'),
                        'rider_earning' => $commission,
                        'total_earn' => $tot_earn,
                        'total_hand' => $tot_hand,
                        'weekly_bonus' => $weeklyBonus,
                        'total_bonus' => $TotalBonus,
                        'date' => date("Y-m-d"),

                    );
                    $this->driver_api_model->addRecord('riders_earning', $data);
                }

                $this->response(['order_detail' => $detail, 'status' => 1, 'message' => $status, 'totearn' => $lastdata, 'th' => $tot_hand, "x" => $avail], REST_Controller::HTTP_OK); // OK */
            } else {
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => 'Order is not available to you'
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    //get order commission
    public function getCommissionList_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $detail = $this->driver_api_model->getCommissionList($user_id);
            $this->response(['CommissionList' => $detail, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
            $this->response(['status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //track driver location
    public function driverTracking_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        $foo = $this->post('foo');
        if ($tokenres) {
            if ($this->post('latitude') && $this->post('longitude')) {
                //$data = array('latitude'=>$this->post('latitude'),'longitude'=>$this->post('longitude'));
                // $this->driver_api_model->updateUser('driver_traking_map',$data,'driver_id',$user_id);
                $traking_data = array(
                    'latitude' => $this->post('latitude'),
                    'longitude' => $this->post('longitude'),
                    'driver_id' => $user_id,
                    'foo' => $foo ? $foo : 'empty',
                    'user_status' => $tokenres->status,
                    'onoff' => $tokenres->onoff,
                    'suspend' => $tokenres->suspend,
                    'engage' => $tokenres->engage
                );
                $this->driver_api_model->addRecord('driver_traking_map', $traking_data);
                $this->response(['status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
            } else {
                $this->response([
                    'status' => 0,
                    'message' =>  $this->lang->line('validation')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //Logout USER
    public function logout_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $userid = $this->post('user_id');
        $tokenres = $this->driver_api_model->getRecord('users', 'entity_id', $userid);
        if ($tokenres) {
            $data = array('device_id' => "");
            $this->driver_api_model->updateUser('users', $data, 'entity_id', $tokenres->entity_id);
            $this->response(['status' => 1, 'message' => $this->lang->line('user_logout')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get user lang
    public function getUserLanguage_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->driver_api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $data = array('language_slug' => $this->post('language_slug'));
            $this->driver_api_model->updateUser('users', $data, 'entity_id', $user_id);
            $this->response(['status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //change firebase token
    public function changeToken_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $userid = $this->post('user_id');
        $tokenusr = $this->driver_api_model->checkToken($token, $userid);
        if ($tokenusr) {
            $data = array('device_id' => $this->post('firebase_token'));
            $this->driver_api_model->updateUser('users', $data, 'entity_id', $userid);
            $this->response(['status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function OnOff_post()
    {
        $data = $this->driver_api_model->getRecord('users', 'entity_id', $this->post('user_id'));
        $this->response([
            'status' => 1,
            'datas' => $data->onoff,
        ], REST_Controller::HTTP_OK);
    }
    public function editOnOff_post()
    {
        $data = array('onoff' => $this->post('onoff'));
        $this->driver_api_model->updateUser('users', $data, 'entity_id', $this->post('entity_id'));
        $this->response([
            'status' => 1,

        ], REST_Controller::HTTP_OK);
    }

    //get ride history
    public function getRidingHistory_post()
    {
        $total_order = $this->driver_api_model->getNumberOfRecords($this->post('user_id'));
        $lastdata = $this->driver_api_model->getLastRecord('riders_earning', 'rider_id', $this->post('user_id'));
        $this->response(['tot_ride' => $total_order, 'last_data' => $lastdata, 'status' => 1, 'message' => "record found"], REST_Controller::HTTP_OK);
    }

    public function getRidingDateHistory_post()
    {
        $order = $this->driver_api_model->getTimeWiseRecord($this->post('start'), $this->post('end'), $this->post('user_id'));

        $this->response(['tot_ride' => $order, 'status' => 1, 'message' => "record found"], REST_Controller::HTTP_OK);
    }

    public function weeklyRide_post()
    {
        $data = $this->driver_api_model->getWeeklyOrders($this->post('user_id'));
        $this->response(['ride' => $data, 'status' => 1, 'message' => "record found"], REST_Controller::HTTP_OK);
    }

    //leader Board
    public function getLeaderBoard_post()
    {
        $data = $this->driver_api_model->getLeaderBoard();
        $this->response(['leader' => $data, 'status' => 1, 'message' => "record found"], REST_Controller::HTTP_OK);
    }
}
