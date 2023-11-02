<?php
class Zone_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //ajax view
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {

        //$this->db->select('restaurant.name,zone.area_name,zone.entity_id,restaurant.currency_id');
        // $this->db->join('restaurant','delivery_charge.restaurant_id = restaurant.entity_id','left');
        $result['total'] = $this->db->count_all_results('zone');
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if ($displayLength > 1)
            $this->db->limit($displayLength, $displayStart);

        if ($this->input->post('page_title') != '') {
            $this->db->like('area_name', $this->input->post('page_title'));
        }
        if ($this->input->post('status') != '') {
            $this->db->like('status', $this->input->post('status'));
        }

        $result['data'] = $this->db->get('zone')->result();
        return $result;
    }
    public function all_city()
    {

        ## Fetch records
        $records = $this->db->select('*')
            ->from('city')
            ->get()
            ->result();
        foreach ($records as $record) {
            $data[] = array(
                'id' => $record->id,
                'name' => $record->name,
                'status' => $record->status,
            );
        }
        ## Response


        return $data;
    }
    public function city_editdata($id)
    {
        $this->db->select('*');
        $this->db->from('city');
        $this->db->where('id', $id);
        $query = $this->db->get();
        // echo "<pre>";
        // print_r($query->result_array());
        // exit();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function update_city($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('city', $data);
        return true;
    }
    public function change_status($id, $status)
    {

        if ($status == 1) {
            $value = 0;
        } else {
            $value = 1;
        }
        $this->db->select('*');
        $this->db->from('city');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $r = $query->result_array();
        // echo "<pre>";
        // print_r($r);
        // exit();
        $data = array(
            'id' => $id,
            'name' => $r[0]['name'],
            'status' => $value,
        );
        $this->db->where('id', $id);
        $this->db->update('city', $data);
        return true;
    }

    public function delete_city($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('city');
        return true;
    }

    //All Vehicle Routes
    public function all_vehicle()
    {

        ## Fetch records
        $records = $this->db->select('*')
            ->from('vehicle_type')
            ->get()
            ->result();
        foreach ($records as $record) {
            $data[] = array(
                'entity_id' => $record->entity_id,
                'name' => $record->name,
                'price' => $record->price,
                'status' => $record->status,
            );
        }
        ## Response


        return $data;
    }
    public function vehicle_editdata($id)
    {
        $this->db->select('*');
        $this->db->from('vehicle_type');
        $this->db->where('entity_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function update_vehicle($data, $id)
    {
        $this->db->where('entity_id', $id);
        $this->db->update('vehicle_type', $data);
        return true;
    }
    public function change_vehicle_status($id, $status)
    {

        if ($status == 1) {
            $value = 0;
        } else {
            $value = 1;
        }
        $this->db->select('*');
        $this->db->from('vehicle_type');
        $this->db->where('entity_id', $id);
        $query = $this->db->get();
        $r = $query->result_array();
        $data = array(
            'entity_id' => $id,
            'name' => $r[0]['name'],
            'price' => $r[0]['price'],
            'status' => $value,
        );
        $this->db->where('entity_id', $id);
        $this->db->update('vehicle_type', $data);
        return true;
    }

    public function delete_vehicle($id)
    {
        $this->db->where('entity_id', $id);
        $this->db->delete('vehicle_type');
        return true;
    }
    //add to db
    public function addData($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    //add city
    public function addcity($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    //add city
    public function addvehicle($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    //get single data
    public function getEditDetail($entity_id)
    {
        return $this->db->get_where('zone', array('entity_id' => $entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data, $tblName, $fieldName, $ID)
    {
        $this->db->where($fieldName, $ID);
        $this->db->update($tblName, $Data);
        return $this->db->affected_rows();
    }
    // delete all records
    public function ajaxDeleteAll($tblname, $content_id)
    {
        $this->db->where('entity_id', $content_id);
        $this->db->delete($tblname);
    }
    //get list
    public function getListData($tblname, $language_slug = NULL)
    {
        $this->db->select('name,entity_id');
        $this->db->where('status', 1);
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('created_by', $this->session->userdata('UserID'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug', $language_slug);
        }
        return $this->db->get($tblname)->result();
    }
    //get city List
    public function getcitylist($tblname)
    {
        $this->db->select('name,id');
        $this->db->where('status', 1);
        return $this->db->get($tblname)->result();
    }
    public function getResLatLong($restaurant_id)
    {
        $this->db->select('latitude,longitude');
        $this->db->where('resto_entity_id', $restaurant_id);
        return $this->db->get('restaurant_address')->first_row();
    }

    //insert batch
    public function insertBatch($tblname, $data, $id)
    {
        if ($id) {
            $this->db->where('zone_id', $id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname, $data);
        return $this->db->insert_id();
    }

    public function getList($entity_id)
    {

        $this->db->select('res.entity_id as restaurant_id, res.name');
        $this->db->join('restaurant as res', 'zone_res_map.restaurant_id = res.entity_id', 'left');
        $this->db->where('zone_res_map.zone_id', $entity_id);
        return $this->db->get('zone_res_map')->result();
    }

    public function UpdatedStatus($tblname, $entity_id, $status)
    {
        if ($status == 0) {
            $userData = array('status' => 1);
        } else {
            $userData = array('status' => 0);
        }
        $this->db->where('entity_id', $entity_id);
        $this->db->update($tblname, $userData);
        return $this->db->affected_rows();
    }
    //Get Driver Data
    public function getDriverData($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10, $city_id = null, $zone_id = null)
    {
        $this->db->select('a.entity_id');
        $this->db->from('users a');
        $this->db->join('order_driver_map b', 'a.entity_id=b.driver_id', 'left');
        $this->db->join('order_status c', 'b.order_id=c.order_id AND c.order_status = "delivered"', 'left');
        $this->db->where('a.user_type', 'Driver');
        $this->db->group_by('a.entity_id');
        $R['total'] = $this->db->count_all_results();
        $city_id = $this->input->post('city_id', TRUE);
        $zone_id = $this->input->post('zone_id', TRUE);
        if ($city_id != '') {
            $this->db->where('a.city_id', $city_id);
        }
        if ($zone_id != '') {
            $this->db->where('a.zone_id', $zone_id);
        }
        $this->db->select('a.first_name,a.onoff,a.mobile_number,a.entity_id');
        $this->db->from('users a');
        $this->db->where('a.user_type', 'Driver');

        $this->db->limit($displayLength, $displayStart);
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        $R['total'] = $this->db->count_all_results();

        $this->db->select('a.entity_id');
        $this->db->from('users a');
        $this->db->join('order_driver_map b', 'a.entity_id=b.driver_id', 'left');
        $this->db->join('order_status c', 'b.order_id=c.order_id AND c.order_status = "delivered"', 'left');
        $this->db->where('a.user_type', 'Driver');
        $this->db->group_by('a.entity_id');
        $R['total'] = $this->db->count_all_results();
        $city_id = $this->input->post('city_id', TRUE);
        $zone_id = $this->input->post('zone_id', TRUE);
        if ($city_id != '') {
            $this->db->where('a.city_id', $city_id);
        }
        if ($zone_id != '') {
            $this->db->where('a.zone_id', $zone_id);
        }
        $this->db->select('a.first_name,a.onoff,a.mobile_number,a.entity_id');
        $this->db->from('users a');
        $this->db->where('a.user_type', 'Driver');

        $this->db->limit($displayLength, $displayStart);
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $result =  $this->db->get()->result();

        $R['data'] = $result;
        // $R['total'] =
        //     count($result);
        // $R['data'] = $result;

        return $R;
    }
}
