<?php
class systemoption_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    function getSystemOptionList($needStatus = null)
    {
        $this->db->select('*')
            ->from('system_option');

        if ($needStatus) {
            $this->db->where('status', 1);
        }

        $q = $this->db->get()->result();
        return $q;
    }
    //Get Refund Data
    public function getRefundGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {

        $this->db->select('order_master.refund,order_master.total_rate,order_master.entity_id as order_id,users.first_name as customer_name,users.mobile_number,order_master.transaction_id,
        order_master.order_date');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.transaction_id !=', "null");
        $this->db->order_by('order_master.entity_id', 'DESC');


        $result['total'] = $this->db->count_all_results('order_master');
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if ($displayLength > 1)
            $this->db->limit($displayLength, $displayStart);

        $this->db->select('order_master.refund,order_master.total_rate,order_master.entity_id as order_id,users.first_name as customer_name,users.mobile_number,order_master.transaction_id,
        order_master.order_date');
        $this->db->join('users', 'order_master.user_id = users.entity_id', 'left');
        $this->db->where('order_master.transaction_id !=', "null");
        $this->db->order_by('order_master.entity_id', 'DESC');

        $result['data'] = $this->db->get('order_master')->result();
        return $result;
    }
    function getRewardOptionList($needStatus = null)
    {
        $this->db->select('*')
            ->from('reward_point_setting')->where('type is null');

        $q = $this->db->get()->result();
        return $q;
    }
    function upateRewardOption($systemOptionData)
    {
        $this->db->update_batch('reward_point_setting', $systemOptionData, 'entity_id');
    }
    function upateSystemOption($systemOptionData)
    {
        $this->db->update_batch('system_option', $systemOptionData, 'SystemOptionID');
    }
    function getVoucherList()
    {
        return $this->db->select('*')->from('reward_point_setting')->where('type is not null')->where('is_delete', 0)->get()->result();
    }
    //
    function get_total_points($user_id)
    {
        $this->db->select("((select ifnull(sum(points),0) from reward_point where cost = 1)-(select ifnull(sum(points),0) from reward_point where cost = 2)) as points");
        $this->db->where('reward_point.user_id', $user_id);
        return $this->db->get('reward_point')->result();
    }
    function get_coupon_points($coupon_id)
    {
        return $this->db->select('*')->from('reward_point_setting')
            ->where('type is not null')
            ->where('status', 1)
            ->where('entity_id', $coupon_id)
            ->where('type', 'Coupon')
            ->get()->result();
    }
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        //update notification unread
        $userData = array('is_read' => 1);
        $this->db->where('is_read', 0);
        $this->db->update('voucher_notification', $userData);
        $this->db->affected_rows();
        //grid list
        if ($this->input->post('status') != '') {
            $this->db->like('is_read', $this->input->post('status'));
        }
        $result['total'] = $this->db->count_all_results('voucher_notification');
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);

        if ($displayLength > 1)
            $this->db->limit($displayLength, $displayStart);
        if ($this->input->post('status') != '') {
            $this->db->like('is_read', $this->input->post('status'));
        }
        $this->db->select('users.first_name,users.last_name,users.mobile_number,reward_point_setting.name,voucher_notification.entity_id as entity_id,voucher_notification.status');
        $this->db->join('reward_point_setting', 'voucher_notification.voucher_id = reward_point_setting.entity_id', 'left');
        $this->db->join('users', 'voucher_notification.user_id = users.entity_id', 'left');
        // $this->db->where('voucher_notification.is_read', 1);
        $result['data'] = $this->db->get('voucher_notification')->result();
        return $result;
    }
    //
    public function addData($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }
    public function getRewardValue($value)
    {
        $this->db->select('value');
        $system_value = $this->db->get_where('reward_point_setting', array('name' => $value))->first_row();
        return $system_value->value;
    }
    public function getValue($value)
    {
        // $value = $this->input->post('option_name') ? $this->input->post('option_name') : '';
        $this->db->select('OptionValue');
        $max_orders = $this->db->get_where('system_option', array('OptionSlug' => $value))->first_row();
        return $max_orders->OptionValue;
    }

    function upateOperationOption($systemOptionData)
    {
        $this->db->update_batch('operation_sytem_option', $systemOptionData, 'name');
    }
    public function soft_delete($tblname, $entity_id)
    {
        $update_data = array('is_delete' => 1, 'status' => 0);
        $this->db->where('entity_id', $entity_id);
        $this->db->update($tblname, $update_data);
        //return $this->db->effected_rows();
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

    function getOperationOptionList()
    {
        $this->db->select('*')
            ->from('operation_sytem_option');


        $q = $this->db->get()->result();
        return $q;
    }
}
