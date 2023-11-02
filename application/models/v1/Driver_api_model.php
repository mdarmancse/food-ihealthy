<?php
class Driver_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model(ADMIN_URL . '/systemoption_model');
        $this->load->model(ADMIN_URL . '/order_model');
    }
    /***************** General API's Function *****************/
    public function getLanguages($current_lang)
    {
        $result = $this->db->select('*')->get_where('languages', array('language_slug' => $current_lang))->first_row();
        return $result;
    }
    public function getRecord($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        return $this->db->get($table)->first_row();
    }
    //get record with multiple where
    public function getRecordMultipleWhere($table, $whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }

    // Login
    public function getLogin($phone, $password)
    {
        $enc_pass  = md5(SALT . $password);
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,active,mobile_number,image,notification, vehicle_type.name as vehicle_name');
        $this->db->join('rider_information', 'rider_information.rider_id = users.entity_id', 'left');
        $this->db->join('vehicle_type', 'vehicle_type.entity_id = rider_information.v_type', 'left');
        $this->db->where('mobile_number', $phone);
        $this->db->where('password', $enc_pass);
        $this->db->where('user_type', 'Driver');
        return $this->db->get('users')->first_row();
    }
    // Update User
    public function updateUser($tableName, $data, $fieldName, $UserID)
    {
        $this->db->where($fieldName, $UserID);
        $this->db->update($tableName, $data);
    }
    // check token for every API Call
    public function checkToken($token, $userid)
    {
        return $this->db->get_where('users', array('mobile_number' => $token, ' entity_id' => $userid))->first_row();
    }
    // Common Add Records
    public function addRecord($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    // Common Add Records Batch
    public function addRecordBatch($table, $data)
    {
        return $this->db->insert_batch($table, $data);
    }
    public function deleteRecord($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        return $this->db->delete($table);
    }
    //get event
    public function getallOrder($user_id)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        //current
        $this->db->select('order_detail.restaurant_detail,order_detail.item_detail, order_detail.receiver_details,order_detail.order_id,order_driver_map.driver_map_id,order_master.order_status,order_master.delivery_charge,order_master.vat,order_master.sd,order_master.commission_rate,order_master.total_rate,order_master.subtotal,order_master.commission_value,order_master.created_date,order_detail.user_detail,users.mobile_number,users.image,restaurant_address.latitude,restaurant_address.longitude,currencies.currency_symbol,currencies.currency_code,order_master.transaction_id,order_master.payment_option');
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id', 'left');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('order_driver_map.driver_map_id IN (SELECT MAX(od.driver_map_id) as dm FROM order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');
        $this->db->where('(order_master.order_status != "delivered" AND order_master.order_status != "cancel" AND order_master.order_status != "not_delivered")');
        $this->db->where('order_master.order_delivery', 'Delivery');
        $this->db->where('order_driver_map.cancel_reason =', NULL);
        $this->db->where('order_driver_map.no_response', 0);
        $this->db->where('order_driver_map.driver_id', $user_id);
        // $this->db->where('DATE(order_master.order_date)',date('Y-m-d'));
        $this->db->order_by('order_master.entity_id', 'desc');
        $current_order = $this->db->get('order_master')->result();

        $current = array();
        if (!empty($current_order)) {
            foreach ($current_order as $key => $value) {
                if (!isset($value->order_id)) {
                    $current[$value->order_id] = array();
                }
                if (isset($value->order_id)) {
                    //$business_type = $value->business_type;
                    $item_detail = unserialize($value->item_detail);
                    // $itms=($item_detail);

                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $user_detail = unserialize($value->user_detail);
                    $receiver_details = unserialize($value->receiver_details);
                    $receiver = $receiver_details;
                    $current[$value->order_id]['item_detail'] = $item_detail;
                    $current[$value->order_id]['commission_value'] = $value->commission_value;
                    $current[$value->order_id]['payment_option'] = $value->payment_option;
                    $current[$value->order_id]['vat'] = round($value->vat);
                    $current[$value->order_id]['sd'] = round($value->sd);
                    $current[$value->order_id]['commission_rate'] = $value->commission_rate;
                    $current[$value->order_id]['delivery_charge'] = $value->delivery_charge;
                    //$current[$value->order_id]['business_type'] = $business_type;
                    // $current[$value->order_id]['name'] = $restaurant_detail->name;
                    $current[$value->order_id]['name'] = $restaurant_detail->name; //($business_type == 1) ? $restaurant_detail->name : "Parcel";
                    $current[$value->order_id]['res_phone_number'] = $restaurant_detail->phone_number;
                    $current[$value->order_id]['res_address'] = $restaurant_detail->address;
                    $current[$value->order_id]['image'] = ($restaurant_detail->image) ? image_url . $restaurant_detail->image : '';
                    $current[$value->order_id]['res_latitude'] = $value->latitude;
                    $current[$value->order_id]['res_longitude'] = $value->longitude;
                    $current[$value->order_id]['order_id'] = $value->order_id;
                    $current[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $current[$value->order_id]['subtotal'] = $value->subtotal;
                    $current[$value->order_id]['total_rate'] = $value->total_rate;
                    $current[$value->order_id]['currency_code'] = $value->currency_code;
                    $current[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                    $current[$value->order_id]['order_status'] = $value->order_status == "not_delivered" ? "Not Delivered" : $value->order_status;
                    $current[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $current[$value->order_id]['latitude'] = (isset($user_detail['latitude'])) ? $user_detail['latitude'] : '';
                    $current[$value->order_id]['longitude'] = (isset($user_detail['longitude'])) ? $user_detail['longitude'] : '';
                    $current[$value->order_id]['address'] = $user_detail['address'] . ' ' . $user_detail['landmark'] . ' ' . $user_detail['zipcode'] . ' ' . $user_detail['city'];
                    $current[$value->order_id]['phone_number'] = $value->mobile_number;
                    $current[$value->order_id]['user_image'] = ($value->image) ? image_url . $value->image : '';
                    $current[$value->order_id]['date'] = date('Y-m-d H:i', strtotime($value->created_date));
                    $current[$value->order_id]['transaction_id'] = $value->transaction_id;
                    $current[$value->order_id]['order_type'] = ($value->transaction_id) ? 'paid' : 'cod';
                    //$current[$value->order_id]['payee'] = ($business_type == 2) ? $value->payee : "0";
                    //$current[$value->order_id]['sender_details'] = ($business_type == 2) ? $user_detail : "";
                    //$current[$value->order_id]['receiver_details'] = ($business_type == 2) ? $receiver : "";
                }
            }
        }
        $finalArray = array();
        foreach ($current as $key => $val) {
            $finalArray[] = $val;
        }
        $data['current'] = $finalArray;
        //past
        $this->db->select(
            'order_detail.restaurant_detail,
        order_detail.item_detail,
        order_detail.receiver_details,
        order_detail.order_id,
        order_driver_map.driver_map_id,
        order_master.order_status,
        order_driver_map.cancel_reason,
        order_master.vat,
        order_master.sd,
        order_master.commission_value,
        order_master.total_rate,
        order_master.subtotal,
        order_master.created_date,
        order_master.delivery_charge,
        order_detail.user_detail,
        users.mobile_number,
        users.image,
        restaurant_address.latitude,
        restaurant_address.longitude,
        currencies.currency_symbol,
        currencies.currency_code,
        order_master.transaction_id,
        vehicle_type.price as vehicle_rate'
        );
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id', 'left');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->join('rider_information', 'rider_information.rider_id = users.entity_id', 'left');
        $this->db->join('vehicle_type', 'vehicle_type.entity_id = rider_information.v_type', 'left');
        $this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('order_driver_map.driver_map_id IN (SELECT MAX(od.driver_map_id) as dm FROM order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');
        $this->db->where('order_driver_map.driver_id', $user_id);
        $this->db->where('order_driver_map.is_accept', 1);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel" OR order_master.order_status = "not_delivered")');
        $this->db->where('order_master.order_delivery', 'Delivery');
        $this->db->order_by('order_master.entity_id', 'desc');
        $past_order = $this->db->get('order_master')->result();
        $past = array();
        if (!empty($past_order)) {
            foreach ($past_order as $key => $value) {

                $vehicle_rate = $value->vehicle_rate;
                $delivery_charge = $value->delivery_charge;
                $restaurant_pay = $value->subtotal + $value->vat + $value->sd - $value->commission_value;
                $hand_cash = $value->total_rate - $restaurant_pay;

                $order_timing_details = $this->get_order_timing_details($value->order_id);
                if (!isset($value->order_id)) {
                    $past[$value->order_id] = array();
                }
                if (isset($value->order_id)) {
                    $restaurant_detail = @unserialize($value->restaurant_detail);
                    $user_detail = @unserialize($value->user_detail);
                    $receiver_details = @unserialize($value->receiver_details);
                    $item_detail = @unserialize($value->item_detail);

                    $past[$value->order_id]['name'] = $restaurant_detail->name; //($business_type == 1) ? $restaurant_detail->name : "Parcel";
                    $past[$value->order_id]['res_phone_number'] = $restaurant_detail->phone_number;
                    $past[$value->order_id]['res_address'] = $restaurant_detail->address;
                    $past[$value->order_id]['itm_dtl'] = $item_detail;
                    $past[$value->order_id]['vat'] = round($value->vat);
                    $past[$value->order_id]['sd'] = round($value->sd);
                    $past[$value->order_id]['commission_value'] = $value->commission_value;
                    $past[$value->order_id]['image'] = ($restaurant_detail->image) ? image_url . $restaurant_detail->image : '';
                    $past[$value->order_id]['res_latitude'] = $value->latitude;
                    $past[$value->order_id]['res_longitude'] = $value->longitude;
                    $past[$value->order_id]['order_id'] = $value->order_id;
                    $past[$value->order_id]['driver_map_id'] = $value->driver_map_id;
                    $past[$value->order_id]['subtotal'] = $value->subtotal;
                    $past[$value->order_id]['total_rate'] = $value->total_rate;
                    $past[$value->order_id]['currency_code'] = $value->currency_code;
                    $past[$value->order_id]['currency_symbol'] = $value->currency_symbol;
                    $past[$value->order_id]['order_status'] = $value->order_status == "not_delivered" ? "Not Delivered" : $value->order_status;
                    $past[$value->order_id]['user_name'] = $user_detail['first_name'];
                    $past[$value->order_id]['latitude'] = (isset($user_detail['latitude'])) ? $user_detail['latitude'] : '';
                    $past[$value->order_id]['longitude'] = (isset($user_detail['longitude'])) ? $user_detail['longitude'] : '';
                    $past[$value->order_id]['address'] = $user_detail['address'] . ' ' . $user_detail['landmark'] . ' ' . $user_detail['zipcode'] . ' ' . $user_detail['city'];
                    $past[$value->order_id]['phone_number'] = $value->mobile_number;
                    $past[$value->order_id]['user_image'] = ($value->image) ? image_url . $value->image : '';
                    $past[$value->order_id]['date'] = date('Y-m-d H:i', strtotime($value->created_date));
                    $past[$value->order_id]['transaction_id'] = $value->transaction_id;
                    $past[$value->order_id]['order_type'] = ($value->transaction_id) ? 'paid' : 'cod';
                    $past[$value->order_id]['order_accept_time'] = $order_timing_details['order_accept_time'];
                    $past[$value->order_id]['order_delivery_time'] = $order_timing_details['order_delivery_time'];
                    $past[$value->order_id]['total_delivery_time'] = $order_timing_details['total_delivery_time'];
                    $past[$value->order_id]['amnt_collectable_with_delivery_charge'] = ($hand_cash - $vehicle_rate) + $delivery_charge;
                    $past[$value->order_id]['amnt_collectable_without_delivery_charge'] = $hand_cash - $vehicle_rate;
                    // $past[$value->order_id]['payee'] = ($business_type == 2) ? $value->payee : "0";
                    // $past[$value->order_id]['sender_details'] = ($business_type == 2) ? $user_detail : "";
                    // $past[$value->order_id]['receiver_details'] = ($business_type == 2) ? $receiver_details : "";
                }
            }
        }
        $final = array();
        foreach ($past as $key => $val) {
            $final[] = $val;
        }
        $data['past'] = $final;
        return $data;
    }
    //accept order
    public function acceptOrder($order_id, $driver_map_id, $user_id)
    {
        //$this->db->set('engage', 1)->where('entity_id', $user_id)->update('users');
        $this->db->set('is_accept', 1)->where('driver_id', $user_id)->where('order_id', $order_id)->where('driver_map_id', $driver_map_id)->update('order_driver_map');
        // $count = $this->db->set('is_accept',1)->where('driver_id',$user_id)->where('order_id', $order_id)->where('driver_map_id',$driver_map_id)->update('order_driver_map');
        // if($count == 1){
        //     $this->db->where('order_id', $order_id);
        //     $this->db->where('is_accept !=',1);
        //     $this->db->where('driver_id !=',$user_id);
        //     $this->db->delete('order_driver_map');
        // }
        $this->db->set('order_status', 'preparing')->where('entity_id', $order_id)->update('order_master');
        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug,users.first_name,users.last_name,users.mobile_number,order_detail.user_detail,restaurant_address.latitude,restaurant_address.longitude');
        $this->db->join('order_master', 'users.entity_id = order_master.user_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->join('restaurant_address', 'order_master.restaurant_id = restaurant_address.resto_entity_id', 'left');
        $this->db->where('order_master.entity_id', $order_id);
        $device = $this->db->get('users')->first_row();

        // load language
        $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);

        $info = array();
        if ($device->device_id) {
            #prep the bundle
            $fields = array();
            $message = $this->lang->line('order_preparing');
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
        $user_detail = unserialize($device->user_detail);
        $info['address'] = $user_detail['address'] . ' ' . $user_detail['landmark'] . ' ' . $user_detail['zipcode'] . ' ' . $user_detail['city'];
        $info['latitude'] = (isset($user_detail['latitude'])) ? $user_detail['latitude'] : '';
        $info['longitude'] = (isset($user_detail['longitude'])) ? $user_detail['longitude'] : '';
        $info['phone_number'] = $device->mobile_number;
        $info['res_latitude'] = $device->latitude;
        $info['res_longitude'] = $device->longitude;
        $info['name'] = $device->first_name . ' ' . $device->last_name;
        $info['order_user_id'] = $device->entity_id;
        return $info;
    }
    //order delivered
    public function deliveredOrder($order_id, $user_id, $status, $subtotal)
    {

        $this->db->set('order_status', $status)->where('entity_id', $order_id)->update('order_master');
        if ($status == 'delivered') {
            $this->db->set('engage', 0)->where('entity_id', $user_id)->update('users');

            $this->db->select('order_driver_map.distance');
            $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id', 'left');
            $this->db->where('order_master.entity_id', $order_id);
            $distance = $this->db->get('order_master')->first_row();

            $comsn = '';
            if ($distance->distance > 3) {
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_more'))->first_row();
            } else {
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_less'))->first_row();
            }
            if ($comsn) {
                $data = array('driver_commission' => $comsn->OptionValue, 'commission' => $comsn->OptionValue);
                $this->db->where('order_id', $order_id);
                $this->db->update('order_driver_map', $data);
            }
        }

        $this->db->select('item_detail,user_detail,currencies.currency_symbol,currencies.currency_code,order_master.restaurant_id');
        $this->db->join('order_master', 'order_detail.order_id = order_master.entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->where('order_id', $order_id);
        $detail =  $this->db->get('order_detail')->first_row();
        $info = array();
        if (!empty($detail)) {
            $order_detail = unserialize($detail->item_detail);
            $user_detail = unserialize($detail->user_detail);
            $info['order_detail'] = $order_detail;
            $info['currency_code'] = $detail->currency_code;
            $info['currency_symbol'] = $detail->currency_symbol;
            $info['address'] = $user_detail['address'] . ' ' . $user_detail['landmark'] . ' ' . $user_detail['zipcode'] . ' ' . $user_detail['city'];
        }

        //get users to send notifcation
        $this->db->select('users.entity_id,users.device_id,users.language_slug,order_master.total_rate,order_master.user_id');
        $this->db->join('order_master', 'users.entity_id = order_master.user_id', 'left');
        $this->db->where('order_master.entity_id', $order_id);
        $device = $this->db->get('users')->first_row();
        // load language
        $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
        $this->lang->load('messages_lang', $languages->language_directory);
        if ($status == "delivered") {

            //Foodi CRM
            $reward_value = $this->systemoption_model->getRewardValue('Earn 1 Point For');
            $total_rate = $device->total_rate;
            $addData = array(
                'points' => floor($total_rate / $reward_value),
                'cost' => 1,
                'date' => date('Y-m-d H:i:s'),
                'order_id' => $order_id,
                'user_id' => $device->user_id
            );
            $reward_point_id = $this->order_model->addData('reward_point', $addData);
            if ($device->device_id) {
                #prep the bundle
                $fields = array();
                $message = $this->lang->line('push_order_delived');
                $fields['to'] = $device->device_id; // only one user to send push notification
                $fields['notification'] = array('body'  => $message, 'sound' => 'default');
                $fields['data'] = array('screenType' => 'delivery', 'restaurant_id' => $detail->restaurant_id, 'order_id' => $order_id, 'rider_id' => $user_id);

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
        }
        if ($status == "ongoing") {
            if ($device->device_id) {
                #prep the bundle
                $fields = array();
                $message = " Your order is on the way";
                $fields['to'] = $device->device_id; // only one user to send push notification
                $fields['notification'] = array('body'  => $message, 'sound' => 'default');
                $fields['data'] = array('screenType' => 'order', 'restaurant_id' => $detail->restaurant_id);

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
        }

        return $info;
    }
    //get commission list
    public function getCommissionList($user_id)
    {
        //last order
        $this->db->select(
            '
        order_master.total_rate,
        order_master.order_status,
        order_master.vat,
        order_master.sd,
        order_master.commission_value,
        order_master.subtotal,
        order_master.delivery_charge,
        order_status.time,
        order_detail.restaurant_detail,
        order_detail.user_detail,
        order_driver_map.order_id,
        order_driver_map.driver_id,
        order_driver_map.commission,
        order_master.order_status,
        order_master.total_rate,
        vehicle_type.price as vehicle_rate,
        currencies.currency_symbol,
        currencies.currency_code'
        );

        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('currencies', 'restaurant.currency_id = currencies.currency_id', 'left');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id', 'left');
        $this->db->join('order_status', 'order_driver_map.order_id = order_status.order_id', 'left');
        $this->db->join('rider_information', 'rider_information.rider_id = order_driver_map.driver_id', 'left');
        $this->db->join('vehicle_type', 'vehicle_type.entity_id = rider_information.v_type', 'left');
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->where('order_driver_map.driver_id', $user_id);
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->order_by('order_master.entity_id', 'desc');
        $this->db->limit(1);
        $details =  $this->db->get('order_master')->result();
        $last_address = array();
        $last_user_id = '';
        $finalArray = array();
        if (!empty($details)) {
            foreach ($details as $key => $value) {
                $vehicle_rate = $value->vehicle_rate;
                $delivery_charge = $value->delivery_charge;
                $restaurant_pay = $value->subtotal + $value->vat + $value->sd - $value->commission_value;
                $hand_cash = $value->total_rate - $restaurant_pay;
                $order_timing_details = $this->get_order_timing_details($value->order_id);

                $last_user_id = $value->order_id;
                if (!isset($value->order_id)) {
                    $last_address[$value->order_id] = array();
                }
                if (isset($value->order_id)) {
                    $address = unserialize($value->user_detail);
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $last_address[$value->order_id]['time'] = ($value->time) ? date('h:i A', strtotime($value->time)) : '';
                    $last_address[$value->order_id]['date'] =  ($value->time) ? date('l j M', strtotime($value->time)) : '';
                    $last_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    $last_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $last_address[$value->order_id]['order_id'] = $value->order_id;
                    $last_address[$value->order_id]['commission'] = ($value->commission) ? $value->commission : '';
                    $last_address[$value->order_id]['name'] = $restaurant_detail->name;
                    $last_address[$value->order_id]['image'] = ($restaurant_detail->image) ? image_url . $restaurant_detail->image : '';
                    $last_address[$value->order_id]['address'] = $address['address'] . ' ' . $address['landmark'] . ' ' . $address['zipcode'] . ' ' . $address['city'];
                    $last_address[$value->order_id]['currency_symbol'] = $restaurant_detail->currency_symbol;
                    $last_address[$value->order_id]['currency_code'] = $restaurant_detail->currency_code;
                    $last_address[$value->order_id]['subtotal'] = $value->subtotal;
                    $last_address[$value->order_id]['order_accept_time'] = $order_timing_details['order_accept_time'];
                    $last_address[$value->order_id]['order_delivery_time'] = $order_timing_details['order_delivery_time'];
                    $last_address[$value->order_id]['total_delivery_time'] = $order_timing_details['total_delivery_time'];
                    $last_address[$value->order_id]['amnt_collectable_with_delivery_charge'] = ($hand_cash - $vehicle_rate) + $delivery_charge;
                    $last_address[$value->order_id]['amnt_collectable_without_delivery_charge'] = $hand_cash - $vehicle_rate;
                    $user_address[$value->order_id]['restaurant_pay'] = $restaurant_pay;
                }
            }
            foreach ($last_address as $key => $val) {
                $finalArray[] = $val;
            }
        }

        $data['last'] = $finalArray;
        //previous order
        $this->db->select('
        order_master.total_rate,
        order_master.order_status,
        order_master.vat,
        order_master.sd,
        order_master.commission_value,
        order_master.subtotal,
        order_master.delivery_charge,
        order_status.time,
        order_detail.restaurant_detail,
        order_detail.user_detail,
        order_driver_map.order_id,
        order_driver_map.driver_id,
        order_driver_map.commission,
        vehicle_type.price as vehicle_rate,
        order_master.order_status,
        order_master.total_rate
        ');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id', 'left');
        $this->db->join('order_status', 'order_driver_map.order_id = order_status.order_id', 'left');
        $this->db->join('rider_information', 'rider_information.rider_id = order_driver_map.driver_id', 'left');
        $this->db->join('vehicle_type', 'vehicle_type.entity_id = rider_information.v_type', 'left');
        $this->db->join('order_detail', 'order_master.entity_id = order_detail.order_id', 'left');
        $this->db->where('order_driver_map.driver_id', $user_id);
        if ($last_user_id) {
            $this->db->where('order_driver_map.order_id !=', $last_user_id);
        }
        $this->db->where('(order_master.order_status = "delivered" OR order_master.order_status = "cancel")');
        $this->db->where('order_driver_map.is_accept', 1);
        $this->db->order_by('order_master.entity_id', 'desc');
        $details =  $this->db->get('order_master')->result();
        $user_address = array();
        $final = array();
        if (!empty($details)) {
            foreach ($details as $key => $value) {
                $vehicle_rate = $value->vehicle_rate;
                $delivery_charge = $value->delivery_charge;
                $restaurant_pay = $value->subtotal + $value->vat + $value->sd - $value->commission_value;
                $hand_cash = $value->total_rate - $restaurant_pay;
                $order_timing_details = $this->get_order_timing_details($value->order_id);

                if (!isset($value->order_id)) {
                    $user_address[$value->order_id] = array();
                }
                if (isset($value->order_id)) {
                    $address = unserialize($value->user_detail);
                    $restaurant_detail = unserialize($value->restaurant_detail);
                    $user_address[$value->order_id]['time'] = ($value->time) ? date('h:i A', strtotime($value->time)) : '';
                    $user_address[$value->order_id]['date'] =  ($value->time) ? date('l j M', strtotime($value->time)) : '';
                    $user_address[$value->order_id]['order_status'] = ucfirst($value->order_status);
                    $user_address[$value->order_id]['total_rate'] = $value->total_rate;
                    $user_address[$value->order_id]['order_id'] = $value->order_id;
                    $user_address[$value->order_id]['commission'] = ($value->commission) ? $value->commission : '';
                    $user_address[$value->order_id]['name'] = $restaurant_detail->name;
                    $user_address[$value->order_id]['image'] = ($restaurant_detail->image) ? image_url . $restaurant_detail->image : '';
                    $user_address[$value->order_id]['address'] = $address['address'] . ' ' . $address['landmark'] . ' ' . $address['zipcode'] . ' ' . $address['city'];
                    $user_address[$value->order_id]['currency_symbol'] = $restaurant_detail->currency_symbol;
                    $user_address[$value->order_id]['currency_code'] = $restaurant_detail->currency_code;
                    $user_address[$value->order_id]['subtotal'] = $value->subtotal;
                    $user_address[$value->order_id]['order_accept_time'] = $order_timing_details['order_accept_time'];
                    $user_address[$value->order_id]['order_delivery_time'] = $order_timing_details['order_delivery_time'];
                    $user_address[$value->order_id]['total_delivery_time'] = $order_timing_details['total_delivery_time'];
                    $user_address[$value->order_id]['amnt_collectable_with_delivery_charge'] = ($hand_cash - $vehicle_rate) + $delivery_charge;
                    $user_address[$value->order_id]['amnt_collectable_without_delivery_charge'] = $hand_cash - $vehicle_rate;
                    $user_address[$value->order_id]['restaurant_pay'] = $restaurant_pay;
                }
            }
            foreach ($user_address as $key => $val) {
                $final[] = $val;
            }
        }
        $data['previous'] = $final;
        return $data;
    }
    //get user of order
    public function getUserofOrder($order_id)
    {
        $this->db->select('users.device_id,users.language_slug');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.entity_id', $order_id);
        return $this->db->get('order_master')->first_row();
    }

    public function checkAccept($order_id)
    {
        $this->db->select('status');
        return $this->db->get_where('order_master', array('entity_id' => $order_id))->first_row();
    }

    public function isOrderAvailable($orderid, $driverid)
    {
        $arr = array('placed', 'onGoing', 'preparing', 'accepted_by_restaurant');
        $x = 0;
        $this->db->select('order_driver_map.driver_id,order_driver_map.order_id');
        $this->db->join('order_master', 'order_master.entity_id =order_driver_map.order_id ', 'left');
        $this->db->where_in('order_master.order_status', $arr);
        $this->db->where('order_driver_map.order_id', $orderid);
        $this->db->order_by('order_driver_map.driver_map_id', 'desc');
        $this->db->limit(1);
        $data =  $this->db->get('order_driver_map')->result();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $x = $value->driver_id;
            }
            if ($driverid == $x) {
                return true;
                // return  $driverid;
            } else {
                return false;
                //return $data;
            }
        } else {
            return false;
        }
    }

    public function updateEngage($user)
    {
        $this->db->set('engage', 0)->where('entity_id', $user)->update('users');
    }

    public function checkEngage($id)
    {
        $this->db->select('engage');
        return $this->db->get_where('users', array('entity_id' => $id))->first_row();
    }

    public function getLastRecord($table, $fieldName, $where)
    {
        $this->db->where($fieldName, $where);
        $this->db->order_by('id', 'desc');
        return $this->db->get($table)->first_row();
    }

    public function getWeeklyOrders($user_id)
    {
        $this->db->select('id,date');
        $this->db->where('rider_id', $user_id);
        $this->db->where("YEARWEEK(riders_earning.date, 1) = YEARWEEK(CURDATE(), 1)");
        return $this->db->get('riders_earning')->num_rows();
    }

    public function getSystemMultiOptoin($OptionSlug, $type)
    {
        $this->db->select('OptionValue');
        $this->db->where('OptionSlug', $OptionSlug);
        $this->db->where('user_type', $type);
        return $this->db->get('system_option')->first_row();
    }

    public function getSystemOptoin($OptionSlug)
    {
        $this->db->select('OptionValue');
        $this->db->where('OptionSlug', $OptionSlug);
        return $this->db->get('system_option')->first_row();
    }

    public function getNumberOfRecords($rider_id)
    {
        $this->db->where('rider_id', $rider_id);
        //$this->db->where('filed2',$filed2);
        return $this->db->get('riders_earning')->num_rows();
    }

    public function getTimeWiseRecord($start, $end, $rider_id)
    {
        $this->db->select("COUNT(id) as count");
        $this->db->select_sum('rider_earning');
        $this->db->select_sum('hand_cash');
        $this->db->select_sum('weekly_bonus');
        $this->db->where('rider_id', $rider_id);
        if (!empty($end) && $start != $end && $end != null && $end != undefined && $end != "null" && $end != "Invalid date") {
            $this->db->where('date>=', $start);
            $this->db->where('date <=', $end);
        } else {
            $this->db->where('date', $start);
        }
        return $this->db->get('riders_earning')->result();
    }

    //leaderboard
    public function getLeaderBoard()
    {
        $this->db->select('id,rider_id,COUNT(id) as c,users.first_name');
        $this->db->join('users', 'users.entity_id = riders_earning.rider_id', 'left');
        $this->db->where('date', date('Y-m-d'));
        $this->db->group_by('rider_id');
        $this->db->order_by(c, 'desc');
        $this->db->limit(10);
        return $this->db->get('riders_earning')->result();
    }

    public function get_order_timing_details($order_id)
    {
        $res = $this->db->select('*')
            ->from('order_status')
            ->where('order_id', $order_id)
            ->group_start()
            ->where('order_status', 'preparing')
            ->or_where('order_status', 'delivered')
            ->group_end()
            ->get()
            ->result_array();

        $order_accept_time = null;
        $order_delivery_time = null;

        foreach ($res as $r) {
            if ($r['order_status'] == 'preparing') {
                $order_accept_time = $r['time'];
            } elseif ($r['order_status'] == 'delivered') {
                $order_delivery_time = $r['time'];
            }
        }

        $total_delivery_time = 'N/A';

        if ($order_delivery_time && $order_accept_time) {
            $time_diff = strtotime($order_delivery_time) - strtotime($order_accept_time);

            $total_delivery_time = round($time_diff / 60);
        }

        $return_data = array(
            'order_accept_time' => $order_accept_time,
            'order_delivery_time'   => $order_delivery_time,
            'total_delivery_time'   => $total_delivery_time,
        );

        return $return_data;
    }
}
