<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class System_option extends CI_Controller
{
    public $controller_name = 'system_option';

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->model(ADMIN_URL . '/systemoption_model');
        $this->load->model(ADMIN_URL . '/coupon_model');
        $this->load->model(ADMIN_URL . '/report_model');
        $this->load->model(ADMIN_URL . '/order_model');
    }
    public function softdelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->systemoption_model->soft_delete($this->input->post('tblname'), $entity_id);
        }
    }
    public function voucher_request()
    {
        $data['meta_title'] = "Vouchers Request" . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/vouchers_request', $data);
    }
    public function refund_view()
    {
        $data['meta_title'] = "Refund View" . ' | ' . $this->lang->line('site_title');

        $this->load->view(ADMIN_URL . '/refund_view', $data);
    }
    public function ajaxrefundview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'voucher_name', 2 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->systemoption_model->getRefundGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $myString = $grid_data['data'][1]->transaction_id;
        $myArray = explode(',', $myString);
        $myArray1 = explode(':', $myArray[1]);
        // echo "<pre>";
        // print_r($myArray1[1]);
        // exit();
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {

            $myString = $val->transaction_id;
            $myArray = explode(',', $myString);

            $transaction_id = $myArray[0];

            $payment = explode(':', $myArray[1]);
            $payment_id = $payment[1];
            $records["aaData"][] = array(
                $nCount,
                $val->order_id,
                $val->customer_name,
                $val->mobile_number,
                $val->total_rate,
                $transaction_id,
                $payment_id,
                $val->order_date,
                $this->lpermission->method('refund', 'update')->access() ?
                    ((!$val->refund) ?
                        '<button onclick="confirm_refund(\'' . $transaction_id . '\'' . ',' . '\'' . $val->total_rate . '\'' . ',' . '\'' . $payment_id . '\'' . ',' . $val->order_id . ')" class="delete btn btn-sm danger-btn margin-bottom"> Refund</button>' :
                        '<button class="delete btn btn-sm danger-btn margin-bottom" disabled> Refunded</button><button class="delete btn btn-sm danger-btn margin-bottom" onclick="check_status(\'' . $transaction_id . '\', \'' . $payment_id . '\')">Show Status</button>') : ""

            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxvoucherview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'voucher_name', 2 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->systemoption_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                $val->first_name,
                $val->mobile_number,
                ($val->status == 1) ? "Approved" : "Not Approved",
                $this->lpermission->method('vouchers_request', 'update')->access() ?
                    '<button onclick="disable_record(' . $val->entity_id . ',' . $val->status . ')"  title="' . $this->lang->line('click_for') . ($val->status ? 'Unapprove' : 'Approve') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->status ? 'times' : 'check') . '"></i> ' . ($val->status ? 'Unapprove' : 'Approve') . '</button>' : ""
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function view()
    {
        $data['meta_title'] = $this->lang->line('titleadmin_systemoptions') . ' | ' . $this->lang->line('site_title');

        if ($this->input->post('SubmitSystemSetting') == "Submit") {
            $systemOptionCount = count($_POST['OptionValue']);
            $systemOptionData = array();
            for ($nCount = 0; $nCount < $systemOptionCount; $nCount++) {
                $systemOptionData[] = array(
                    'SystemOptionID'  => $_POST['SystemOptionID'][$nCount],
                    'OptionValue'  => $_POST['OptionValue'][$nCount],
                    'UpdatedBy'    => $this->session->userdata("adminID"),
                    'UpdatedDate'  => date('Y-m-d h:i:s')
                );
            }
            $this->systemoption_model->upateSystemOption($systemOptionData);
            $this->session->set_flashdata('SystemOptionMSG', $this->lang->line('success_update'));
        }
        $data['SystemOptionList'] = $this->systemoption_model->getSystemOptionList();

        $getExtraSetting = $this->systemoption_model->getOperationOptionList();
        foreach ($getExtraSetting as $extra) {
            $extra_setting[$extra->name] = $extra->value;
        }
        $data['extra_setting'] = $extra_setting;
        $this->load->view(ADMIN_URL . '/system_option', $data);
    }
    public function AvailCoupon()
    {
        $coupon_id = $this->input->post('coupon_id');
        $user_id = $this->input->post('user_id');
        // return $coupon_id;
        $total_points = $this->systemoption_model->get_total_points($user_id);
        $get_coupon_points = $this->systemoption_model->get_coupon_points($coupon_id);
        $start_date = date('y-m-d');
        $futureDate = date('y-m-d', strtotime('+30 days'));
        $available_points = $total_points[0]->points;
        $coupon_points = $get_coupon_points[0]->cost;
        if ($available_points < $coupon_points) {
            return "Not Enough Points";
        } else {
            //Array data for craete coupon
            $add_data = array(
                'coupon_type' => 'selected_user',
                'name' => 'Get' . $get_coupon_points[0]->value . 'Taka Free',
                'description' => 'Get' . $get_coupon_points[0]->value . 'Burning' . $coupon_points . 'Points',
                'amount_type' => 'Amount',
                'amount' => $get_coupon_points[0]->value,
                'max_amount' => $get_coupon_points[0]->cost,
                'start_date' => $start_date,
                'end_date' => $futureDate,
                'status' => 1,
                'maximum_use' => 1,
                'discount_amount' => null,
                'usablity' => 'regular',
            );
            $entity_id = $this->coupon_model->addData('coupon', $add_data);
            $burn_data = array(
                'points' => $coupon_points,
                'cost' => 2,
                'date' => date('Y-m-d H:i:s'),
                'user_id' => $user_id,
                'reason' => 'Reedemed ' . $coupon_points . ' as ' . $get_coupon_points[0]->type
            );
            $burn_id = $this->coupon_model->addData('reward_point', $burn_data);
            //Coupon Created for all Restaurant
            $all_resturant = $this->coupon_model->getAllRestaurantID();
            foreach ($all_resturant as $key => $value) {
                $res_data[] = array(
                    'restaurant_id' => $value['entity_id'],
                    'coupon_id' => $entity_id
                );
            }
            $this->coupon_model->insertBatch('coupon_restaurant_map', $res_data, $id = '');
            //Set User Coupon Map

            // $user_data = array();
            // $user_data[0]['user_id'] = $user_id;
            // $user_data[0]['coupon_id'] = $coupon_id;
            $user_data = array(
                'user_id' => $user_id,
                'coupon_id' => $entity_id
            );
            if (!empty($user_data))
                $this->coupon_model->addData('coupon_user_map', $user_data);

            echo "Success";
        }
    }


    public function reward_view()
    {
        $data['meta_title'] = "Reward Point Setting" . ' | ' . $this->lang->line('site_title');

        if ($this->input->post('SubmitSystemSetting') == "Submit") {
            $systemOptionCount = count($_POST['entity_id']);
            $systemOptionData = array();
            for ($nCount = 0; $nCount < $systemOptionCount; $nCount++) {
                $systemOptionData[] = array(
                    'entity_id'  => $_POST['entity_id'][$nCount],
                    'value'  => $_POST['value'][$nCount],
                    'updated_by'    => $this->session->userdata("adminID"),
                    'updated_date'  => date('Y-m-d h:i:s')
                );
            }
            $this->systemoption_model->upateRewardOption($systemOptionData);
            $this->session->set_flashdata('SystemOptionMSG', $this->lang->line('success_update'));
        }
        $data['Reward_System_List'] = $this->systemoption_model->getRewardOptionList();
        $data['vouchers_list'] = $this->systemoption_model->getVoucherList();
        $this->load->view(ADMIN_URL . '/reward_setting_option', $data);
    }
    public function ajaxDelete($entity_id, $tblname)
    {
        $this->db->where('entity_id', $entity_id);
        return $this->db->delete($tblname);
    }
    public function ajaxDisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->systemoption_model->UpdatedStatus($this->input->post('tblname'), $entity_id, $this->input->post('status'));
        }
    }
    public function AddVoucher()
    {
        // echo "<pre>";
        // print_r($_POST);
        // exit();
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $cost = ($this->input->post('cost')) ? $this->input->post('cost') : 0;
        $value = ($this->input->post('value')) ? $this->input->post('value') : 0;

        if ($type && $cost && $name) {
            $addData = array(
                'name' => $name,
                'cost' => $cost,
                'type' => $type,
                'value' => $value,
                'status' => 1,
                'created_date' => date('Y-m-d H:i:s')
            );
            if (!empty($_FILES['Image']['name'])) {

                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/voucher';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '5120'; //in KB
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/voucher')) {
                    @mkdir('./uploads/voucher', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('Image')) {
                    $img = $this->upload_cloud->data();
                    $addData['image'] = "voucher/" . $img['file_name'];
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }
            // echo "<pre>";
            // print_r($addData);
            // exit();
            $this->systemoption_model->addData('reward_point_setting', $addData);
        }
    }

    public function UpdaterefundStatus()
    {
        $order_id = $this->input->post('order_id');
        $refund_id = $this->input->post('refundTrxID');
        $order_details = $this->order_model->order_master($order_id);
        $transaction_id = $order_details['transaction_id'] . ',' . $refund_id;
        $update_data = array(
            'refund' => 1,
            'refund_trnxID' => $refund_id
        );
        $this->order_model->updateData($update_data, 'order_master', 'entity_id', $order_id);
    }


    public function addMoreSettings()
    {
        $update_data[] = array(
            'name'   => 'operation_on_off',
            'value'  => $this->input->post('operation_on_off', TRUE),
        );

        if (!empty($_FILES['Image']['name'])) {
            $this->load->library('upload_cloud');
            $config['upload_path'] = './uploads/special';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '5120'; //in KB
            $config['encrypt_name'] = TRUE;
            // create directory if not exists
            if (!@is_dir('uploads/special')) {
                @mkdir('./uploads/special', 0777, TRUE);
            }
            $this->upload_cloud->initialize($config);
            if ($this->upload_cloud->do_upload('Image')) {
                $img = $this->upload_cloud->data();
                $img_url = "special/" . $img['file_name'];
                if ($this->input->post('uploaded_image')) {
                    @unlink(FCPATH . 'uploads/special/' . $this->input->post('uploaded_image'));
                }
            } else {
                $data['Error'] = $this->upload_cloud->display_errors();
                $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
            }

            $update_data[] = array(
                'name'   => 'operation_off_image',
                'value'  => $img_url,
            );
        }

        if (!empty($this->input->post('operation_timing'))) {
            $timingsArr = $this->input->post('operation_timing');
            $newTimingArr = array();
            foreach ($timingsArr as $key => $value) {
                if (!isset($value['on'])) {
                    $newTimingArr[$key]['open'] = '';
                    $newTimingArr[$key]['close'] = '';
                    $newTimingArr[$key]['on'] = '0';
                } else {
                    if (!empty($value['open']) && !empty($value['close'])) {
                        $newTimingArr[$key]['open'] = $value['open'];
                        $newTimingArr[$key]['close'] = $value['close'];
                        $newTimingArr[$key]['on'] = '1';
                    } else {
                        $newTimingArr[$key]['open'] = '';
                        $newTimingArr[$key]['close'] = '';
                        $newTimingArr[$key]['on'] = '0';
                    }
                }
            }
            $update_data[] = array(
                'name'   => 'operation_timing',
                'value'  => serialize($newTimingArr),
            );
        }


        if ($this->systemoption_model->upateOperationOption($update_data)) {
            // echo 'Success';
        }
        $this->session->set_flashdata('SystemOptionMSG', $this->lang->line('success_update'));
        redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
    }
    //Report Related to CRM
    public function Earning_report_view()
    {
        $data['meta_title'] = 'Earning Report' . ' | ' . $this->lang->line('site_title');
        $data['groups'] = $this->report_model->getAllUsers();
        $this->load->view(ADMIN_URL . '/earning_report', $data);
    }
    public function Burning_report_view()
    {
        $data['meta_title'] = 'Burning Report' . ' | ' . $this->lang->line('site_title');
        $data['groups'] = $this->report_model->getAllUsers();
        $this->load->view(ADMIN_URL . '/burning_report', $data);
    }
    public function Claim_report_view()
    {
        $data['meta_title'] = 'Claim Report' . ' | ' . $this->lang->line('site_title');
        $data['groups'] = $this->report_model->getAllUsers();
        $this->load->view(ADMIN_URL . '/claim_report', $data);
    }
    //Report Related to Crm
    public function Earning_report()
    {
        $this->load->model('report_model');
        $postData = $this->input->post();
        $data = $this->report_model->getUsersEarningList($postData);
        echo json_encode($data);
    }
    public function Claim_report()
    {
        $this->load->model('report_model');
        $postData = $this->input->post();
        $data = $this->report_model->getClaimVoucherList($postData);
        echo json_encode($data);
    }
    public function Burning_report()
    {
        $this->load->model('report_model');
        $postData = $this->input->post();
        $data = $this->report_model->getBurnedVoucherList($postData);
        echo json_encode($data);
    }
}
