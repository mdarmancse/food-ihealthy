<?php
class Order_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(ADMIN_URL . '/sub_dashboard_model');
	}
	// method for getting all
	public function getGridList($sortFieldName = '', $sortOrder = 'DESC', $displayStart = 0, $displayLength = 10, $order_status, $start_date = null, $end_date = null)
	{
		$zone_id = $this->input->post('zone_id', TRUE);
		if (is_array($zone_id)) {
			if (empty($zone_id[array_key_last($zone_id)])) {
				$zone_id = null;
			} else {
				$zone_id = explode(',', $zone_id[array_key_last($zone_id)]);
			}
		}

		if ($this->input->post('page_title') != '') {
			$this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%" . $this->input->post('page_title') . "%'");
		}
		if ($this->input->post('order') != '') {
			$this->db->like('o.entity_id', $this->input->post('order'));
		}
		if ($this->input->post('driver') != '') {
			$this->db->where("(driver.first_name LIKE '%" . $this->input->post('driver') . "%' OR driver.last_name LIKE '%" . $this->input->post('driver') . "%' OR (driver.mobile_number LIKE '%" . $this->input->post('driver') . "%' AND driver.user_type = 'Driver'))");
		}
		if ($this->input->post('Status') != '') {
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if ($this->input->post('restaurant') != '') {
			$name = $this->input->post('restaurant');
			$where = "(restaurant.name LIKE '%" . $this->input->post('restaurant') . "%' OR restaurant.phone_number LIKE '%" . $this->input->post('restaurant') . "%')";
			$this->db->or_where($where);
		}
		if ($this->input->post('order_total') != '') {
			$this->db->like('o.total_rate', $this->input->post('order_total'));
		}
		if ($this->input->post('order_status') != '') {
			$this->db->like('o.order_status', $this->input->post('order_status'));
		}
		if ($this->input->post('order_date') != '') {
			$this->db->like('o.created_date', $this->input->post('order_date'));
		}
		// if ($this->input->post('start_date') != '') {
		// 	$this->db->where('o.order_date >=', $this->input->post('start_date'));
		// }
		// if ($this->input->post('end_date') != '') {
		// 	$this->db->where('o.order_date <=', $this->input->post('end_date'));
		// }
		if ($this->input->post('start_date') == $this->input->post('end_date')) {
			$this->db->like('o.order_date', $this->input->post('start_date'));
		} else {
			if ($this->input->post('start_date') != '') {
				$this->db->where('o.order_date >=', $this->input->post('start_date'));
			}
			if ($this->input->post('end_date') != '') {
				$this->db->where('o.order_date <=', $this->input->post('end_date'));
			}
		}
		if ($this->input->post('order_delivery') != '') {
			$this->db->where('o.order_delivery', $this->input->post('order_delivery'));
		}
		if ($zone_id && $zone_id != '') {
			$this->db->where_in(
				'o.zone_id',
				$zone_id
			);
		}
		if ($this->input->post('is_unassigned') == 1) {
			$this->db->group_start();
			$this->db->where('order_driver_map.driver_map_id IN (SELECT MAX(od.driver_map_id) as dm FROM order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');
			$this->db->where('order_driver_map.driver_id', 0);
			$this->db->group_end();
		}

		if ($this->input->post('is_cancelled') == 1) {
			$this->db->where('order_status.order_status = "cancel"');
		}
		if ($this->input->post('is_delivered') == 1) {
			$this->db->where('order_status.order_status = "delivered"');
		}
		if ($this->input->post('is_accepted') == 1) {
			$this->db->where('order_status.order_status = "accepted_by_restaurant"');
		}
		if ($this->input->post('city_id') != '') {

			$this->db->where(
				'zone.city_id',
				$this->input->post('city_id')
			);
		}

		$this->db->select('o.entity_id');
		$this->db->join('users as u', 'o.user_id = u.entity_id', 'left');
		$this->db->join('restaurant', 'o.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('order_status', 'o.entity_id = order_status.order_id', 'left');
		//$this->db->join('order_driver_map','o.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
		$this->db->join('order_driver_map', 'o.entity_id = order_driver_map.order_id AND order_driver_map.cancel = 0 AND order_driver_map.no_response = 0', 'left');
		$this->db->join('order_detail', 'o.entity_id = order_detail.order_id', 'left');
		$this->db->join('users as driver', 'order_driver_map.driver_id = driver.entity_id', 'left');
		$this->db->join('zone', 'zone.entity_id = o.zone_id', 'left');

		if (!($this->lpermission->method('full_order_view', 'read')->access())) {

			if ($this->session->userdata('UserType') == 'Admin') {
				$this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
			}
			if ($this->session->userdata('UserType') == 'ZonalAdmin') {
				$this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
			}
			if ($this->input->post('start_date') == $this->input->post('end_date')) {
				$this->db->like('o.order_date', $this->input->post('start_date'));
			} else {
				if ($this->input->post('start_date') != '') {
					$this->db->where('o.order_date >=', $this->input->post('start_date'));
				}
				if ($this->input->post('end_date') != '') {
					$this->db->where('o.order_date <=', $this->input->post('end_date'));
				}
			}

			if ($this->session->userdata('UserType') == 'CentralAdmin') {
				$this->db->group_start();
				$this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
				$this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
				$this->db->group_end();
			}
		}
		if ($order_status) {
			$this->db->where('o.order_status', $order_status);
		}
		$this->db->group_by('o.entity_id');
		$result['total'] = $this->db->count_all_results('order_master as o');
		// echo "<pre>";
		// print_r($this->db->get('order_master as o')->result());
		// exit();
		//Order View start

		if ($sortFieldName != '')
			$this->db->order_by($sortFieldName, $sortOrder);
		if ($this->input->post('page_title') != '') {
			$this->db->where("CONCAT(u.first_name,' ',u.last_name) like '%" . $this->input->post('page_title') . "%'");
		}
		if ($this->input->post('driver') != '') {
			$this->db->where("(driver.first_name LIKE '%" . $this->input->post('driver') . "%' OR driver.last_name LIKE '%" . $this->input->post('driver') . "%' OR (driver.mobile_number LIKE '%" . $this->input->post('driver') . "%' AND driver.user_type = 'Driver'))");
		}
		if ($this->input->post('Status') != '') {
			$this->db->like('o.status', $this->input->post('Status'));
		}
		if ($this->input->post('restaurant') != '') {
			$name = $this->input->post('restaurant');
			$where = "(restaurant.name LIKE '%" . $this->input->post('restaurant') . "%' OR restaurant.phone_number LIKE '%" . $this->input->post('restaurant') . "%')";
			$this->db->where($where);
		}
		if ($this->input->post('order_total') != '') {
			$this->db->like('o.total_rate', $this->input->post('order_total'));
		}

		if ($this->input->post('pay_type') != '') {
			$this->db->like('o.payment_option', $this->input->post('pay_type'), 'both');
		}
		if ($this->input->post('order_status') != '') {
			$this->db->like('o.order_status', $this->input->post('order_status'));
		}
		if ($this->input->post('order') != '') {
			$this->db->like('o.entity_id', $this->input->post('order'));
		}
		if ($this->input->post('order_date') != '') {
			$this->db->like('o.created_date', $this->input->post('order_date'));
		}
		// if ($this->input->post('start_date') != '') {
		// 	$this->db->where('o.order_date >=', $this->input->post('start_date'));
		// }
		// if ($this->input->post('end_date') != '') {
		// 	$this->db->where('o.order_date <=', $this->input->post('end_date'));
		// }
		if ($this->input->post('start_date') == $this->input->post('end_date')) {
			$this->db->like('o.created_date', $this->input->post('start_date'));
		} else {
			if ($this->input->post('start_date') != '') {
				$this->db->where('o.created_date >=', $this->input->post('start_date'));
			}
			if ($this->input->post('end_date') != '') {
				$this->db->where('o.created_date <=', $this->input->post('end_date'));
			}
		}
		if ($zone_id && $zone_id != '') {
			$this->db->where_in('o.zone_id', $zone_id);
		}
		if ($this->input->post('city_id') != '') {

			$this->db->where('zone.city_id', $this->input->post('city_id'));
		}

		if ($this->input->post('order_delivery') != '') {
			$this->db->where('o.order_delivery', $this->input->post('order_delivery'));
		}


		if ($displayLength > 1)
			$this->db->limit($displayLength, $displayStart);
		$this->db->select('
		order_driver_map.is_accept as accept,
		zone.city_id,
		zone.area_name,
		k.landmark as user_address,
		order_driver_map.cancel as cancel,
		o.order_delivery,
		o.preorder_mode,
		o.not_delivered,
		o.preorder_date,
		o.total_rate as rate,
		o.subtotal as sub_total,
		o.commission_rate as commission,
		o.commission_value,
		o.vat,
		o.verify_order,
		o.sd,
		o.order_status as ostatus,
		o.status,
		o.restaurant_id,
		o.created_date,
		o.entity_id as entity_id,
		o.user_id,
		u.first_name as fname,
		u.last_name as lname,
		u.mobile_number as u_mobile_number,
		u.entity_id as user_id,
		order_status.order_status as orderStatus,
		driver.first_name,
		driver.last_name,
		driver.mobile_number as driver_mobile_number,
		driver.entity_id as driver_id,
		order_detail.restaurant_detail,
		restaurant.name,
		restaurant.currency_id,
		o.address_id,
		o.delivery_charge,
		o.payment_option');
		$this->db->join('users as u', 'o.user_id = u.entity_id', 'left');
		$this->db->join('order_detail', 'o.entity_id = order_detail.order_id', 'left');
		$this->db->join('order_status', 'o.entity_id = order_status.order_id', 'left');
		$this->db->join('restaurant', 'o.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('order_driver_map', 'o.entity_id = order_driver_map.order_id AND order_driver_map.no_response = 0 AND order_driver_map.cancel = 0', 'left');
		$this->db->join('users as driver', 'order_driver_map.driver_id = driver.entity_id', 'left');
		//Extra Added on 07-03-2021
		$this->db->join('user_address k', 'k.entity_id = o.address_id', 'left');
		$this->db->join('zone', 'zone.entity_id = o.zone_id', 'left');
		$this->db->where('order_driver_map.driver_map_id IN (SELECT MAX(od.driver_map_id) as dm FROM order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');

		//$this->db->join('city y', 'y.id = z.city_id', 'left');

		if ($order_status) {
			$this->db->where('order_status.order_status', $order_status);
		}
		if ($this->input->post('is_unassigned') == 1) {

			$this->db->group_start();
			$this->db->where('order_driver_map.driver_map_id IN (SELECT MAX(od.driver_map_id) as dm FROM order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');
			$this->db->where('order_driver_map.driver_id', 0);
			$this->db->group_end();
		}
		if ($this->input->post('is_cancelled') == 1) {
			$this->db->where('order_status.order_status = "cancel"');
		}
		if ($this->input->post('is_delivered') == 1) {
			$this->db->where('order_status.order_status = "delivered"');
		}
		if ($this->input->post('is_accepted') == 1) {
			$this->db->where('order_status.order_status = "accepted_by_restaurant"');
		}

		if (!($this->lpermission->method('full_order_view', 'read')->access())) {
			if ($this->session->userdata('UserType') == 'CentralAdmin') {
				$this->db->group_start();
				$this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
				$this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
				$this->db->group_end();
			}
			if ($this->session->userdata('UserType') == 'Admin') {
				$this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
			}
			if ($this->session->userdata('UserType') == 'ZonalAdmin') {
				$this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
			}
		}
		$this->db->group_by('o.entity_id');
		//$result['total'] = $this->db->count_all_results('order_master');
		$result['data'] = $this->db->get('order_master as o')->result();
		foreach ($result['data'] as $k => $v) {
			$this->db->select('users.first_name as rider_name');
			$this->db->from('order_driver_map');
			$this->db->where('order_driver_map.order_id', $v->entity_id);
			$this->db->join('users', 'order_driver_map.driver_id = users.entity_id');
			$this->db->order_by('order_driver_map.driver_map_id', "DESC");
			$this->db->limit(1);
			$res = $this->db->get()->first_row();
			$result['data'][$k]->first_name = $res->rider_name;
		}
		return $result;
	}
	// method for adding
	public function addData($tblName, $Data)
	{
		$this->db->insert($tblName, $Data);
		return $this->db->insert_id();
	}
	// method for adding
	public function addBatch($tblName, $Data)
	{
		$this->db->insert_batch($tblName, $Data);
		return $this->db->insert_id();
	}
	public function CheckDriver($order_id, $driver_id)
	{
		$this->db->select('order_driver_map.cancel');
		$this->db->where('order_driver_map.driver', $driver_id);
		$this->db->where('order_driver_map.order_id', $order_id);
		return $this->db->get('order_driver_map')->result();
	}
	// get the drivers to asiign to the orders
	public function getDrivers()
	{
		$this->db->select('users.entity_id,users.first_name,users.last_name');
		$this->db->where('user_type', 'Driver');
		$this->db->where('suspend', 1);
		$this->db->where('onoff', 1);
		$this->db->where('engage', 0);
		//$this->db->where('device_id !=','');
		if ($this->session->userdata('UserType') == 'Admin') {
			$this->db->where('created_by', $this->session->userdata('UserID'));
		}
		return $this->db->get('users')->result();
	}
	// assign driver
	public function getOrderDetails($order_id)
	{
		$this->db->select("(6371 * acos ( cos ( radians(user_address.latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians(user_address.longitude) ) + sin ( radians(user_address.latitude) ) * sin( radians( address.latitude )))) as distance");
		$this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('restaurant_address as address', 'restaurant.entity_id = address.resto_entity_id', 'left');
		$this->db->join('user_address', 'order_master.address_id = user_address.entity_id', 'left');
		$this->db->where('order_master.entity_id', $order_id);
		return $distance = $this->db->get('order_master')->result();
	}
	// method to get details by id
	public function getEditDetail($entity_id)
	{
		$this->db->select('u.mobile_number,order.vat,order.sd,order.*,res.name, address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode');
		$this->db->join('restaurant as res', 'order.restaurant_id = res.entity_id', 'left');
		$this->db->join('restaurant_address as address', 'res.entity_id = address.resto_entity_id', 'left');
		$this->db->join('users as u', 'order.user_id = u.entity_id', 'left');
		$this->db->join('user_address as uaddress', 'u.entity_id = uaddress.user_entity_id', 'left');
		return  $this->db->get_where('order_master as order', array('order.entity_id' => $entity_id))->first_row();
	}
	// update data common function
	public function updateData($Data, $tblName, $fieldName, $ID)
	{
		$this->db->where($fieldName, $ID);
		$this->db->update($tblName, $Data);
		return $this->db->affected_rows();
	}
	public function add_update_cart_history($tblName, $Data)
	{
		$this->db->insert($tblName, $Data);
		return $this->db->insert_id();
	}
	public function update_cart($item_data, $tblName, $order_id)
	{
		$updated_item = array('item_detail' => $item_data);
		$this->db->where('order_id', $order_id);
		$this->db->update($tblName, $updated_item);
		return $this->db->affected_rows();
	}
	public function update_cart_data($item_data, $tblName, $order_id)
	{
		$this->db->where('entity_id', $order_id);
		$this->db->update($tblName, $item_data);
		return $this->db->affected_rows();
	}
	//Update Order Drievr Map
	public function update_order_driver($order_id, $tablename, $data)
	{
		// $this->db->where('driver_map_id', $order_id);
		// $this->db->update($tablename, $data);
		// return $this->db->affected_rows();
		$this->db->where('driver_map_id', $order_id);
		$this->db->delete($tablename);
	}
	public function get_order_map($order_id)
	{
		$this->db->select('*');
		$this->db->where('order_id', $order_id);
		return $this->db->get('order_driver_map')->result();
	}
	// updating status and send request to driver
	public function UpdatedStatus($tblname, $entity_id, $restaurant_id, $order_id, $order_status)
	{
		// echo "<pre>";
		// print_r($order_status);
		// exit();
		if ($order_status == 'preorder') {
			$this->db->set('order_status', 'placed')->where('entity_id', $order_id)->update('order_master');
		}
		// if ($order_status == 'placed') {
		// 	$this->db->set('order_status', 'accepted_by_restaurant')->where('entity_id', $order_id)->update('order_master');
		// }
		//
		$this->db->set('status', 1)->where('entity_id', $order_id)->update('order_master');
		$this->db->set('accept_order_time', date("Y-m-d H:i:s"))->where('entity_id', $order_id)->update('order_master');
		//send notification to user
		$this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug');
		$this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
		$this->db->where('order_master.entity_id', $order_id);
		$device = $this->db->get('order_master')->first_row();

		if ($device->device_id) {
			//get langauge
			$languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
			$this->lang->load('messages_lang', $languages->language_directory);
			#prep the bundle
			$fields = array();
			$message = $this->lang->line('push_order_accept');
			$fields['to'] = $device->device_id; // only one user to send push notification
			$fields['notification'] = array('body'  => $message, 'sound' => 'default');
			$fields['data'] = array('screenType' => 'order');

			$headers = array(
				'Authorization: key=' . Driver_FCM_KEY,
				'Content-Type: application/json'
			);
			#Send Reponse To FireBase Server
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			curl_close($ch);
		}
		//send notification to driver
		// if($device->order_delivery == 'Delivery'){
		//     $this->db->select('users.entity_id');
		//     $this->db->where('user_type','Driver');
		//     $driver = $this->db->get('users')->result_array();

		//     $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
		//     $this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
		//     $this->db->where('users.status',1);
		//     if(!empty($driver)){
		//     	$this->db->where_in('driver_id',array_column($driver, 'entity_id'));
		//     }
		//     $this->db->where('driver_traking_map.created_date = (SELECT
		//      driver_traking_map.created_date
		//  FROM
		//      driver_traking_map
		//  WHERE
		//      driver_traking_map.driver_id = users.entity_id
		//  ORDER BY
		//  	driver_traking_map.created_date desc
		//  LIMIT 1)');
		//     $detail = $this->db->get('driver_traking_map')->result();

		//     $flag = false;
		//     if(!empty($detail)){
		//         foreach ($detail as $key => $value) {
		//             $longitude = $value->longitude;
		//             $latitude = $value->latitude;
		//             $this->db->select("(6371 * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
		//             $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
		//             $this->db->where('restaurant.entity_id',$restaurant_id);
		//             $this->db->having('distance <',NEAR_KM);
		//             $result = $this->db->get('restaurant')->result();
		//             if(!empty($result)){
		//                 if($value->device_id){
		//                 	//get langauge
		//                 	$languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
		//     					$this->lang->load('messages_lang', $languages->language_directory);
		//                     $flag = true;
		//                     $array = array(
		//                         'order_id'=>$order_id,
		//                         'driver_id'=>$value->driver_id,
		//                         'date'=>date('Y-m-d H:i:s'),
		//                         'distance'=>$result[0]->distance
		//                     );
		//                     $id = $this->addData('order_driver_map',$array);
		//                     #prep the bundle
		//                     $fields = array();
		//                     $message = $this->lang->line('push_new_order');
		//                     $fields['to'] = $value->device_id; // only one user to send push notification
		//                     $fields['notification'] = array ('body'  => $message,'sound'=>'default');
		//                     $fields['data'] = array ('screenType'=>'order');

		//                     $headers = array (
		//                         'Authorization: key=' . FCM_KEY,
		//                         'Content-Type: application/json'
		//                     );
		//                     #Send Reponse To FireBase Server
		//                     $ch = curl_init();
		//                     curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		//                     curl_setopt( $ch,CURLOPT_POST, true );
		//                     curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		//                     curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		//                     curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		//                     curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		//                     $result = curl_exec($ch);
		//                     curl_close($ch);
		//                 }
		//             }
		//         }
		//     }
		// }
	}
	// delete
	public function ajaxDelete($tblname, $entity_id)
	{
		$this->db->delete($tblname, array('entity_id' => $entity_id));
	}
	//get users detail
	public function getUsersDetail($user_id)
	{
		$this->db->select('users.first_name');
		$this->db->where('entity_id', $user_id);
		return $this->db->get('users')->result();
	}
	//get list
	public function getListData($tblname)
	{
		$a = new DateTime();
		$currentTime = $a->format('Y-m-d H:i:s');
		if ($tblname == 'users') {
			$this->db->select('first_name,last_name,entity_id');
			$this->db->where('status', 1);
			$this->db->where('user_type !=', 'MasterAdmin');
			if ($this->session->userdata('UserType') == 'Admin') {
				$this->db->where('created_by', $this->session->userdata('UserID'));
			}
			return $this->db->get($tblname)->result();
		} else if ($tblname == 'restaurant') {
			$this->db->select('name,entity_id,amount_type,amount');
			$this->db->where('status', 1);
			if ($this->session->userdata('UserType') == 'Admin') {
				$this->db->where('created_by', $this->session->userdata('UserID'));
			}
			return $this->db->get($tblname)->result();
		} else {
			$this->db->select('name,entity_id,amount_type,amount,coupon_type,gradual_all_items,max_amount,discount_amount,maximum_use,usablity');
			$this->db->where('status', 1);
			$this->db->where('end_date >', $currentTime);
			$this->db->where('start_date <', $currentTime);
			return $this->db->get($tblname)->result();
		}
	}
	//get items
	//get items
	public function getItem($entity_id)
	{
		$this->db->select('entity_id,name,price,check_add_ons');
		$this->db->where('restaurant_id', $entity_id);
		$this->db->where('status', 1);
		$result = $this->db->get('restaurant_menu_item')->result();

		return $result;
	}
	//get address
	public function getAddress($entity_id)
	{
		$this->db->where('user_entity_id', $entity_id);
		return $this->db->get('user_address')->result();
	}
	//get invoice data
	public function getInvoiceMenuItem($entity_id)
	{
		$this->db->select('order_detail.*, order_master.total_rate');
		$this->db->join('order_master', 'order_master.entity_id = order_detail.order_id', 'left');
		$this->db->where('order_id', $entity_id);
		return $this->db->get('order_detail')->first_row();
	}
	//get user data
	public function getUserDate($entity_id)
	{
		$this->db->select('device_id,language_slug');

		$this->db->where('entity_id', $entity_id);
		return $this->db->get('users')->first_row();
	}
	//delete multiple order
	public function deleteMultiOrder($order_id)
	{
		$this->db->where_in('entity_id', $order_id);
		$this->db->delete('order_master');
		return $this->db->affected_rows();
	}
	//get item name
	public function getItemName($item_id)
	{
		$this->db->where('entity_id', $item_id);
		return $this->db->get('restaurant_menu_item')->first_row();
	}
	//get order status history
	public function statusHistory($order_id, $latest = null)
	{
		if ($latest === 'latest') {
			$this->db->select('order_status.order_id,order_status.order_status,order_status.status_created_by,users.email,order_status.time')
				->order_by('status_id', 'desc')
				->limit(1);
		}
		if ($latest === 'latest_two') {
			$this->db->select('order_status.order_id,order_status.order_status,order_status.status_created_by,users.email,order_status.time')
				->order_by('status_id', 'desc')
				->limit(2);
		}
		$this->db->select('order_status.order_id,order_status.order_status,order_status.status_created_by,users.email,order_status.time');
		$this->db->join('users', 'order_status.updated_by = users.entity_id', 'left');
		$this->db->where('order_id', $order_id);
		return $this->db->get('order_status')->result();
	}
	//get rest detail
	public function getRestaurantDetail($entity_id)
	{
		$this->db->select('restaurant.name,restaurant.image,restaurant.commission,restaurant.phone_number,restaurant.email,restaurant.amount_type,restaurant.amount,restaurant_address.address,restaurant_address.landmark,restaurant_address.zipcode,restaurant_address.city,currencies.currency_symbol');
		$this->db->join('restaurant_address', 'restaurant.entity_id = restaurant_address.resto_entity_id', 'left');
		$this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
		$this->db->where('restaurant.entity_id', $entity_id);
		return $this->db->get('restaurant')->first_row();
	}
	//get list of restaurant
	public function getRestaurantList()
	{
		if ($this->session->userdata('UserType') == 'Admin') {
			$this->db->where('created_by', $this->session->userdata('UserID'));
		}
		return $this->db->get('restaurant')->result();
	}
	//generate report data
	public function generate_report($restaurant_id, $order_type, $order_date)
	{
		$this->db->select('order_master.*,restaurant.name,users.first_name,users.last_name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
		$this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
		$this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
		$this->db->where('restaurant_id', $restaurant_id);
		if ($order_type) {
			$this->db->where('order_delivery', $order_type);
		}
		if ($order_date != '') {
			$this->db->like('order_master.created_date', date('Y-m-d', strtotime($order_date)));
		}
		/*if($order_date){
			$monthsplit = explode("-",$order_date);
			$this->db->where('MONTH(order_master.created_date)',$monthsplit[0]);
			$this->db->where('YEAR(order_master.created_date)',$monthsplit[1]);
		}*/
		return $this->db->get('order_master')->result();
	}

	public function getDevice($user_id)
	{
		$this->db->select('users.entity_id,users.device_id,users.language_slug');
		$this->db->where('users.entity_id', $user_id);
		return $this->db->get('users')->first_row();
	}

	//get order details
	public function orderDetails($entity_id)
	{
		$this->db->where('order_master.entity_id', $entity_id);
		$this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
		return $this->db->get('order_master')->result();
	}


	// get latest order of logged in user
	public function getLatestOrder($order_id)
	{
		$this->db->select('order_master.entity_id as master_order_id,order_master.*,order_detail.*,order_driver_map.driver_id,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant_address.address,restaurant.timings,restaurant.image as rest_image,restaurant.name,currencies.currency_symbol,currencies.currency_code,currencies.currency_id');
		$this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
		$this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1', 'left');
		$this->db->join('users', 'order_driver_map.driver_id = users.entity_id AND order_driver_map.is_accept = 1', 'left');
		$this->db->join('driver_traking_map', 'users.entity_id = driver_traking_map.driver_id AND driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY created_date DESC LIMIT 1)', 'left');

		$this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
		$this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
		$this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel")');
		$this->db->where('order_master.entity_id', $order_id);

		$result = $this->db->get('order_master')->first_row();
		if (!empty($result)) {
			$result->placed = $result->created_date;
			$result->preparing = '';
			$result->onGoing = '';
			$result->delivered = '';
			// get order status
			$this->db->where('order_status.order_id', $result->master_order_id);
			$Ostatus = $this->db->get('order_status')->result_array();
			if (!empty($Ostatus)) {
				foreach ($Ostatus as $key => $ovalue) {
					if ($ovalue['order_status'] == 'accepted_by_restaurant') {
						$result->accepted_by_restaurant = $ovalue['time'];
					}
					if ($ovalue['order_status'] == 'preparing') {
						$result->preparing = $ovalue['time'];
					}
					if ($ovalue['order_status'] == 'onGoing') {
						$result->onGoing = $ovalue['time'];
					}
					if ($ovalue['order_status'] == 'delivered') {
						$result->delivered = $ovalue['time'];
					}
				}
			}
			$user_detail = unserialize($result->user_detail);
			if (!empty($user_detail)) {
				$result->user_first_name = $user_detail['first_name'];
				$result->user_address = $user_detail['address'];
				$result->user_latitude = $user_detail['latitude'];
				$result->user_longitude = $user_detail['longitude'];
				$result->image = ($result->image) ? image_url . $result->image : '';
			}
		}
		return $result;
	}

	// get latest order of logged in user
	public function getZoneDrivers($order_id)
	{
		$currentDate = strtotime(date('Y-m-d H:i:s'));
		$futureDate = $currentDate - (60 * 10);
		$formatDate = date("Y-m-d H:i:s", $futureDate);
		$this->db->select('order_master.entity_id as master_order_id,order_detail.user_detail,
		users.first_name as driver_fname,users.entity_id as user_id,users.last_name as driver_lname,driver_traking_map.latitude,driver_traking_map.longitude,users.mobile_number,users.image,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,
		restaurant_address.address as restaurant_address,restaurant.name as restaurant_name');
		$this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
		$this->db->join('users', 'users.zone_id = order_master.zone_id', 'left');
		$this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
		$this->db->join('driver_traking_map', 'driver_traking_map.driver_id = users.entity_id', 'left');
		// $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = (SELECT driver_traking_map.traking_id FROM driver_traking_map WHERE driver_traking_map.driver_id = users.entity_id ORDER BY driver_traking_map.traking_id LIMIT 1)', 'left');
		$this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('user_address', 'user_address.user_entity_id = order_master.user_id', 'left');
		$this->db->where('driver_traking_map.traking_id in (SELECT MAX(dm.traking_id) from driver_traking_map as dm GROUP BY dm.driver_id ORDER BY dm.traking_id DESC)');
		$this->db->where('order_master.entity_id', $order_id);
		$this->db->where('users.user_type = "Driver"');
		$this->db->where('driver_traking_map.created_date >=', $formatDate);
		// $this->db->where('driver_traking_map.traking_id = (select max(traking_id) from driver_traking_map where driver_traking_map.driver_id = users.entity_id)', NULL, FALSE);
		$this->db->group_by('users.entity_id');
		$result = $this->db->get('order_master')->result();
		$user_detail = unserialize($result[0]->user_detail);
		if (!empty($user_detail)) {
			$user_lat = $user_detail['latitude'];
			$user_long = $user_detail['longitude'];
		}
		// echo "<pre>";
		// print_r($user_detail);
		// exit();

		$rider_max_radius = $this->db->select('OptionValue')
			->from('system_option')
			->where('OptionSlug', 'rider_radius')
			->get()
			->first_row()
			->OptionValue;

		foreach ($result as $k => $v) {

			$res_lat = $v->resLat;
			$res_long = $v->resLong;
			$driver_lat = $v->latitude;
			$driver_long = $v->longitude;

			// https://stackoverflow.com/questions/27928/calculate-distance-between-two-latitude-longitude-points-haversine-formula

			$R = 6371; // Radius of the earth in km
			$dLat = deg2rad($res_lat - $driver_lat);  // deg2rad below
			$dLon = deg2rad($res_long - $driver_long);
			$a =
				sin($dLat / 2) * sin($dLat / 2) +
				cos(deg2rad($driver_lat)) * cos(deg2rad($res_lat)) *
				sin($dLon / 2) * sin($dLon / 2);
			$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
			$d = $R * $c; // Distance in km

			if ($d > $rider_max_radius) {
				unset($result->$k);
			}
		}

		$i = 0;
		$distance = array();
		foreach ($result as $res) {
			$user_id = $res->user_id;
			$flag = 1;
			// $flag = $this->sub_dashboard_model->online_riders(null, null, null, $user_id, null);
			$active_orders = $this->sub_dashboard_model->order_count($user_id);
			$res_lat = $res->resLat;
			$res_long = $res->resLong;
			$driver_lat = $res->latitude;
			$driver_long = $res->longitude;
			$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $res_lat . "," . $res_long . "&destination=" . $driver_lat . "," . $driver_long . "&key=AIzaSyANO7UcO8EO1_9y3TaSZS5E_E9cFOpiEl8&mode=driving";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			$result_map = curl_exec($ch);
			curl_close($ch);
			$datas = json_decode($result_map, true);
			$apicall = $datas['routes'][0]['legs'][0]['distance']['value'];
			$distance = $apicall;
			if ($distance  >= 1000) {
				$distance = ($distance / 1000) . " Km";
			} else {
				$distance = $distance . " M";
			}
			$result[$i]->distance = $distance;
			$result[$i]->flag = $flag;
			$result[$i]->order_count = $active_orders;
			$result[$i]->user_latitude = $user_lat;
			$result[$i]->user_longitude = $user_long;
			$i++;
		}
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $res_lat . "," . $res_long . "&destination=" . $user_lat . "," . $user_long . "&key=AIzaSyANO7UcO8EO1_9y3TaSZS5E_E9cFOpiEl8&mode=driving";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result_map = curl_exec($ch);
		curl_close($ch);
		$datas = json_decode($result_map, true);
		$apicall = $datas['routes'][0]['legs'][0]['distance']['value'];
		$user_distance = $apicall;
		if ($user_distance  > 1000) {
			$user_distance = ($user_distance / 1000) . " Km";
		} else {
			$user_distance = $user_distance . " M";
		}
		if ($result) {
			$result[0]->user_distance = $user_distance;
		}
		if (!empty($result)) {
			$user_detail = unserialize($result->user_detail);
			if (!empty($user_detail)) {
				$result->user_first_name = $user_detail['first_name'];
				$result->user_address = $user_detail['address'];
				$result->user_latitude = $user_detail['latitude'];
				$result->user_longitude = $user_detail['longitude'];
				$result->image = ($result->image) ? image_url . $result->image : '';
			}
		}
		return $result;
	}


	public function getResName($entity_id)
	{
		return  $this->db->select('entity_id,name')->get_where('restaurant', array('entity_id' => $entity_id))->first_row();
	}

	public function deliveryCharge($id)
	{
		$this->db->select('price_charge');
		$this->db->where('restaurant_id', $id);
		return $this->db->get('delivery_charge')->first_row();
	}

	public function getVatSd($id)
	{
		$this->db->select('vat,sd');
		$this->db->where('entity_id', $id);
		return $this->db->get('restaurant_menu_item')->first_row();
	}
	//use of a single coupon
	public function couponUseByUser($coupon_id, $user_id)
	{
		$this->db->select('coupon_id, user_id');
		$this->db->where('coupon_id', $coupon_id);
		$this->db->where('user_id', $user_id);

		return $this->db->get("order_master")->num_rows();
	}
	//get coupon list
	public function getcouponList($subtotal, $restaurant_id, $order_delivery, $user_id)
	{

		$a = new DateTime();
		$currentTime = $a->format('Y-m-d H:i:s');

		$this->db->select('coupon.name,coupon.entity_id as coupon_id,coupon.amount_type,coupon.amount,coupon.description,coupon.coupon_type,currencies.currency_symbol,currencies.currency_code,coupon.usablity,coupon.maximum_use,coupon.image');
		$this->db->join('coupon_restaurant_map', 'coupon.entity_id = coupon_restaurant_map.coupon_id', 'left');
		$this->db->join('restaurant', 'coupon_restaurant_map.restaurant_id = restaurant.entity_id', 'left');
		$this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
		$this->db->where('max_amount <=', $subtotal);
		$this->db->where('coupon_restaurant_map.restaurant_id', $restaurant_id);
		$this->db->where('end_date >', $currentTime);
		$this->db->where('start_date <', $currentTime);

		$this->db->where('coupon.status', 1);
		//$this->db->where('(coupon_type = "discount_on_cart" OR coupon_type = "user_registration")');
		if ($order_delivery == 'Delivery') {
			$this->db->where_or('coupon_type', "free_delivery");
		}
		// return $this->db->get('coupon')->result();

		$coupons = $this->db->get('coupon')->result();

		foreach ($coupons as $key => $value) {
			$value->image = ($value->image) ? image_url . $value->image : '';
			if ($value->maximum_use != 0) {
				$maximum_use = $value->maximum_use;
				$coupon_use = $this->couponUseByUser($value->coupon_id, $user_id);

				if ($maximum_use <= $coupon_use) {
					unset($coupons[$key]);
				}
			}

			if ($value->coupon_type == 'selected_user') {
				$check = $this->checkCouponUser($value->coupon_id, $user_id);
				if ($check == null) {
					unset($coupons[$key]);
				}
			}

			if ($value->usablity == 'onetime') {
				$check = $this->checkOneTimeUser($value->coupon_id, $user_id);
				if ($check > 0) {
					unset($coupons[$key]);
				}
			}

			if ($value->coupon_type == 'user_registration') {

				$rows = $this->db->get_where('order_master', array('user_id' => $user_id))->num_rows();

				if ($rows > 0) {
					unset($coupons[$key]);
				}
			}

			if ($value->coupon_type == 'discount_on_items' || $value->coupon_type == 'gradual') {
				unset($coupons[$key]);
			}
		}

		return array_values($coupons);
	}
	public function getMenuItem($menu_id)
	{
		$this->db->select(
			'menu.is_deal,
            menu.entity_id as menu_id,
            menu.status,
            menu.name,
            menu.price,
            menu.menu_detail,
            menu.image,
            menu.is_veg,
            menu.recipe_detail,
            availability,
            c.name as category,
            c.entity_id as category_id,
            c.sort_value as sort_value,
            add_ons_master.add_ons_name,
            add_ons_master.add_ons_price,
            add_ons_category.name as addons_category,
            menu.check_add_ons,
            add_ons_category.entity_id as addons_category_id,
            add_ons_master.add_ons_id,
            add_ons_master.is_multiple,
            add_ons_master.variation_id,
            add_ons_master.has_variation,
            add_ons_master.max_choice,
            variations.variation_name,
            variations.variation_add_on,
            variations.variation_price'

		);
		$this->db->join('category as c', 'menu.category_id = c.entity_id', 'left');
		$this->db->join('add_ons_master', 'menu.entity_id = add_ons_master.menu_id AND menu.check_add_ons = 1', 'left');
		//$this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id', 'left');
		$this->db->join('add_ons_category', 'add_ons_master.category_id = add_ons_category.entity_id and add_ons_category.status = 1', 'left');
		$this->db->join('variations', 'add_ons_master.variation_id = variations.entity_id', 'left');
		// $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
		$this->db->where('menu.entity_id', $menu_id);
		$this->db->where('menu.status', 1);
		$this->db->where('c.status', 1);
		// if ($popular == 1) {
		// 	$this->db->where('popular_item', 1);
		// 	$this->db->where('menu.image !=', '');
		// } else {
		// 	if ($price == 1) {
		// 		$this->db->order_by('menu.price', 'desc');
		// 	} else {
		// 		$this->db->order_by('menu.price', 'asc');
		// 	}
		// 	if ($food != '') {
		// 		$this->db->where('menu.is_veg', $food);
		// 	}
		// }
		// $this->db->where('menu.language_slug', $language_slug);
		$result = $this->db->get('restaurant_menu_item as menu')->result();

		// $result_copy = $result;

		// foreach ($result_copy as $key => $value) {
		//     $menu_timing = $value->availability;

		//     if ($menu_timing && ($menu_timing != '' || $menu_timing != null)) {
		//         $menu_timing = unserialize($menu_timing);

		//         $break_count = 0;
		//         foreach ($menu_timing as $t_key => $t_value) {


		//             $menuOpenTime = new DateTime($t_value['open']);
		//             $menuCloseTime = new DateTime($t_value['close']);
		//             if ((($menuOpenTime->diff(new DateTime)->format('%R') == '+') &&
		//                 ($menuCloseTime->diff(new DateTime)->format('%R') == '-'))) {
		//                 $break_count++;
		//             }
		//         }

		//         if ($break_count == 0) {
		//             unset($result_copy[$key]);
		//         }
		//     }
		// }

		// $result = array_values($result_copy);

		$menu = array();
		foreach ($result as $key => $value) {

			$offer_price = '';
			// if (in_array($value->menu_id, $ItemDiscount)) {
			//     if (!empty($couponAmount)) {
			//         if ($couponAmount[0]['max_amount'] < $value->price) {
			//             if ($couponAmount[0]['amount_type'] == 'Percentage') {
			//                 $offer_price = $value->price - round(($value->price * $couponAmount[0]['amount']) / 100);
			//             } else if ($couponAmount[0]['amount_type'] == 'Amount') {
			//                 $offer_price = $value->price - $couponAmount[0]['amount'];
			//             }
			//         }
			//     }
			// }
			// $offer_price = ($offer_price) ? $offer_price : '';
			if (!isset($menu[$value->category_id])) {
				$menu[$value->category_id] = array();
				$menu[$value->category_id]['category_id'] = $value->category_id;
				$menu[$value->category_id]['category_name'] = $value->category;
				$menu[$value->category_id]['sort_value'] = $value->sort_value;
			}
			$image = ($value->image && $value->image != '') ? image_url . $value->image : image_url . 'menu/400a67c7f2b91270860c732212131af9.jpg';
			$total = 0;
			if ($value->check_add_ons == 1) {
				if (!isset($menu[$value->category_id]['items'][$value->menu_id])) {
					$menu[$value->category_id]['items'][$value->menu_id] = array();
					$menu[$value->category_id]['items'][$value->menu_id] = array(
						'menu_id'       => $value->menu_id,
						'name'          => $value->name,
						'price'         => $value->price,
						'offer_price'   => $offer_price,
						'menu_detail'   => $value->menu_detail,
						'image'         => $image,
						'recipe_detail' => $value->recipe_detail,
						// 'availability'  => $value->availability,
						'is_veg'        => $value->is_veg,
						'is_customize'  => $value->check_add_ons,
						'is_deal'       => $value->is_deal,
						'has_variation' => $value->has_variation,
						'status'        => $value->status
					);
				}
				if ($value->has_variation == 1) {


					if (!isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id])) {
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id] = array();
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_id'] = $value->variation_id;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_name'] = $value->variation_name;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['variation_price'] = $value->variation_price;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['hasVariationAddon'] = $value->variation_add_on;
					}

					if ($value->variation_add_on == 1 && !isset($menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id])) {
						$i = 0;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id] = array();
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
						$menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
					}
					($value->addons_category) ? $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price) : '';
					($value->is_multiple == 1) ? $menu[$value->category_id]['items'][$value->menu_id]['variation_list'][$value->variation_id]['addons_category_list'][$value->addons_category_id]['max_choice'] = ($value->max_choice ? $value->max_choice : $i + 1) : '';
					$i++;
				} else {

					$menu[$value->category_id]['items'][$value->menu_id]['has_variation'] = 0;
					if (!isset($menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]) && $value->addons_category) {
						$i = 0;
						$menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id] = array();
						$menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category'] = $value->addons_category;
						$menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_category_id'] = $value->addons_category_id;
						$menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['is_multiple'] = $value->is_multiple;
					}
					($value->addons_category) ? $menu[$value->category_id]['items'][$value->menu_id]['addons_category_list'][$value->addons_category_id]['addons_list'][$i] = array('add_ons_id' => $value->add_ons_id, 'add_ons_name' => $value->add_ons_name, 'add_ons_price' => $value->add_ons_price) : '';
					$i++;
				}
			} else {
				$menu[$value->category_id]['items'][]  = array('menu_id' => $value->menu_id, 'name' => $value->name, 'price' => $value->price, 'offer_price' => $offer_price, 'menu_detail' => $value->menu_detail, 'image' => $image, 'recipe_detail' => $value->recipe_detail, 'availability' => $value->availability, 'is_veg' => $value->is_veg, 'is_customize' => $value->check_add_ons, 'is_deal' => $value->is_deal, 'status' => $value->status);
			}
		}

		// echo '<pre>';
		// print_r($menu);
		// exit();

		$finalArray = array();
		$final = array();
		$semifinal = array();
		$quarterfinal = array();
		$new = array();
		foreach ($menu as $nm => $va) {
			$final = array();
			foreach ($va['items'] as $kk => $items) {
				if ($items['is_customize'] == 1) {
					if ($items['has_variation'] == 1) {
						if (!empty($items['variation_list'])) {
							$semifinal = array();
							foreach ($items['variation_list'] as $variation_list) {
								$quarterfinal = array();
								if ($variation_list['hasVariationAddon'] == 1) {
									foreach ($variation_list['addons_category_list'] as $each_add_cat) {
										if ($each_add_cat) {
											array_push($quarterfinal, $each_add_cat);
										}
									}
									if ($variation_list) {
										$variation_list['addons_category_list'] = $quarterfinal;
										array_push($semifinal, $variation_list);
									}
								} else {
									array_push($semifinal, $variation_list);
								}
							}
							$items['variation_list'] = $semifinal;
						}
					} else {
						if (!empty($items['addons_category_list'])) {
							$semifinal = array();
							foreach ($items['addons_category_list'] as $addons_cat_list) {
								if ($addons_cat_list) {
									array_push($semifinal, $addons_cat_list);
								}
							}

							$items['addons_category_list'] = $semifinal;
						}
					}
				}


				// if ($semifinal) {
				//     array_push($final, $items);
				// }


				// if ($items['is_customize'] == 0) {
				array_push($final, $items);
				// }
			}
			$va['items'] = $final;
			array_push($finalArray, $va);
		}
		return $finalArray;
	}

	public function getAddOnsDetails($id)
	{
		// $this->db->select('menu.name as menu,menu.entity_id,master.*,cat.name');
		$this->db->distinct();
		$this->db->select('cat.name,cat.entity_id');
		$this->db->join('restaurant_menu_item as menu', 'menu.entity_id = master.menu_id', 'left');
		$this->db->join('add_ons_category as cat', 'cat.entity_id = master.category_id', 'left');
		$this->db->where('menu.entity_id', $id);
		$result = $this->db->get('add_ons_master as master')->result();

		foreach ($result as $key => $value) {
			$this->db->select('master.*');
			$this->db->join('add_ons_category as cat', 'cat.entity_id = master.category_id', 'left');
			$this->db->where('master.category_id', $value->entity_id);
			$this->db->where('master.menu_id', $id);
			$final[$value->name] = $this->db->get('add_ons_master as master')->result();
		}

		return $final;
	}


	public function getCouponRestaurant($entity_id, $res_id)
	{
		return $this->db->get_where('coupon_restaurant_map', array('coupon_id' => $entity_id, 'restaurant_id' => $res_id))->num_rows();
	}

	public function checkUserCountCoupon($UserID)
	{
		$this->db->where('user_id', $UserID);
		return $this->db->get('order_master')->num_rows();
	}


	public function checkPreviousOrder($user_id, $coupon_id)
	{
		return $this->db->get_where('order_master', array('coupon_id' => $coupon_id, 'user_id' => $user_id))->num_rows();
	}

	public function getSequence($coupon_id)
	{
		$this->db->order_by('sequence', 'asc');
		return $this->db->get_where('coupon_gradual', array('coupon_id' => $coupon_id))->result();
	}

	public function gradualItem($coupon_id)
	{
		return $this->db->get_where('coupon_item_map', array('coupon_id' => $coupon_id))->result();
	}

	public function checkRecords($tblName, $user_id, $coupon_id)
	{
		return $this->db->get_where($tblName, array('user_id' => $user_id, 'coupon_id' => $coupon_id))->first_row();
	}

	public function checkCouponUser($id, $user)
	{
		return $this->db->get_where('coupon_user_map', array('user_id' => $user, 'coupon_id' => $id))->first_row();
	}

	public function getUserAddLatLong($address_id, $restaurant_id)
	{
		$lat_long = $this->db->select('latitude,longitude')->get_where('user_address', array('entity_id' => $address_id))->first_row();

		$zone_id = $this->checkGeoFenceForZone($lat_long->latitude, $lat_long->longitude);

		if ($zone_id) {
			$check = $this->db->get_where('zone_res_map', array('zone_id' => $zone_id, 'restaurant_id' => $restaurant_id))->num_rows();

			if ($check > 0) {
				$delivery_charge = $this->db->select('price_charge,price_charge_2')->get_where('zone', array('entity_id' => $zone_id))->first_row();
				$res_lat_long = $this->db->select('latitude,longitude')->get_where('restaurant_address', array('resto_entity_id' => $restaurant_id))->first_row();

				$distance = $this->distance($lat_long->latitude, $lat_long->longitude, $res_lat_long->latitude, $res_lat_long->longitude);

				$data['zone_id'] = $zone_id;
				if ($distance <= 3) {
					$data['delivery_charge'] = $delivery_charge->price_charge;
					return $data;
				} else {
					$data['delivery_charge'] = $delivery_charge->price_charge_2;
					return $data;
				}
			} else {
				return;
			}
		} else {
			return;
		}
	}

	public function getZones()
	{
		return $this->db->select('entity_id,lat_long')->get_where('zone', array('status' => 1))->result();
	}

	public function checkGeoFenceForZone($latitude, $longitude)
	{
		$result = $this->getZones();

		$oddNodes = "";

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
			$intersections = 0;
			$status = "";
			for ($i = 0; $i < $n; $i++) {

				$j++;

				if ($polygon[$i][0] == $y && $polygon[$i][1] == $x) {
					$status = "vertex";
				}

				if ($polygon[$i][0] == $polygon[$j][0] && $polygon[$i][0] == $y && $x > min($polygon[$i][1], $polygon[$j][1]) && $x < max($polygon[$i][1], $polygon[$j][1])) { // Check if point is on an horizontal polygon boundary
					$status = "boundary";
				}
				if ($y > min($polygon[$i][0], $polygon[$j][0]) && $y <= max($polygon[$i][0], $polygon[$j][0]) && $x <= max($polygon[$i][1], $polygon[$j][1]) && $polygon[$i][0] != $polygon[$j][0]) {
					$xinters = ($y - $polygon[$i][0]) * ($polygon[$j][1] - $polygon[$i][1]) / ($polygon[$j][0] - $polygon[$i][0]) + $polygon[$i][1];
					if ($xinters == $x) { // Check if point is on the polygon boundary (other than horizontal)
						$status = "boundary";
					}
					if ($polygon[$i][1] == $polygon[$j][1] || $x <= $xinters) {
						$intersections++;
					}
				}
			}

			// If the number of edges we passed through is odd, then it's in the polygon.
			if ($intersections % 2 != 0 || $status == 'boundary' || $status == 'vertex') {
				//return "inside";
				$oddNodes = $value->entity_id;
				break;
			}
		}
		//$oddNodes = $value->entity_id;
		return $oddNodes;
	}

	public function distance($lat1, $lon1, $lat2, $lon2)
	{
		if (($lat1 == $lat2) && ($lon1 == $lon2)) {
			return 0;
		} else {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;

			return ($miles * 1.609344);
		}
	}

	public function getRestaurantArea($lat, $long, $restaurant_id)
	{

		$zone_id = $this->checkGeoFenceForZone($lat, $long);

		if ($zone_id) {
			$check = $this->db->get_where('zone_res_map', array('zone_id' => $zone_id, 'restaurant_id' => $restaurant_id))->num_rows();

			if ($check > 0) {
				$delivery_charge = $this->db->select('price_charge,price_charge_2')->get_where('zone', array('entity_id' => $zone_id))->first_row();
				$res_lat_long = $this->db->select('latitude,longitude')->get_where('restaurant_address', array('resto_entity_id' => $restaurant_id))->first_row();

				$distance = $this->distance($lat, $long, $res_lat_long->latitude, $res_lat_long->longitude);

				$data['zone_id'] = $zone_id;
				if ($distance <= 3) {
					$data['delivery_charge'] = $delivery_charge->price_charge;
					return $data;
				} else {
					$data['delivery_charge'] = $delivery_charge->price_charge_2;
					return $data;
				}
			} else {
				return;
			}
		} else {
			return;
		}
	}

	public function checkMaximumUsage($user_id, $coupon_id)
	{
		$this->db->where('coupon_id', $coupon_id);
		$this->db->where('user_id', $user_id);

		return $this->db->get("order_master")->num_rows();
	}


	public function checkOneTimeUser($user_id, $coupon_id)
	{
		return $this->db->get_where('order_master', array('user_id' => $user_id, 'coupon_id' => $coupon_id))->num_rows();
	}


	public function checkToken($userid)
	{
		return $this->db->get_where('users', array('entity_id' => $userid))->first_row();
	}

	public function checkExist($mobile_number)
	{
		$this->db->where('mobile_number', $mobile_number);
		return $this->db->get('users')->num_rows();
	}

	public function order_master($order_id)
	{
		$this->db->where('entity_id', $order_id);
		return $this->db->get('order_master')->result();
	}

	public function getCurrency()
	{
		$this->db->where('currency_code', 'BDT');
		return $this->db->get('currencies')->first_row();
	}

	public function rider_type($order_id)
	{
		$this->db->select('driver_id');
		$result = $this->db->get_where('order_driver_map', array('order_id ' => $order_id, 'is_accept' => 1, 'driver_id !=' => 0))->first_row();
		$this->db->select('rider_types');
		return $this->db->get_where('users', array('entity_id ' => $result->driver_id))->first_row();
	}

	public function getSystemOptoin($OptionSlug)
	{
		$this->db->select('OptionValue');
		$this->db->where('OptionSlug', $OptionSlug);
		return $this->db->get('system_option')->first_row();
	}

	public function updateEngage($order_id)
	{
		$this->db->select('driver_id');
		$result = $this->db->get_where('order_driver_map', array('order_id' => $order_id, 'cancel_reason' => null, 'cancel' => 0, 'no_response' => 0))->first_row();

		$num_of_active_order = $this->sub_dashboard_model->order_count($result->driver_id);

		if ($num_of_active_order < 1)
			$this->db->set('engage', 0)->where('entity_id', $result->driver_id)->update('users');

		return $result->driver_id;
	}

	public function getRiderVehicleCharge($order_id)
	{
		return $this->db->select('v.price')
			->from('order_driver_map as odm')
			->join('rider_information r', 'r.rider_id = odm.driver_id', 'left')
			->join('vehicle_type v', 'v.entity_id = r.v_type', 'left')
			->where('odm.order_id', $order_id)
			->order_by('driver_map_id', 'DESC')
			->get()
			->first_row();
	}

	public function getUserNumber($user_id)
	{
		$this->db->select('mobile_number');
		return $this->db->get_where('users', array('entity_id' => $user_id))->first_row();
	}
}
