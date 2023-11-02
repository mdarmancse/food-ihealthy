<?php
class Sub_dashboard_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    //ajax view
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10, $city_id = null)
    {

        //$this->db->select('restaurant.name,zone.area_name,zone.entity_id,restaurant.currency_id');
        // $this->db->join('restaurant','delivery_charge.restaurant_id = restaurant.entity_id','left');
        $this->db->join('zone z', 'z.city_id = c.id');
        if ($city_id && $city_id != '') {
            $this->db->where('c.id', $city_id);
        }
        $result['total'] = $this->db->count_all_results('city c');

        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if ($displayLength > 1)
            $this->db->limit($displayLength, $displayStart);


        $this->db->select('c.id, z.area_name, z.entity_id as zone_id')
            ->from('city c')
            ->join('zone z', 'z.city_id = c.id');

        if ($city_id && $city_id != '') {
            $this->db->where('c.id', $city_id);
        }
        $res = $this->db->get()->result_array();

        $new_zone = array();

        foreach ($res as $zone) {
            $dat['zone_id'] = $zone['zone_id'];
            $dat['zone_name'] = $zone['area_name'];
            $dat['total_deliverd_order'] = $this->average_delivery_time($zone['zone_id'])['total_deliverd_order'];
            $dat['avg_dt'] = $this->average_delivery_time($zone['zone_id'])['avg_dt'];
            $dat['total_cancelled_order'] = $this->average_cancel_rate($zone['zone_id'])['total_cancelled_order'];
            $dat['avg_cr'] = $this->average_cancel_rate($zone['zone_id'])['avg_cr'];
            $dat['total_accepted_order'] = $this->average_accept_rate($zone['zone_id'])['total_accepted_order'];
            $dat['avg_ar'] = $this->average_accept_rate($zone['zone_id'])['avg_ar'];
            $dat['rider_cancel_order'] = $this->order_cancelled_rider_issue($zone['zone_id']);
            $dat['open_rider'] = $this->open_rider($zone['zone_id']);
            $dat['rider_1_order'] = $this->get_total_rider_with_n_order(1, $zone['zone_id']);
            $dat['rider_2_order'] = $this->get_total_rider_with_n_order(2, $zone['zone_id']);
            $dat['rider_3_order'] = $this->get_total_rider_with_n_order(3, $zone['zone_id']);

            $new_zone[] = $dat;
        }

        $result['data'] = $new_zone;

        return $result;
    }


    public function online_riders($flag = null, $city_id = null, $zone_id = null, $user_id = null, $status = null)
    {

        $currentDate = strtotime(date('Y-m-d H:i:s'));
        $futureDate = $currentDate - (60 * 20);
        $formatDate = date("Y-m-d H:i:s", $futureDate);
        $this->db->where('d.created_date >=', $formatDate);
        // $this->db->where('d.created_date <=', $currentDate);
        $this->db->select('d.onoff, d.traking_id as traking_id,u.first_name,u.last_name,u.mobile_number,u.entity_id');
        $this->db->where('d.traking_id in (SELECT MAX(dm.traking_id) from driver_traking_map as dm GROUP BY dm.driver_id ORDER BY dm.traking_id DESC)');
        $this->db->join('users as u', 'd.driver_id = u.entity_id', 'left');
        $this->db->where('u.status', 1);
        if ($city_id || $zone_id) {
            if ($city_id) {
                $this->db->where('u.city_id', $city_id);
            }
            if ($zone_id && $zone_id != '') {

                $this->db->where_in('u.zone_id', $zone_id);
            }
        }
        if ($user_id) {
            $this->db->where('d.driver_id', $user_id);
        }
        $this->db->group_by('d.driver_id');
        $res = $this->db->get('driver_traking_map as d')
            ->result_array();
        $count = 0;
        $offline = 0;
        $online_riders = array();
        foreach ($res as $r) {
            if ($r['onoff'] == 1) {
                $count++;
                array_push($online_riders, $r);
            }
        }
        //Offline Riders
        $today = date('Y-m-d 00:00:00');

        $currentDate = strtotime(date('Y-m-d H:i:s'));
        $futureDate = $currentDate - (60 * 10);
        $formatDate = date("Y-m-d H:i:s", $futureDate);

        $this->db->select('d.onoff, d.traking_id,u.first_name,u.last_name,u.mobile_number,u.entity_id');
        $this->db->join('users as u', 'd.driver_id = u.entity_id', 'left');
        $this->db->where('d.traking_id in (SELECT MAX(dm.traking_id) from driver_traking_map as dm GROUP BY dm.driver_id ORDER BY dm.traking_id DESC)');
        $this->db->where('d.created_date >', $today);
        $this->db->where('d.created_date <', $formatDate);
        $this->db->where('u.status', 1);
        if ($city_id || $zone_id) {
            if ($city_id) {
                $this->db->where('u.city_id', $city_id);
            }
            if ($zone_id && $zone_id != '') {

                $this->db->where_in('u.zone_id', $zone_id);
            }
        }
        $this->db->group_by('d.driver_id');
        $offline_riders = $this->db->get('driver_traking_map as d')
            ->result_array();


        // echo "<pre>";
        // print_r($online_riders);
        // exit();
        $offline = count($offline_riders);

        $data['online'] = $count ? $count : 0;
        $data['offline'] = $offline ?  $offline : 0;

        if ($status == 1) {
            return $online_riders;
        }
        if ($status == 2) {
            return $offline_riders;
        }
        if ($flag == 1) {
            return $data;
        } else {
            return $count;
        }
    }
    public function inactive_rider($flag = null, $zone_id = null, $city_id = null, $status = null)
    {
        $today = date('Y-m-d 00:00:00');
        //   echo "<pre>";
        // print_r($today);
        // exit();
        $this->db->where('d.created_date <', $today);
        $this->db->select('u.entity_id,MAX(d.traking_id) as traking_id,u.first_name,u.last_name,u.mobile_number');
        $this->db->join('driver_traking_map as d', 'd.driver_id = u.entity_id', 'left');
        $this->db->where('u.status', 1);

        if ($city_id) {
            $this->db->where('u.city_id', $city_id);
        }
        if ($zone_id) {

            $this->db->where_in('u.zone_id', $zone_id);
        }
        $this->db->group_by('d.driver_id');
        $res = $this->db->get('users as u')
            ->result_array();

        $count = 0;
        foreach ($res as $r) {
            $count++;
        }
        $this->db->select('count(users.entity_id) as total');
        $this->db->where('users.user_type = "Driver"');
        $this->db->where('users.status', 1);
        if ($city_id) {
            $this->db->where('users.city_id', $city_id);
        }
        if ($zone_id) {

            $this->db->where_in('users.zone_id', $zone_id);
        }
        $total = $this->db->get('users')
            ->result_array();
        if ($status == 3) {
            return $res;
        } else {
            //return $total[0]['total'] - $count;
            return $count;
        }
    }

    public function get_total_rider_with_n_order($n, $zone_id = null, $city_id = null)
    {
        if ($zone_id)
            $this->db->where_in('users.zone_id', $zone_id);
        $res = $this->db->select('users.entity_id as user_id, order_driver_map.order_id, order_status')
            ->from('users')
            ->where('users.user_type', 'Driver')
            ->where('users.status', 1)
            ->join('order_driver_map', 'order_driver_map.driver_id = users.entity_id')
            ->join('order_status', 'order_status.order_id = order_driver_map.order_id')
            ->order_by('order_status.order_id', 'desc')
            ->get()
            ->result_array();


        $count = 0;
        $num_count = 0;
        $data = array();
        foreach ($res as $r) {

            if (!isset($data[$r['user_id']])) {
                $data[$r['user_id']] = array();
            }

            if (!isset($data[$r['user_id']][$r['order_id']])) {
                $data[$r['user_id']][$r['order_id']] = array();
            }

            $data[$r['user_id']][$r['order_id']][] =  $r['order_status'];
        }

        foreach ($data as $d) {
            foreach ($d as $per_rider) {
                if ($per_rider[0] == 'preparing' || $per_rider[0] == 'onGoing') {
                    $count++;
                }
                if ($count == $n) {
                    $num_count++;
                    $count = 0;
                }
            }
        }

        return $num_count;
    }

    public function get_total_active_orders($city_id = null)
    {
        $res = $this->db->select('order_id, order_status')
            ->from('order_status')
            // ->group_by('order_id')
            ->order_by('status_id', 'desc')
            ->get()
            ->result_array();




        $count = 0;
        $data = array();
        foreach ($res as $r) {
            if (!isset($data[$r['order_id']])) {
                $data[$r['order_id']] = array();
            }

            $data[$r['order_id']][] =  $r['order_status'];
        }

        foreach ($data as $d) {
            if ($d[0] == 'preparing' || $d[0] == 'onGoing') {
                $count++;
            }
        }

        // echo '<pre>';
        // print_r($count);
        // exit();

        return $count;
    }

    public function getUnassignedOrders($from_date = null, $to_date = null, $city_id = null, $zone_id = null)
    {
        $this->db->select('order_driver_map.order_id');
        $this->db->from('order_driver_map');
        $this->db->group_start();
        $this->db->where('order_driver_map.driver_map_id in (SELECT MAX(od.driver_map_id) as dm from order_driver_map as od GROUP BY od.order_id ORDER BY od.driver_map_id DESC)');
        $this->db->where('order_driver_map.driver_id', 0);
        $this->db->group_end();


        //$this->db->group_by('odm.order_id');

        $this->db->join('order_master as om', 'om.entity_id = order_driver_map.order_id', 'left');
        $this->db->join('restaurant', 'om.restaurant_id = restaurant.entity_id', 'left');

        if (!($this->lpermission->method('full_order_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }

        if ($from_date && $from_date != '') {
            $this->db->where('om.order_date >=', $from_date);
        }
        if ($to_date && $to_date != '') {
            $this->db->where('om.order_date <=', $to_date);
        }
        if ($zone_id && $zone_id != '') {
            $this->db->where_in('om.zone_id', $zone_id);
        }
        if ($city_id && $city_id != '') {
            $this->db->join('zone as z', 'z.entity_id = om.zone_id');
            //$this->db->join('city as c', 'c.id = z.city_id');
            $this->db->where('z.city_id', $city_id);
        }

        $res = $this->db->get()->result();

        return count($res);
    }

    public function average_delivery_time($zone_id = null, $rider_id = null, $city_id = null, $from_date = null, $to_date = null)
    {
        $this->db->select('a.entity_id as orderID, a.zone_id, a.order_date, c.time')
            ->from('order_master a');

        if ($zone_id)
            $this->db->where_in('a.zone_id', $zone_id);

        if ($rider_id) {
            $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left')
                ->where('driver_id', $rider_id);
        }
        if ($from_date != '') {
            $this->db->where('a.order_date >=', $from_date);
        }
        if ($to_date != '') {
            $this->db->where('a.order_date <=', $to_date);
        }
        if ($city_id != '') {
            $this->db->join('zone as z', 'z.entity_id = a.zone_id');
            $this->db->join('city as f', 'f.id = z.city_id', 'left');
            $this->db->where('f.id', $city_id);
        }

        $this->db->join('restaurant', 'a.restaurant_id = restaurant.entity_id', 'left');

        if (!($this->lpermission->method('full_order_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }

        $this->db->join('order_status c', 'c.order_id = a.entity_id')
            ->where('c.order_status', 'delivered');

        $this->db->group_by('a.entity_id');

        $res = $this->db->get()->result_array();



        $order_count = 0;
        $total_time = 0;
        $data = array();

        foreach ($res as $r) {
            $order_count++;
            if (!isset($data[$r['orderID']])) {
                $data[$r['orderID']] =  array(
                    'order_time' => $r['order_date'],
                    'delivered_time' => $r['time'],
                );
            }
        }


        foreach ($data as $d) {

            $time_diff = 0;
            $time_diff = strtotime($d['delivered_time']) - strtotime($d['order_time']);
            // echo '<pre>';
            // print_r($time_diff);

            $total_time += (($time_diff) / 60);
        }

        $avg_dt =  $order_count ? round($total_time / $order_count) : $order_count;

        $ret = array(
            'total_deliverd_order'  => $order_count,
            'avg_dt'                => $avg_dt
        );

        return $ret;
    }

    public function average_cancel_rate($zone_id = null, $rider_id = null, $city_id = null, $from_date = null, $to_date = null)
    {
        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id)
            $this->db->where_in('a.zone_id', $zone_id);

        if ($rider_id) {
            $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left')
                ->where('driver_id', $rider_id);
        }
        if ($city_id) {
            $this->db->join('zone as z', 'z.entity_id = a.zone_id', 'left');
            $this->db->join('city as f', 'f.id = z.city_id', 'left');
            $this->db->where('f.id', $city_id);
        }
        if ($from_date != '') {
            $this->db->where('a.created_date >=', $from_date);
        }
        if ($to_date != '') {
            $this->db->where('a.created_date <=', $to_date);
        }

        $this->db->join('restaurant', 'a.restaurant_id = restaurant.entity_id', 'left');

        if (!($this->lpermission->method('full_order_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }

        $this->db->join('order_status c', 'c.order_id = a.entity_id', 'left')
            ->where('c.order_status', 'cancel');

        $total_cancelled_order = $this->db->count_all_results();

        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id)
            $this->db->where_in('a.zone_id', $zone_id);

        if ($rider_id) {
            $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left')
                ->where('driver_id', $rider_id);
        }

        $total_order = $this->db->count_all_results();




        $avg_cr = $total_order > 0 ? ($total_cancelled_order / $total_order) * 100 : $total_order;

        $ret = array(
            'total_cancelled_order'  => $total_cancelled_order,
            'avg_cr'                => round($avg_cr, 2)
        );
        return $ret;
    }

    public function average_accept_rate($zone_id = null, $rider_id = null, $city_id = null, $from_date = null, $to_date = null)
    {
        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id && $zone_id != '')
            $this->db->where_in('a.zone_id', $zone_id);

        if ($city_id && $city_id != '') {
            $this->db->join('zone as z', 'z.entity_id = a.zone_id');
            $this->db->where('z.city_id', $city_id);
        }

        if ($rider_id && $rider_id != '') {
            $this->db->join('order_driver_map b', 'b.order_id = a.entity_id')
                ->where('driver_id', $rider_id);
        }

        if ($from_date && $from_date != '') {
            $this->db->where('a.accept_order_time >=', $from_date);
        }
        if ($to_date && $to_date != '') {
            $this->db->where('a.accept_order_time <=', $to_date);
        }

        $this->db->join('restaurant', 'a.restaurant_id = restaurant.entity_id');

        if (!($this->lpermission->method('full_order_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }

        $this->db->join('order_status c', 'c.order_id = a.entity_id')
            ->where('c.order_status', 'accepted_by_restaurant');

        $this->db->group_by('a.entity_id');

        $total_accepted_order = $this->db->count_all_results();

        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id && $zone_id != '')
            $this->db->where_in('a.zone_id', $zone_id);

        if ($rider_id && $rider_id != '') {
            $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left')
                ->where('driver_id', $rider_id);
        }
        if ($city_id && $city_id != '') {
            $this->db->join('zone as z', 'z.entity_id = a.zone_id');
            $this->db->join('city as f', 'f.id = z.city_id', 'left');
            $this->db->where('f.id', $city_id);
        }
        if ($from_date && $from_date != '') {
            $this->db->where('a.accept_order_time >=', $from_date);
        }
        if ($to_date && $to_date != '') {
            $this->db->where('a.accept_order_time <=', $to_date);
        }

        $total_order = $this->db->count_all_results();




        $avg_ar = $total_order > 0 ? ($total_accepted_order / $total_order) * 100 : $total_order;

        $ret = array(
            'total_accepted_order'  => $total_accepted_order,
            'avg_ar'                => round($avg_ar, 2)
        );


        return $ret;
    }

    public function order_cancelled_rider_issue($zone_id = null, $rider_id = null)
    {
        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id)
            $this->db->where_in('a.zone_id', $zone_id);

        if ($rider_id) {
            $this->db->where('b.driver_id', $rider_id);
        }

        $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left')
            ->where('b.cancel', 1);



        $total_rider_cancelled_order = $this->db->count_all_results();

        return $total_rider_cancelled_order;
    }

    public function total_unassigned_order($zone_id = null)
    {
        $this->db->select('a.entity_id')
            ->from('order_master a');

        if ($zone_id)
            $this->db->where_in('a.zone_id', $zone_id);

        $this->db->join('order_driver_map b', 'b.order_id = a.entity_id', 'left');

        $this->db->where('a.entity_id is null');

        $total_unassigned_order = $this->db->count_all_results();

        return $total_unassigned_order;
    }

    public function open_rider($zone_id = null)
    {
        if ($zone_id)
            $this->db->where_in('users.zone_id', $zone_id);
        $res = $this->db->select('users.entity_id as user_id, order_driver_map.order_id, order_status')
            ->from('users')
            ->where('users.user_type', 'Driver')
            ->where('users.status', 1)
            ->join('order_driver_map', 'order_driver_map.driver_id = users.entity_id')
            ->join('order_status', 'order_status.order_id = order_driver_map.order_id')
            ->order_by('order_status.order_id', 'desc')
            ->get()
            ->result_array();


        $count = 0;
        $num_count = 0;
        $data = array();
        foreach ($res as $r) {

            if (!isset($data[$r['user_id']])) {
                $data[$r['user_id']] = array();
            }

            if (!isset($data[$r['user_id']][$r['order_id']])) {
                $data[$r['user_id']][$r['order_id']] = array();
            }

            $data[$r['user_id']][$r['order_id']][] =  $r['order_status'];
        }

        // echo '<pre>';
        // print_r($data);
        // exit();

        foreach ($data as $d) {
            foreach ($d as $per_rider) {
                if ($per_rider[0] == 'preparing' || $per_rider[0] == 'onGoing') {
                    $count++;
                }
            }
            if ($count != 0) {
                $num_count++;
            }
        }

        // exit();

        if ($zone_id)
            $this->db->where_in('users.zone_id', $zone_id);

        $not_assigned_ever = $this->db->select('users.entity_id')
            ->from('users')
            ->join('order_driver_map', 'order_driver_map.driver_id = users.entity_id', 'left')
            ->where('order_driver_map.driver_map_id is null')
            ->where('users.user_type', 'Driver')
            ->where('users.status', 1)
            ->count_all_results();

        $num_count += $not_assigned_ever;
        return $num_count;
    }

    //get users
    public function getcity()
    {
        $this->db->select('name,id');
        $this->db->where('status', 1);
        return $this->db->get('city')->result();
    }
    //get zone
    public function getzone($city_id = null)
    {
        if ($city_id)
            $this->db->where('city_id', $city_id);

        $this->db->select('area_name,entity_id');
        // $this->db->where('status' == 1);
        return $this->db->get('zone')->result();
    }

    public function getAttendance($rider_id = null, $from_date = null, $to_date = null)
    {
        if ($rider_id)
            $this->db->where('driver_traking_map.driver_id', $rider_id);

        if ($from_date)
            $this->db->where('DATE(created_date) >=', $from_date);

        if ($to_date)
            $this->db->where('DATE(created_date) <=', $to_date);

        $query = $this->db->select('driver_traking_map.traking_id, driver_traking_map.driver_id, driver_traking_map.created_date', $rider_id)
            ->from('driver_traking_map')
            ->where('traking_id = (SELECT driver_traking_map.traking_id WHERE ('
                . ($rider_id ?
                    'driver_traking_map.driver_id =' . $rider_id . ' AND '
                    : '')
                . 'driver_traking_map.onoff = 1))')
            ->group_by('DATE(driver_traking_map.created_date)');

        if ($rider_id) {
            return $query->count_all_results();
        } else {
            return $query->get()->result_array();
        }
    }

    //Number of Rider's Ongoing orders
    public function order_count($user_id)
    {
        $res = $this->db->select('order_status.order_status,order_driver_map.driver_id,order_status.order_id,order_status.status_id')
            ->from('order_status')
            ->where('order_status.status_id in (
                SELECT MAX(od.status_id)
                from order_status as od
                GROUP BY od.order_id)')
            ->join('order_driver_map', 'order_status.order_id = order_driver_map.order_id', 'left')
            ->where('order_driver_map.driver_id', $user_id)
            ->group_by('order_driver_map.order_id')
            ->get()
            ->result_array();
        // echo "<pre>";
        // print_r($res);
        // exit();

        $count = 0;
        $data = array();
        foreach ($res as $r) {
            if (!isset($data[$r['driver_id']])) {
                $data[$r['driver_id']] = array();
            }

            if (!isset($data[$r['driver_id']][$r['order_id']])) {
                $data[$r['driver_id']][$r['order_id']] = array();
            }

            $data[$r['driver_id']][$r['order_id']][] =  $r['order_status'];
        }
        // echo "<pre>";
        // print_r($data);
        // exit();
        foreach ($data as $d) {

            foreach ($d as $per_rider) {
                $length = count($per_rider);
                if ($per_rider[$length - 1] == 'preparing' || $per_rider[$length - 1] == 'onGoing') {
                    $count++;
                }
            }
        }

        return $count;
    }
}
