<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkout extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model(ADMIN_URL . '/common_model');
		$this->load->model('/restaurant_model');
		$this->load->model('/cart_model');
		$this->load->model('/home_model');
		$this->load->model('/checkout_model');
		$this->load->model('v1/api_model');
		if (empty($this->session->userdata('language_slug'))) {
			$data['lang'] = $this->common_model->getdefaultlang();
			$this->session->set_userdata('language_directory', $data['lang']->language_directory);
			$this->config->set_item('language', $data['lang']->language_directory);
			$this->session->set_userdata('language_slug', $data['lang']->language_slug);
		}
	}
	// index chechout page
	public function index()
	{
		$data['current_page'] = 'Checkout';
		$data['page_title'] = $this->lang->line('title_checkout') . ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$this->db->update('cart_amount', ['cart_total' => $data['cart_details']['cart_total_price']], ['user_id' => $this->session->UserID]);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		if ($this->input->post('submit_login_page') == "Login") {
			$this->form_validation->set_rules('login_phone_number', 'Phone Number', 'trim|required');
			$this->form_validation->set_rules('login_password', 'Password', 'trim|required');
			if ($this->form_validation->run()) {
				$phone_number = trim($this->input->post('login_phone_number'));
				$enc_pass = md5(SALT . trim($this->input->post('login_password')));

				$this->db->where('mobile_number', $phone_number);
				$this->db->where('password', $enc_pass);
				$this->db->where("(user_type='User')");
				$val = $this->db->get('users')->first_row();
				if (!empty($val)) {
					if ($val->status != '0') {
						$this->session->set_userdata(
							array(
								'UserID' => $val->entity_id,
								'userFirstname' => $val->first_name,
								'userLastname' => $val->last_name,
								'userEmail' => $val->email,
								'userPhone' => $val->mobile_number,
								'is_admin_login' => 0,
								'is_user_login' => 1,
								'UserType' => $val->user_type,
								'package_id' => array(),
							)
						);
						// remember ME
						$cookie_name = "adminAuth";
						if ($this->input->post('rememberMe') == 1) {
							$this->input->set_cookie($cookie_name, 'usr=' . $phone_number . '&hash=' . trim($this->input->post('login_password')), 60 * 60 * 24 * 5); // 5 days
						} else {
							delete_cookie($cookie_name);
						}
						redirect(base_url() . 'checkout');
					} else if ($val->status == '0') {
						$data['loginError'] = $this->lang->line('front_login_deactivate');
					} else {
						$data['loginError'] = $this->lang->line('front_login_error');
					}
				} else {
					$data['loginError'] = $this->lang->line('front_login_error');
				}
				$this->session->set_flashdata('error_MSG', $data['loginError']);
				redirect(base_url() . 'checkout');
				exit;
			}
			$data['page'] = "login";
		}
		$deliveryCharge = $this->checkout_model->getDeliveryCharges();
		$this->session->set_userdata(array('checkDelivery' => 'pickup', 'deliveryCharge' => $deliveryCharge->price_charge));
		$this->load->view('checkout', $data);
	}
	// ajax checkout page for filters
	public function ajax_checkout()
	{
		$data['current_page'] = 'Checkout';
		$cart_details = get_cookie('cart_details');
		$arr_cart_details = json_decode($cart_details);
		$cart_restaurant = get_cookie('cart_restaurant');
		if (!empty($this->input->post('entity_id')) && !empty($this->input->post('restaurant_id'))) {
			if ($this->input->post('action') == "plus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity + 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
						foreach (json_decode($cart_details) as $key => $value) {
							if ($key == $menukey) {
								$cookie = array(
									'menu_id'   => $value->menu_id,
									'quantity' => ($value->quantity) ? ($value->quantity + 1) : 1,
									'has_variation' => ($value->has_variation && $value->has_variation == 1) ? 1 : 0,
									'addons'  => $value->addons,
								);
								$arrayDetails[] = $cookie;
							} else {
								$oldcookie = $value;
								$arrayDetails[] = $oldcookie;
							}
						}
					}
					$this->input->set_cookie('cart_details', json_encode($arrayDetails), 60 * 60 * 24 * 1); // 1 day
					$this->input->set_cookie('cart_restaurant', $this->input->post('restaurant_id'), 60 * 60 * 24 * 1); // 1 day
				}
			} else if ($this->input->post('action') == "minus") {
				$menukey = '';
				$arrayDetails = array();
				if ($cart_restaurant == $this->input->post('restaurant_id')) {
					if (!empty($arr_cart_details)) {
						foreach ($arr_cart_details as $ckey => $value) {
							if ($ckey == $this->input->post('cart_key')) {
								$value->quantity = $value->quantity - 1;
								$menukey = $ckey;
							}
						}
					}
					if (!empty(json_decode($cart_details))) {
						foreach (json_decode($cart_details) as $key => $value) {
							if ($value->quantity > 1) {
								if ($key == $menukey) {
									$cookie = array(
										'menu_id'   => $value->menu_id,
										'quantity' => ($value->quantity) ? ($value->quantity - 1) : 1,
										'has_variation' => ($value->has_variation && $value->has_variation == 1) ? 1 : 0,
										'addons'  => $value->addons,
									);
									$arrayDetails[] = $cookie;
								} else {
									$oldcookie = $value;
									$arrayDetails[] = $oldcookie;
								}
							} else {
								if ($key != $menukey) {
									$oldcookie = $value;
									$arrayDetails[] = $oldcookie;
								}
							}
						}
					}
					$this->input->set_cookie('cart_details', json_encode($arrayDetails), 60 * 60 * 24 * 1); // 1 day
					$cart_details = $this->getcookie('cart_details');
					if (empty(json_decode($cart_details))) {
						delete_cookie('cart_details');
						delete_cookie('cart_restaurant');
					} else {
						$this->input->set_cookie('cart_restaurant', $this->input->post('restaurant_id'), 60 * 60 * 24 * 1); // 1 day
					}
				}
			} else if ($this->input->post('action') == "remove" && $this->input->post('cart_key') != '') {
				$arrayDetails = array();
				if (!empty(json_decode($cart_details))) {
					foreach (json_decode($cart_details) as $key => $value) {
						if ($key != $this->input->post('cart_key')) {
							$oldcookie = $value;
							$arrayDetails[] = $oldcookie;
						}
					}
				}
				$this->input->set_cookie('cart_details', json_encode($arrayDetails), 60 * 60 * 24 * 1); // 1 day
				$cart_details = $this->getcookie('cart_details');
				if (empty(json_decode($cart_details))) {
					delete_cookie('cart_details');
					delete_cookie('cart_restaurant');
				} else {
					$this->input->set_cookie('cart_restaurant', $this->input->post('restaurant_id'), 60 * 60 * 24 * 1); // 1 day
				}
			}
			$cart_details = $this->getcookie('cart_details');
			$cart_restaurant = $this->getcookie('cart_restaurant');
		}
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$ajax_your_items = $this->load->view('ajax_your_items', $data, true);
		$order_summary = $this->load->view('ajax_order_summary', $data, true);
		$array_view = array(
			'ajax_your_items' => $ajax_your_items,
			'ajax_order_summary' => $order_summary
		);
		echo json_encode($array_view);
	}
	// get the recently added cookies
	public function getcookie($name)
	{
		$cookies = [];
		$headers = headers_list();
		foreach ($headers as $key => $header) {
			if (strpos($header, 'Set-Cookie: ') === 0) {
				$value = str_replace('&', urlencode('&'), substr($header, 12));
				parse_str(current(explode(';', $value)), $pair);
				$cookies = array_merge_recursive($cookies, $pair);
			}
		}
		return $cookies[$name];
	}
	// get Cart items
	public function getCartItems($cart_details, $cart_restaurant)
	{
		$cartItems = array();
		$cartTotalPrice = 0;
		if (!empty($cart_details)) {
			foreach (json_decode($cart_details) as $key => $value) {
				$details = $this->restaurant_model->getMenuItem($value->menu_id, $cart_restaurant);
				if (!empty($details)) {
					if ($details[0]['items'][0]['is_customize'] == 1) {
						$addons_category_id = array_column($value->addons, 'addons_category_id');
						$add_onns_id = array_column($value->addons, 'add_onns_id');

						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!in_array($cat_value['addons_category_id'], $addons_category_id)) {
									unset($details[0]['items'][0]['addons_category_list'][$key]);
								} else {
									if (!empty($cat_value['addons_list'])) {
										foreach ($cat_value['addons_list'] as $addkey => $add_value) {
											if (!in_array($add_value['add_ons_id'], $add_onns_id)) {
												unset($details[0]['items'][0]['addons_category_list'][$key]['addons_list'][$addkey]);
											}
										}
									}
								}
							}
						}
					}
					// getting subtotal
					if ($details[0]['items'][0]['is_customize'] == 1) {
						$subtotal = 0;
						if (!empty($details[0]['items'][0]['addons_category_list'])) {
							foreach ($details[0]['items'][0]['addons_category_list'] as $key => $cat_value) {
								if (!empty($cat_value['addons_list'])) {
									foreach ($cat_value['addons_list'] as $addkey => $add_value) {
										$subtotal += $add_value['add_ons_price'];
									}
								}
							}
						}
					} else {
						$subtotal = 0;
						if ($details[0]['items'][0]['is_deal'] == 1) {
							$price = ($details[0]['items'][0]['offer_price']) ? $details[0]['items'][0]['offer_price'] : (($details[0]['items'][0]['price']) ? $details[0]['items'][0]['price'] : 0);
						} else {
							$price = ($details[0]['items'][0]['price']) ? $details[0]['items'][0]['price'] : 0;
						}
						$subtotal = $subtotal + $price;
					}
					$cartTotalPrice = ($subtotal * $value->quantity) + $cartTotalPrice;
					$cartItems[] = array(
						'menu_id' => $details[0]['items'][0]['menu_id'],
						'restaurant_id' => $cart_restaurant,
						'name' => $details[0]['items'][0]['name'],
						'quantity' => $value->quantity,
						'is_customize' => $details[0]['items'][0]['is_customize'],
						'is_veg' => $details[0]['items'][0]['is_veg'],
						'is_deal' => $details[0]['items'][0]['is_deal'],
						'price' => $details[0]['items'][0]['price'],
						'vat' => $details[0]['items'][0]['vat'],
						'sd' => $details[0]['items'][0]['sd'],
						'offer_price' => $details[0]['items'][0]['offer_price'],
						'subtotal' => $subtotal,
						'totalPrice' => ($subtotal * $value->quantity),
						'cartTotalPrice' => $cartTotalPrice,
						'addons_category_list' => $details[0]['items'][0]['addons_category_list'],
					);
				}
			}
		}
		$cart_details = array(
			'cart_items' => $cartItems,
			'cart_total_price' => $cartTotalPrice,
		);
		return $cart_details;
	}
	// get lat long from the address
	public function getAddressLatLng()
	{
		$latlong = array();
		if (!empty($this->input->post('entity_id'))) {
			$latlong = $this->checkout_model->getAddressLatLng($this->input->post('entity_id'));
		}
		echo json_encode($latlong);
	}
	// get the delivery charges
	public function getDeliveryCharges()
	{
		$check = '';
		$cart_restaurant = get_cookie('cart_restaurant');
		if (!empty($this->input->post('action')) && $this->input->post('action') == "get") {
			$latitude = $this->input->post('latitude');
			$longitude = $this->input->post('longitude');
			$res_address = $this->api_model->getRecord('restaurant_address', 'resto_entity_id', $cart_restaurant);
			$reslat = $res_address->latitude;
			$reslong = $res_address->longitude;
			$zone_id = $this->checkout_model->getResZone($cart_restaurant);
			///
			$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $reslat . "," . $reslong . "&destination=" . $latitude . "," . $longitude . "&key=" . MAP_API_KEY . "&mode=driving";
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
			if ($check) {
				$this->session->set_userdata(array('checkDelivery' => 'available', 'deliveryCharge' => $check));
			} else {
				$this->session->set_userdata(array('checkDelivery' => 'notAvailable', 'deliveryCharge' => 0));
			}
		}
		if (!empty($this->input->post('action')) && $this->input->post('action') == "remove") {
			$check = 0;
			$this->session->set_userdata(array('checkDelivery' => 'pickup', 'deliveryCharge' => 0));
		}
		if ($check == '' || $check == 0) {
			$this->session->set_userdata(array('coupon_id' => '', 'coupon_applied' => 'no'));
		}
		/*if ($this->session->userdata('coupon_applied') == "yes" && $this->session->userdata('coupon_id') != '') {
			$checkCoupon = $this->checkout_model->getCouponDetails($this->session->userdata('coupon_id'));
    		if(!empty($checkCoupon)){
				$discount = $this->session->userdata('deliveryCharge');
                $this->session->set_userdata(
	            	array(
		            	'coupon_id' => $checkCoupon->entity_id,
		            	'coupon_type' => $checkCoupon->amount_type,
		            	'coupon_amount' => $checkCoupon->amount,
		            	'coupon_discount' => abs($discount),
		            	'coupon_name' => $checkCoupon->name
	            	)
	            );
    		}
		}*/ //echo '<pre>';
		//print_r($this->session->userdata());
		$cart_details = get_cookie('cart_details');
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$order_summary = $this->load->view('ajax_order_summary', $data, true);
		$array_view = array(
			'check' => $check,
			'ajax_order_summary' => $order_summary
		);
		//echo '<pre>'; print_r($array_view); exit;
		echo json_encode($array_view);


		//echo $check;
	}
	// remove the delivery charges
	public function removeDeliveryOptions()
	{
		$this->session->set_userdata(array('checkDelivery' => 'pickup', 'deliveryCharge' => 0));
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$data['order_mode'] = $this->session->userdata('order_mode');
		$this->load->view('ajax_order_summary', $data);
	}
	//check lat long exist in area
	public function checkGeoFence($latitude, $longitude, $price_charge, $restaurant_id)
	{
		$result = $this->checkout_model->checkGeoFence($restaurant_id);
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
	// check geo fence area
	public function checkFence($point, $polygon, $price_charge)
	{
		if ($polygon[0] != $polygon[count($polygon) - 1])
			$polygon[count($polygon)] = $polygon[0];
		$j = 0;
		$oddNodes = '';
		$x = $point[1];
		$y = $point[0];
		$n = count($polygon);
		for ($i = 0; $i < $n; $i++) {
			$j++;
			if ($j == $n) {
				$j = 0;
			}
			if ((($polygon[$i][0] <= $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] <= $y) && ($polygon[$i][0] >=
				$y))) {
				if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
					$polygon[$i][1]) < $x) {
					$oddNodes = 'true';
				}
			}
		}
		$oddNodes = ($oddNodes) ? $price_charge : $oddNodes;
		return $oddNodes;
	}
	// get the coupons
	public function getCoupons()
	{
		$html = '';
		$cart_restaurant = get_cookie('cart_restaurant');
		$this->session->set_userdata(array('coupon_id' => '', 'coupon_applied' => 'no'));
		$this->session->unset_userdata('coupon_type');
		$this->session->unset_userdata('coupon_amount');
		$this->session->unset_userdata('coupon_discount');
		$this->session->unset_userdata('coupon_name');
		if (!empty($this->input->post('subtotal')) && !empty($this->input->post('order_mode'))) {
			$coupons = $this->checkout_model->getCouponsList($this->input->post('subtotal'), $cart_restaurant, $this->input->post('order_mode'),  $this->session->userdata('UserID'));
			$order_mode = "'" . $this->input->post('order_mode') . "'";
			if (!empty($coupons)) {
				$html = '<h5>' . $this->lang->line("choose_avail_coupons") . '</h5>
				<form id="coupon_form" name="coupon_form" class="form-horizontal float-form">
						<div class="login-details">
                            <div id="coupons" class="form-group">
								<select class="form-control" name="add_coupon" id="add_coupon" onchange="getCouponDetails(this.value,' . $this->input->post('subtotal') . ',' . $order_mode . ')">
			                        <option value="">' . $this->lang->line("select") . '</option>';
				foreach ($coupons as $key => $value) {
					$html .= '<option value="' . $value->coupon_id . '">' . $value->name . '</option>';
				}
				$html .= '</select>
				                <label>' . $this->lang->line("your_coupons") . '</label>
							</div>
						</div>
				</form>';
			} else {
				$html = '<h5>' . $this->lang->line("no_coupons_available") . '</h5>';
				$this->session->set_userdata(array('coupon_id' => '', 'coupon_applied' => 'no'));
			}
		}
		$this->session->set_userdata(array('order_mode' => $this->input->post('order_mode')));
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$data['order_mode'] = $this->input->post('order_mode');
		$order_summary = $this->load->view('ajax_order_summary', $data, true);
		$array_view = array(
			'html' => $html,
			'ajax_order_summary' => $order_summary
		);
		echo json_encode($array_view);
	}
	// add a coupon for a order
	public function addCoupon()
	{
		$data['page_title'] = $this->lang->line('add_coupon') . ' | ' . $this->lang->line('site_title');
		if (!empty($this->input->post('coupon_id')) && !empty($this->input->post('subtotal'))) {
			$this->session->set_userdata(array('coupon_id' => $this->input->post('coupon_id'), 'coupon_applied' => 'yes'));
			$check = $this->checkout_model->getCouponDetails($this->input->post('coupon_id'));
			$status = 1;
			if (!empty($check)) {
				if ($check->coupon_type == 'discount_on_cart') {
					if ($check->amount_type == 'Percentage') {
						$discount = (($this->input->post('subtotal') * $check->amount) / 100);
					} else if ($check->amount_type == 'Amount') {
						$discount = $check->amount;
					}

					$coupon_id = $check->entity_id;
					$coupon_type = $check->amount_type;
					$coupon_amount = $check->amount;
					$coupon_discount = ($discount);
					$name = $check->name;
				}
				if ($check->coupon_type == 'selected_user') {

					if ($check->amount_type == 'Percentage') {
						$discount =  (($this->input->post('subtotal') * $check->amount) / 100);
					} else if ($check->amount_type == 'Amount') {
						// $discount_amount = $check->amount/$item_number;
						$discount = $check->amount;
					}
					$coupon_id = $check->entity_id;
					$coupon_type = $check->amount_type;
					$coupon_amount = $check->amount;
					$coupon_discount = $discount;
					$name = $check->name;
				}
				if ($check->coupon_type == 'free_delivery') {

					$discount = $this->session->userdata('deliveryCharge');

					$coupon_id = $check->entity_id;
					$coupon_type = $check->amount_type;
					$coupon_amount = $check->amount;
					$coupon_discount = ($discount);
					$name = $check->name;
				}
				if ($check->coupon_type == 'user_registration') {
					$checkOrderCount = $this->checkout_model->checkUserCountCoupon($this->session->userdata('UserID'));
					if ($checkOrderCount > 0) {
						$status = 2;
					} else {
						if ($check->amount_type == 'Percentage') {
							$discount = (($this->input->post('subtotal') * $check->amount) / 100);
						} else if ($check->amount_type == 'Amount') {
							$discount = $check->amount;
						}
						$coupon_id = $check->entity_id;
						$coupon_type = $check->amount_type;
						$coupon_amount = $check->amount;
						$coupon_discount = ($discount);
						$name = $check->name;
					}
				}
			}
			if ($status == 1) {
				$this->session->set_userdata(
					array(
						'coupon_id' => $coupon_id,
						'coupon_type' => $coupon_type,
						'coupon_amount' => $coupon_amount,
						'coupon_discount' => $coupon_discount,
						'coupon_name' => $name
					)
				);
			}
		} else {
			$this->session->set_userdata(array('coupon_id' => '', 'coupon_applied' => 'no'));
			$this->session->unset_userdata('coupon_type');
			$this->session->unset_userdata('coupon_amount');
			$this->session->unset_userdata('coupon_discount');
			$this->session->unset_userdata('coupon_name');
		}
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$data['cart_details'] = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		$data['currency_symbol'] = $this->common_model->getRestaurantCurrencySymbol($cart_restaurant);
		$data['order_mode'] = $this->input->post('order_mode');
		$this->load->view('ajax_order_summary', $data);
	}
	//add order
	public function addOrder()
	{
		$data['page_title'] = $this->lang->line('add_order') . ' | ' . $this->lang->line('site_title');
		$cart_details = get_cookie('cart_details');
		$cart_restaurant = get_cookie('cart_restaurant');
		$cart_item_details = $this->common_model->getCartItems($cart_details, $cart_restaurant);
		if ($this->session->userdata('is_user_login') == 1 && !empty($this->session->userdata('UserID')) && !empty($cart_restaurant)) {
			$restaurant_detail = $this->checkout_model->getRestaurantTax($cart_restaurant);
			$zone_id = $this->checkout_model->getResZone($cart_restaurant);
			$commission = $this->api_model->commission($cart_restaurant);
			$commissionValue = ($this->input->post('subtotal') * $commission->commission) / 100;
			$add_data = array(
				'user_id' => $this->session->userdata('UserID'),
				'restaurant_id' => $cart_restaurant,
				'address_id' => ($this->input->post('your_address')) ? $this->input->post('your_address') : '',
				'order_status' => 'placed',
				'order_date' => date('Y-m-d H:i:s'),
				'subtotal' => ($this->input->post('subtotal')) ? $this->input->post('subtotal') : 0,
				'total_rate' => ($this->session->userdata('total_price')) ? $this->session->userdata('total_price') : '',
				'status' => 0,
				'delivery_charge' => ($this->session->userdata('deliveryCharge')) ? $this->session->userdata('deliveryCharge') : '',
				'extra_comment' => ($this->input->post('extra_comment')) ? $this->input->post('extra_comment') : '',
				'payment_option' => ($this->input->post('payment_method')) ? $this->input->post('payment_method') : '',
				'transaction_id' => ($this->input->post('transaction_id')) ?
					($this->input->post('payment_method') == "bKash payment" ?  $this->input->post('transaction_id') . ',PaymentId:' . $this->input->post('paymentID') :
						$this->input->post('transaction_id')) : null,
				'zone_id'		=> $zone_id,
				'commission_rate' => ($commission->commission) ? $commission->commission : 0,
				'commission_value' => ($commissionValue) ? $commissionValue : 0,
			);
			if ($this->session->userdata('coupon_applied') == "yes") {
				$add_data['coupon_id'] = ($this->session->userdata('coupon_id')) ? $this->session->userdata('coupon_id') : '';
				$add_data['coupon_type'] = ($this->session->userdata('coupon_type')) ? $this->session->userdata('coupon_type') : '';
				$add_data['coupon_amount'] = ($this->session->userdata('coupon_amount')) ? $this->session->userdata('coupon_amount') : '';
				$add_data['coupon_discount'] = ($this->session->userdata('coupon_discount')) ? $this->session->userdata('coupon_discount') : '';
				$add_data['coupon_name'] = ($this->session->userdata('coupon_name')) ? $this->session->userdata('coupon_name') : '';
			}
			if ($this->input->post('choose_order') == 'delivery') {
				$add_data['order_delivery'] = 'Delivery';
			} else {
				$add_data['order_delivery'] = 'PickUp';
				$default_address = $this->common_model->getSingleRowMultipleWhere('user_address', array('user_entity_id' => $this->session->userdata('UserID'), 'is_main' => 1));
				if (!empty($default_address)) {
					$add_data['address_id'] = $default_address->entity_id;
				}
			}
			$order_id = $this->common_model->addData('order_master', $add_data);
			// get user details array
			$user_detail = array();
			if ($this->input->post('choose_order') == 'delivery') {
				if ($this->input->post('add_new_address') == "add_your_address" && !empty($this->input->post('your_address'))) {
					$address = $this->checkout_model->getAddress($this->input->post('your_address'));
					$user_detail = array(
						'first_name' => $this->session->userdata('userFirstname'),
						'last_name' => ($this->session->userdata('userLastname')) ? $this->session->userdata('userLastname') : '',
						'address' => ($address) ? $address->address : '',
						'landmark' => ($address) ? $address->landmark : '',
						'zipcode' => ($address) ? $address->zipcode : '',
						'city' => ($address) ? $address->city : '',
						'latitude' => ($address) ? $address->latitude : '',
						'longitude' => ($address) ? $address->longitude : '',
					);
				} else if ($this->input->post('add_new_address') == "add_new_address") {
					$add_address = array(
						'address' => $this->input->post('add_address'),
						'landmark' => $this->input->post('landmark'),
						'latitude' => $this->input->post('add_latitude'),
						'longitude' => $this->input->post('add_longitude'),
						'zipcode' => $this->input->post('zipcode'),
						'city' => $this->input->post('city'),
						'user_entity_id' => $this->session->userdata('UserID')
					);
					$this->common_model->addData('user_address', $add_address);
					$user_detail = array(
						'first_name' => $this->session->userdata('userFirstname'),
						'last_name' => ($this->session->userdata('userLastname')) ? $this->session->userdata('userLastname') : '',
						'address' => $this->input->post('add_address'),
						'landmark' => $this->input->post('landmark'),
						'zipcode' => $this->input->post('zipcode'),
						'city' => $this->input->post('city'),
						'latitude' => $this->input->post('add_latitude'),
						'longitude' => $this->input->post('add_longitude'),
					);
				}
			} else if (!empty($add_data['address_id'])) {
				$address = $this->checkout_model->getAddress($add_data['address_id']);
				$user_detail = array(
					'first_name' => $this->session->userdata('userFirstname'),
					'last_name' => ($this->session->userdata('userLastname')) ? $this->session->userdata('userLastname') : '',
					'address' => ($address) ? $address->address : '',
					'landmark' => ($address) ? $address->landmark : '',
					'zipcode' => ($address) ? $address->zipcode : '',
					'city' => ($address) ? $address->city : '',
					'latitude' => ($address) ? $address->latitude : '',
					'longitude' => ($address) ? $address->longitude : '',
				);
			}
			// get item details array
			$add_item = array();
			if (!empty($cart_details) && !empty($cart_item_details['cart_items'])) {
				foreach ($cart_item_details['cart_items'] as $key => $value) {
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
							"qty_no" => $value['quantity'],
							"rate" => ($value['price']) ? $value['price'] : '',
							"offer_price" => ($value['offer_price']) ? $value['offer_price'] : '',
							"order_id" => $order_id,
							"is_customize" => 1,
							"is_deal" => $value['is_deal'],
							"subTotal" => $value['subtotal'],
							"itemTotal" => $value['totalPrice'],
							'has_variation' => $value['has_variation'] == 1 ? 1 : 0,
							$value['has_variation'] ? 'variation_list' : 'addons_category_list' => $customization
						);
					} else {
						$add_item[] = array(
							"item_name" => $value['name'],
							"item_id" => $value['menu_id'],
							"qty_no" => $value['quantity'],
							"rate" => ($value['price']) ? $value['price'] : '',
							"offer_price" => ($value['offer_price']) ? $value['offer_price'] : '',
							"order_id" => $order_id,
							"is_customize" => 0,
							"is_deal" => $value['is_deal'],
							"subTotal" => $value['subtotal'],
							"itemTotal" => $value['totalPrice'],
						);
					}
				}
			}
		}
		$order_detail = array(
			'order_id' => $order_id,
			'user_detail' => serialize($user_detail),
			'item_detail' => serialize($add_item),
			'restaurant_detail' => serialize($restaurant_detail),
		);
		$this->common_model->addData('order_detail', $order_detail);

		if ($order_id) {
			$this->api_model->updateDriver($order_id);
		}

		$verificationCode = random_string('alnum', 25);
		$language_slug = ($this->session->userdata('language_slug')) ? $this->session->userdata('language_slug') : 'en';
		$email_template = $this->db->get_where('email_template', array('email_slug' => 'order-receive-alert', 'language_slug' => $language_slug, 'status' => 1))->first_row();

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
			$this->email->to(trim($restaurant_detail->email));
			$this->email->subject($email_template->subject);
			$this->email->message($email_template->message);
			$this->email->send();
		}
		if ($order_id) {
			$this->session->unset_userdata('checkDelivery');
			$this->session->unset_userdata('deliveryCharge');
			$this->session->set_userdata(array('coupon_id' => '', 'coupon_applied' => 'no'));
			$this->session->unset_userdata('coupon_type');
			$this->session->unset_userdata('coupon_amount');
			$this->session->unset_userdata('coupon_discount');
			$this->session->unset_userdata('coupon_name');
			delete_cookie('cart_details');
			delete_cookie('cart_restaurant');
			//echo "success";
			if ($this->input->post('choose_order') == 'delivery') {
				$order_id = "<a href='" . base_url() . 'order/track_order/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($order_id)) . "' class='btn'>Track Order</a>";
			} else {
				// $order_id = "<a href='".base_url().'myprofile'."' class='btn'>View Details</a>";
				$order_id = "<a href = '" . base_url() . 'myprofile' . "' class = 'btn' >" . $this->lang->line('view_details') . "</a>";
			}
			$arrdata = array('result' => 'success', 'order_id' => $order_id);
		} else {
			$arrdata = array('result' => 'fail', 'order_id' => '');
		}
		echo json_encode($arrdata);
	}
}
