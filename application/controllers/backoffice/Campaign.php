<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Campaign extends CI_Controller
{
    public $controller_name = 'campaign';
    public $prefix = '_camp';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/campaign_model');
    }
    // view coupon
    public function view()
    {
        $data['meta_title'] = ' Campaign| ' . $this->lang->line('site_title');
        // $data['campaign'] = $this->campaign_model->getSortData('campaign');
        // echo "<pre>";
        // print_r($data);
        // exit();

        $this->load->view(ADMIN_URL . '/campaign', $data);
    }
    public function get_campaign_list()
    {
        $this->db->select("name,entity_id");
        $this->db->where('campaign.status', 1);
        $this->db->from("campaign");
        $this->db->order_by('sort_value');
        $result = $this->db->get()->result();
        $html = '';
        foreach ($result as $key => $value) {
            $html .= '<li class="ui-sortable-handle" id="' . $value->entity_id . '">'
                . '<a href="javascript:void(0);" class="menu_link">' .
                '<h4>' . $value->name . '</h4>'
                . '</a>' .
                '</li>';
        }
        echo $html;
    }
    public function orderUpdate()
    {
        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            // Generate ids array

            $idArray = explode(",", $ids);

            $count = 1;
            foreach ($idArray as $id) {
                // Update image order by id
                $data = array('sort_value' => $count);
                $update = $this->campaign_model->updateMenuSort($data, $id);
                $count++;
            }
        }

        return true;
    }
    // add coupon
    public function add()
    {
        $data['meta_title'] = ' Campaign| ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Campaign Name', 'trim|callback_checkExist');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('restaurant_id[]', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
            if (empty($_FILES['image']['name'])) {
                $this->form_validation->set_rules('image', 'Image', 'required');
            }
            //check form validation using codeigniter


            if ($this->form_validation->run()) {
                $add_data = array(

                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('start_date'))),
                    'end_date' => date('Y-m-d H:i:s', strtotime($this->input->post('end_date'))),
                    'status' => 1,

                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/campaign';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/campaign')) {
                        @mkdir('./uploads/campaign', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $add_data['image'] = "campaign/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }


                if (empty($data['Error'])) {
                    $entity_id = $this->campaign_model->addData('campaign', $add_data);

                    if (!empty($this->input->post('restaurant_id'))) {
                        $res_data = array();
                        foreach ($this->input->post('restaurant_id') as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value,
                                'campaign_id' => $entity_id
                            );
                        }
                        $this->campaign_model->insertBatch('campaign_restaurant_map', $res_data, $id = '');
                    }

                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $data['restaurant'] = $this->campaign_model->getListData('restaurant', array('status' => 1));
        $this->load->view(ADMIN_URL . '/campaign_add', $data);
    }
    // edit coupon
    public function edit()
    {
        $data['meta_title'] = "Edit Campaign" . ' | ' . $this->lang->line('site_title');
        // check if form is submitted
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Campaign Name', 'trim|callback_checkExist');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('restaurant_id[]', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
            // if (empty($_FILES['image']['name'])) {
            //     $this->form_validation->set_rules('image', 'Image', 'required');
            // }
            //check form validation using codeigniter

            if ($this->form_validation->run()) {
                $edit_data = array(

                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('start_date'))),
                    'end_date' => date('Y-m-d H:i:s', strtotime($this->input->post('end_date'))),

                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/campaign';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/campaign')) {
                        @mkdir('./uploads/campaign', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "campaign/" . $img['file_name'];
                        // code for delete existing image
                        if ($this->input->post('uploaded_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (empty($data['Error'])) {
                    $this->campaign_model->updateData($edit_data, 'campaign', 'entity_id', $this->input->post('entity_id'));

                    if (!empty($this->input->post('restaurant_id'))) {
                        $res_data = array();
                        foreach ($this->input->post('restaurant_id') as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value,
                                'campaign_id' => $this->input->post('entity_id')
                            );
                        }
                        $this->campaign_model->insertBatch('campaign_restaurant_map', $res_data, $this->input->post('entity_id'));
                    }

                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->campaign_model->getEditDetail($entity_id);
        $data['restaurant'] =  $this->campaign_model->getListData('restaurant', array('status' => 1));
        $data['restaurant_map'] = $this->campaign_model->getListData('campaign_restaurant_map', array('campaign_id' => $entity_id));
        $this->load->view(ADMIN_URL . '/campaign_add', $data);
    }

    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'name', 2 => 'status');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->campaign_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $deeplink = DEEP_LINK_BASE_URL . generateDeepLink('campaign', $val->entity_id);

            $records["aaData"][] = array(
                $nCount,
                $val->name,
                '<button class="btn" onclick="copyToClipboard(\'' . $deeplink . '\')"><i class="fa fa-copy"></i></button>',
                ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
                ($this->lpermission->method('campaign', 'update')->access()
                    ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit') . '</a>'
                    : '') .
                    ($this->lpermission->method('campaign', 'delete')->access()
                        ? '<button onclick="deleteDetail(' . $val->entity_id . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>'
                        : '')
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change coupon status
    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->coupon_model->UpdatedStatus('coupon', $entity_id, $this->input->post('status'));
        }
    }
    // method for deleting a coupon
    public function ajaxDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->campaign_model->deleteUser('campaign', $entity_id);
    }
    public function checkExist()
    {
        $campaign = ($this->input->post('campaign') != '') ? $this->input->post('campaign') : '';

        if ($campaign != '') {
            $check = $this->campaign_model->checkExist($campaign, $this->input->post('entity_id'));
            echo $check;
        }
    }
    public function getItem()
    {
        $entity_id = $this->input->post('entity_id');
        $coupon_type = $this->input->post('coupon_type');
        $type = $this->input->post('type');
        $coupon_id = $this->input->post('coupon_id');
        $html = '';
        if (!empty($entity_id)) {
            $result =  $this->coupon_model->getItem($entity_id[0], $coupon_type, $type, $coupon_id);
            if (!empty($result)) {
                foreach ($result as $key => $value) {
                    $html .= '<optgroup label="' . $value[0]->restaurant_name . '">';
                    foreach ($value as $k => $val) {
                        $html .= '<option value=' . $val->entity_id . '>' . $val->name . '</option>';
                    }
                    $html .= '</optgroup>';
                }
            }
        }
        echo $html;
    }

    public function getNonGradualRestaurant()
    {
        // $coupon_type = $this->input->post('coupon_type');
        $html = '';

        $result =  $this->coupon_model->getNonGradualRestaurant();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                //$html .= '<optgroup label="'.$value[0]->restaurant_name.'">';

                $html .= '<option value=' . $value['entity_id'] . '>' . $value['name'] . '</option>';

                //$html .= '</optgroup>';
            }
        }

        echo $html;
    }
}
