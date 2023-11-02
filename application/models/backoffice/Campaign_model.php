<?php
class Campaign_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 25)
    {

        $this->db->select('campaign.*,restaurant.currency_id');
        $this->db->join('campaign_restaurant_map', 'campaign.entity_id = campaign_restaurant_map.campaign_id', 'left');
        $this->db->join('restaurant', 'campaign_restaurant_map.restaurant_id = restaurant.entity_id', 'left');
        $this->db->group_by('campaign.entity_id');
        $result['total'] = $this->db->count_all_results('campaign');
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if ($this->input->post('page_title') != '') {
            $this->db->like('campaign.name', $this->input->post('page_title'));
        }

        if ($this->input->post('Status') != '') {
            $this->db->like('status', $this->input->post('Status'));
        }
        if ($displayLength > 1)
            $this->db->limit($displayLength, $displayStart);
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('created_by', $this->session->userdata('UserID'));
        }
        $this->db->select('campaign.*,restaurant.currency_id');
        $this->db->join('campaign_restaurant_map', 'campaign.entity_id = campaign_restaurant_map.campaign_id', 'left');
        $this->db->join('restaurant', 'campaign_restaurant_map.restaurant_id = restaurant.entity_id', 'left');
        $this->db->group_by('campaign.entity_id');
        $result['data'] = $this->db->get('campaign')->result();
        return $result;
    }
    //add to db
    public function addData($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    //get single data
    public function getEditDetail($entity_id)
    {
        $this->db->select('c.*');
        return $this->db->get_where('campaign as c', array('c.entity_id' => $entity_id))->first_row();
    }
    // update data common function
    public function updateData($Data, $tblName, $fieldName, $ID)
    {
        $this->db->where($fieldName, $ID);
        $this->db->update($tblName, $Data);
        return $this->db->affected_rows();
    }
    // updating the changed status
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
    // delete user
    public function deleteUser($tblname, $entity_id)
    {
        $this->db->delete($tblname, array('entity_id' => $entity_id));
    }
    //get list
    public function getListData($tblname, $where)
    {
        $this->db->where($where);
        return $this->db->get($tblname)->result_array();
    }
    public function updateMenuSort($data = array(), $id)
    {

        $this->db->where('entity_id', $id);
        $this->db->update('campaign', $data);
        return $this->db->affected_rows();
    }
    public function checkExist($campaign, $entity_id)
    {
        $this->db->where('name', $campaign);
        $this->db->where('entity_id !=', $entity_id);
        return $this->db->get('campaign')->num_rows();
    }
    //insert batch 
    public function insertBatch($tblname, $data, $id)
    {
        if ($id) {
            $this->db->where('campaign_id', $id);
            $this->db->delete($tblname);
        }
        $this->db->insert_batch($tblname, $data);
        return $this->db->insert_id();
    }
}
