<?php
defined('BASEPATH') or exit('No direct script access allowed');
//error_reporting(-1);
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Api extends REST_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('v1/api_model');
        $this->load->library('form_validation');
        $this->current_lang = "en";
    }
    public function getcouponHistory_post()
    {
        $user_id = $this->post('user_id');
        if ($user_id) {
            $earn = $this->api_model->getAllRecordMultipleWhere('reward_point', array('cost' => 1, 'user_id' => $user_id));
            $burn = $this->api_model->getAllRecordMultipleWhere('reward_point', array('cost' => 2, 'user_id' => $user_id));
            $this->response([
                'status' => 1,
                'earn' => $earn,
                'burn' => $burn,
                'msg' => "Not Enough Points",
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => 0,
                'msg' => "no data found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function AvailCoupon_post()
    {
        $coupon_id = $this->post('coupon_id');
        $user_id = $this->post('user_id');
        // return $coupon_id;
        $total_points = $this->api_model->get_total_points($user_id);
        $expire_day = $this->api_model->getRewardValue('Expire Times');
        $get_coupon_points = $this->api_model->get_coupon_points($coupon_id);
        $start_date = date('y-m-d');
        $futureDate = date('y-m-d', strtotime('+' . $expire_day . ' days'));
        $available_points = $total_points[0]->points;
        $coupon_points = $get_coupon_points[0]->cost;
        if ($available_points < $coupon_points) {
            $this->response([
                'status' => 0,
                'msg' => "Not Enough Points",
            ], REST_Controller::HTTP_OK);
        } else if ($get_coupon_points[0]->type == 'Voucher') {

            $voucher_data = array(
                'voucher_id' => $coupon_id,
                'user_id' => $user_id,
                'is_read' => 0
            );
            $notification_id = $this->api_model->addRecord('voucher_notification', $voucher_data);
            $burn_data = array(
                'points' => $coupon_points,
                'cost' => 2,
                'date' => date('Y-m-d H:i:s'),
                'user_id' => $user_id,
                'reason' => 'Reedemed ' . $coupon_points . ' as ' . $get_coupon_points[0]->type,
                'coupon_type' => $get_coupon_points[0]->type,
                'coupon_id' => $coupon_id,
            );
            $burn_id = $this->api_model->addRecord('reward_point', $burn_data);
            if ($notification_id && $burn_id) {
                $this->response(
                    [
                        'status' => 1,
                        'msg' => 'Voucher Created Successfully'

                    ],
                    REST_Controller::HTTP_OK
                );
            } else {
                $this->response(
                    [
                        'status' => 0,
                        'msg' => 'Failed'

                    ],
                    REST_Controller::HTTP_OK
                );
            }
        } else {
            $rndm_data = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 7);

            //Array data for craete coupon
            $add_data = array(
                'coupon_type' => 'selected_user',
                // 'name' => 'Get ' . $get_coupon_points[0]->value . ' Taka Free',
                'name' =>   'GET' . $get_coupon_points[0]->value . 'TAKAOFF-' . $rndm_data,
                'description' => 'Get ' . $get_coupon_points[0]->value . ' Burning ' . $coupon_points . ' Points',
                'amount_type' => 'Amount',
                'amount' => $get_coupon_points[0]->value,
                'max_amount' => $get_coupon_points[0]->cost,
                'start_date' => $start_date,
                'end_date' => $futureDate,
                'status' => 1,
                'maximum_use' => 1,
                'discount_amount' => null,
                'usablity' => 'onetime',
                'source'   => 'crm'
            );
            $entity_id = $this->api_model->addCouponData('coupon', $add_data);
            $burn_data = array(
                'points' => $coupon_points,
                'cost' => 2,
                'date' => date('Y-m-d H:i:s'),
                'user_id' => $user_id,
                'reason' => 'Reedemed ' . $coupon_points . ' as ' . $get_coupon_points[0]->type,
                'coupon_type' => $get_coupon_points[0]->type,
                'coupon_id' => $entity_id,
            );
            $burn_id = $this->api_model->addCouponData('reward_point', $burn_data);
            //Coupon Created for all Restaurant
            $all_resturant = $this->api_model->getAllRestaurantID();
            foreach ($all_resturant as $key => $value) {
                $res_data[] = array(
                    'restaurant_id' => $value['entity_id'],
                    'coupon_id' => $entity_id
                );
            }
            $this->api_model->insertBatch('coupon_restaurant_map', $res_data, $id = '');
            //Set User Coupon Map

            $user_data = array(
                'user_id' => $user_id,
                'coupon_id' => $entity_id
            );
            if (!empty($user_data))
                $this->api_model->addRecord('coupon_user_map', $user_data);

            $this->response(
                [
                    'status' => 1,
                    'msg' => 'Coupon Created Successfully'

                ],
                REST_Controller::HTTP_OK
            );
        }
    }

    public function rewardPoint_post()
    {
        $user_id = $this->post('user_id');
        if ($user_id) {
            $data = $this->api_model->getrewardPoints($user_id);
            $this->response([
                'status' => 1,
                'data' => $data

            ], REST_Controller::HTTP_OK);
        }
    }

    //get restaurant timing
    public function getRestaurantTiming_post()
    {
        date_default_timezone_set('Asia/Dhaka');
        $restaurant_id = $this->post('restaurantId');
        //$content_id = $this->post('contentId');
        $openValue = true;
        $new_date = new DateTime();
        $server_time = $new_date->format('Y-m-d');
        $timings = $this->api_model->getTimings($restaurant_id, $openValue);
        if ($timings == false) {
            $openValue = false;
            $timings = $this->api_model->getTimings($restaurant_id, $openValue);
        } else {
            $resOnOff = $this->api_model->checkRestaurantOnOff($restaurant_id);
            if ($resOnOff[0]->timings['off'] == 'open') {
                $open = true;
            } else {
                $open = false;
            }
        }

        $this->response([
            'Open' => $open,
            'server_time'   => $server_time,
            'timings' => $timings

        ], REST_Controller::HTTP_OK);
    }
    public function getPaymentOption_post()
    {
        $user_id = $this->post('user_id');
        $data = $this->api_model->getRecordAll('agreement_info', 'user_id', $user_id);
        $key = 10;
        foreach ($data as $keys => $values) {
            $values->name = "XXXXXXX" . $values->customerMsisdn[7] . $values->customerMsisdn[8] . $values->customerMsisdn[9] . $values->customerMsisdn[10];
            $values->id = $key;
            $key = $key + 1;
        }
        $this->response([
            'status' => 1,
            'data' =>  $data,
            'key' => $key
        ], REST_Controller::HTTP_OK);
    }
    public function sendOTPforgetpass_post()
    {
        $number = $this->post('PhoneNumber');
        $checkRecord = $this->api_model->getRecord('users', 'mobile_number', $this->post('PhoneNumber'));
        if (!empty($checkRecord)) {
            $otp = mt_rand(100000, 999999);
            $this->sendOtp($otp, $number);
            $data = array(
                'sms_otp' => $otp
            );
            $this->api_model->updateUser('users', $data, 'mobile_number', $number);
            $this->response([
                'status' => 1,
                'msg' => 'otp sent',
            ], REST_Controller::HTTP_OK);
        }
    }
    public function verifyOTPforgetpass_post()
    {
        $number = $this->post('PhoneNumber');
        $otp = $this->post('otp');
        $checkRecord = $this->api_model->getRecord('users', 'mobile_number', $this->post('PhoneNumber'));
        if (!empty($checkRecord)) {
            $otp_server = $checkRecord->sms_otp;
            if ($otp == $otp_server) {
                $this->response([
                    'status' => 1,
                    'msg' => "otp matched",
                ], REST_Controller::HTTP_OK);
            } else {

                $this->response([
                    'status' => 0,
                    'msg' => "otp not matched",
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => -1,
                'msg' => "record not found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function testSms_post()
    {
        $s =  $this->sendOtp("123456", "01835264732");
        $this->response([
            'status' => 1,
            'msg' => $s,
        ], REST_Controller::HTTP_OK);
    }


    public function sendOtp($otp, $number)
    {

        $number = "88" . $number;
        $api_key = "jncl2wn2-o0omijuf-z6om6uzt-nvgaof5i-w74dhpwc";
        $sid = "FOODIBRAND";
        // $to="88".$number;
        $msg = "Your OTP for Foodi App is " . $otp;
        $url = "https://smsplus.sslwireless.com/api/v3/send-sms";
        $data = [
            "api_token" => $api_key,
            "sid" => $sid,
            "msisdn" => $number,
            "sms" => $msg,
            "csms_id" => random_string('alnum', 8)
        ];
        // $inserted_data=array(
        //     "to" => $to,
        //     "from" => $from,
        //     "message" => $msg,
        // );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $smsresult = curl_exec($ch);
        $p = explode("|", $smsresult);
        $sendstatus = $p[0];
        curl_close($ch);
        return $sendstatus;
        //return $smsresult;
    }


    //common lang fucntion
    public function getLang()
    {
        $this->current_lang = ($this->post('language_slug')) ? $this->post('language_slug') : $this->current_lang;
        $languages = $this->api_model->getLanguages($this->current_lang);
        $this->lang->load('messages_lang', $languages->language_directory);
    }

    public function getVersionNumber_post()
    {
        $ans = null;
        $os_type = $this->post("os_type");
        $data = $this->api_model->getSystemOptoin("version");
        $android_versions = APP_ANDROID_VERSION;
        $ios_versions = APP_IOS_VERSION;
        if (in_array($this->post('installed_version'), $os_type == "android" ? $android_versions : $ios_versions)) {
            $ans = true;
        } else {
            $ans = false;
        }

        $this->response([
            'status' => 1,
            'is_version_allowed' => $ans
            // 'force_logout'=> 'true'
        ], REST_Controller::HTTP_OK);
    }

    //update new pass
    public function updatePassword_post()
    {
        $checkRecord = $this->db->select('entity_id')->where('mobile_number', $this->post('PhoneNumber'))->get('users')->first_row();
        if (!empty($checkRecord)) {
            // $password = random_string('alnum', 8);
            if ($this->post('confirm_password') == $this->post('password')) {
                $pass = md5(SALT . $this->post('password'));
                $this->db->set('password', $pass);
                $this->db->where('entity_id', $checkRecord->entity_id);
                $this->db->update('users');
                $this->response(['status' => 1, 'phone' => $this->post('PhoneNumber'), 'entity_id' => $checkRecord->entity_id, 'password' => $this->post('password'), 'SaltedPassword' => md5(SALT . $this->post('password')), 'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('confirm_password')], REST_Controller::HTTP_OK); // OK
            }
            // $password = md5(SALT.$this->post('password'));
            // $this->db->set("password", $password)->where('entity_id',$checkRecord->entity_id)->update('users');
            // // $data = array('active_code'=>$activecode,'password'=>md5(SALT.$password));
            // //$this->api_model->updateUser('users',$data,'mobile_number',$this->post('PhoneNumber'));
            // //$this->response(['status' => 1,'password'=>$password,'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            // $this->response(['status' => 1,'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code

        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('user_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    // Registration API
    public function registration_post()
    {
        $provider = 1;
        if ($this->post('provider') != null) {
            $provider = $this->post('provider');
        }


        if ($this->post('FirstName') != "" && $this->post('PhoneNumber') != "" && $this->post('Password') != "") {
            $checkRecord = $this->api_model->getRecord('users', 'mobile_number', $this->post('PhoneNumber'));
            if (empty($checkRecord)) {
                $addUser = array(
                    'mobile_number' => trim($this->post('PhoneNumber')),
                    'phone_code' => trim($this->post('phone_code')),
                    'first_name' => trim($this->post('FirstName')),
                    'last_name' => '',
                    'password' => $provider == 1 ?  md5(SALT . $this->post('Password')) :  NULL,
                    'user_type' => 'User',
                    'status' => 1,
                    'login_provider' => $provider,
                    'login_provider_id' => $this->post('providerId'),
                    'login_provider_detail' => $provider == 1 ? NULL : serialize(json_decode($this->post('providerDetail'), true)),

                );
                $UserID = $this->api_model->addRecord('users', $addUser);
                $login = $this->api_model->getRegisterRecord('users', $UserID);
                if ($UserID) {
                    $data = array('device_id' => $this->post('firebase_token'));
                    $this->api_model->updateUser('users', $data, 'entity_id', $UserID);
                    $this->response(['User' => $login, 'active' => false, 'status' => 1, 'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('registration_fail')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            } else {
                $this->response([
                    'status' => 0,
                    'userExists' => true,
                    'message' => $this->lang->line('user_exist')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('regi_validation')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    // public function registration_post()
    // {
    //     $this->getLang();
    //     if($this->post('FirstName') !="" && $this->post('PhoneNumber') != "" && $this->post('Email') !="" && $this->post('Password') !="")
    //     {
    //         $checkRecord = $this->api_model->getRecord('users', 'mobile_number',$this->post('PhoneNumber'));
    //         $checkemail = $this->api_model->getRecord('users', 'email',$this->post('Email'));
    //         if(empty($checkRecord) && empty($checkemail))
    //         {
    //             $addUser = array(
    //                 'mobile_number'=>trim($this->post('PhoneNumber')),
    //                 'first_name'=>trim($this->post('FirstName')),
    //                 'email'=>trim(strtolower($this->post('Email'))),
    //                 'password'=>md5(SALT.$this->post('Password')),
    //                 'last_name'=>'',
    //                 'user_type'=>'User',
    //                 'status'=>1
    //             );
    //             $UserID = $this->api_model->addRecord('users', $addUser);
    //             $login = $this->api_model->getRegisterRecord('users',$UserID);
    //             if($UserID)
    //             {
    //                 $data = array('device_id'=>$this->post('firebase_token'));
    //                 $this->api_model->updateUser('users',$data,'entity_id',$UserID);
    //                 if($this->post('Email')){
    //                      // confirmation link
    //                     $verificationCode = random_string('alnum', 20).$UserID.random_string('alnum', 5);
    //                     $confirmationLink = '<a href='.base_url().'user/verify_account/'.$verificationCode.'>here</a>';
    //                     $email_template = $this->db->get_where('email_template',array('email_slug'=>'verify-account','language_slug'=>'en'))->first_row();
    //                     $arrayData = array('FirstName'=>$this->post('FirstName'),'ForgotPasswordLink'=>$confirmationLink);
    //                     $EmailBody = generateEmailBody($email_template->message,$arrayData);

    //                     //get System Option Data
    //                     $this->db->select('OptionValue');
    //                     $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

    //                     $this->db->select('OptionValue');
    //                     $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();

    //                     $this->load->library('email');
    //                     $config['charset'] = "utf-8";
    //                     $config['mailtype'] = "html";
    //                     $config['newline'] = "\r\n";
    //                     $this->email->initialize($config);
    //                     $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
    //                     $this->email->to($this->post('Email'));
    //                     $this->email->subject($email_template->subject);
    //                     $this->email->message($EmailBody);
    //                     $this->email->send();


    //                     // update verification code
    //                     $addata = array('email_verification_code'=>$verificationCode);
    //                     $this->api_model->updateUser('users',$addata,'entity_id',$UserID);
    //                 }
    //                 $this->response(['User' => $login,'active'=>false,'status'=>1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    //             }
    //             else
    //             {
    //                 $this->response([
    //                     'status' => 0,
    //                     'message' => $this->lang->line('registration_fail')
    //                 ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
    //             }
    //         }
    //         else
    //         {
    //             $this->response([
    //                 'status' => 0,
    //                 'message' => $this->lang->line('user_exist')
    //             ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
    //         }
    //     }
    //     else
    //     {
    //         $this->response([
    //             'status' => 0,
    //             'message' => $this->lang->line('regi_validation')
    //         ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
    //     }
    // }
    // Add Address
    public function addAddress_post()
    {
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $address_id = $this->post('address_id');
            $add_data = array(
                'address' => $this->post('address'),
                'appartment' => $this->post('appartment'),
                'information' => $this->post('information'),
                'landmark' => $this->post('landmark'),
                'latitude' => $this->post('latitude'),
                'longitude' => $this->post('longitude'),
                'zipcode' => $this->post('zipcode'),
                'city' => $this->post('city'),
                'user_entity_id' => $this->post('user_id')
            );
            if ($address_id) {
                $this->api_model->updateUser('user_address', $add_data, 'entity_id', $address_id);
            } else {
                $address_id = $this->api_model->addRecord('user_address', $add_data);
            }
            $this->response(['address_id' => $address_id, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    // Login API
    public function login_post()
    {
        $this->getLang();
        $provider = 1;
        if ($this->post('provider') != null) {
            $provider = $this->post('provider');
        }
        if ($provider == 1) {


            $login = $this->api_model->getLogin($this->post('PhoneNumber'), $this->post('Password'));
        } else {
            $login = $this->api_model->getLoginForProvider($provider, $this->post('providerId'));
        }


        if (!empty($login)) {
            if ($login->active == 1) {
                $app_version_number = $this->post('app_version_number') ? $this->post('app_version_number') : '';
                $app_os = $this->post('app_os') ? $this->post('app_os') : '';
                $data = array('active' => 1, 'device_id' => $this->post('firebase_token'),  'app_version_number' => $app_version_number, 'app_os' => $app_os);
                if ($login->status == 1) {
                    // update device
                    $image = ($login->image) ? image_url . $login->image : '';
                    $this->api_model->updateUser('users', $data, 'entity_id', $login->entity_id);
                    //get rating
                    $rating = $this->api_model->getRatings($login->entity_id);
                    $review = (!empty($rating)) ? $rating->rating : '';

                    $last_name = ($login->last_name) ? $login->last_name : '';
                    $login_detail = array('FirstName' => $login->first_name, 'LastName' => $last_name, 'image' => $image, 'PhoneNumber' => $login->mobile_number, 'UserID' => $login->entity_id, 'notification' => $login->notification, 'rating' => $review, 'Email' => $login->email);
                    $this->response(['login' => $login_detail, 'status' => 1, 'active' => true, 'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else if ($login->status == 0) {
                    $adminEmail = $this->api_model->getSystemOptoin('Admin_Email_Address');
                    $this->response(['status' => 2, 'message' => $this->lang->line('login_deactive'), 'email' => $adminEmail->OptionValue], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            } else {
                $this->response([
                    'status' => 0,
                    'active' => false,
                    'phoneNumber' => $login->mobile_number,
                    'message' => $this->lang->line('otp_inactive')
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $emailexist = $this->api_model->getRecord('users', 'mobile_number', $this->post('PhoneNumber'));
            if ($emailexist) {
                $this->response([
                    'status' => 0,
                    'notFound' => true,
                    'message' => $this->lang->line('pass_validation')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response([
                    'status' => 0,
                    'notFound' => true,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    //verify OTP
    public function verifyOTP_post()
    {
        $this->getLang();
        $otp = $this->post('sms_otp');
        $provider = 1;
        if ($this->post('provider') != null) {
            $provider = $this->post('provider');
        }
        if ($provider == 1) {

            $login = $this->api_model->getLogin($this->post('PhoneNumber'), $this->post('Password'));
        } else {
            $login = $this->api_model->getLoginWithPhoneOnly($this->post('PhoneNumber'));
        }
        if (!empty($login) && $login->sms_otp == $otp) {
            if ($this->post('active') == 1) {
                $data = array('active' => 1);
                $this->api_model->updateUser('users', $data, 'entity_id', $login->entity_id);
                $image = ($login->image) ? image_url . $login->image : '';
                $last_name = ($login->last_name) ? $login->last_name : '';
                $login_detail = array('FirstName' => $login->first_name, 'LastName' => $last_name, 'image' => $image, 'PhoneNumber' => $login->mobile_number, 'UserID' => $login->entity_id, 'notification' => $login->notification);
                $this->response(['login' => $login_detail, 'active' => true, 'status' => 1, 'message' => $this->lang->line('success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response([
                    'status' => 0,
                    'active' => false,
                    'message' => $this->lang->line('otp_inactive')
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get homepage
    public function getHome_post()
    {
        date_default_timezone_set('Asia/Dhaka');
        //for event
        $this->getLang();
        if ($this->post('isEvent') == 1) {
            $latitude = ($this->post('latitude')) ? $this->post('latitude') : '';
            $longitude = ($this->post('longitude')) ? $this->post('longitude') : '';
            $searchItem = ($this->post('itemSearch')) ? $this->post('itemSearch') : '';
            $restaurant = $this->api_model->getEventRestaurant($latitude, $longitude, $searchItem, $this->current_lang, $this->post('count'), $this->post('page_no'));
            if (!empty($restaurant)) {
                $this->response([
                    'date' => date("Y-m-d g:i A"),
                    'restaurant' => $restaurant,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->response([
                    'status' => 1,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
        } else { // for home page
            if ($this->post('latitude') != "" && $this->post('longitude') != "") {
                $date = date('Y-m-d H:i:s');
                $food = $this->post('food');
                $rating = $this->post('rating');
                $distance = $this->post('distance');
                $priceRange = $this->post('priceRange');
                $offers = $this->post('offerType');
                $user_id = $this->post('user_id');
                $isPopular = $this->post('isPopular');
                $opsSetting = $this->api_model->getOperationSettings();
                $PopupBanner = $opsSetting['operation_on_off'] == 1 ?  $this->api_model->getpopupbanner() : [];
                $searchItem = ($this->post('itemSearch')) ? $this->post('itemSearch') : '';
                $restaurant = $this->api_model->getHomeRestaurant($user_id, $this->post('latitude'), $this->post('longitude'), $searchItem, $food, $rating, $priceRange, $date, $distance, $offers, $this->current_lang, $this->post('count'), $this->post('page_no'), $campaign_id = null, $zone_id = null, $search_restaurant_only = false, $isPopular);
                if ($restaurant) {
                    $zone_id = $opsSetting['operation_on_off'] == 1 ? $restaurant[0]->zone_id : null;
                    $slider = $opsSetting['operation_on_off'] == 1 ? $this->api_model->getbanner($zone_id) : null;
                    $campaign = $opsSetting['operation_on_off'] == 1 ? $this->api_model->getCampaign($restaurant) : null;
                    $feature_items = $opsSetting['operation_on_off'] == 1 ? $this->api_model->getFeatureItems($restaurant) : null; //for eid
                    $category = $opsSetting['operation_on_off'] == 1 ? $this->api_model->getcategory("en", $zone_id) : null;

                    $this->response([
                        'opsOnOff' => $opsSetting['operation_on_off'] == 1 ? 'on' : 'off',
                        'opsOffImage'  => $opsSetting['operation_on_off'] == 1 ? null : image_url . $opsSetting['operation_off_image'],
                        'date' => date('Y-m-d H:i:s'), //date("Y-m-d g:i A"),
                        'zone_id' => $zone_id,
                        'restaurant' => $opsSetting['operation_on_off'] == 1 ? $restaurant : [],
                        'PopupBanner' => $PopupBanner ? $PopupBanner : [],
                        'slider' => $slider,
                        'campaign' => $campaign,
                        'feature_items' => $feature_items,
                        'category' => $category,
                        'status' => 1,
                        'message' => $this->lang->line('record_found')
                    ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                } else {
                    $this->response([
                        'status' => 1,
                        'restaurant' => [],
                        'message' => $this->lang->line('not_found')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            } else {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function getSetAppleLogin_post()
    {
        $add_data = array(
            'user_id' => $this->post('userId'),
            'full_name' => $this->post('fullName'),
            'email' => $this->post('email'),
        );
        $appleLogin = $this->api_model->getRecord('apple_login', 'user_id', $this->post('userId'));
        if (!empty($appleLogin)) {
            $this->response(['appleLogin' => $appleLogin, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
        } else {
            $id = $this->api_model->addRecord('apple_login', $add_data);
            $appleLoginFromDb = $this->api_model->getRecord('apple_login', 'entity_id', $id);
            $this->response(['appleLogin' => $appleLoginFromDb, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
        }
    }
    // Forgot Password
    public function forgotpassword_post()
    {
        $checkRecord = $this->api_model->getRecordMultipleWhere('users', array('mobile_number' => $this->post('PhoneNumber'), 'status' => 1, 'login_provider' => 1));
        if (!empty($checkRecord)) {
            $activecode = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $password = random_string('alnum', 8);
            $data = array('active_code' => $activecode, 'password' => md5(SALT . $password));
            $this->api_model->updateUser('users', $data, 'mobile_number', $this->post('PhoneNumber'));
            $this->response(['status' => 1, 'password' => $password, 'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' => "User doesn't exist"
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    // public function forgotpassword_post()
    // {
    //     $this->getLang();
    //     $checkRecord = $this->api_model->getRecordMultipleWhere('users', array('email'=>strtolower($this->post('Email')),'status'=>1));
    //     if(!empty($checkRecord))
    //     {
    //         // confirmation link
    //         if($this->post('Email')){
    //             $verificationCode = random_string('alnum', 20).$checkRecord->entity_id.random_string('alnum', 5);
    //             $confirmationLink = '<a href='.base_url().'user/reset/'.$verificationCode.'>here</a>';
    //             $email_template = $this->db->get_where('email_template',array('email_slug'=>'forgot-password','language_slug'=>'en'))->first_row();
    //             $arrayData = array('FirstName'=>$checkRecord->first_name,'ForgotPasswordLink'=>$confirmationLink);
    //             $EmailBody = generateEmailBody($email_template->message,$arrayData);


    //             //get System Option Data
    //             $this->db->select('OptionValue');
    //             $FromEmailID = $this->db->get_where('system_option',array('OptionSlug'=>'From_Email_Address'))->first_row();

    //             $this->db->select('OptionValue');
    //             $FromEmailName = $this->db->get_where('system_option',array('OptionSlug'=>'Email_From_Name'))->first_row();

    //             $this->load->library('email');
    //             $config['charset'] = "utf-8";
    //             $config['mailtype'] = "html";
    //             $config['newline'] = "\r\n";
    //             $this->email->initialize($config);
    //             $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
    //             $this->email->to($this->post('Email'));
    //             $this->email->subject($email_template->subject);
    //             $this->email->message($EmailBody);
    //             $this->email->send();
    //             // update verification code
    //             $addata = array('email_verification_code'=>$verificationCode);
    //             $this->api_model->updateUser('users',$addata,'entity_id',$checkRecord->entity_id);
    //         }
    //         $this->response(['status' => 1,'message' => $this->lang->line('success_password_change')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    //     }
    //     else
    //     {
    //         $this->response([
    //             'status' => 0,
    //             'message' => $this->lang->line('user_not_found')
    //         ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
    //     }
    // }
    // Get CMS Pages
    public function getCMSPage_post()
    {
        $this->getLang();
        $cms_slug  = $this->post('cms_slug');
        $cmsData = $this->api_model->getCMSRecord('cms', $cms_slug, $this->post('language_slug'));
        if ($cmsData) {
            $this->response([
                'cmsData' => $cmsData,
                'status' => 1,
                'message' => $this->lang->line('found')
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function CheckAvailable_post()
    {
        $user_id = $this->post('user_id');
        $restaurant = $this->post('restaurant_id');
        $items = $this->post('items');
        $itemDetail = json_decode($items, true);
        // $itemDetail = $items;
        $message = array();
        $i = 0;
        $final = '';
        $status = 1;

        //Check restaurant timing and menu item status and user
        if ($this->post('restaurant_id')) {
            $restaurant = $this->api_model->getRecord('restaurant', 'entity_id', $restaurant);

            // $timing =  unserialize(html_entity_decode($restaurant->timings));
            // $day = date("l");
            // $currentTime = new DateTime(date('G:i:s'));
            // foreach ($timing as $keys => $values) {
            //     if ($keys == strtolower($day)) {
            //         $restaurantOpenTime = new DateTime($values['open']);
            //         $restaurantCloseTime = new DateTime($values['close']);

            //         $restaurant_status = (!empty($values['open']) && !empty($values['close'])) ? (($restaurantOpenTime <= $currentTime && $restaurantCloseTime >= $currentTime) ? 'open' : 'close') : 'close';

            //         if ($restaurant_status == 'close') {
            //             $message[$i] = 'Restaurant is closed.';
            //             $i++;
            //             //$this->response(['status' => false, 'message' => 'Restaurant is closed'], REST_Controller::HTTP_OK); // OK
            //         }
            //     }
            // }

            if ($restaurant->status == 0) {
                $message[$i] = 'Restaurant is deactivated.';
                $i++;
            }

            foreach ($itemDetail['items'] as $m => $n) {
                $menu = $this->api_model->getRecord('restaurant_menu_item', 'entity_id', $n['menu_id']);
                $category = $this->api_model->getRecord('category', 'entity_id', $menu->category_id);
                if ($menu->status == 0) {
                    $message[$i] = 'Menu is not active.';
                    $i++;
                    break;
                    //$this->response(['status' => false, 'message' => 'Menu is not active'], REST_Controller::HTTP_OK); // OK
                }

                if ($category->status == 0) {
                    $message[$i] = 'Category is deactivated.';
                    $i++;
                    break;
                }

                $menu_timing = $menu->availability;

                if ($menu_timing && ($menu_timing != '' || $menu_timing != null)) {
                    $menu_timing = @unserialize($menu_timing);

                    $menuOpenTime = null;

                    $break_count = 0;
                    $time_count = 0;
                    foreach ($menu_timing as $t_key => $t_value) {

                        if ($t_value['on'] != 0 && $t_value['open'] != '' && $t_value['close'] != '') {

                            $time_count++;
                            $menuOpenTime = new DateTime($t_value['open']);
                            $menuCloseTime = new DateTime($t_value['close']);
                            if ((($menuOpenTime->diff(new DateTime)->format('%R') == '+') &&
                                ($menuCloseTime->diff(new DateTime)->format('%R') == '-'))) {
                                $break_count++;
                            }
                        }
                    }

                    if ($time_count > 0 && $break_count == 0) {
                        $message[$i] = $menu->name . ' currently unavailable. ' . ($menuOpenTime
                            ? 'Check again at ' . $menuOpenTime->format('H:i A')
                            : '');
                        $i++;
                    }
                }
            }


            $user = $this->api_model->getRecord('users', 'entity_id', $user_id);

            if ($user->status == 0) {
                $message[$i] = 'Your acount is not active';
                $i++;
                //$this->response(['status' => false, 'message' => 'Bloody user !!! you are deactivated -_- (Authorized by Shouvik Chowdhury Oni)'], REST_Controller::HTTP_OK); // OK
            }
        }

        if (!empty($message)) {
            $final = implode('  ', $message);
            $status = 0;
        }

        $this->response(['status' => $status, 'message' => $final], REST_Controller::HTTP_OK); // OK

    }
    public function getResNameAddress_post()
    {

        $data = $this->api_model->getResName($this->post('res_id'));
        $this->response(['status' => 1, 'message' => "data found", 'data' => $data], REST_Controller::HTTP_OK);
    }
    //add review
    public function addReview_post()
    {
        $this->getLang();
        if ($this->post('rating') != '' && $this->post('review') != '') {
            $add_data = array(
                'rating' => trim($this->post('rating')),
                'review' => trim($this->post('review')),
                'restaurant_id' => $this->post('restaurant_id'),
                'user_id' => $this->post('user_id'),
                'order_user_id' => ($this->post('driver_id')) ? $this->post('driver_id') : null,
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            $this->api_model->addRecord('review', $add_data);
            $review = $this->api_model->getRestaurantReview($this->post('restaurant_id'));
            $this->response(['status' => 1, 'message' => $this->lang->line('success_add'), 'review' => $review], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('validation')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function addRiderReview_post()
    {
        $this->getLang();
        if ($this->post('rating') != '' && $this->post('review') != '') {
            $add_data = array(
                'rating' => trim($this->post('rating')),
                'review' => trim($this->post('review')),
                'order_id' => $this->post('order_id'),
                'rider_id' => $this->post('rider_id'),
                'user_id' => $this->post('user_id'),
            );
            $this->api_model->addRecord('rider_review', $add_data);
            $review = $this->api_model->getRestaurantReview($this->post('restaurant_id'));
            $this->response(['status' => 1, 'message' => $this->lang->line('success_add'), 'review' => $review], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('validation')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get restaurant
    public function getRestaurantDetail_post()
    {
        $this->getLang();

        if ($this->post('restaurant_id')) {
            date_default_timezone_set('Asia/Dhaka');
            $date = date('Y-m-d H:i:s');
            $details = $this->api_model->getRestaurantDetail($this->post('content_id'), $this->current_lang, $date);
            $item_image = $this->api_model->item_image($this->post('restaurant_id'), $this->current_lang);
            $popular_item = $this->api_model->getMenuItem($this->post('restaurant_id'), $this->post('food'), $this->post('price'), $this->current_lang, $popular = 1);
            $menu_item = $this->api_model->getMenuItem($this->post('restaurant_id'), $this->post('food'), $this->post('price'), $this->current_lang, $popular = 0);
            $review = $this->api_model->getRestaurantReview($this->post('restaurant_id'));
            $package = $this->api_model->getPackage($this->post('restaurant_id'), $this->current_lang);
            $feature_items = $this->api_model->getFeatureItemsforDetails($this->post('restaurant_id'));
            $this->response(
                [
                    'restaurant' => $details,
                    'item_image' => $item_image,
                    'feature_items' => $feature_items,
                    'popular_item' => $popular_item,
                    'menu_item' => $menu_item,
                    'review' => $review,
                    'package' => $package,
                    'status' => 1,
                    'message' => $this->lang->line('found')
                ],
                REST_Controller::HTTP_OK
            ); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' =>  $this->lang->line('not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function editProfile_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenusr = $this->api_model->checkToken($token, $user_id);
        if ($tokenusr) {
            $add_data = array(
                'first_name' => $this->post('first_name'),
                'last_name' => $this->post('last_name'),
                'notification' => $this->post('notification'),
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
            $this->api_model->updateUser('users', $add_data, 'entity_id', $this->post('user_id'));
            $token = $this->api_model->checkToken($token, $user_id);
            $image = ($token->image) ? image_url . $token->image : '';
            $last_name = ($token->last_name) ? $token->last_name : '';
            $login_detail = array('FirstName' => $token->first_name, 'LastName' => $last_name, 'image' => $image, 'PhoneNumber' => $token->mobile_number, 'UserID' => $token->entity_id, 'notification' => $token->notification);
            $this->response(['profile' => $login_detail, 'status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200)
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //package avalability
    public function bookingAvailable_post()
    {
        $this->getLang();
        if ($this->post('booking_date') != '' && $this->post('people') != '') {
            $time = date('Y-m-d H:i:s', strtotime($this->post('booking_date')));
            $date = date('Y-m-d H:i:s');
            if (date('Y-m-d', strtotime($this->post('booking_date'))) == date('Y-m-d') && date($time) < date($date)) {
                $this->response(['status' => 0, 'message' => $this->lang->line('greater_than_current_time')], REST_Controller::HTTP_OK); // OK
            } else {
                $check = $this->api_model->getBookingAvailability($this->post('booking_date'), $this->post('people'), $this->post('restaurant_id'));
                if ($check) {
                    $this->response(['status' => 1, 'message' => $this->lang->line('booking_available')], REST_Controller::HTTP_OK); // OK
                } else {
                    $this->response(['status' => 0, 'message' => $this->lang->line('booking_not_available')], REST_Controller::HTTP_OK); // OK
                }
            }
        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('not_found'),
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //book event
    public function bookEvent_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            if ($this->post('booking_date') != '' && $this->post('people') != '') {
                $add_data = array(
                    'name' => $this->post('name'),
                    'no_of_people' => $this->post('people'),
                    'booking_date' => date('Y-m-d H:i:s', strtotime($this->post('booking_date'))),
                    'restaurant_id' => $this->post('restaurant_id'),
                    'user_id' => $this->post('user_id'),
                    'package_id' => $this->post('package_id'),
                    'status' => 1,
                    'created_by' => $this->post('user_id'),
                    'event_status' => 'pending'
                );
                $event_id = $this->api_model->addRecord('event', $add_data);
                $users = array(
                    'first_name' => $tokenres->first_name,
                    'last_name' => ($tokenres->last_name) ? $tokenres->last_name : ''
                );
                $taxdetail = $this->api_model->getRestaurantTax('restaurant', $this->post('restaurant_id'), $flag = "order");
                $package = $this->api_model->getRecord('restaurant_package', 'entity_id', $this->post('package_id'));
                $package_detail = '';
                if (!empty($package)) {
                    $package_detail = array(
                        'package_price' => $package->price,
                        'package_name' => $package->name,
                        'package_detail' => $package->detail
                    );
                }
                $serialize_array = array(
                    'restaurant_detail' => (!empty($taxdetail)) ? serialize($taxdetail) : '',
                    'user_detail' => (!empty($users)) ? serialize($users) : '',
                    'package_detail' => (!empty($package_detail)) ? serialize($package_detail) : '',
                    'event_id' => $event_id
                );
                $this->api_model->addRecord('event_detail', $serialize_array);
                $this->response(['status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK
            } else {
                $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get booking
    public function getBooking_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $data = $this->api_model->getBooking($user_id);
            $this->response(['upcoming_booking' => $data['upcoming'], 'past_booking' => $data['past'], 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //delete address
    public function deleteAddress_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $checkOrderStatus = $this->api_model->checkOrderStatus($this->post('address_id'));

        $tokenres = $this->api_model->checkToken($token, $user_id);

        if ($tokenres && $checkOrderStatus < 1) {
            $this->api_model->deleteRecord('user_address', 'entity_id', $this->post('address_id'));
            $this->response(['status' => 1, 'message' => $this->lang->line('record_deleted')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response([
                'status' => -1,
                'message' => 'Cant delete address. User has pending order'
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get recipe
    public function getRecipe_post()
    {
        $this->getLang();
        $searchItem = ($this->post('itemSearch')) ? $this->post('itemSearch') : '';
        $food = $this->post('food');
        $timing = $this->post('timing');
        $popular_item = $this->api_model->getRecipe($searchItem, $food, $timing, $this->post('language_slug'));
        if ($popular_item) {
            $this->response(['items' => $popular_item, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response(['status' => 0, 'message' => $this->lang->line('not_found')], REST_Controller::HTTP_OK); // OK
        }
    }
    //delete booking
    public function deleteBooking_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $this->api_model->deleteRecord('event', 'entity_id', $this->post('event_id'));
            $this->response(['status' => 1, 'message' => $this->lang->line('record_deleted')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get Adress List
    public function getAddress_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $address = $this->api_model->getAddress('user_address', 'user_entity_id', $user_id);
            $this->response(['address' => $address, 'status' => 1, 'message' => $this->lang->line('success_add')], REST_Controller::HTTP_OK); // OK
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }


    public function addtoCart_post()
    {
        $this->getLang();
        $user_id = $this->post('user_id');
        $zone_id = $this->post('zoneId');
        $cart_id = $this->post('cart_id');
        $items = $this->post('items');
        $itemDetail = is_array($items) ? $items : json_decode($items, true);
        // $itemDetail = json_decode($items, true);

        // echo '<pre>';
        // print_r($itemDetail['items']);
        // exit();

        $coupon_exist = $this->post('coupon');
        if ($coupon_exist && !$itemDetail['subtotal']) {
            $coupon_exist = null;
        }
        // $itemDetail = $items; // for postman call
        $menuIds = array_column($itemDetail['items'], 'menu_id');
        $item_number = $this->post('itemLength');
        $not_applicable = 0;
        $item = array();
        $same_coupon = array();
        $subtotal = 0;
        $discount = 0;
        $dscnt = 0;
        $discount_upto = 999999999;
        $gradual_discount_upto = 0;
        $discount_rate = 0;
        $discount_amount = 0;
        $discount_delivery = 0;
        $total = -1;
        $vat_total = 0;
        $sd_total = 0;
        $coupon_id = $coupon_amount = $coupon_type = $name  = $isApply = $coupon_discount = '';
        $taxdetail = $this->api_model->getRestaurantTax('restaurant', $this->post('restaurant_id'), $flag = '');
        $currencyDetails  = $this->api_model->getRestaurantCurrency($this->post('restaurant_id'));

        $autoApply = $this->api_model->getActiveAutoApply($this->post('restaurant_id'));
        // will get the details of active gradual coupons for this particular restaurant
        $gradual = $this->api_model->getActiveGradual($this->post('restaurant_id'));

        $gradual_specific_coupon  = array();
        $all_items = 0;
        $apply_gradual = 0;


        if ($gradual) {

            foreach ($gradual as $key => $value) {
                $checkAllItems = $this->api_model->checkAllItems($value->coupon_id);

                if ($checkAllItems == 0) {

                    $user_previous_order = $this->api_model->checkPreviousOrder($user_id, $value->coupon_id);

                    $get_sequence = $this->api_model->getSequence($value->coupon_id);

                    if ($get_sequence[$user_previous_order]) {
                        $all_items = 1; //restaurant has discount on all items
                        $couponId = $value->coupon_id;
                        break;
                    }
                }

                if ($checkAllItems > 0) {  //if that restaurant has discount on specific items
                    // $gradual_menu = $this->api_model->gradualItem($value->coupon_id);
                    // $gradual_menu_ids = array_column($gradual_menu, 'item_id');
                    array_push($gradual_specific_coupon, $value->coupon_id);
                }
            }
        }

        //for discount on items
        $discounted_menu = $this->api_model->discountedItem($this->post('restaurant_id'));

        if ($discounted_menu) {

            $discounted_ids = array_column($discounted_menu, 'item_id');

            //compare to get same ids
            $check_ids = array_intersect($discounted_ids, $menuIds);
            $check_ids = array_values($check_ids);
        }

        if (!empty($itemDetail)) {
            if ($coupon_exist || $gradual || $autoApply) {

                if ($coupon_exist && empty($check_ids) && empty($gradual)) {
                    $check = $this->api_model->checkCoupon($coupon_exist, $itemDetail['subtotal'], $this->post('restaurant_id'), 'Delivery', $this->post('user_id'));
                    if (!empty($check)) {
                        if (strtotime($check->end_date) > strtotime(date('Y-m-d H:i:s'))) {
                            // $this->response([
                            //     'status'=>1,
                            //     'message' =>"ok"], REST_Controller::HTTP_OK); // OK

                            if ($check->discount_amount) {
                                $discount_upto = $check->discount_amount;
                            }
                            if ($check->coupon_type == 'discount_on_cart') {
                                if ($check->amount_type == 'Percentage') {
                                    $discount_rate = ($check->amount) / 100;
                                } else if ($check->amount_type == 'Amount') {
                                    // $discount_amount = $check->amount / $item_number;
                                    $discount_amount = $check->amount;
                                }

                                $coupon_id = $check->entity_id;
                                $coupon_type = $check->amount_type;
                                $coupon_amount = $check->amount;
                                $coupon_discount = $discount + $discount_amount;
                                $name = $check->name;
                            }
                            if ($check->coupon_type == 'free_delivery') {

                                $discount_delivery = 1;

                                $coupon_id = $check->entity_id;
                                $coupon_type = $check->amount_type;
                                $coupon_amount = $check->amount;
                                $coupon_discount = $discount + $discount_amount;
                                $name = $check->name;
                            }
                            if ($check->coupon_type == 'selected_user') {

                                if ($check->amount_type == 'Percentage') {
                                    $discount_rate = ($check->amount) / 100;
                                } else if ($check->amount_type == 'Amount') {
                                    // $discount_amount = $check->amount/$item_number;
                                    $discount_amount = $check->amount;
                                }
                                $coupon_id = $check->entity_id;
                                $coupon_type = $check->amount_type;
                                $coupon_amount = $check->amount;
                                $coupon_discount = $discount + $discount_amount;
                                $name = $check->name;
                            }
                            if ($check->coupon_type == 'user_registration') {
                                $checkOrderCount = $this->api_model->checkUserCountCoupon($user_id,  $check->entity_id);
                                if ($checkOrderCount > 0) {
                                    $messsage = $this->lang->line('not_applied');
                                    $status = 2;
                                } else {
                                    if ($check->amount_type == 'Percentage') {
                                        $discount_rate = ($check->amount) / 100;
                                    } else if ($check->amount_type == 'Amount') {
                                        // $discount_amount = $check->amount / $item_number;
                                        $discount_amount = $check->amount;
                                    }

                                    $coupon_id = $check->entity_id;
                                    $coupon_type = $check->amount_type;
                                    $coupon_amount = $check->amount;
                                    $coupon_discount = $discount + $discount_amount;
                                    $name = $check->name;
                                }
                            }
                        } else {
                            $messsage = $this->lang->line('coupon_expire');
                            $status = 2;
                        }
                    } else {
                        $messsage = $this->lang->line('coupon_not_found');
                        $status = 2;
                    }
                }

                if ($gradual) {
                    if ($all_items == 1) {


                        foreach ($gradual as $key => $c) {

                            if ($c->coupon_id == $couponId) {

                                $user_previous_order = $this->api_model->checkPreviousOrder($user_id, $c->coupon_id);

                                if ($c->discount_amount) {
                                    $discount_upto = $c->discount_amount;
                                }

                                $get_sequence = $this->api_model->getSequence($c->coupon_id);


                                if ($get_sequence[$user_previous_order]) {
                                    $discount_rate = ($get_sequence[$user_previous_order]->percentage) / 100;

                                    $coupon_id = $c->coupon_id;
                                    $coupon_type = $c->amount_type;
                                    $coupon_amount = $get_sequence[$user_previous_order]->percentage;
                                    $coupon_discount = $discount + $discount_amount;
                                    $name = $c->name;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if ($autoApply) {
                        if ($autoApply->coupon_type == 'discount_on_cart') {
                            if ($autoApply->amount_type == 'Percentage') {
                                $discount_rate = ($autoApply->amount) / 100;
                            } else if ($autoApply->amount_type == 'Amount') {
                                // $discount_amount = $autoApply->amount / $item_number;
                                $discount_amount = $autoApply->amount;
                            }

                            $coupon_id = $autoApply->entity_id;
                            $coupon_type = $autoApply->amount_type;
                            $coupon_amount = $autoApply->amount;
                            $coupon_discount = $discount + $discount_amount;
                            $name = $autoApply->name;
                        }
                    }
                }
            }

            if (!empty($check_ids)) {

                //getting coupon name for discounted items.
                foreach ($discounted_menu as $key => $n) {
                    if ($check_ids[0] == $n->item_id) {
                        $coupon_name = $n->name;
                        break;
                    }
                }

                $check = $this->api_model->checkCoupon($coupon_name);

                if ($check->coupon_type == 'discount_on_items') {
                    if ($check->amount_type == 'Percentage') {
                        $discount_rate_item = ($check->amount) / 100;
                    } else if ($check->amount_type == 'Amount') {
                        // $discount_amount_item = $check->amount / $item_number;
                        $discount_amount_item = $check->amount;
                    }

                    if ($check->discount_amount) {
                        $discount_upto = $check->discount_amount;
                    }

                    $coupon_id = $check->entity_id;
                    $coupon_type = $check->amount_type;
                    $coupon_amount = $check->amount;
                    $coupon_discount = $discount + $discount_amount_item;
                    $name = $check->name;
                }
            }
            //$addonscount = 0;
            foreach ($itemDetail['items'] as $key => $value) {

                $data = $this->api_model->checkExist($value['menu_id']);

                //if current menu id is discounted item or not
                if ($check_ids && in_array($value['menu_id'], $check_ids)) {
                    $match = 1;
                } else {
                    $match = 0;
                }
                if (!empty($data)) {
                    $image = ($data->image) ? image_url . $data->image : '';
                    $itemTotal = 0;

                    $priceRate = ($value['offer_price']) ? $value['offer_price'] : $data->price;
                    if ($value['is_customize'] == 1) {

                        if ($value['has_variation'] == 1) {
                            $customization = array();
                            foreach ($value['variation_list'] as $k => $each_variation) {
                                $variation = array();

                                $variation_data = $this->api_model->getVariationDetails($each_variation['variation_id']);
                                $itemTotal += $variation_data->variation_price;

                                $addons = array();
                                if ($each_variation['addons_category_list'] && count($each_variation['addons_category_list']) > 0) {
                                    foreach ($each_variation['addons_category_list'] as $k => $val) {
                                        $addonscust = array();

                                        foreach ($val['addons_list'] as $m => $mn) {
                                            $add_ons_data = $this->api_model->getAddonsPrice($mn['add_ons_id']);

                                            $addonscust[] = array(
                                                'add_ons_id' => $mn['add_ons_id'],
                                                'add_ons_name' => $add_ons_data->add_ons_name,
                                                'add_ons_price' => $add_ons_data->add_ons_price
                                            );
                                            $itemTotal += $add_ons_data->add_ons_price;
                                        }
                                        $addons[] = array(
                                            'addons_category_id' => $val['addons_category_id'],
                                            'addons_category' => $val['addons_category'],
                                            'addons_list' => $addonscust
                                        );
                                    }
                                }

                                $customization[] = array(
                                    'variation_id'  => $each_variation['variation_id'],
                                    'variation_name'   => $variation_data->variation_name,
                                    'variation_price'   => $variation_data->variation_price,
                                    'addons_category_list'  => $addons
                                );
                            }
                        } else {


                            $customization = array();
                            if ($value['addons_category_list'] && count($value['addons_category_list']) > 0) {
                                foreach ($value['addons_category_list'] as $k => $val) {
                                    $addonscust = array();
                                    foreach ($val['addons_list'] as $m => $mn) {
                                        $add_ons_data = $this->api_model->getAddonsPrice($mn['add_ons_id']);
                                        if ($value['is_deal'] == 1) {
                                            $addonscust[] = array(
                                                'add_ons_id' => $mn['add_ons_id'],
                                                'add_ons_name' => $add_ons_data->add_ons_name,
                                            );
                                            $price = ($value['offer_price']) ? $value['offer_price'] : $data->price;
                                        } else {
                                            $addonscust[] = array(
                                                'add_ons_id' => $mn['add_ons_id'],
                                                'add_ons_name' => $add_ons_data->add_ons_name,
                                                'add_ons_price' => $add_ons_data->add_ons_price
                                            );
                                            $itemTotal += $add_ons_data->add_ons_price;
                                        }
                                    }
                                    $customization[] = array(
                                        'addons_category_id' => $val['addons_category_id'],
                                        'addons_category' => $val['addons_category'],
                                        'addons_list' => $addonscust
                                    );
                                }
                            }
                        }

                        if ($itemTotal) {
                            $itemTotal = ($value['quantity']) ? $value['quantity'] * $itemTotal : '';
                        } else {
                            $itemTotal = ($priceRate && $value['quantity']) ? $value['quantity'] * $priceRate : '';
                        }
                        $item[] = array(
                            'name' => $data->name,
                            'image' => $image,
                            'note' => $value['note'],
                            'menu_id' => $value['menu_id'],
                            'quantity' => $value['quantity'],
                            'price' => $data->price,
                            'offer_price' => ($value['offer_price']) ? $value['offer_price'] : '',
                            'is_veg' => $data->is_veg,
                            'is_customize' => 1,
                            'is_deal' => $value['is_deal'],
                            'itemTotal' => $itemTotal,
                            'has_variation' => $value['has_variation'] == 1 ? 1 : 0,
                            $value['has_variation'] ? 'variation_list' : 'addons_category_list' => $customization
                        );
                    } else {
                        $itemTotal = ($priceRate) ? $value['quantity'] * $priceRate : '';
                        $item[] = array(
                            'name' => $data->name,
                            'image' => $image,
                            'note' => $value['note'],
                            'menu_id' => $value['menu_id'],
                            'quantity' => $value['quantity'],
                            'price' => $data->price,
                            'offer_price' => ($value['offer_price']) ? $value['offer_price'] : '',
                            'is_veg' => $data->is_veg,
                            'is_customize' => 0,
                            'itemTotal' => $itemTotal,
                            'is_deal' => $value['is_deal']
                        );
                        $price = ($value['offer_price']) ? $value['offer_price'] : $data->price;
                    }

                    if ($discount_rate > 0) {
                        $ls1 = ($itemTotal) * $discount_rate;
                        $dscnt = $dscnt + $ls1;
                        $initial_total = ($itemTotal);
                        $subtotal = $subtotal + $initial_total;
                        $SD = (($initial_total * $data->sd) / 100);
                        $sd_add = $initial_total + $SD;
                        $sd_total = $sd_total + $SD;
                        $VAT = (($sd_add * $data->vat) / 100);
                        $vat_total = $vat_total + $VAT;
                    } elseif ($match == 1 && $discount_rate_item > 0) {
                        $ls1 = ($itemTotal) * $discount_rate_item;
                        $dscnt = $dscnt + $ls1;
                        $initial_total = ($itemTotal);
                        $subtotal = $subtotal + $initial_total;
                        $SD = (($initial_total * $data->sd) / 100);
                        $sd_add = $initial_total + $SD;
                        $sd_total = $sd_total + $SD;
                        $VAT = (($sd_add * $data->vat) / 100);
                        $vat_total = $vat_total + $VAT;
                    } else if ($discount_amount > 0) {
                        $initial_total = ($itemTotal);
                        $subtotal = $subtotal + $initial_total;
                        $SD = (($initial_total * $data->sd) / 100);
                        $sd_add = $initial_total + $SD;
                        $sd_total = $sd_total + $SD;
                        $VAT = (($sd_add * $data->vat) / 100);
                        $vat_total = $vat_total + $VAT;
                    } elseif ($match == 1 && $discount_amount_item > 0) {
                        $initial_total = ($itemTotal);
                        $subtotal = $subtotal + $initial_total;
                        $SD = (($initial_total * $data->sd) / 100);
                        $sd_add = $initial_total + $SD;
                        $sd_total = $sd_total + $SD;
                        $VAT = (($sd_add * $data->vat) / 100);
                        $vat_total = $vat_total + $VAT;
                    } elseif ($gradual) {
                        $flag = 0;
                        if ($all_items == 0) {

                            foreach ($gradual_specific_coupon as $key => $v) {
                                $get_items = $this->api_model->gradualItem($v);

                                $gradual_menu_ids = array_column($get_items, 'item_id');

                                //compare to get same ids
                                $gradual_ids = in_array($value['menu_id'], $gradual_menu_ids);

                                if ($gradual_ids) {
                                    $flag = 1;


                                    //if user didnt apply any current gradual coupon

                                    foreach ($gradual as $key => $c) {
                                        //check previous order for user
                                        $user_previous_order = $this->api_model->checkRecords('gradual_coupon_track', $user_id, $c->coupon_id);
                                        if ($c->coupon_id == $v) {

                                            if (!empty($same_coupon)) {

                                                $same_check = in_array($c->coupon_id, $same_coupon);

                                                if (!$same_check) {
                                                    array_push($same_coupon, $c->coupon_id);
                                                }
                                            } else {
                                                array_push($same_coupon, $c->coupon_id);
                                            }


                                            $get_sequence = $this->api_model->getSequence($c->coupon_id);

                                            if (empty($user_previous_order)) {


                                                if ($get_sequence[0]) {
                                                    $discount_rate = ($get_sequence[0]->percentage) / 100;

                                                    $coupon_id = -100; //$c->coupon_id; //-100 for specfic gradual items
                                                    $coupon_type = $c->amount_type;
                                                    $coupon_amount = $get_sequence[0]->percentage;
                                                    $coupon_discount = $discount + $discount_amount;
                                                    $name = 'Discount'; //$c->name;
                                                    break;
                                                }
                                            }

                                            if ($user_previous_order) {
                                                if ($get_sequence[$user_previous_order->last_applied] && $user_previous_order->count > 0) {
                                                    $discount_rate = ($get_sequence[$user_previous_order->last_applied]->percentage) / 100;

                                                    $coupon_id = -100; //$c->coupon_id; //-100 for specfic gradual items
                                                    $coupon_type = $c->amount_type;
                                                    $coupon_amount = $get_sequence[$user_previous_order->last_applied]->percentage;
                                                    $coupon_discount = $discount + $discount_amount;
                                                    $name = 'Discount'; //$c->name;
                                                    break;
                                                } else {
                                                    $flag = 0;
                                                }
                                            }
                                        }
                                    }

                                    if ($discount_rate > 0) {

                                        $ls1 = ($itemTotal) * $discount_rate;
                                        if ($c->discount_amount && ($ls1 > $c->discount_amount) && !$same_check) {
                                            //for multiple discount upto
                                            $dscnt = $dscnt + $c->discount_amount;
                                        } elseif ($same_check) {
                                            $dscnt = $dscnt + $ls1;
                                            if ($c->discount_amount && ($dscnt > $c->discount_amount)) {
                                                $dscnt = $c->discount_amount;
                                            }
                                        } else {
                                            $dscnt = $dscnt + $ls1;
                                        }
                                        $initial_total = ($itemTotal);
                                        $subtotal = $subtotal + $initial_total;
                                        $SD = (($initial_total * $data->sd) / 100);
                                        $sd_add = $initial_total + $SD;
                                        $sd_total = $sd_total + $SD;
                                        $VAT = (($sd_add * $data->vat) / 100);
                                        $vat_total = $vat_total + $VAT;
                                        $discount_rate = 0;
                                        break;
                                    }
                                }
                            }

                            if ($flag == 0) {

                                $initial_total = ($itemTotal);
                                $subtotal = $subtotal + $initial_total;
                                $SD = (($initial_total * $data->sd) / 100);
                                $sd_add = $initial_total + $SD;
                                $sd_total = $sd_total + $SD;
                                $VAT = (($sd_add * $data->vat) / 100);
                                $vat_total = $vat_total + $VAT;
                            }
                        }
                    } else {
                        $initial_total = ($itemTotal);
                        $subtotal = $subtotal + $initial_total;
                        $SD = (($initial_total * $data->sd) / 100);
                        $sd_add = $initial_total + $SD;
                        $sd_total = $sd_total + $SD;
                        $VAT = (($sd_add * $data->vat) / 100);
                        $vat_total = $vat_total + $VAT;
                    }
                }
            }
        }
        $messsage =  $this->lang->line('record_found');
        $status = 1;
        $subtotalCal = $subtotal;
        $deliveryPrice = '';
        if ($this->post('order_delivery') == 'Delivery') {
            //check delivery charge available
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            $res_address = $this->api_model->getRecord('restaurant_address', 'resto_entity_id', $this->post('restaurant_id'));
            $reslat = $res_address->latitude;
            $reslong = $res_address->longitude;
            ///
            $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $reslat . "," . $reslong . "&destination=" . $latitude . "," . $longitude . "&key=AIzaSyCf6ULw0KomuiBnGs_drfnoYKBiEHwziYU&mode=driving";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            $datas = json_decode($result, true);
            $apicall = $datas['routes'][0]['legs'][0]['distance']['value'];
            $distance = $apicall / 1000;
            ///
            //$check = $this->checkGeoFence($latitude, $longitude, $price_charge = true, $this->post('restaurant_id'));
            $check_delivery = $this->api_model->getRecord('zone', 'entity_id', $zone_id);
            if ($distance <= 3) {
                $check = $check_delivery->price_charge;
            } else if ($distance > 3 && $distance <= 5) {
                $check = $check_delivery->price_charge_2;
            } else {
                $check = $check_delivery->price_charge_3;
            }
            $temp = $subtotal + $dscnt;
            //$check = $this->getDeliveryPriceSlot($subtotalCal);
            if ($check) {
                if ($discount_delivery > 0) {
                    $total = $subtotal + $vat_total + $sd_total;

                    $deliveryPrice = 0;
                } else {
                    $d_v = ($check * $data->vat) / 100;
                    $vat_total = ($vat_total);
                    $total = $subtotal + $check + $vat_total + $sd_total;
                    $deliveryPrice = $check;
                }
            } else {
                $total = $subtotal + $check + $vat_total + $sd_total;
            }
        } else {
            $total = $subtotal + $check + $vat_total + $sd_total;
        }


        $discount = ($discount) ? array('label' => $this->lang->line('discount'), 'value' => abs($discount), 'label_key' => "Discount") : '';

        if ($discount_amount > 0 || $discount_amount_item > 0) {
            if ($discount_amount > 0) {
                $dscnt = $discount_amount;
            } else {
                $dscnt = $discount_amount_item;
            }
        }

        if ($dscnt > 0 || $discount_delivery > 0) {

            if ($dscnt > $discount_upto) {
                $not_applicable = 1;
                $total = ($subtotal + $sd_total + $vat_total + $deliveryPrice) - $discount_upto;
            } else {
                $total = ($subtotal + $sd_total + $vat_total + $deliveryPrice) - $dscnt;
            }

            $priceArray = array(
                array('label' => $this->lang->line('sub_total'), 'value' => $subtotal, 'label_key' => "Sub Total"),
                // $discount,
                array('label' => "Discount", 'value' => ($not_applicable == 1) ? $discount_upto : $dscnt, 'label_key' => "discount"),
                array('label' => "SD", 'value' => $sd_total, 'label_key' => "SD"),
                array('label' => "VAT", 'value' => $vat_total, 'label_key' => "VAT"),
                ($deliveryPrice) ? array('label' => $this->lang->line('delivery_charge'), 'value' => $deliveryPrice, 'label_key' => "Delivery Charge") : '',
                array('label' => $this->lang->line('total'), 'value' => $total, 'label_key' => "Total"),
            );
            $isApply = true;
        } else {
            $priceArray = array(

                array('label' => $this->lang->line('sub_total'), 'value' => ($subtotal ? $subtotal : 0) + ($discount ? $discount : 0), 'label_key' => "Sub Total"),
                ($deliveryPrice) ? array('label' => $this->lang->line('delivery_charge'), 'value' => $deliveryPrice, 'label_key' => "Delivery Charge") : '',
                array('label' => "SD", 'value' => $sd_total, 'label_key' => "SD"),
                array('label' => "VAT", 'value' => $vat_total, 'label_key' => "VAT"),
                array('label' => $this->lang->line('total'), 'value' => $total, 'label_key' => "Total"),
            );
        }
        $add_data = array(
            'user_id' => ($user_id) ? $user_id : '',
            'items' => serialize($item),
            'restaurant_id' => ($this->post('restaurant_id')) ? $this->post('restaurant_id') : ''
        );
        if ($cart_id == '') {
            $cart_id = $this->api_model->addRecord('cart_detail', $add_data);
        } else {
            $this->api_model->updateUser('cart_detail', $add_data, 'cart_id', $cart_id);
        }
        if (!empty($user_id)) {
            $totalrounded = ceil($total);
            $checkRecordAmount = $this->db->select('id')->where('user_id', $this->post('user_id'))->get('cart_amount')->first_row();
            if (!empty($checkRecordAmount)) {
                $this->db->set('cart_total', $totalrounded);
                $this->db->set('sub_total', $subtotal);
                $this->db->where('user_id', $this->post('user_id'));
                $this->db->update('cart_amount');
                //$this->response(['status'=>1,'message' => $checkRecord], REST_Controller::HTTP_OK);

            } else {
                $data = array('user_id' => $this->post('user_id'), 'cart_total' => $totalrounded, 'sub_total' => $subtotal);
                $this->api_model->addRecord('cart_amount', $data);
                //$this->response(['status'=>1,'message' => "Notworking"], REST_Controller::HTTP_OK);

            }
        }
        $this->response([
            'total' => $total,
            'cart_id' => $cart_id,
            'items' => $item,
            'price' => $priceArray,
            'coupon_id' => $coupon_id,
            'coupon_amount' => ($coupon_amount) ? $coupon_amount : '',
            'coupon_type' => $coupon_type,
            'coupon_name' => $name,
            'coupon_discount' => ($not_applicable == 1) ? $discount_upto : $dscnt,
            'subtotal' => ($not_applicable == 1) ? ($subtotal)  : $subtotal,
            'vat' => $vat_total,
            'sd' => $sd_total,
            'currency_code' => $currencyDetails[0]->currency_code,
            'currency_symbol' => $currencyDetails[0]->currency_symbol,
            'delivery_charge' => ($deliveryPrice) ? $deliveryPrice : '',
            'is_apply' => $isApply,
            'status' => $status,
            'message' => $messsage
        ], REST_Controller::HTTP_OK); // OK
    }


    //change address
    public function changePassword_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
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
    //add order
    public function addOrder_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $dif = $this->post('dif') ? $this->post('dif') : 0;
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($this->post('preorder_mode') == 1) {
            $orderStatus = 'preorder';
            $order_mode = 1;
        } else {
            $orderStatus = 'placed';
            $order_mode = 0;
        }
        if ($tokenres) {
            $subtotal = $this->post('subtotal');
            $taxdetail = $this->api_model->getRestaurantTax('restaurant', $this->post('restaurant_id'), $flag = "order");
            $commission = $this->api_model->commission($this->post('restaurant_id'));
            $commissionValue = ($subtotal * $commission->commission) / 100;
            $total = 0;

            // if ($this->post('coupon_name')) {
            //     $limitations = $this->api_model->getLimitations($this->post('coupon_name'));
            //     if ($limitations->user_limitation) {
            //         $limitationValue = $limitations->user_limitation - 1;

            //         if ($limitationValue == 0) {
            //             $column = array('status' => 0, 'user_limitation' => null);
            //             $this->api_model->updateValue('coupon', $column, $limitations->entity_id);
            //         } else {
            //             $column = array('user_limitation' => $limitationValue);
            //             $this->api_model->updateValue('coupon', $column, $limitations->entity_id);
            //         }
            //     }
            // }

            $app_version_number = $this->post('app_version_number') ? $this->post('app_version_number') : '';
            $app_os = $this->post('app_os') ? $this->post('app_os') : '';
            $total = $this->post('total');
            $max_order_verification = $this->db->get_where('system_option', array('OptionSlug' => 'max_order_verification'))->first_row();
            if ($total >= floatval($max_order_verification->OptionValue)) {
                $notify_value = 0;
            } else {
                $notify_value = 1;
            }


            $add_data = array(
                'user_id' => $this->post('user_id'),
                'restaurant_id' => $this->post('restaurant_id'),
                'address_id' => $this->post('address_id'),
                'coupon_id' => $this->post('coupon_id') ? $this->post('coupon_id') : 0,
                'order_status' => $orderStatus,
                'order_date' => ($this->post('preorder_mode') == 1) ? date('Y-m-d H:i:s', strtotime($this->post('preorder_date'))) : date('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'coupon_type' => $this->post('coupon_type') ? $this->post('coupon_type') : null,
                'coupon_amount' => ($this->post('coupon_amount')) ? $this->post('coupon_amount') : 0,
                'total_rate' => $this->post('order_delivery') == "Pickup" ? $this->post('total') - $this->post('delivery_charge') : $this->post('total') + $dif,
                'status' => 0,
                'coupon_discount' => ($this->post('coupon_discount')) ? $this->post('coupon_discount') : 0,
                'delivery_charge' => $this->post('order_delivery') == "Delivery" ? $this->post('delivery_charge') : 0,
                'extra_comment' => $this->post('extra_comment'),
                'coupon_name' => $this->post('coupon_name') ? $this->post('coupon_name') : '',
                'sd' => $this->post('sd') ? $this->post('sd') : 0,
                'vat' => $this->post('vat') ? $this->post('vat') : 0,
                'preorder_mode' => $order_mode,
                'preorder_date' => date('Y-m-d H:i:s', strtotime($this->post('preorder_date'))),
                'app_version_number' => $app_version_number,
                'app_os' => $app_os,
                'transaction_id' => $this->post('transaction_id') ? $this->post('transaction_id') : "null",
                'payment_option' => $this->post('payment_option') ? $this->post('payment_option') : "null",
                'zone_id' => ($this->post('zoneId')) ? $this->post('zoneId') : NULL,
                'commission_rate' => ($commission->commission) ? $commission->commission : 0,
                'commission_value' => ($commissionValue) ? $commissionValue : 0,
                'verify_order' => $notify_value
            );
            if ($this->post('order_delivery') == 'Delivery') {
                $add_data['order_delivery'] = 'Delivery';
            } else {
                $add_data['order_delivery'] = 'PickUp';
            }
            $order_id = $this->api_model->addRecord('order_master', $add_data);

            if ($orderStatus == 'preorder') {
                $addData = array(
                    'order_status' => $orderStatus,
                    'order_id' => $order_id,
                    'time' => date('Y-m-d H:i:s'),
                    'status_created_by' => 'User'
                );
                $this->api_model->addData($addData);
            }
            //add items
            $items = $this->post('items');
            $itemDetail = json_decode($items, true);
            // $itemDetail = $items;
            $add_item = array();
            $same_coupon = array();
            if (!empty($itemDetail)) {
                foreach ($itemDetail['items'] as $key => $value) {
                    if ($this->post('coupon_id') == -100) {

                        $specific_item_gradual = $this->api_model->getSpecificGradual($this->post('restaurant_id'));
                        //$gradual_check =  in_array($value['menu_id'], array_column($specific_item_gradual, 'item_id'));

                        foreach ($specific_item_gradual as $key => $v) {
                            if ($value['menu_id'] == $v->item_id) {

                                if (!empty($same_coupon)) {

                                    $same_check = in_array($v->coupon_id, $same_coupon);

                                    if (!$same_check) {
                                        array_push($same_coupon, $v->coupon_id);
                                    }
                                } else {
                                    array_push($same_coupon, $v->coupon_id);
                                }

                                $check_previous = $this->api_model->checkRecords('gradual_coupon_track', $user_id, $v->coupon_id);

                                if ($check_previous && !$same_check) {
                                    $decrement = array('user_id' => $user_id, 'coupon_id' => $v->coupon_id);
                                    $this->db->set('count', 'count-1', FALSE);
                                    $this->db->set('last_applied', 'last_applied+1', FALSE);
                                    $this->db->where($decrement);
                                    $this->db->update('gradual_coupon_track');
                                }

                                if (empty($check_previous)) {
                                    $getHighestSequence = $this->api_model->getHighestSequence($v->coupon_id);

                                    $track_detail = array(
                                        'user_id' => $user_id,
                                        'coupon_id' => $v->coupon_id,
                                        'count' => $getHighestSequence->sequence - 1,
                                        'last_applied' => 1
                                    );
                                    $this->api_model->addRecord('gradual_coupon_track', $track_detail);
                                }
                            }
                        }
                    }

                    if ($value['is_customize'] == 1) {

                        if ($value['has_variation'] == 1) {
                            $customization = array();
                            foreach ($value['variation_list'] as $k => $each_variation) {
                                $addons = array();
                                if ($each_variation['addons_category_list'] && !empty($each_variation['addons_category_list'])) {
                                    foreach ($each_variation['addons_category_list'] as $k => $val) {
                                        $addonscust = array();

                                        if ($val['addons_list'] && !empty($val['addons_list'])) {
                                            foreach ($val['addons_list'] as $m => $mn) {

                                                $addonscust[] = array(
                                                    'add_ons_id' => $mn['add_ons_id'],
                                                    'add_ons_name' => $mn['add_ons_name'],
                                                    'add_ons_price' => $mn['add_ons_price'],
                                                );
                                            }
                                            $addons[] = array(
                                                'addons_category_id' => $val['addons_category_id'],
                                                'addons_category' => $val['addons_category'],
                                                'addons_list' => $addonscust
                                            );
                                        }
                                    }
                                }

                                $customization[] = array(
                                    'variation_id'  => $each_variation['variation_id'],
                                    'variation_name'   => $each_variation['variation_name'],
                                    'variation_price'   => $each_variation['variation_price'],
                                    'addons_category_list'  => $addons
                                );
                            }
                        } else {
                            $customization = array();
                            if ($value['addons_category_list'] && !empty($value['addons_category_list'])) {
                                foreach ($value['addons_category_list'] as $k => $val) {
                                    $addonscust = array();
                                    foreach ($val['addons_list'] as $m => $mn) {
                                        if ($value['is_deal'] == 1) {
                                            $addonscust[] = array(
                                                'add_ons_id' => $mn['add_ons_id'],
                                                'add_ons_name' => $mn['add_ons_name'],
                                            );
                                        } else {
                                            $addonscust[] = array(
                                                'add_ons_id' => $mn['add_ons_id'],
                                                'add_ons_name' => $mn['add_ons_name'],
                                                'add_ons_price' => $mn['add_ons_price']
                                            );
                                        }
                                    }
                                    $customization[] = array(
                                        'addons_category_id' => $val['addons_category_id'],
                                        'addons_category' => $val['addons_category'],
                                        'addons_list' => $addonscust
                                    );
                                }
                            }
                        }

                        $add_item[] = array(
                            "item_name" => $value['name'],
                            "item_id" => $value['menu_id'],
                            'note' => $value['note'],
                            "qty_no" => $value['quantity'],
                            "rate" => ($value['price']) ? $value['price'] : '',
                            "offer_price" => ($value['offer_price']) ? $value['offer_price'] : '',
                            "order_id" => $order_id,
                            "is_customize" => 1,
                            "is_deal" => $value['is_deal'],
                            "itemTotal" => $value['itemTotal'],
                            'has_variation' => $value['has_variation'] == 1 ? 1 : 0,
                            $value['has_variation'] ? 'variation_list' : 'addons_category_list' => $customization
                        );
                    } else {
                        $add_item[] = array(
                            "item_name" => $value['name'],
                            "item_id" => $value['menu_id'],
                            'note' => $value['note'],
                            "qty_no" => $value['quantity'],
                            "rate" => ($value['price']) ? $value['price'] : '',
                            "offer_price" => ($value['offer_price']) ? $value['offer_price'] : '',
                            "order_id" => $order_id,
                            "is_customize" => 0,
                            "itemTotal" => $value['itemTotal'],
                            "is_deal" => $value['is_deal'],
                        );
                    }
                }
            }

            $address = $this->api_model->getAddress('user_address', 'entity_id', $this->post('address_id'));

            $user_detail = array(
                'first_name' => $tokenres->first_name,
                'last_name' => ($tokenres->last_name) ? $tokenres->last_name : '',
                'address' => ($address) ? $address[0]->address : '',
                'landmark' => ($address) ? $address[0]->landmark : '',
                'zipcode' => ($address) ? $address[0]->zipcode : '',
                'city' => ($address) ? $address[0]->city : '',
                'latitude' => ($address) ? $address[0]->latitude : '',
                'longitude' => ($address) ? $address[0]->longitude : '',
            );
            $order_detail = array(
                'order_id' => $order_id,
                'user_detail' => serialize($user_detail),
                'item_detail' => serialize($add_item),
                'restaurant_detail' => serialize($taxdetail),
            );
            $this->api_model->addRecord('order_detail', $order_detail);
            $this->api_model->sendNotiRestaurant($this->post('restaurant_id'));
            $verificationCode = random_string('alnum', 25);
            $email_template = $this->db->get_where('email_template', array('email_slug' => 'order-receive-alert', 'language_slug' => 'en', 'status' => 1))->first_row();

            $this->db->select('OptionValue');
            $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

            $this->db->select('OptionValue');
            $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
            if (!empty($email_template)) {
                $this->load->library('email');
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'html';
                $this->email->initialize($config);
                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                $this->email->to(trim($taxdetail->email));
                $this->email->subject($email_template->subject);
                $this->email->message($email_template->message);
                $this->email->send();
            }
            $order_status = $orderStatus;
            $message = $this->lang->line('success_add');
            // send invoice to user
            $data['order_records'] = $this->api_model->getEditDetail($order_id);
            $data['menu_item'] = $this->api_model->getInvoiceMenuItem($order_id);
            $html = $this->load->view('backoffice/order_invoice', $data, true);
            if (!@is_dir('uploads/invoice')) {
                @mkdir('./uploads/invoice', 0777, TRUE);
            }
            $filepath = 'uploads/invoice/' . $order_id . '.pdf';
            $this->load->library('M_pdf');
            $mpdf = new mPDF('', 'Letter');
            $mpdf->SetHTMLHeader('');
            $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="http://restaura.evdpl.com/~restaura/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
            $mpdf->AddPage(
                '', // L - landscape, P - portrait
                '',
                '',
                '',
                '',
                0, // margin_left
                0, // margin right
                10, // margin top
                23, // margin bottom
                0, // margin header
                0 //margin footer
            );
            $mpdf->autoScriptToLang = true;
            $mpdf->SetAutoFont();
            $mpdf->WriteHTML($html);
            $mpdf->output($filepath, 'F');

            //send invoice as email
            $user = $this->db->get_where('users', array('entity_id' => $this->post('user_id')))->first_row();
            $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();
            $this->db->select('OptionValue');
            $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
            $this->db->select('subject,message');
            $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'new-order-invoice', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
            $arrayData = array('FirstName' => $user->first_name, 'Order_ID' => $order_id);
            $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
            if (!empty($EmailBody)) {
                $this->load->library('email');
                $config['charset'] = 'iso-8859-1';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'html';
                $this->email->initialize($config);
                $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                $this->email->to(trim($user->email));
                $this->email->subject($Emaildata->subject);
                $this->email->message($EmailBody);
                $this->email->attach($filepath);
                $this->email->send();
            }


            $order_status = $orderStatus;

            $maparray = array(
                'order_id' => $order_id,
                'driver_id' => 0,
            );
            $this->api_model->addDataDriver('order_driver_map', $maparray);

            if ($order_status != 'preorder' && $this->post('order_delivery') == 'Delivery' && $notify_value == 1) {
                $this->api_model->updateDriver($order_id);
            }

            // if ($order_status == 'preorder') {
            //     $maparray = array(
            //         'order_id' => $order_id,
            //         'driver_id' => 0,
            //     );
            //     $this->api_model->addDataDriver('order_driver_map', $maparray);
            // }

            $this->response(['restaurant_detail' => $taxdetail, 'order_id' => $order_id, 'order_status' => $order_status, 'order_date' => ($this->post('preorder_mode') == 1) ? date('Y-m-d H:i:s', strtotime($this->post('preorder_date'))) : date('Y-m-d H:i:s', strtotime($this->post('order_date'))), 'status' => 1, 'message' => $message], REST_Controller::HTTP_OK); // OK */
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function fetchCategory_get()
    {
        $data = $this->api_model->fetchCategory();

        if (!empty($data)) {
            $this->response(['categories' => $data, 'status' => 1, 'message' => $this->lang->line('record_found')]);
        } else {
            $this->response(['status' => 0, 'message' => $this->lang->line('record_not_found')]);
        }
    }

    //order detail proccess
    public function inProcessOrderDetail_post()
    {
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        $count = ($this->post('count')) ? $this->post('count') : 10;
        $page_no = ($this->post('page_no')) ? $this->post('page_no') : 1;
        if ($tokenres) {
            $result = $this->api_model->getOrderDetail('process', $user_id, $count, $page_no);
            $result2 = $this->api_model->getOrderDetail('past', $user_id, $count, $page_no);
            $result3 = $this->api_model->getOrderDetail('preorder', $user_id, $count, $page_no);
            $this->response(['in_process' => $result, 'past' => $result2, 'preorder' => $result3, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //order detail past
    public function pastOrderDetail_post()
    {
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        $count = ($this->post('count')) ? $this->post('count') : 10;
        $page_no = ($this->post('page_no')) ? $this->post('page_no') : 1;
        if ($tokenres) {
            $result = $this->api_model->getOrderDetail('past', $user_id, $count, $page_no);
            $this->response(['past' => $result, 'status' => 1, 'message' => $this->lang->line('record_found')], REST_Controller::HTTP_OK); // OK */
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get promocode list
    public function couponList_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $is_admin = $this->post('is_admin');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $subtotal = $this->post('subtotal');
            $coupon = $this->api_model->getcouponList($subtotal, $this->post('restaurant_id'), $this->post('order_delivery'), $user_id, $is_admin);
            if (!empty($coupon)) {
                $this->response([
                    'coupon_list' => $coupon,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                ],  REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('promocode')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get notification list
    function getNotification_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $notification = $this->api_model->getNotification($user_id, $this->post('count'), $this->post('page_no'));
            if (!empty($notification)) {
                $this->response([
                    //'notification' => $notification['result'],
                    'noti' => $notification['noti'],
                    'status' => 1,
                    'notificaion_count' => $notification['count'],
                    'message' => $this->lang->line('record_found')
                ],  REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //check users order delivery
    public function checkOrderDelivery_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $zone_id = $this->post('zoneId');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $deliveryPrice = '';
            if ($this->post('order_delivery') == 'Delivery') {
                $users_latitude = $this->post('users_latitude');
                $users_longitude = $this->post('users_longitude');
                $user_km = ($this->post('user_km')) ? $this->post('user_km') : '';
                $driver_km = ($this->post('driver_km')) ? $this->post('driver_km') : '';
                // $detail = true;
                //$detail = $this->api_model->checkOrderDelivery($users_latitude, $users_longitude, $user_id, $this->post('restaurant_id'), $request = '', $order_id = '', $user_km, $driver_km);
                $res_address = $this->api_model->getRecord('restaurant_address', 'resto_entity_id', $this->post('restaurant_id'));
                $reslat = $res_address->latitude;
                $reslong = $res_address->longitude;
                $url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $reslat . "," . $reslong . "&destination=" . $users_latitude . "," . $users_longitude . "&key=AIzaSyCf6ULw0KomuiBnGs_drfnoYKBiEHwziYU&mode=driving";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                curl_close($ch);
                $datas = json_decode($result, true);
                $apicall = $datas['routes'][0]['legs'][0]['distance']['value'];
                $distance = $apicall / 1000;
                $check_delivery = $this->api_model->getRecord('zone', 'entity_id', $zone_id);
                if ($distance <= 3) {
                    $check = $check_delivery->price_charge;
                } else if ($distance > 3 && $distance <= 5) {
                    $check = $check_delivery->price_charge_2;
                } else {
                    $check = $check_delivery->price_charge_3;
                }
                $detail = $this->api_model->checkGeoFenceForDelivery($users_latitude, $users_longitude, $zone_id);
                if ($detail) {
                    $restaurantAvail = true;
                    //$restaurantAvail = $this->api_model->checkRestaurantAvailability($users_latitude, $users_longitude, $user_id, $this->post('restaurant_id'), $request = '', $order_id = '', $user_km, $driver_km);
                    if ($restaurantAvail) {
                        $resstatus = 1;
                        $message = $this->lang->line('delivery_available');
                    } else {
                        $resstatus = 0;
                        $message = $this->lang->line('restaurant_delivery_not_available');
                    }

                    $this->response([
                        "lat" => $users_latitude,
                        "long" => $users_longitude,
                        'status' => ($resstatus == 1) ? 1 : 0,
                        "delivery_charge" => $check,
                        'message' => $message,
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code

                } else {
                    $this->response([
                        'status' => 0,
                        "detail" => $detail,
                        'check' =>   $this->post('restaurant_id'),
                        'message' => $this->lang->line('delivery_not_available')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //get driver location
    public function driverTracking_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $order_id = $this->post('order_id');
            $detail = $this->api_model->getdriverTracking($order_id, $user_id);
            if ($detail) {
                $this->response([
                    'detail' => $detail,
                    'status' => 1,
                    'message' => $this->lang->line('record_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //check if order is delivered or not
    public function checkOrderDelivered_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $order_id = $this->post('order_id');
            $is_delivered = $this->post('is_delivered');
            if ($is_delivered != 1) {
                $this->db->set('order_status', 'pending')->where('entity_id', $order_id)->update('order_master');
                $add_data = array('order_id' => $order_id, 'order_status' => 'pending', 'time' => date('Y-m-d H:i:s'), 'status_created_by' => 'User');
                $this->api_model->addRecord('order_status', $add_data);
                $this->response([
                    'status' => 1,
                    'message' => $this->lang->line('success_update')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response([
                    'status' => 1,
                    'message' => $this->lang->line('success_update')
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
        $token = $this->post('token');
        $userid = $this->post('user_id');
        $tokenres = $this->api_model->getRecord('users', 'entity_id', $userid);
        if ($tokenres) {
            $data = array('device_id' => "");
            $this->api_model->updateUser('users', $data, 'entity_id', $tokenres->entity_id);
            $this->response(['status' => 1, 'message' => $this->lang->line('user_logout')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    //check lat long exist in area
    public function checkGeoFence($latitude, $longitude, $price_charge, $restaurant_id)
    {
        $result = $this->api_model->checkGeoFence('delivery_charge', 'restaurant_id', $restaurant_id);
        $latlongs =  array($latitude, $longitude);
        $data = '';
        $oddNodes = false;
        $delivery_charge = '';
        foreach ($result as $key => $value) {

            $lat_longs = $value->lat_long;
            $lat_longs =  explode('~', $lat_longs);
            $polygon = array();
            foreach ($lat_longs as $key => $val) {
                if ($val) {
                    $val = str_replace(array('[', ']'), array('', ''), $val);
                    $polygon[] = explode(',', $val);
                }
            }
            if ($polygon[0] != $polygon[count($polygon) - 1])
                $polygon[count($polygon)] = $polygon[0];
            $j = 0;
            $x = $longitude;
            $y = $latitude;
            $n = count($polygon);
            for ($i = 0; $i < $n; $i++) {
                $j++;
                if ($j == $n) {
                    $j = 0;
                }
                if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >= $y))) {
                    if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] - $polygon[$i][1]) < $x) {
                        $oddNodes = true;
                        $delivery_charge = $value->price_charge;
                    }
                }
            }
        }
        $oddNodes = ($price_charge) ? $delivery_charge : $oddNodes;
        return $oddNodes;
    }
    //get user lang
    public function getUserLanguage_post()
    {
        $this->getLang();
        $token = $this->post('token');
        $user_id = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $user_id);
        if ($tokenres) {
            $data = array('language_slug' => $this->post('language_slug'));
            $this->api_model->updateUser('users', $data, 'entity_id', $user_id);
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
        $token = $this->post('token');
        $userid = $this->post('user_id');
        $tokenres = $this->api_model->checkToken($token, $userid);
        if ($tokenres) {
            $data = array('device_id' => $this->post('firebase_token'));
            $this->api_model->updateUser('users', $data, 'entity_id', $userid);
            $this->response(['status' => 1, 'message' => $this->lang->line('success_update')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function getRestaurantMenuItemSearch_post()
    {
        $latitude = ($this->post('latitude')) ? $this->post('latitude') : '';
        $longitude = ($this->post('longitude')) ? $this->post('longitude') : '';
        $searchItem = ($this->post('itemSearch')) ? $this->post('itemSearch') : '';
        date_default_timezone_set('Asia/Dhaka');
        $date = date('Y-m-d H:i:s');

        $restaurants = $this->api_model->getHomeRestaurant(null, $latitude, $longitude, $searchItem, '', '', '', $date, null, '', $this->current_lang, 0, 0, '', '', false);
        //$res_id = array_column($restaurants,'restuarant_id');
        $zone_id = $this->api_model->checkGeoFenceForZone($latitude, $longitude);

        if ($zone_id)
            $menuItems = $this->api_model->getMenuItems($latitude, $longitude, $searchItem, $zone_id);

        foreach ($menuItems as $key => $value) {
            $value->image = ($value->image) ? image_url . $value->image : '';
        }

        $this->response(['status' => 1, 'restaurants' => $restaurants, 'menuItems' => $menuItems, 'zone_id' => $zone_id], REST_Controller::HTTP_OK);
    }

    public function getOrderAmountBoundary_get()
    {
        $min = $this->api_model->getOrderAmountBoundary('Minimum Order Value');
        $max = $this->api_model->getOrderAmountBoundary('Maximum Order Value');

        $this->response(['minimum_order_value' => ($min->OptionValue) ? (int)$min->OptionValue : 0, 'maximum_order_value' => ($max->OptionValue) ? (int)$max->OptionValue : 0]);
    }


    public function restaurant_time_change_post()
    {
        $open = $this->post('open');
        $close = $this->post('close');
        if (!empty($open) && !empty($close)) {
            $this->api_model->changeTiming($open, $close);
        } else {
            $this->response([
                'message' => "Invalid"
            ], REST_Controller::HTTP_OK);
        }
    }


    public function getCampaignRestaurants_post()
    {
        if ($this->post('latitude') != "" && $this->post('longitude') != "") {
            date_default_timezone_set('Asia/Dhaka');
            $date = date('Y-m-d H:i:s');
            $food = $this->post('food');
            $rating = $this->post('rating');
            $distance = $this->post('distance');
            $priceRange = $this->post('priceRange');
            $searchItem = ($this->post('itemSearch')) ? $this->post('itemSearch') : '';
            $category_id = ($this->post('restaurant_category')) ? $this->post('restaurant_category') : '';
            $campaign_id = $this->post('campaign_id');
            $zone_id = $this->post('zone_id');

            $restaurant = $this->api_model->getHomeRestaurant(null, $this->post('latitude'), $this->post('longitude'), $searchItem, $category_id, $food, $rating, $priceRange, $date, $distance, $this->current_lang, $this->post('count'), $this->post('page_no'), $campaign_id, $zone_id);
            $this->response([
                'date' => date("Y-m-d g:i A"),
                'restaurant' => $restaurant,
                'status' => 1,
                'message' => $this->lang->line('record_found')
            ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function userFavouriteRestaurants_post()
    {
        $user_id = $this->post('user_id');
        $restaurant_id = $this->post('restaurant_id');

        if ($restaurant_id && $user_id) {
            $checkRecord = $this->api_model->getRecord('user_favourite_restaurants', 'user_id', $user_id);

            if ($checkRecord) {

                $favourite_restaurants = unserialize($checkRecord->favourite_restaurants);

                if ($this->post('action') == 'like') {
                    array_push($favourite_restaurants, $restaurant_id);
                    $this->db->set('likes', 'likes+1', FALSE);
                    $this->db->where('entity_id', $restaurant_id);
                    $this->db->update('restaurant');
                }
                if ($this->post('action') == 'dislike') {

                    if (($key = array_search($restaurant_id, $favourite_restaurants)) !== false) {
                        unset($favourite_restaurants[$key]);
                    }

                    $this->db->set('likes', 'likes-1', FALSE);
                    $this->db->where('entity_id', $restaurant_id);
                    $this->db->update('restaurant');
                }

                $update = array('favourite_restaurants' => serialize($favourite_restaurants));
                $this->api_model->updateUser('user_favourite_restaurants', $update, 'user_id', $user_id);
                $this->response(['status' => 1], REST_Controller::HTTP_OK);
            } else {
                $restaurants = array();
                array_push($restaurants, $restaurant_id);
                $addData = array(
                    'user_id' => $user_id,
                    'favourite_restaurants' => serialize($restaurants)
                );
                $this->api_model->addRecord('user_favourite_restaurants', $addData);
                $this->response(['status' => 1], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function userFavouriteRestaurantsLists_post()
    {
        $user_id = $this->post('user_id');
        $zone_id = $this->post('zone_id');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');

        if ($user_id) {
            $checkRecord = $this->api_model->getRecord('user_favourite_restaurants', 'user_id', $user_id);

            if ($checkRecord) {
                $favourite_restaurants = unserialize($checkRecord->favourite_restaurants);
                $restaurants = $this->api_model->getFavouriteRestaurants($favourite_restaurants, $zone_id, $latitude, $longitude);
                $this->response([
                    'status' => 1,
                    'restaurants' => $restaurants
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            } else {
                $this->response([
                    'status' => -1,
                    'message' => ''
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function checkreroute_post()
    {
        $data =  $this->api_model->getOrders();
        $this->response(['status' => 1, 'message' => $data], REST_Controller::HTTP_OK);
    }

    public function reRouting_post()
    {
        $getOrders = $this->api_model->getOrders();

        if ($getOrders) {
            // $this->api_model->writeLog("FOUND REROUTING ORDERS");
            foreach ($getOrders as $key => $value) {
                // $this->api_model->writeLog('REROUTING STARTED FOR '.$value->order_id.': driver:'.$value->driver);
                $a = new DateTime();

                $b = new DateTime();

                $diff = (strtotime($a->format('Y-m-d H:i:s')) - strtotime($b->format($value->timer))) / 60;

                if ($diff >= 0) {

                    //$this->api_model->writeLog('TIME DIFFERENCE FOUND: '.$diff.' ');
                    //update response
                    $condition = ['order_id' => $value->order_id, 'driver_id' => $value->driver];
                    $this->api_model->checkResponse($condition, $value->driver);
                    $this->api_model->updateDriver($value->order_id);
                }
                //$this->api_model->writeLog('NOT EXPIRED ORDER, PASSING: '.$diff.' ');
            }
            $this->response([
                'status' => -1,
                'message' => $getOrders
            ], REST_Controller::HTTP_OK);
        }
        $this->response([
            'status' => 10,
            'message' => $getOrders
        ], REST_Controller::HTTP_OK);
    }

    public function getLinkDetails_post()
    {
        $link = $this->post('link');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $date = date('Y-m-d H:i:s');
        $user_id = $this->post('user_id');

        if ($link && $latitude && $longitude) {
            $data = array();
            $linkWithoutQueryString = strtok($link, '?');
            $dlStr = array_pop(explode('/', $linkWithoutQueryString));
            $dlDetails = decryptDeepLink($dlStr);
            if (!$dlDetails) {
                $data['page'] = "Home";
                $data['id'] = null;
            }

            $zone_id = $this->api_model->checkGeoFenceForZone($latitude, $longitude);
            $opsSetting = $this->api_model->getOperationSettings();

            if ($dlDetails['page'] == 'Restaurant') {
                $data['details'] = $zone_id
                    ?
                    ($opsSetting['operation_on_off'] == 1
                        ? $this->api_model->getRestaurantDetail('', $this->current_lang, $date, $dlDetails['id'], $zone_id, $user_id, $latitude, $longitude)
                        : null)
                    : [];
            } else if ($dlDetails['page'] == 'Campaign') {
                $restaurant = $this->api_model->getHomeRestaurant($user_id, $this->post('latitude'), $this->post('longitude'), $searchItem, $food, $rating, $priceRange, $date, $distance, $offers, $this->current_lang, $this->post('count'), $this->post('page_no'), $dlDetails['id'], null);
                $campaigns = $this->api_model->getCampaign($restaurant);
                $data['details'] = [];
                foreach ($campaigns as $k => $v) {
                    if ($v->entity_id == $dlDetails['id']) {
                        $data['details'] = $opsSetting['operation_on_off'] == 1 ? [$v] : null;
                    }
                }
            }
            $data['zone_id'] = $zone_id;
            $data['page'] =  $dlDetails ? $dlDetails['page'] : $data['page'];
            $data['id'] = $dlDetails ? $dlDetails['id'] :  $data['id'];
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;
            $this->response([
                'status' => 1,
                'data' => $data,
                'message' => $data['details'] ? "Success" : "Not available for you."
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => -1,
                'message' => ''
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
}
