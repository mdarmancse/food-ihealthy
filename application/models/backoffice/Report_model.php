<?php
class report_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('common_model');
    }

    function restaurant_details($email)
    {
        $this->db->select('entity_id,name');
        $this->db->where('email', $email);
        return $this->db->get('restaurant')->result();
    }
    public function getcouponvalue($coupon_id)
    {
        $this->db->select('*');
        $coupon_value = $this->db->get_where('coupon', array('entity_id' => $coupon_id))->first_row();
        return $coupon_value;
    }
    public function getvouchervalue($coupon_id)
    {
        $this->db->select('value');
        $coupon_value = $this->db->get_where('reward_point_setting', array('entity_id' => $coupon_id))->first_row();
        return $coupon_value->value;
    }
    function getAllUsers()
    {
        return $this->db->query("select entity_id,first_name,mobile_number from users where user_type='User'");
    }

    function fetch($Fdate, $Tdate, $restaurent)
    {

        $this->db->select('o.*,order.user_detail,order.	item_detail');
        $this->db->join('order_detail as order', 'order.order_id = o.entity_id', 'left');
        $this->db->where('o.order_date >=', $Fdate);
        $this->db->where('o.order_date <=', $Tdate);
        $this->db->where('o.restaurant_id', $restaurent);
        return $this->db->get('order_master as o');
        // $query="select * from  order_master  WHERE order_date BETWEEN $Fdate AND $Tdate AND  restaurant_id=$Gjc"
        // return $this->db->query('select * from  order_master where BETWEEN '.$Fdate.' AND '.$Tdate.'restaurant_id='.$Gjc.'');

    }

    function getDrivers($mail)
    {
        $this->db->select('entity_id');
        $this->db->where('email', $mail);
        $restaurentId = $this->db->get('restaurant')->row()->entity_id;
        $this->db->select('users.entity_id, users.first_name');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id = users.entity_id', 'left');
        $this->db->where('order_master.restaurant_id', $restaurentId);
        $this->db->group_by('users.entity_id');
        return $this->db->get('order_master');
    }
    // function exportAllDeleveredData($Fdate, $Tdate, $restaurent)
    // {
    //     $this->db->select('entity_id,subtotal,delivery_charge,coupon_discount,vat,sd,total_rate');
    //     $this->db->where('order_date >=', $Fdate);
    //     $this->db->where('order_date <=', $Tdate);
    //     $this->db->where('order_status="delivered"');
    //     $this->db->where('restaurant_id', $restaurent);
    //     return $this->db->get('order_master');
    // }

    function exportAllData($Fdate, $Tdate, $restaurent)
    {
        $this->db->select('o.entity_id,order.user_detail,order.item_detail,o.subtotal,o.order_date,o.delivery_charge,o.coupon_discount,o.vat,o.sd,o.total_rate,');
        $this->db->join('order_detail as order', 'order.order_id = o.entity_id', 'left');
        $this->db->where('o.order_date >=', $Fdate);
        $this->db->where('o.order_date <=', $Tdate);
        $this->db->where('o.restaurant_id', $restaurent);
        return $this->db->get('order_master as o');
    }

    function riders_report($Fdate, $Tdate, $dropdown)
    {
        $this->db->select('order_master.entity_id,order_master.total_rate,order_driver_map.commission,(order_master.total_rate-order_driver_map.commission) as total');
        $this->db->from('order_master');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id');
        $this->db->where('driver_id', $dropdown);
        $this->db->where('is_accept=1');
        $this->db->where('order_date >=', $Fdate);
        $this->db->where('order_date <=', $Tdate);
        return $this->db->get();
    }

    function fetchRiders($Fdate, $Tdate, $dropdown)
    {

        $this->db->select('*');
        $this->db->from('order_master');
        $this->db->join('order_driver_map', 'order_master.entity_id = order_driver_map.order_id');
        $this->db->where('driver_id', $dropdown);
        $this->db->where('is_accept=1');
        $this->db->where('order_date >=', $Fdate);
        $this->db->where('order_date <=', $Tdate);
        return $this->db->get();
    }


    function getAllGroups()
    {
        return $this->db->query("select entity_id,first_name,mobile_number from users where user_type='Driver'");
    }
    function get_drivers($city_id = null, $zone_id = null)
    {
        $this->db->select('users.entity_id,users.first_name,users.mobile_number');
        $this->db->where('user_type', 'Driver');
        if ($city_id != '') {
            $this->db->where('users.city_id', $city_id);
        }
        if ($zone_id != '') {
            $this->db->where('users.zone_id', $zone_id);
        }
        return $this->db->get('users')->result();
    }

    function getAllRestaurant()
    {

        $this->db->select('entity_id,name');

        if (!($this->lpermission->method('full_report_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }
        return $this->db->get("restaurant");
    }

    function getAllCustomer()
    {

        return $this->db->query("select entity_id,first_name,mobile_number from users where user_type='User'");
    }

    function allOrder_pdf($entity_id, $order_status, $to_date, $from_date)
    {
        $this->db->select('u.entity_id as r_id,u.first_name as r_name,users.entity_id, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id, restaurant.name,order_driver_map.order_id,order_driver_map.driver_id');
        // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
        $this->db->from('order_master');
        $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
        $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        // $this->db->join('order_status','order_master.entity_id= order_status.order_id','left');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        // $this->db->or_where('order_status.order_status','delivered');
        if (!empty($entity_id)) {
            $this->db->where('restaurant_id', $entity_id);
        }
        if (!empty($from_date)) {
            $this->db->where('DATE(order_date) >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('DATE(order_date) <=', $to_date);
        }

        if ($order_status) {
            $this->db->where('order_status', $order_status);
        }

        $this->db->order_by('order_master.entity_id', 'DESC');

        return $this->db->get();
    }
    public function getAccountsReport($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchRider = $postData['searchRider'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];
        $searchcity = $postData['searchcity'];
        $searchzone = $postData['searchzone'];
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' ) ";
        }
        if ($searchRider != '') {
            $search_arr[] = "users.entity_id='" . $searchRider . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(order_status.time) >='" . $searchFromDate . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(order_status.time) <='" . $searchToDate . "'";
        }
        if ($searchcity != '') {
            $search_arr[] = "city.id='" . $searchcity . "' ";
        }
        if ($searchzone != '') {
            $search_arr[] = "order_master.zone_id='" . $searchzone . "' ";
        }
        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->from('order_master');
        $this->db->select('zone.area_name,order_master.payment_option,users.entity_id as driver_id, users.first_name,users.mobile_number,order_master.entity_id as eid,order_status.order_id as osid,order_status.*,restaurant.*,order_detail.user_detail');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'order_master.zone_id= zone.entity_id');
        $this->db->join('city', 'city.id= zone.city_id');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_status.order_status', 'delivered');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->from('order_master');
        $this->db->select('zone.area_name,order_master.payment_option,users.entity_id as driver_id, users.first_name,users.mobile_number,order_master.entity_id as eid,order_status.order_id as osid,order_status.*,restaurant.*,order_detail.user_detail');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'order_master.zone_id= zone.entity_id');
        $this->db->join('city', 'city.id= zone.city_id');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_status.order_status', 'delivered');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();

        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select('zone.area_name,
        order_master.payment_option,
        users.entity_id as driver_id,
        users.first_name,
        users.mobile_number
        ,order_master.entity_id as e_id,

        order_master.vat as order_vat,
        order_master.sd as order_sd,
        order_status.order_id as osid,
        order_master.total_rate as total_rate,
        order_master.subtotal as subtotal,
        order_master.commission_value,
         order_master.delivery_charge,
        order_status.*,
        restaurant.*,
        order_detail.user_detail,
        cart_update_history.prev_total
        ');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('cart_update_history ', 'order_master.entity_id=
        (SELECT cart_update_history.order_id as co_id WHERE cart_update_history.order_id = order_master.entity_id ORDER BY cart_update_history.entity_id LIMIT 1)', 'left');
        $this->db->join('zone', 'order_master.zone_id= zone.entity_id');
        $this->db->join('city', 'city.id= zone.city_id');
        // $this->db->join('rider_information', 'rider_information.rider_id= order_driver_map.driver_id');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_status.order_status', 'delivered');
        $this->db->order_by('order_master.entity_id', 'DESC');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('order_master')->result();

        $data = array();

        foreach ($records as $key => $record) {
            $user_id = $record->user_id;
            $vehicle_rate = $this->get_rate($user_id);
            $delivery_charge = $record->delivery_charge;
            $restaurant_pay = $record->subtotal + $record->order_vat + $record->order_sd - $record->commission_value;
            $hand_cash = $record->total_rate - $restaurant_pay;
            $res_collectable_with_charge = ($hand_cash - $vehicle_rate[0]->price) + $delivery_charge;
            $res_collectable_without_charge = $hand_cash - $vehicle_rate[0]->price;

            $customer_address = unserialize($record->user_detail);

            $data[] = array(
                'sl'               => ++$key,
                'e_id'             => $record->e_id,
                'time'             => $record->time,
                'name'             => $record->name,
                'customer_address' => $record->customer_address['address'] . ', ' . $customer_address['landmark'] . ', ' . $customer_address['zipcode'] . ', ' . $customer_address['city'],
                'driver_id'       => $record->driver_id,
                'first_name'       => $record->first_name . " " .
                    $record->mobile_number,
                'zone_name'         => $record->area_name,
                'payment_option'     => $record->payment_option,
                'customer_pay'     => $record->total_rate,
                'restaurant_pay'   => number_format((float)$restaurant_pay, 2),
                'hand_cash'        => number_format((float)$hand_cash, 2),
                'rider_earning'    => 'N/A',
                'cart_update'               => $record->prev_total ? '<nobr>Previous : ' . number_format((float) $record->prev_total, 2) . '</nobr><br><nobr>Updated : ' . number_format((float) $record->total_rate, 2) . '</nobr>' : 'N/A',
                'cart_update_difference'    =>   $record->prev_total  ? number_format((float) ($record->total_rate - $record->prev_total), 2) : 'N/A',
                'res_collectable_with_charge' => number_format((float)$res_collectable_with_charge, 2),
                // 'rider_ening'    => $delivery_charge,
                'res_collectable'    => number_format((float)$res_collectable_without_charge, 2, '.', ''),

            );
        }
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }
    function allAccount_pdf()
    {
        ## Fetch records
        $this->db->select('zone.area_name,order_master.payment_option,users.entity_id as driver_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*,order_status.order_id as osid,order_status.*,restaurant.*,order_detail.user_detail');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'order_master.zone_id= zone.entity_id');
        $this->db->join('city', 'city.id= zone.city_id');
        // $this->db->join('rider_information', 'rider_information.rider_id= order_driver_map.driver_id');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_status.order_status', 'delivered');
        $this->db->order_by('order_master.entity_id', 'DESC');
        $this->db->group_by('order_master.entity_id');
        // if ($searchQuery != '')
        //     $this->db->where($searchQuery);
        // $this->db->order_by($columnName, $columnSortOrder);
        // $this->db->limit($rowperpage, $start);
        return $this->db->get('order_master');
        // $records = $this->db->get('order_master')->result();

    }
    function allRider_pdf($entity_id, $to_date, $from_date)
    {
        $this->db->select('order_driver_map.driver_id as driver_id,order_detail.user_detail,users.entity_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*,order_status.order_id as osid,order_status.*,restaurant.*');
        $this->db->from('order_master');
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_status.order_status', 'delivered');

        $this->db->group_by('order_master.entity_id');

        if (!empty($entity_id)) {
            $this->db->where('users.entity_id', $entity_id);
        }
        if (!empty($from_date)) {
            $this->db->where('DATE(order_date) >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('DATE(order_date) <=', $to_date);
        }

        if ($order_status) {
            $this->db->where('order_status', $order_status);
        }

        $this->db->order_by('order_status.time', 'DESC');

        return $this->db->get();
    }

    function riderSalary_pdf($rider_id, $zone_id, $city_id, $to_date, $from_date)
    {
        $this->load->model(ADMIN_URL . '/sub_dashboard_model');


        if ($rider_id) {
            $this->db->where('u.entity_id', $rider_id);
        }

        if ($city_id) {
            $this->db->where('u.city_id', $city_id);
        }

        if ($zone_id) {
            $this->db->where('u.zone_id', $zone_id);
        }

        $this->db->select('u.entity_id as rider_id, u.first_name,u.mobile_number, ri.bkash_no, ri.nagad_no, v.name as vehicle_name, z.area_name');
        $this->db->join('rider_information ri', 'ri.rider_id = u.entity_id', 'left');
        $this->db->join('vehicle_type v', 'v.entity_id = ri.v_type', 'left');
        $this->db->join('zone z', 'z.entity_id = u.zone_id', 'left');
        $this->db->where('u.user_type', 'Driver');
        $this->db->from('users u');

        $this->db->order_by('u.entity_id', 'DESC');
        $this->db->group_by('u.entity_id');

        $records = $this->db->get('order_master')->result();
        $data = array();

        foreach ($records as $key => $record) {


            $data[] = array(

                'sl'               => ++$key,
                'rider_id'               => $record->rider_id,
                'first_name'       => $record->first_name,
                'mobile_number'    => $record->mobile_number,
                'bkash_no'             => $record->bkash_no,
                'nagad_no'             => $record->nagad_no,
                'total_attendance'             => $this->sub_dashboard_model->getAttendance($record->rider_id, $from_date, $to_date),
                'zone' => $record->area_name,
                'total_working_hours'   => 'N/A',
                'total_order'        => $this->sub_dashboard_model->average_delivery_time(null, $record->rider_id, null, $from_date, $to_date)['total_deliverd_order'],
                'vehicle'    => $record->vehicle_name,
            );
        }
        return $data;
    }

    function alldeliveredOrder_pdf($entity_id, $order_status, $to_date, $from_date)
    {
        $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id,restaurant.name,order_driver_map.order_id as oid,order_driver_map.driver_id,order_status.*');
        $this->db->from('order_master');
        $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
        $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id', 'left');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_status.order_status', 'delivered');



        if (!empty($entity_id)) {
            $this->db->where('restaurant_id', $entity_id);
        }
        if (!empty($from_date)) {
            $this->db->where('DATE(order_date) >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('DATE(order_date) <=', $to_date);
        }

        if ($order_status) {
            $this->db->where('order_status', $order_status);
        }

        $this->db->order_by('order_master.entity_id', 'DESC');

        return $this->db->get();
    }

    function cusOrder_pdf($entity_id, $to_date, $from_date)
    {
        $this->db->select('users.entity_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*');
        $this->db->from('order_master');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');


        if (!empty($entity_id)) {
            $this->db->where('users.entity_id', $entity_id);
        }
        if (!empty($from_date)) {
            $this->db->where('DATE(order_date) >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('DATE(order_date) <=', $to_date);
        }

        if ($order_status) {
            $this->db->where('order_status', $order_status);
        }

        $this->db->order_by('accept_order_time', 'DESC');

        return $this->db->get();
    }

    function resOrder_pdf($entity_id, $to_date, $from_date)
    {
        $this->db->select('restaurant.entity_id,restaurant.name,order_master.entity_id as e_id,order_master.order_date,order_master.subtotal,order_master.delivery_charge,order_master.order_status,order_master.sd,order_master.vat');
        $this->db->from('order_master');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');


        if (!empty($entity_id)) {
            $this->db->where('restaurant.entity_id', $entity_id);
        }
        if (!empty($from_date)) {
            $this->db->where('DATE(order_date) >=', $from_date);
        }
        if (!empty($to_date)) {
            $this->db->where('DATE(order_date) <=', $to_date);
        }


        $this->db->order_by('accept_order_time', 'DESC');

        return $this->db->get();
    }



    function fetchdelivered($Fdate, $Tdate, $restaurent)
    {
        $this->db->select('o.entity_id,order.user_detail,order.item_detail,o.subtotal,o.order_date,o.delivery_charge,o.coupon_discount,o.vat,o.sd,o.total_rate,');
        $this->db->join('order_detail as order', 'order.order_id = o.entity_id', 'left');
        $this->db->where('o.order_date >=', $Fdate);
        $this->db->where('o.order_date <=', $Tdate);
        $this->db->where('o.order_status="delivered"');
        $this->db->where('o.restaurant_id', $restaurent);
        return $this->db->get('order_master as o');

        // $query="select * from  order_master  WHERE order_date BETWEEN $Fdate AND $Tdate AND  restaurant_id=$Gjc"


        // return $this->db->query('select * from  order_master where BETWEEN '.$Fdate.' AND '.$Tdate.'restaurant_id='.$Gjc.'');


    }

    function fetchExportData($Fdate, $Tdate, $restaurent)
    {
        $this->db->select('o.entity_id,order.user_detail,order.item_detail,o.subtotal,o.order_date,o.delivery_charge,o.coupon_discount,o.vat,o.sd,o.total_rate,');
        $this->db->join('order_detail as order', 'order.order_id = o.entity_id', 'left');
        $this->db->where('o.order_date >=', $Fdate);
        $this->db->where('o.order_date <=', $Tdate);
        $this->db->where('o.order_status="cancel"');
        $this->db->where('o.restaurant_id', $restaurent);
        return $this->db->get('order_master as o');
    }

    public function getAllOrderList($postData = null)
    {

        $response = array();
        if ($postData['searchFromDate'] != '' && $postData['searchToDate'] != '') {

            ## Read value
            $draw = $postData['draw'];
            $start = $postData['start'];
            $rowperpage = $postData['length']; // Rows display per page
            $columnIndex = $postData['order'][0]['column']; // Column index
            $columnName = $postData['columns'][$columnIndex]['data']; // Column name
            $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
            $searchValue = $postData['search']['value']; // Search value

            // Custom search filter
            $searchTypes = $postData['searchTypes'];
            $searchRestaurant = $postData['searchRestaurant'];
            $searchFromDate = $postData['searchFromDate'];
            $searchToDate = $postData['searchToDate'];
            $zone_id = $postData['searchZone'];



            ## Search
            $search_arr = array();
            $searchQuery = "";
            if ($searchValue != '') {
                $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or users.mobile_number like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or u.first_name like '%" . $searchValue . "%' or
         restaurant.name like '%" . $searchValue . "%' or order_master.delivery_charge like'%" . $searchValue . "%' ) ";
            }

            if ($searchTypes != '') {
                $search_arr[] = "order_master.order_status='" . $searchTypes . "' ";
            }
            if ($searchRestaurant != '') {
                $search_arr[] = "restaurant.entity_id='" . $searchRestaurant . "' ";
            }
            if ($searchFromDate != '') {
                $search_arr[] = "DATE(order_master.order_date) >='" . $searchFromDate . "'";
            }
            if ($searchToDate != '') {
                $search_arr[] = "DATE(order_master.order_date) <='" . $searchToDate . "'";
            }

            if ($zone_id != '') {
                $search_arr[] = "order_master.zone_id='" . $zone_id . "' ";
            }


            if (count($search_arr) > 0) {
                $searchQuery = implode(" and ", $search_arr);
            }

            ## Total number of records without filtering

            $this->db->from('order_master');
            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id, restaurant.name,order_driver_map.order_id,order_driver_map.driver_id');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id','left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');

            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $records = $this->db->count_all_results();
            $totalRecords = $records;
            // echo "<pre>";
            //  print_r($totalRecords);
            //  exit();

            ## Total number of record with filtering
            $this->db->from('order_master');
            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id, restaurant.name,order_driver_map.order_id,order_driver_map.driver_id');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id','left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            // $this->db->or_where('order_status.order_status','delivered');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');

            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $records = $this->db->count_all_results();
            $totalRecordwithFilter = $records;

            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.*,order_master.entity_id as e_id,restaurant.entity_id as res_id, restaurant.name,order_driver_map.order_id,order_driver_map.driver_id');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id','left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            // $this->db->or_where('order_status.order_status','delivered');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');

            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }

            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $this->db->order_by($columnName, $columnSortOrder);
            $this->db->limit($rowperpage, $start);
            $records = $this->db->get('order_master')->result();

            $data = array();

            foreach ($records as $key => $record) {
                $food_bill = $record->subtotal;
                $resto_pay = $record->subtotal + $record->vat + $record->sd - $record->commission_value;
                if (!empty($record->r_name)) {
                    $rider_name = $record->r_name;
                } else {
                    $rider_name = "Not assigned by system admin";
                }


                $data[] = array(
                    'sl'               => ++$key,
                    'e_id'             => $record->e_id,
                    'order_date'       => date("d-m-Y H:i:s", strtotime($record->order_date)),
                    'first_name'       => $record->first_name,
                    'name'             => $record->name,
                    'r_name'           => $rider_name,
                    'food_bill'        => number_format((float)$food_bill, 2),
                    'vat'              => number_format((float)$record->vat, 2),
                    'sd'               => number_format((float)$record->sd, 2),
                    'resto_pay'        => number_format((float)$resto_pay, 2),
                    'delivery_charge'  => number_format((float)$record->delivery_charge, 2),
                    'coupon_discount'  => number_format((float)$record->coupon_discount, 2),
                    'customer_pay'     => number_format((float)$record->total_rate, 2),
                    'order_status'     => strtoupper($record->order_status),


                );
            }

            ## Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordwithFilter,
                "iTotalDisplayRecords" => $totalRecords,
                "aaData" => $data
            );

            return $response;
        } else {
            $response = array(
                "draw" => intval($postData['draw']),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );

            return $response;
        }
    }

    public function getItemsFromOrders($order_details)
    {

        foreach ($order_details as $order) {
            $order->user =  unserialize($order->user);
            $order->item =  unserialize($order->item);
            $order->res =  unserialize($order->res);
        }

        $ordered_items = array();

        foreach ($order_details as $order) {

            $user_detail = $order->user;

            $i = 0;
            foreach ($order->item as $val) {
                if ($val['is_customize'] == 1) {

                    for ($i = 0; $i < sizeof($val['addons_category_list']); $i++) {
                        $val['addons_category_list'][$i]['addons_category_count'] = 1;

                        for ($j = 0; $j < sizeof($val['addons_category_list'][$i]['addons_list']); $j++) {
                            $val['addons_category_list'][$i]['addons_list'][$j]['add_ons_count'] = 1;
                        }
                    }
                }
                $val['user_details'] = $user_detail;
                $val['user_details']['user_phone_number'] = $order->user_phone_number;
                $ordered_items[] = $val;
            }
        }

        $item_ids = array_column($ordered_items, 'item_id');
        array_multisort($item_ids, SORT_ASC, $ordered_items);

        $items = array(array());

        $items[0] = $ordered_items[0];

        $j = 0;

        for ($i = 1; $i < sizeof($ordered_items); $i++) {

            if ($items[$j]['item_id'] ==  $ordered_items[$i]['item_id']) {

                if ($items[$j]['is_customize'] == 1) {


                    for ($ii = 0; $ii < sizeof($ordered_items[$i]['addons_category_list']); $ii++) {

                        $addonsCategoryNotMatched = true;
                        for ($jj = 0; $jj < sizeof($items[$j]['addons_category_list']); $jj++) {

                            if ($items[$j]['addons_category_list'][$jj]['addons_category_id'] == $ordered_items[$i]['addons_category_list'][$ii]['addons_category_id']) {
                                $addonsCategoryNotMatched = false;
                                $items[$j]['addons_category_list'][$jj]['addons_category_count']++;

                                for ($iii = 0; $iii < sizeof($ordered_items[$i]['addons_category_list'][$ii]['addons_list']); $iii++) {

                                    $addonNotMatched = true;
                                    for ($jjj = 0; $jjj < sizeof($items[$j]['addons_category_list'][$jj]['addons_list']); $jjj++) {

                                        if ($items[$j]['addons_category_list'][$jj]['addons_list'][$jjj]['add_ons_id'] == $ordered_items[$i]['addons_category_list'][$ii]['addons_list'][$iii]['add_ons_id']) {
                                            $addonNotMatched = false;
                                            $items[$j]['addons_category_list'][$jj]['addons_list'][$jjj]['add_ons_count']++;
                                            break;
                                        }
                                    }

                                    if ($addonNotMatched) {
                                        $items[$j]['addons_category_list'][$jj]['addons_list'][] = $ordered_items[$i]['addons_category_list'][$ii]['addons_list'][$iii];
                                    }
                                }
                            }
                        }
                        if ($addonsCategoryNotMatched) {
                            $items[$j]['addons_category_list'][] = $ordered_items[$i]['addons_category_list'][$ii];
                        }
                    }
                }


                $items[$j]['qty_no'] += $ordered_items[$i]['qty_no'];
                $items[$j]['itemTotal'] += $ordered_items[$i]['itemTotal'];
            } else {
                $j++;
                $items[$j] = $ordered_items[$i];
            }
        }

        return $items;
    }

    public function getAllItems_resWise($restaurent, $Fdate, $Tdate)
    {
        $this->db->select('order_id, user_detail as user, restaurant_detail as res, item_detail as item, u.mobile_number as user_phone_number');
        $this->db->join('order_master as o_m', 'o_d.order_id = o_m.entity_id', 'left');
        $this->db->join('users as u', 'o_m.user_id = u.entity_id', 'left');
        $this->db->where('o_m.order_date >=', $Fdate);
        $this->db->where('o_m.order_date <=', $Tdate);
        $this->db->where('o_m.order_status="delivered"');
        if (!empty($restaurent)) {
            $this->db->where('o_m.restaurant_id', $restaurent);
        }

        $order_details = $this->db->get('order_detail as o_d')->result();
        return $this->getItemsFromOrders($order_details);
    }

    public function getAllItems_userWise($restaurent, $user, $Fdate, $Tdate)
    {
        $this->db->select('order_id, user_detail as user, restaurant_detail as res, item_detail as item, u.mobile_number as user_phone_number');
        $this->db->join('order_master as o_m', 'o_d.order_id = o_m.entity_id', 'left');
        $this->db->join('users as u', 'o_m.user_id = u.entity_id', 'left');
        $this->db->where('o_m.order_date >=', $Fdate);
        $this->db->where('o_m.order_date <=', $Tdate);
        $this->db->where('o_m.order_status="delivered"');
        if (!empty($restaurent)) {
            $this->db->where('o_m.restaurant_id', $restaurent);
        }
        if (!empty($user)) {
            $this->db->where('o_m.user_id', $user);
        }

        $order_details = $this->db->get('order_detail as o_d')->result();
        return $this->getItemsFromOrders($order_details);
    }

    public function getAllRiderList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchRider = $postData['searchRider'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];
        $searchcity = $postData['searchcity'];
        $searchzone = $postData['searchzone'];
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' ) ";
        }
        if ($searchRider != '') {
            $search_arr[] = "users.entity_id='" . $searchRider . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "order_status.time >='" . date("Y-m-d 00:00:00", strtotime($searchFromDate)) . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "order_status.time <='" . date("Y-m-d 23:59:59", strtotime($searchToDate)) . "'";
        }
        if ($searchcity != '') {
            $search_arr[] = "users.city_id='" . $searchcity . "' ";
        }
        if ($searchzone != '') {
            $search_arr[] = "order_master.zone_id='" . $searchzone . "' ";
        }
        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->from('order_master');
        $this->db->select('users.entity_id as uid');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('order_driver_map', 'order_driver_map.order_id = order_master.entity_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'zone.entity_id= order_master.zone_id', 'left');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_master.order_status', 'delivered');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->from('order_master');
        $this->db->select('users.entity_id as uid');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('(
            SELECT    MAX(driver_map_id) max_id, order_id
            FROM      order_driver_map
            GROUP BY  order_id
        ) max_driver',  '(max_driver.order_id = order_master.entity_id)', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('order_driver_map', 'max_driver.max_id = order_driver_map.driver_map_id', 'left');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'zone.entity_id= order_master.zone_id', 'left');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_master.order_status', 'delivered');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        // if ($searchCity != '') {
        // $this->db->where('users.city_id', $searchCity);
        // }
        // if ($searchZone != '') {
        //     $this->db->where('users.zone_id', $searchZone);
        // }
        $records = $this->db->count_all_results();

        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select(
            '
        users.entity_id as uid,
        order_master.payment_option,
        order_driver_map.driver_id as driver_id,
        zone.area_name, users.first_name,
        users.mobile_number,
        order_master.entity_id as e_id,

        order_master.vat as order_vat,
        order_master.sd as order_sd,
        order_status.order_id as osid,
        order_master.total_rate as total_rate,
        order_master.subtotal as subtotal,
        order_master.commission_value,
        order_status.*,
        restaurant.*,
        order_detail.user_detail'
        );
        $this->db->join('(
            SELECT    MAX(driver_map_id) max_id, order_id
            FROM      order_driver_map
            GROUP BY  order_id
        ) max_driver',  '(max_driver.order_id = order_master.entity_id)', 'left');
        $this->db->join('order_driver_map', 'max_driver.max_id = order_driver_map.driver_map_id', 'left');
        $this->db->join('order_status', 'order_master.entity_id= order_status.order_id');
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->join('users', 'order_driver_map.driver_id= users.entity_id', 'left');
        $this->db->join('order_detail', 'order_master.entity_id= order_detail.order_id');
        $this->db->join('zone', 'zone.entity_id= order_master.zone_id', 'left');
        $this->db->where('order_driver_map.is_accept=1');
        $this->db->where('order_master.order_status', 'delivered');
        $this->db->order_by('order_status.time', 'DESC');
        $this->db->group_by('order_master.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('order_master')->result();
        $data = array();
        // echo "<pre>";
        // print_r($records);
        // exit();

        foreach ($records as $key => $record) {
            $driver_id = $record->driver_id;
            $vehicle_rate = $this->get_rate($driver_id);
            $restaurant_pay = $record->subtotal + $record->order_vat + $record->order_sd - $record->commission_value;
            $hand_cash = $record->total_rate - $restaurant_pay;
            $rider_payable = $hand_cash - $vehicle_rate[0]->price;
            $customer_address = unserialize($record->user_detail);

            $data[] = array(
                'sl'               => ++$key,
                'first_name'       => $record->first_name,
                'mobile_number'    => $record->mobile_number,
                'area_name'    => $record->area_name,
                'e_id'             => $record->e_id,
                'time'             => $record->time,
                'name'             => $record->name,
                'pay_type'         => $record->payment_option,
                'customer_address' => $customer_address['address'] . ', ' . $customer_address['landmark'] . ', ' . $customer_address['zipcode'] . ', ' . $customer_address['city'],
                'customer_pay'     => number_format((float)$record->total_rate, 2),
                'actual_price'   => $record->subtotal,
                'restaurant_pay'   => number_format((float)$restaurant_pay, 2),
                'hand_cash'        => number_format((float)$hand_cash, 2),
                'rider_earning'    => number_format($vehicle_rate[0]->price, 2),
                // 'rider_earning'    => $record->delivery_charge,
                'rider_payable'    => number_format((float)$rider_payable, 2,),

            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }
    public function getAllModifiedMenu($postData = null)
    {
        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $restaurant_id = $this->input->post('restaurant_id', TRUE);
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (restaurant_menu_item.name like '%" . $searchValue . "%' or restaurant_menu_item.entity_id like '%" . $searchValue . "%' or
         restaurant.name like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering

        $this->db->select('count(*) as allcount');
        $this->db->select('restaurant_menu_item.name as menu_name,restaurant.name as res_name');
        $this->db->join('restaurant_menu_item', 'restaurant_menu_item.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('restaurant_menu_item.status = 0');
        $this->db->where('restaurant_menu_item.need_modification = 1');
        if ($restaurant_id != '') {
            $this->db->where('restaurant.entity_id', $restaurant_id);
        }
        if ($searchQuery != '')
            $this->db->where($searchQuery);

        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
        }
        if ($this->session->userdata('UserType') == 'ZonalAdmin') {
            $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
        }

        if ($this->session->userdata('UserType') == 'CentralAdmin') {
            $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
            $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
        }

        $records = $this->db->get('restaurant')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->select('restaurant_menu_item.name as menu_name,restaurant.name as res_name');
        $this->db->join('restaurant_menu_item', 'restaurant_menu_item.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('restaurant_menu_item.status = 0');
        $this->db->where('restaurant_menu_item.need_modification = 1');
        if ($restaurant_id != '') {
            $this->db->where('restaurant.entity_id', $restaurant_id);
        }
        if ($searchQuery != '')
            $this->db->where($searchQuery);

        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
        }
        if ($this->session->userdata('UserType') == 'ZonalAdmin') {
            $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
        }

        if ($this->session->userdata('UserType') == 'CentralAdmin') {
            $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
            $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
        }
        $records = $this->db->get('restaurant')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('restaurant_menu_item.entity_id as id,restaurant_menu_item.name as menu_name,restaurant.name as res_name');
        $this->db->join('restaurant_menu_item', 'restaurant_menu_item.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('restaurant_menu_item.status = 0');
        $this->db->where('restaurant_menu_item.need_modification = 1');
        if ($restaurant_id != '') {
            $this->db->where('restaurant.entity_id', $restaurant_id);
        }
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);

        if (!($this->lpermission->method('full_menu_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
            }
        }

        $records = $this->db->get('restaurant')->result();
        $Languages = $this->common_model->getLanguages();
        $data = array();

        foreach ($records as $key => $record) {
            foreach ($Languages as $lang) {
                $edit = '<a href="' . base_url() . ADMIN_URL . '/' . "Restaurant" . '/edit_menu/'  . $lang->language_slug . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($record->id)) . '" title="' . $this->lang->line('click_edit') . '"><i class="fa fa-edit"></i> </a>';

                $data[] = array(
                    'sl'               => ++$key,
                    'res_name'       => $record->res_name,
                    'menu_name'    => $record->menu_name,
                    'edit'    => $edit,

                );
            }
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }


    public function getRiderSalary($postData = null)
    {
        $this->load->model(ADMIN_URL . '/sub_dashboard_model');
        $response = array();
        $city_id = $this->input->post('city_id', TRUE);
        $zone_id = $this->input->post('zone_id', TRUE);
        $from_date = $this->input->post('from_date', TRUE);
        $to_date = $this->input->post('to_date', TRUE);
        $rider_id = $this->input->post('rider_id', TRUE);
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchRider = $postData['searchRider'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];

        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (u.first_name like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' or u.entity_id like '%" . $searchValue . "%' ) ";
        }
        if ($searchRider != '') {
            $search_arr[] = "u.entity_id='" . $searchRider . "' ";
        }

        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Fetch records

        if ($rider_id) {
            $this->db->where('u.entity_id', $rider_id);
        }

        if ($city_id) {
            $this->db->where('u.city_id', $city_id);
        }

        if ($zone_id) {
            $this->db->where('u.zone_id', $zone_id);
        }

        $this->db->select('u.entity_id as rider_id,v.price as vehicle_price,u.first_name,u.mobile_number, ri.bkash_no, ri.nagad_no, v.name as vehicle_name, z.area_name');
        $this->db->join('rider_information ri', 'ri.rider_id = u.entity_id', 'left');
        $this->db->join('vehicle_type v', 'v.entity_id = ri.v_type', 'left');
        $this->db->join('zone z', 'z.entity_id = u.zone_id', 'left');
        $this->db->where('u.user_type', 'Driver');
        $this->db->from('users u');

        $this->db->order_by('u.entity_id', 'DESC');
        $this->db->group_by('u.entity_id');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('order_master')->result();
        $data = array();

        foreach ($records as $key => $record) {


            $data[] = array(
                'sl'               => ++$key,
                'rider_id'               => $record->rider_id,
                'first_name'       => $record->first_name,
                'mobile_number'    => $record->mobile_number,
                'bkash_no'             => $record->bkash_no,
                'nagad_no'             => $record->nagad_no,
                'total_attendance'             => $this->sub_dashboard_model->getAttendance($record->rider_id, $from_date, $to_date),
                'zone' => $record->area_name,
                'total_working_hours'   => 'N/A',
                'total_order'        => $this->sub_dashboard_model->average_delivery_time(null, $record->rider_id, null, $from_date, $to_date)['total_deliverd_order'],
                'vehicle'    => $record->vehicle_name,
                'vehicle_price' => $record->vehicle_price
            );
        }
        $totalRecords = $this->db->count_all_results();

        $totalRecordwithFilter = $totalRecords;

        ## Response
        $response = array(
            "draw" => intval($draw),
            "aaData" => $data
        );

        return $response;
    }


    public function getAllDelieredList($postData = null)
    {

        $response = array();

        if ($postData['searchFromDate'] != '' && $postData['searchToDate'] != '') {

            ## Read value
            $draw = $postData['draw'];
            $start = $postData['start'];
            $rowperpage = $postData['length']; // Rows display per page
            $columnIndex = $postData['order'][0]['column']; // Column index
            $columnName = $postData['columns'][$columnIndex]['data']; // Column name
            $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
            $searchValue = $postData['search']['value']; // Search value

            // Custom search filter
            $searchTypes = $postData['searchTypes'];
            $searchRestaurant = $postData['searchRestaurant'];
            $searchFromDate = $postData['searchFromDate'];
            $searchToDate = $postData['searchToDate'];

            ## Search
            $search_arr = array();
            $searchQuery = "";
            if ($searchValue != '') {
                $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or users.mobile_number like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or u.first_name like '%" . $searchValue . "%' or
         restaurant.name like '%" . $searchValue . "%' or order_master.delivery_charge like'%" . $searchValue . "%' ) ";
            }

            if ($searchRestaurant != '') {
                $search_arr[] = "restaurant.entity_id='" . $searchRestaurant . "' ";
            }
            if ($searchFromDate != '') {
                $search_arr[] = "DATE(order_master.order_date) >='" . $searchFromDate . "'";
            }
            if ($searchToDate != '') {
                $search_arr[] = "DATE(order_master.order_date) <='" . $searchToDate . "'";
            }

            if (count($search_arr) > 0) {
                $searchQuery = implode(" and ", $search_arr);
            }

            ## Total number of records without filtering

            $this->db->from('order_master');

            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id,restaurant.name,order_driver_map.order_id as oid,order_driver_map.driver_id');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            $this->db->join('order_status', 'order_master.entity_id= order_status.order_id', 'left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            $this->db->where('order_status.order_status', 'delivered');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');


            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $records = $this->db->count_all_results();
            $totalRecords = $records;

            ## Total number of record with filtering
            $this->db->from('order_master');
            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id,restaurant.name,order_driver_map.order_id as oid,order_driver_map.driver_id');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            $this->db->join('order_status', 'order_master.entity_id= order_status.order_id', 'left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            $this->db->where('order_status.order_status', 'delivered');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');

            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $records = $this->db->count_all_results();
            $totalRecordwithFilter = $records;

            $this->db->select('u.entity_id as r_id,u.first_name as r_name, users.first_name,order_master.entity_id as e_id,order_master.*,restaurant.entity_id as res_id,restaurant.name,order_driver_map.order_id as oid,order_driver_map.driver_id,order_status.time as time');
            // $this->db->join('order_status','order_master.entity_id= order_status.order_id');
            $this->db->join('order_driver_map', 'order_master.entity_id=order_driver_map.order_id', 'left');
            $this->db->join('users as u', 'order_driver_map.driver_id=u.entity_id', 'left');
            $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
            $this->db->join('order_status', 'order_master.entity_id= order_status.order_id', 'left');
            $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
            $this->db->where('order_status.order_status', 'delivered');
            $this->db->order_by('order_master.entity_id', 'DESC');
            $this->db->group_by('order_master.entity_id');

            if (!($this->lpermission->method('full_report_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }

            if ($searchQuery != '')
                $this->db->where($searchQuery);
            $this->db->order_by($columnName, $columnSortOrder);
            $this->db->limit($rowperpage, $start);
            $records = $this->db->get('order_master')->result();
            // echo "<pre>";
            // print_r($records);
            // exit();

            $data = array();

            foreach ($records as $key => $record) {
                $d1 = strtotime($record->time);
                $d2 = strtotime($record->accept_order_time);
                $duration = round(abs($d1 - $d2) / 60, 2) . " m";
                $food_bill = $record->subtotal;
                $resto_pay = $record->subtotal + $record->vat + $record->sd - $record->commission_value;
                if (!empty($record->r_name)) {
                    $rider_name = $record->r_name;
                } else {
                    $rider_name = "Not assigned by system admin";
                }


                $data[] = array(
                    'sl'               => ++$key,
                    'e_id'             => $record->e_id,
                    'duration'         => $duration,
                    'order_date'       => date("d-m-Y H:i:s", strtotime($record->time)),
                    'first_name'       => $record->first_name,
                    'name'             => $record->name,
                    'r_name'           => $rider_name,
                    'food_bill'        => number_format((float)$food_bill, 2),
                    'vat'              => number_format((float)$record->vat, 2),
                    'sd'               => number_format((float)$record->sd, 2),
                    'resto_pay'        => number_format((float)$resto_pay, 2),
                    'delivery_charge'  => number_format((float)$record->delivery_charge, 2),
                    'coupon_discount'  => number_format((float)$record->coupon_discount, 2),
                    'customer_pay'     => number_format((float)$record->total_rate, 2),
                    'order_status'     => strtoupper($record->order_status),


                );
            }

            ## Response
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordwithFilter,
                "iTotalDisplayRecords" => $totalRecords,
                "aaData" => $data
            );

            return $response;
        } else {
            $response = array(
                "draw" => intval($postData['draw']),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array()
            );

            return $response;
        }
    }

    //Order Report Customer wise

    public function getCusOrderList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchCustomer = $postData['searchCustomer'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];

        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or users.mobile_number like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or order_master.subtotal like '%" . $searchValue . "%' or
         order_master.vat like '%" . $searchValue . "%' or
         order_master.delivery_charge like'%" . $searchValue . "%' ) ";
        }
        if ($searchCustomer != '') {
            $search_arr[] = "users.entity_id='" . $searchCustomer . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(order_master.accept_order_time) >='" . $searchFromDate . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(order_master.accept_order_time) <='" . $searchToDate . "'";
        }

        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->select('count(*) as allcount');
        $this->db->select('users.entity_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('order_master')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->select('users.entity_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('order_master')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        $this->db->select('users.entity_id, users.first_name,users.mobile_number,order_master.entity_id as e_id,order_master.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        $this->db->order_by('accept_order_time', 'DESC');


        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('order_master')->result();

        $data = array();

        foreach ($records as $key => $record) {


            $data[] = array(
                'sl'               => ++$key,
                'first_name'       => $record->first_name,
                'mobile_number'    => $record->mobile_number,
                'e_id'         => $record->e_id,
                'accept_order_time' => date("m-d-Y", strtotime($record->accept_order_time)),
                'subtotal'       => $record->subtotal,
                'vat'              => $record->vat,
                'sd'               => $record->sd,
                'delivery_charge'  => $record->delivery_charge,
                'discount'         => $record->coupon_discount,
                'total_rate'       => $record->total_rate,


            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    //Order Report (restaurant wise)

    public function getResOrderList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchRestaurant = $postData['searchRestaurant'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];

        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (restaurant.name like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%') ";
        }
        if ($searchRestaurant != '') {
            $search_arr[] = "restaurant.entity_id='" . $searchRestaurant . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(order_master.order_date)>='$searchFromDate'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(order_master.order_date)<='$searchToDate'";
        }

        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->select('count(*) as allcount');
        $this->db->select('restaurant.entity_id, restaurant.name,order_master.entity_id as e_id,order_master.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('order_master')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->select('restaurant.entity_id, restaurant.name,order_master.entity_id as e_id,order_master.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('order_master')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        $this->db->select('restaurant.entity_id,restaurant.name,order_master.entity_id as e_id,order_master.order_date,order_master.subtotal,order_master.delivery_charge,order_master.commission_value,order_master.order_status,order_master.sd as order_sd,order_master.vat as order_vat');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->join('restaurant', 'order_master.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('order_master.order_status', 'delivered');
        $this->db->order_by('accept_order_time', 'DESC');


        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('order_master')->result();

        $data = array();

        foreach ($records as $key => $record) {
            $restaurant_payable = $record->subtotal +  $record->order_vat +  $record->order_sd -  $record->commission_value;

            $data[] = array(
                'sl'         => ++$key,
                'name'       => $record->name,
                'entity_id'   => $record->e_id,
                'order_date' => date("m-d-Y", strtotime($record->order_date)),
                'subtotal'   => number_format((float)$record->subtotal, 2),
                'commission_value' => number_format((float)$record->commission_value, 2),
                'restaurant_payable' => number_format((float)$restaurant_payable, 2),


            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    //User Acquisition Report

    public function getAcquisitionUserList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        //$searchRestaurant = $postData['searchRestaurant'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];

        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (first_name like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%') ";
        }

        if ($searchFromDate != '') {
            $search_arr[] = "DATE(users.created_date)>='$searchFromDate'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(users.created_date)<='$searchToDate'";
        }


        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->select('count(*) as allcount');
        $this->db->select('users.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->where('user_type', 'User');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('users')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->select('users.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->where('user_type', 'User');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get('users')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        $this->db->select('users.*');
        //$this->db->join('order_master','order_master.restaurant_id =' .$restaurentId);
        $this->db->where('user_type', 'User');
        $this->db->order_by('created_date', 'dsc');

        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        // $this->db->join('user_address', 'user_address.user_entity_id=users.entity_id', 'left');
        $records = $this->db->get('users')->result();

        $this->db->select('*');
        $user_address = $this->db->get('user_address')->result();

        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $name = $record->first_name . " " . $record->last_name;
            $address = '';
            foreach ($user_address as $value) {
                if ($record->entity_id == $value->user_entity_id) {
                    $address = $value->address . '<br>' . $value->landmark . '<br>' . $value->city;
                    break;
                }
            }

            $temp = $address;

            $data[] = array(
                'sl' => $sl,
                'first_name'   => $name,
                'address'   => $address,
                'created_date' => date("d-m-Y", strtotime($record->created_date)),
                'mobile_number' => $record->mobile_number

            );
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => intval($totalRecordwithFilter),
            "iTotalDisplayRecords" => intval($totalRecords),
            "aaData" => $data
        );

        return $response;
    }


    // function fetchcancel($Fdate, $Tdate, $restaurent)
    // {
    //     $this->db->where('order_date >=', $Fdate);
    //     $this->db->where('order_date <=', $Tdate);
    //     $this->db->where('order_status="cancel"');
    //     $this->db->where('restaurant_id', $restaurent);
    //     return $this->db->get('order_master');

    //     // $query="select * from  order_master  WHERE order_date BETWEEN $Fdate AND $Tdate AND  restaurant_id=$Gjc"
    //     // return $this->db->query('select * from  order_master where BETWEEN '.$Fdate.' AND '.$Tdate.'restaurant_id='.$Gjc.'');

    // }
    function get_rate($user_id)
    {
        $this->db->select('vehicle_type.price as price');
        $this->db->join('rider_information', 'rider_information.v_type = vehicle_type.entity_id', 'left');
        $this->db->where('rider_information.rider_id', $user_id);
        return $this->db->get('vehicle_type')->result();
        //return $user_id;
    }
    public function getorderdetails($coupon_id)
    {
        // $this->db->select('*');
        // $order_details = $this->db->get_where('order_master', array('coupon_id' => $coupon_id))->first_row();
        $this->db->select('*');
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('order_status', "delivered");
        $order_details = $this->db->get('order_master')->first_row();

        return $order_details->entity_id;
    }

    function topUsers($Fdate, $Tdate)
    {
        $this->db->select('users.first_name,users.last_name,users.mobile_number,address.*,sum(o.total_rate) as total_price,count(o.entity_id) as total_order');
        $this->db->join('user_address as address', 'users.entity_id = address.user_entity_id', 'left');
        $this->db->join('user_address as add', 'address.user_entity_id = add.user_entity_id AND address.entity_id < add.entity_id', 'left'); // for getting the last address
        $this->db->join('order_master as o', 'users.entity_id = o.user_id', 'left');
        $this->db->where('add.entity_id IS NULL', null, false); //for the last address
        $this->db->where('DATE(order_date) >=', $Fdate);
        $this->db->where('DATE(order_date) <=', $Tdate);
        $this->db->where('o.order_status', 'delivered');
        $this->db->group_by('address.entity_id');
        $this->db->order_by('total_order', 'desc');
        return $this->db->get('users');
    }

    public function getUsersEarningList($postData = null)
    {
        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchUser = $postData['searchUser'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or users.entity_id like '%" . $searchValue . "%' or
            reward_point.points like '%" . $searchValue . "%' or order_master.total_rate like '%" . $searchValue . "%' or reward_point.order_id like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' ) ";
        }
        if ($searchUser != '') {
            $search_arr[] = "users.entity_id='" . $searchUser . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(reward_point.date) >='" . $searchFromDate . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(reward_point.date) <='" . $searchToDate . "'";
        }
        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->from('reward_point');
        $this->db->select('reward_point.points,reward_point.order_id,order_master.total_rate,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->join('order_master', 'order_master.entity_id= reward_point.order_id');
        $this->db->where('reward_point.cost=1');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->from('reward_point');
        $this->db->select('reward_point.points,reward_point.order_id,order_master.total_rate,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->join('order_master', 'order_master.entity_id= reward_point.order_id');
        $this->db->where('reward_point.cost=1');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select('reward_point.points,reward_point.date,reward_point.user_id,reward_point.order_id,order_master.total_rate,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->join('order_master', 'order_master.entity_id= reward_point.order_id');
        $this->db->where('reward_point.cost=1');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('reward_point')->result();
        $data = array();
        foreach ($records as $key => $record) {
            $data[] = array(
                'sl'               => ++$key,
                'user_id'       => $record->user_id,
                'name'    => $record->first_name,
                'order_id'    => $record->order_id,
                'total_rate'             => $record->total_rate,
                'points'             => $record->points,
                'date'             => $record->date,
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }
    public function getClaimVoucherList($postData = null)
    {
        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchUser = $postData['searchUser'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or users.entity_id like '%" . $searchValue . "%' or
            reward_point.points like '%" . $searchValue . "%' or reward_point.coupon_type like '%" . $searchValue . "%' or reward_point_setting.value like '%" . $searchValue . "%' or reward_point.date like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' ) ";
        }
        if ($searchUser != '') {
            $search_arr[] = "users.entity_id='" . $searchUser . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(reward_point.date) >='" . $searchFromDate . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(reward_point.date) <='" . $searchToDate . "'";
        }
        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->from('reward_point');
        $this->db->select('reward_point.points,reward_point.user_id,reward_point.coupon_type,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        // $this->db->join('reward_point_setting', 'reward_point.coupon_id= reward_point_setting.entity_id', 'left');
        $this->db->where('reward_point.cost=2');
        // $this->db->where('reward_point_setting.type is not null');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->from('reward_point');
        $this->db->select('reward_point.points,reward_point.user_id,reward_point.coupon_type,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        // $this->db->join('reward_point_setting', 'reward_point.coupon_id= reward_point_setting.entity_id', 'left');
        $this->db->where('reward_point.cost=2');
        // $this->db->where('reward_point_setting.type is not null');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select('reward_point.points,reward_point.coupon_id,reward_point.date,reward_point.user_id,reward_point.coupon_type,users.first_name,
        users.mobile_number');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        // $this->db->join('reward_point_setting', 'reward_point_setting.entity_id = reward_point.coupon_id', 'left');
        $this->db->where('reward_point.cost', 2);
        // $this->db->where('reward_point_setting.type is not null');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('reward_point')->result();
        $data = array();
        foreach ($records as $key => $record) {
            if ($record->coupon_type == "Voucher") {
                // $value = $this->report_model->getvouchervalue($record->coupon_id);
                $value = 'N\A';
            } else {
                $get_coupon_value = $this->report_model->getcouponvalue($record->coupon_id);
                $value = $get_coupon_value->amount;
            }
            $data[] = array(
                'sl'               => ++$key,
                'user_id'       => $record->user_id,
                'name'    => $record->first_name,
                'points'    => $record->points,
                'coupon_type' => $record->coupon_type,
                'value'             => $value,
                'date'             => $record->date,
            );
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }
    public function getBurnedVoucherList($postData = null)
    {
        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        // Custom search filter
        $searchRider = $postData['searchRider'];
        $searchFromDate = $postData['searchFromDate'];
        $searchToDate = $postData['searchToDate'];
        ## Search
        $search_arr = array();
        $searchQuery = "";
        if ($searchValue != '') {
            $search_arr[] = " (users.first_name like '%" . $searchValue . "%' or reward_point_setting.name like '%" . $searchValue . "%' or users.entity_id like '%" . $searchValue . "%' or order_master.entity_id like '%" . $searchValue . "%' or
         mobile_number like '%" . $searchValue . "%' ) ";
        }
        if ($searchRider != '') {
            $search_arr[] = "users.entity_id='" . $searchRider . "' ";
        }
        if ($searchFromDate != '') {
            $search_arr[] = "DATE(order_status.time) >='" . $searchFromDate . "'";
        }
        if ($searchToDate != '') {
            $search_arr[] = "DATE(order_status.time) <='" . $searchToDate . "'";
        }
        if (count($search_arr) > 0) {
            $searchQuery = implode(" and ", $search_arr);
        }

        ## Total number of records without filtering

        $this->db->from('reward_point');
        $this->db->select('reward_point.user_id,reward_point.coupon_type,users.first_name,users.mobile_number,reward_point.coupon_id');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->where('reward_point.cost=2');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->from('reward_point');
        $this->db->select('reward_point.user_id,reward_point.coupon_type,users.first_name,users.mobile_number,reward_point.coupon_id');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->where('reward_point.cost=2');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->count_all_results();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select('reward_point.user_id,reward_point.coupon_id,reward_point.coupon_type,users.first_name,users.mobile_number,reward_point.coupon_id');
        $this->db->join('users', 'reward_point.user_id= users.entity_id', 'left');
        $this->db->where('reward_point.cost=2');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('reward_point')->result();
        // echo "<pre>";
        // print_r($records);
        // exit();
        $i = 0;
        $data = array();
        foreach ($records as $key => $record) {
            if ($record->coupon_type == "Voucher") {
                $is_read = $this->report_model->getvoucherdetails($record->coupon_id);
                // echo "<pre>";
                // print_r($is_read[0]->voucher_name);
                // exit();
                if ($is_read[0]->is_read == 1) {

                    $temp = array(
                        'sl'               => ++$i,
                        'user_id'       => $record->user_id,
                        'name'    => $record->first_name . "(" . $record->mobile_number . ") ",
                        'order_id'    => 'N\A',
                        'coupon_id'    => $record->coupon_id,
                        'coupon_type' => $record->coupon_type,
                        'coupon_name' => $is_read[0]->voucher_name
                    );
                    array_push($data, $temp);
                    //       echo "<pre>";
                    // print_r($data);
                    // exit();
                }
            } else {
                $value = $this->report_model->getcouponvalue($record->coupon_id);
                $order_id = $this->report_model->getorderdetails($record->coupon_id);
                // echo "<pre>";
                //     print_r($order_id);
                //     exit();

                $coupon_name = $value->name;
                if ($order_id) {
                    $temp = array(
                        'sl'               => ++$i,
                        'user_id'       => $record->user_id,
                        'name'    => $record->first_name . "(" . $record->mobile_number . ") ",
                        'order_id'    => $order_id,
                        'coupon_id'    => $record->coupon_id,
                        'coupon_type' => $record->coupon_type,
                        'coupon_name' => $coupon_name
                    );

                    array_push($data, $temp);
                }
            }
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => count(
                $data
            ),
            "iTotalDisplayRecords" => count(
                $data
            ),
            "aaData" => $data
        );

        return $response;
    }
    public function getvoucherdetails($coupon_id)
    {
        $this->db->select('voucher_notification.is_read,reward_point_setting.name as voucher_name');
        $this->db->join('voucher_notification', 'reward_point_setting.entity_id= voucher_notification.voucher_id', 'left');
        $this->db->where('reward_point_setting.entity_id', $coupon_id);
        return $this->db->get('reward_point_setting')->result();
    }
}
