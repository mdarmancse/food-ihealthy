<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL . '/common_model');
		$this->load->model('/home_model');

		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory', $data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug', $data['lang']->language_slug);
		}
	}
	// get home page
	public function index()
	{
		$data['current_page'] = 'HomePage';
		$data['page_title'] = $this->lang->line('home_page') . ' | ' . $this->lang->line('site_title');
		$this->session->set_userdata('previous_url', current_url());
//		if ($this->session->userdata('is_user_login')) {
//			$restaurants = $this->home_model->getRestaurants();
//			$data['restaurants'] = array_values($restaurants);
//		} else {
//			$restaurants = $this->home_model->getRestaurants("popular");
//			$data['restaurants'] = array_values($restaurants);
//		}
//		if (!empty($data['restaurants'])) {
//			foreach ($data['restaurants'] as $key => $value) {
//				$ratings = $this->home_model->getRestaurantReview($value['MainRestaurantID']);
//				$data['restaurants'][$key]['ratings'] = $ratings;
//			}
//		}
		// $data['categories'] = $this->home_model->getAllCategories();
	//	$data['feature_items'] = $this->home_model->getFeatureItems();
		//$data['campaign'] = $this->home_model->getCampaign();

		// $data['coupons'] = $this->home_model->getAllCoupons();
		$this->load->view('home_page', $data);
	}
	// frontend user login
	public function login()
	{
		$data['page_title'] = $this->lang->line('title_login') . ' | ' . $this->lang->line('site_title');
		if ($this->input->post('submit_page') == "Login") {
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required');
			if ($this->form_validation->run()) {
				$phone_number = trim($this->input->post('phone_number'));
				$enc_pass = md5(SALT . trim($this->input->post('password')));

				$this->db->where('mobile_number', $phone_number);
				$this->db->where('password', $enc_pass);
				$this->db->where("(user_type='User')");
				$val = $this->db->get('users')->first_row();
				if (!empty($val)) {
					if ($val->active == '1' && $val->status == '1') {
						$this->session->set_userdata(
							array(
								'UserID' => $val->entity_id,
								'userFirstname' => $val->first_name,
								'userLastname' => $val->last_name,
								'userEmail' => $val->email,
								'userPhone' => $val->mobile_number,
								'userImage' => $val->image,
								'is_admin_login' => 0,
								'is_user_login' => 1,
								'UserType' => $val->user_type,
								'package_id' => array(),
							)
						);
						// remember ME
						$cookie_name = "adminAuth";
						if ($this->input->post('rememberMe') == 1) {
							$this->input->set_cookie($cookie_name, 'usr=' . $phone_number . '&hash=' . trim($this->input->post('password')), 60 * 60 * 24 * 5); // 5 days
						} else {
							delete_cookie($cookie_name);
						}
						if ($this->session->userdata('previous_url')) {
							redirect($this->session->userdata('previous_url'));
						} else {
							redirect(base_url() . 'myprofile');
						}
					} else if ($val->status == '0') {
						$data['loginError'] = $this->lang->line('front_login_deactivate');
					} else if ($val->active == '0' || $val->active == '') {
						$this->session->set_userdata("temp_mobile", $val->mobile_number);
						redirect(base_url() . 'home/verifyAccount');
					} else {
						$data['loginError'] = $this->lang->line('front_login_error');
					}
				} else {
					$data['loginError'] = $this->lang->line('front_login_error');
				}
				$this->session->set_flashdata('error_MSG', $data['loginError']);
				redirect(base_url() . 'home/login');
				exit;
			}
		}
		$data['current_page'] = 'Login';
		$this->load->view('login', $data);
	}
	/*
    * Server side validation check email exist
    */
	public function checkPhone($str)
	{
		$checkPhone = $this->home_model->checkPhone($str);
		if ($checkPhone > 0) {
			$this->form_validation->set_message('checkPhone', $this->lang->line('number_already_registered'));
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function verifyAccount()
	{
		if (!$this->session->userdata("temp_mobile")) {
			redirect(base_url() . 'home');
		}
		$data['page_title'] = "Verify Account" . ' | ' . $this->lang->line('site_title');
		if ($this->input->post('submit_page') == "Send") {

			$checkRecords = $this->common_model->getRowsMultipleWhere('users', array('mobile_number' => $this->session->userdata("temp_mobile"), 'status' => 1));
			if (!empty($checkRecords[0])) {
				if ($this->session->userdata("temp_mobile")) {
					$otp = mt_rand(100000, 999999);
					$this->sendOtp($otp, $this->session->userdata("temp_mobile"));
					$addata = array('sms_otp' => $otp);
					$this->common_model->updateData('users', $addata, 'entity_id', $checkRecords[0]->entity_id);
					$data['success'] = "OTP sent to your number. Enter it below";
				}
			} else {
				$data['error'] = "Something went wrong";
			}

			echo json_encode($data);
		} else {

			$this->load->view('verify_account', $data);
		}
	}
	/*
	* Server side validation check email exist
	*/
	public function checkEmail($str)
	{
		$checkEmail = $this->home_model->checkEmail($str);
		if ($checkEmail > 0) {
			$this->form_validation->set_message('checkEmail', 'User have already registered with this email!');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// frontend user registration
	public function registration()
	{
		$data['page_title'] = $this->lang->line('title_registration') . ' | ' . $this->lang->line('site_title');
		if ($this->input->post('submit_page') == "Register") {
			$this->form_validation->set_rules('name', 'Name', 'trim|required');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|callback_checkPhone');
			// $this->form_validation->set_rules('email', 'Email', 'trim|required|callback_checkEmail');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
			if ($this->form_validation->run()) {
				$checkRecords = $this->home_model->mobileCheck(trim($this->input->post('phone_number')));
				if ($checkRecords == 0) {
					$name = trim($this->input->post('name'));
					$namearr = explode(" ", $name);
					if (!empty($namearr)) {
						foreach ($namearr as $key => $value) {
							if ($key != 0) {
								$last_name[] = $value;
							}
						}
					}
					$userData = array(
						"first_name" => (!empty($namearr[0])) ? $namearr[0] : '',
						"last_name" => (!empty($last_name)) ? implode(" ", $last_name) : '',
						"password" => md5(SALT . $this->input->post('password')),
						// "email" => trim($this->input->post('email')),
						"mobile_number" => trim($this->input->post('phone_number')),
						"user_type" => "User",
						"status" => 1
					);
					$entity_id = $this->common_model->addData('users', $userData);
					if ($entity_id) {
						$data['reg_success'] = $this->lang->line('registration_success');
					}
					if ($this->input->post('phone_number')) {
						$otp = mt_rand(100000, 999999);
						$this->sendOtp($otp, $this->input->post('phone_number'));
						$addata = array('sms_otp' => $otp);
						$this->common_model->updateData('users', $addata, 'entity_id', $entity_id);
						$data['reg_success'] = "OTP sent to your number. Enter it below";
					}
				} else {
					$data['reg_error'] = $this->lang->line('front_registration_fail');
				}
			} else {
				$data['reg_error'] = validation_errors();
			}

			echo json_encode($data);
		} else {
			$data['current_page'] = 'Registration';
			$this->load->view('registration', $data);
		}
	}
	// user forgot password
	public function forgot_password()
	{
		if ($this->input->post('forgot_submit_page') == "Submit") {
			$this->form_validation->set_rules('mobile_forgot', 'Number', 'required');
			if ($this->form_validation->run()) {
				$checkRecord = $this->common_model->getRowsMultipleWhere('users', array('mobile_number' => $this->input->post('mobile_forgot'), 'status' => 1));
				$arr['forgot_success'] = '';
				$arr['forgot_error'] = '';
				if (!empty($checkRecord[0])) {
					// confirmation link
					if ($this->input->post('mobile_forgot')) {
						$otp = mt_rand(100000, 999999);
						$this->sendOtp($otp, $checkRecord[0]->mobile_number);
						// update verification code
						$addata = array('sms_otp' => $otp);
						$this->common_model->updateData('users', $addata, 'entity_id', $checkRecord[0]->entity_id);
					}
					$arr['forgot_success'] = "OTP has been sent to your number. Enter it below.";
				} else {
					$arr['forgot_error'] = "Phone number not found.";
				}
			}
		}

		echo json_encode($arr);
	}

	private function sendOtp($otp, $number)
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
	// user logout
	public function logout()
	{
		$this->session->unset_userdata('UserID');
		$this->session->unset_userdata('userFirstname');
		$this->session->unset_userdata('userLastname');
		$this->session->unset_userdata('userEmail');
		$this->session->unset_userdata('userPhone');
		$this->session->unset_userdata('is_user_login');
		$this->session->unset_userdata('package_id');
		delete_cookie('cart_details');
		delete_cookie('cart_restaurant');
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
	}

	public function update_password()
	{
		$new_password = $this->input->post('new_password', TRUE);
		$phone_number = $this->input->post('PhoneNumber', TRUE);
		$otp = $this->input->post('sms_otp', TRUE);

		$checkRecord = $this->common_model->getRowsMultipleWhere('users', array('mobile_number' => $phone_number, 'status' => 1));
		if (!empty($checkRecord[0])) {
			if ($otp == $checkRecord[0]->sms_otp) {
				$updateData = [
					'password'	=> md5(SALT . $new_password),
				];
				$this->common_model->updateData('users', $updateData, 'mobile_number', $checkRecord[0]->mobile_number);
				$this->session->set_flashdata('page_MSG', "OTP not validated.");
				redirect(base_url() . 'home/login');
			} else {
				// $arr['forgot_error'] = "OTP not validated.";
				$this->session->set_flashdata('error_MSG', "OTP not validated.");
				redirect(base_url() . 'home/login');
			}
		} else {
			// $arr['forgot_error'] = "User not found.";
			$this->session->set_flashdata('error_MSG', "User not found.");
			redirect(base_url() . 'home/login');
		}
	}
	// add lat long to session once if searched by user
	public function addLatLong()
	{
		if (!empty($this->input->post('lat')) && !empty($this->input->post('long')) && !empty($this->input->post('address'))) {
			$this->session->set_userdata(
				array(
					'searched_lat' => $this->input->post('lat'),
					'searched_long' => $this->input->post('long'),
					'searched_address' => $this->input->post('address'),
				)
			);
		}
	}
	// get Popular Resturants
	public function getPopularResturants()
	{
		$data['page_title'] = $this->lang->line('popular_restaurants') . ' | ' . $this->lang->line('site_title');
		$restaurants = $this->home_model->getRestaurants();
		if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) {
			$address = $this->getAddress($this->input->post('latitude'), $this->input->post('longitude'));
			if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) {
					$distance = $this->getDistance($this->input->post('latitude'), $this->input->post('longitude'), $value['latitude'], $value['longitude']);
					if ((int)$distance < MAXIMUM_RANGE) {
						$nearbyRestaurants[] = $restaurants[$key];
					}
				}
			}
			if (!empty($nearbyRestaurants)) {
				foreach ($nearbyRestaurants as $key => $value) {
					$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
					$nearbyRestaurants[$key]['ratings'] = $ratings;
				}
			}
			$data['nearbyRestaurants'] = $nearbyRestaurants;
		} else {
			if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) {
					$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
					$restaurants[$key]['ratings'] = $ratings;
				}
			}
			$data['nearbyRestaurants'] = array_values($restaurants);
		}
		$this->load->view('popular_restaurants', $data);
	}

	public function ajaxGetPopularResturants()
	{
		$data['page_title'] = $this->lang->line('popular_restaurants') . ' | ' . $this->lang->line('site_title');
		$headers = array(
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, base_url() . "v1/Api/getHome");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
			"latitude" => $this->input->post('latitude'),
			"longitude" => $this->input->post('longitude'),
			"count" => $this->input->post('limit'),
		)));
		$curlData = json_decode(curl_exec($ch));
		curl_close($ch);
		$restaurants = $curlData->restaurant;

		if (!empty($this->input->post('latitude')) && !empty($this->input->post('longitude'))) {
			if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) {

					$restaurants[$key] = (array) $value;
					$restaurants[$key]['timings'] = (array) $restaurants[$key]['timings'];
					$distance = $this->getDistance($this->input->post('latitude'), $this->input->post('longitude'), $value->latitude, $value->longitude);
					if ((int)$distance < MAXIMUM_RANGE) {
						$nearbyRestaurants[] = $restaurants[$key];
					}
				}
			}

			$data['nearbyRestaurants'] = $nearbyRestaurants;
		} else {
			if (!empty($restaurants)) {
				foreach ($restaurants as $key => $value) {
					$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
					$restaurants[$key]['ratings'] = $ratings;
				}
			}
			$data['nearbyRestaurants'] = array_values($restaurants);
		}

		$this->load->view('popular_restaurants', $data);
	}
	// get user's address with lat long
	public function getUserAddress()
	{
		$this->session->set_userdata(
			array(
				'latitude' => $this->input->post('latitude'),
				'longitude' => $this->input->post('longitude'),
			)
		);
		$address = $this->getAddress($this->input->post('latitude'), $this->input->post('longitude'));
		echo json_encode($address);
	}
	// get distance between two pair of coordinates
	function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
	{
		$earth_radius = 6371;

		$dLat = deg2rad($latitude2 - $latitude1);
		$dLon = deg2rad($longitude2 - $longitude1);

		$a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
		$c = 2 * asin(sqrt($a));
		$d = $earth_radius * $c;
		return $d;
	}
	// get address from lat long
	function getAddress($latitude, $longitude)
	{
		if (!empty($latitude) && !empty($longitude)) {
			//Send request and receive json data by address
			$geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($latitude) . ',' . trim($longitude) . '&key=AIzaSyANO7UcO8EO1_9y3TaSZS5E_E9cFOpiEl8');
			$output = json_decode($geocodeFromLatLong);
			$status = $output->status;
			//Get address from json data
			$address = ($status == "OK") ? $output->results[1]->formatted_address : '';
			//Return address of the given latitude and longitude
			if (!empty($address)) {
				return $address;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	// categories search
	public function quickCategorySearch()
	{
		$data['page_title'] = $this->lang->line('popular_restaurants') . ' | ' . $this->lang->line('site_title');
		$restaurants = $this->home_model->searchRestaurants($this->input->post('category_id'));
		if (!empty($restaurants)) {
			foreach ($restaurants as $key => $value) {
				$distance = $this->getDistance($this->session->userdata('latitude'), $this->session->userdata('longitude'), $value['latitude'], $value['longitude']);
				if ($distance < MAXIMUM_RANGE) {
					$nearbyRestaurants[] = $restaurants[$key];
				}
			}
		}
		if (!empty($nearbyRestaurants)) {
			foreach ($nearbyRestaurants as $key => $value) {
				$ratings = $this->home_model->getRestaurantReview($value['restaurant_id']);
				$nearbyRestaurants[$key]['ratings'] = $ratings;
			}
		}
		$data['nearbyRestaurants'] = $nearbyRestaurants;
		$this->load->view('popular_restaurants', $data);
	}
	// function to get  the address
	function get_lat_long($address)
	{
		$address = str_replace(" ", "+", $address);
		$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
		$json = json_decode($json);
		$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
		$latlng = array('latitude' => $lat, 'longitude' => $long);
		return json_encode($latlng);
	}
	// get users notification
	public function getNotifications()
	{
		if (!empty($this->session->userdata('UserID'))) {
			$data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'), 'unread');
			$data['notification_count'] = count($data['userUnreadNotifications']);
			$data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
			$this->load->view('ajax_notifications', $data);
		}
	}
	// get unread notifications
	public function unreadNotifications()
	{
		if (!empty($this->session->userdata('UserID'))) {
			$updateData = array(
				'view_status' => 1,
			);
			$this->common_model->updateData('user_order_notification', $updateData, 'user_id', $this->session->userdata('UserID'));
			$this->common_model->updateData('user_event_notifications', $updateData, 'user_id', $this->session->userdata('UserID'));
			$data['userUnreadNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'), 'unread');
			$data['notification_count'] = count($data['userUnreadNotifications']);
			$data['userNotifications'] = $this->common_model->getUsersNotification($this->session->userdata('UserID'));
		}
	}
}
