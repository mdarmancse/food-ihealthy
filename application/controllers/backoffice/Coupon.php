<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Coupon extends CI_Controller
{
    public $controller_name = 'coupon';
    public $prefix = '_cpn';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/coupon_model');
    }
    // view coupon
    public function view()
    {
        $data['meta_title'] = $this->lang->line('title_admin_coupon') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/coupon', $data);
    }
    // add coupon
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_couponadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {

            $this->form_validation->set_rules('name', 'Coupon Name', 'trim|callback_checkExist');
            $this->form_validation->set_rules('coupon_type', 'Coupon Type', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            // $this->form_validation->set_rules('restaurant_id[]', 'Restaurant', 'trim|required');
            if ($this->input->post('coupon_type') != 'free_delivery' && $this->input->post('coupon_type') != 'gradual' && $this->input->post('coupon_type') != 'discount_on_items') {
                $this->form_validation->set_rules('amount_type', 'Amount type', 'trim|required');
                $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
                $this->form_validation->set_rules('max_amount', 'Max Amount', 'trim|required');
            }

            $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
            //check form validation using codeigniter

            $_POST['restaurant_id'] = json_decode($this->input->post('restaurant_id'));
            $_POST['item_id'] = json_decode($this->input->post('item_id'));
            $_POST['user_id'] = json_decode($this->input->post('user_id'));

            $maximum_use = $this->input->post('maximum_use');
            if ($maximum_use == null || $maximum_use == '') {
                $maximum_use = 0;
            }


            if ($this->form_validation->run()) {
                $add_data = array(
                    'coupon_type' => $this->input->post('coupon_type'),
                    'name' => strtoupper($this->input->post('name')),
                    'description' => $this->input->post('description'),
                    'amount_type' => ($this->input->post('coupon_type') != 'free_delivery' || $this->input->post('coupon_type') != 'gradual') ? $this->input->post('amount_type') : NULL,
                    'amount' => ($this->input->post('coupon_type') == 'gradual') ? 0 : ($this->input->post('amount') ? $this->input->post('amount') : null),
                    'max_amount' => ($this->input->post('max_amount')) ? $this->input->post('max_amount') : 0,
                    'start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('start_date'))),
                    'end_date' => date('Y-m-d H:i:s', strtotime($this->input->post('end_date'))),
                    'status' => 1,
                    'created_by' => $this->session->userdata("UserID"),
                    'maximum_use' => $maximum_use,
                    'discount_amount' => ($this->input->post('discount_amount')) ? $this->input->post('discount_amount') : null,
                    'usablity' => $this->input->post('usablity'),
                    'gradual_all_items' => $this->input->post('gradual_all_items') ? $this->input->post('gradual_all_items') : 0
                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/coupons';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/coupons')) {
                        @mkdir('./uploads/coupons', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $add_data['image'] = "coupons/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }


                if (empty($data['Error'])) {
                    // echo '<pre>';
                    // print_r($this->input->post('all_restaurant'));
                    // exit();
                    $entity_id = $this->coupon_model->addData('coupon', $add_data);

                    // for gradual
                    if ($this->input->post('coupon_type') == 'gradual') {
                        foreach ($this->input->post('gradual_group') as $key => $v) {
                            $gradual_data[] = array(
                                'coupon_id' => $entity_id,
                                'percentage' => $v['gradual_discount'],
                                'sequence' => $v['sequence']
                            );

                            //$this->coupon_model->addData('coupon_gradual',$gradual_data);
                        }
                        $this->coupon_model->insertBatch('coupon_gradual', $gradual_data, $id = '');
                    }

                    if ($this->input->post('all_restaurant') == "true") {
                        $all_resturant = $this->coupon_model->getAllRestaurantID();

                        foreach ($all_resturant as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value['entity_id'],
                                'coupon_id' => $entity_id
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_restaurant_map', $res_data, $id = '');

                        $all_res_id = [];

                        if ($all_resturant) {
                            foreach ($all_resturant as $k => $v) {
                                $all_res_id[] = $v['entity_id'];
                            }

                            $all_resturant_menu = $this->coupon_model->getResturantMenuIDs($all_res_id);

                            foreach ($all_resturant_menu as $key => $value) {
                                $item_data[] = array(
                                    'item_id' => $value['entity_id'],
                                    'coupon_id' => $entity_id
                                );
                            }
                            $this->coupon_model->insertBatch('coupon_item_map', $item_data, $id = '');
                        }
                    } else if (!empty($this->input->post('restaurant_id'))) {

                        $res_data = array();
                        foreach ($this->input->post('restaurant_id') as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value,
                                'coupon_id' => $entity_id
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_restaurant_map', $res_data, $id = '');
                    }

                    if ($this->input->post('all_menu') == "true") {
                        if (!empty($this->input->post('restaurant_id'))) {
                            $all_resturant_menu = $this->coupon_model->getResturantMenuIDs($this->input->post('restaurant_id'));
                            foreach ($all_resturant_menu as $key => $value) {
                                if (!empty($value['entity_id'])) {
                                    $item_data[] = array(
                                        'item_id' => $value['entity_id'],
                                        'coupon_id' => $entity_id
                                    );
                                }
                            }
                            if (!empty($item_data))
                                $this->coupon_model->insertBatch('coupon_item_map', $item_data, $id = '');
                        }
                    } else if (!empty($this->input->post('item_id'))) {
                        $item_data = array();
                        foreach ($this->input->post('item_id') as $key => $value) {
                            if (!empty($value)) {
                                $item_data[] = array(
                                    'item_id' => $value,
                                    'coupon_id' => $entity_id
                                );
                            }
                        }
                        if (!empty($item_data))
                            $this->coupon_model->insertBatch('coupon_item_map', $item_data, $id = '');
                    }

                    if ($this->input->post('all_user') == "true") {
                        $user_data = $this->coupon_model->getAllUser();
                        foreach ($this->input->post('user_id') as $key => $value) {
                            $user_data[] = array(
                                'user_id' => $value['entity_id'],
                                'coupon_id' => $entity_id
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_user_map', $user_data, $id = '');
                    } else {
                        if (!empty($_FILES['upload_csv']['name'])) {
                            $user_data = array();
                            $tmpName = $_FILES['upload_csv']['tmp_name'];
                            $csvAsArray = array_map('str_getcsv', file($tmpName));

                            foreach ($csvAsArray as $k => $v) {
                                if ($k !== 0) {
                                    $num_list[] = $v[0];
                                }
                            }

                            $user_id_list = $this->coupon_model->getUserByMobile($num_list);

                            foreach ($user_id_list as $key => $value) {
                                if (!empty($value['entity_id'])) {
                                    $user_data[] = array(
                                        'user_id' => $value['entity_id'],
                                        'coupon_id' => $entity_id
                                    );
                                }
                            }
                            if (!empty($user_data))
                                $this->coupon_model->insertBatch('coupon_user_map', $user_data, $id = '');
                        } else if (!empty($this->input->post('user_id'))) {
                            $user_data = array();
                            foreach ($this->input->post('user_id') as $key => $value) {
                                if (!empty($value)) {

                                    $user_data[] = array(
                                        'user_id' => $value,
                                        'coupon_id' => $entity_id
                                    );
                                }
                            }
                            // echo '<pre>';
                            // print_r($user_data);
                            // exit();

                            if (!empty($user_data))
                                $this->coupon_model->insertBatch('coupon_user_map', $user_data, $this->input->post('entity_id'));
                        }
                    }
                    echo "success";
                    exit;
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
                echo "failed";
                exit;
            }
            echo "failed";
            exit;
        }
        // $data['restaurant'] = $this->coupon_model->getListData('restaurant', array('status' => 1));
        // $data['user'] = $this->coupon_model->getListData('users', array('status' => 1, 'user_type' => 'User'));
        $this->load->view(ADMIN_URL . '/coupon_add', $data);
    }
    // edit coupon
    public function edit()
    {
        $data['meta_title'] = $this->lang->line('title_admin_couponedit') . ' | ' . $this->lang->line('site_title');
        // check if form is submitted
        if ($this->input->post('submit_page') == "Submit") {

            $this->form_validation->set_rules('name', 'Coupon Name', 'trim|callback_checkExist');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('coupon_type', 'Coupon Type', 'trim|required');
            if ($this->input->post('coupon_type') != 'free_delivery' && $this->input->post('coupon_type') != 'gradual' && $this->input->post('coupon_type') != 'discount_on_items') {
                $this->form_validation->set_rules('amount_type', 'Amount type', 'trim|required');
                $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
                $this->form_validation->set_rules('max_amount', 'Max Amount', 'trim|required');
            }

            $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'End Date', 'trim|required');
            //check form validation using codeigniter

            $_POST['restaurant_id'] = json_decode($this->input->post('restaurant_id'));
            $_POST['item_id'] = json_decode($this->input->post('item_id'));
            $_POST['user_id'] = json_decode($this->input->post('user_id'));
            $maximum_use = $this->input->post('maximum_use');
            if ($maximum_use == null || $maximum_use == '') {
                $maximum_use = 0;
            }

            if ($this->form_validation->run()) {
                $edit_data = array(
                    'coupon_type' => $this->input->post('coupon_type'),
                    'name' => strtoupper($this->input->post('name')),
                    'description' => $this->input->post('description'),
                    'amount_type' => ($this->input->post('coupon_type') != 'free_delivery' || $this->input->post('coupon_type') != 'gradual') ? $this->input->post('amount_type') : NULL,
                    'amount' => ($this->input->post('coupon_type') == 'gradual') ? 0 : $this->input->post('amount'),
                    'max_amount' => ($this->input->post('max_amount')) ? $this->input->post('max_amount') : 0,
                    'start_date' => date('Y-m-d H:i:s', strtotime($this->input->post('start_date'))),
                    'end_date' => date('Y-m-d H:i:s', strtotime($this->input->post('end_date'))),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID'),
                    'maximum_use' => $maximum_use,
                    'discount_amount' => ($this->input->post('discount_amount')) ? $this->input->post('discount_amount') : null,
                    'usablity' => $this->input->post('usablity'),
                    'gradual_all_items' => $this->input->post('gradual_all_items') ? $this->input->post('gradual_all_items') : 0,
                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/coupons';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/coupons')) {
                        @mkdir('./uploads/coupons', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "coupons/" . $img['file_name'];
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
                    $this->coupon_model->updateData($edit_data, 'coupon', 'entity_id', $this->input->post('entity_id'));

                    $entity_id = $this->input->post('entity_id');
                    // for gradual
                    if ($this->input->post('coupon_type') == 'gradual') {
                        foreach ($this->input->post('gradual_group') as $key => $v) {
                            $gradual_data[] = array(
                                'coupon_id' => $this->input->post('entity_id'),
                                'percentage' => $v['gradual_discount'],
                                'sequence' => $v['sequence']
                            );

                            //$this->coupon_model->addData('coupon_gradual',$gradual_data);
                        }
                        $this->coupon_model->insertBatch('coupon_gradual', $gradual_data, $this->input->post('entity_id'));
                    }

                    if ($this->input->post('all_restaurant') == "true") {
                        $all_resturant = $this->coupon_model->getAllRestaurantID();
                        foreach ($all_resturant as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value['entity_id'],
                                'coupon_id' => $entity_id
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_restaurant_map', $res_data, $entity_id);

                        $all_res_id = [];

                        if ($all_resturant) {
                            foreach ($all_resturant as $k => $v) {
                                $all_res_id[] = $v['entity_id'];
                            }
                            $all_resturant_menu = $this->coupon_model->getResturantMenuIDs($all_res_id);
                            foreach ($all_resturant_menu as $key => $value) {
                                $item_data[] = array(
                                    'item_id' => $value['entity_id'],
                                    'coupon_id' => $entity_id
                                );
                            }
                            $this->coupon_model->insertBatch('coupon_item_map', $item_data, $entity_id);
                        }
                    } else if (!empty($this->input->post('restaurant_id'))) {
                        $res_data = array();
                        foreach ($this->input->post('restaurant_id') as $key => $value) {
                            $res_data[] = array(
                                'restaurant_id' => $value,
                                'coupon_id' => $this->input->post('entity_id')
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_restaurant_map', $res_data, $this->input->post('entity_id'));
                    }

                    if ($this->input->post('all_menu') == "true") {
                        if (!empty($this->input->post('restaurant_id'))) {
                            $all_resturant_menu = $this->coupon_model->getResturantMenuIDs($this->input->post('restaurant_id'));

                            foreach ($all_resturant_menu as $key => $value) {
                                if (!empty($value['entity_id'])) {
                                    $item_data[] = array(
                                        'item_id' => $value['entity_id'],
                                        'coupon_id' => $entity_id
                                    );
                                }
                            }
                            if (!empty($item_data))
                                $this->coupon_model->insertBatch('coupon_item_map', $item_data, $entity_id);
                        }
                    } else if (!empty($this->input->post('item_id'))) {
                        $item_data = array();
                        foreach ($this->input->post('item_id') as $key => $value) {
                            if (!empty($value)) {
                                $item_data[] = array(
                                    'item_id' => $value,
                                    'coupon_id' => $entity_id
                                );
                            }
                        }
                        if (!empty($item_data))
                            $this->coupon_model->insertBatch('coupon_item_map', $item_data, $entity_id);
                    }

                    if ($this->input->post('all_user') == "true") {
                        $user_data = $this->coupon_model->getAllUser();
                        foreach ($this->input->post('user_id') as $key => $value) {
                            $user_data[] = array(
                                'user_id' => $value['entity_id'],
                                'coupon_id' => $entity_id
                            );
                        }
                        $this->coupon_model->insertBatch('coupon_user_map', $user_data, $entity_id);
                    } else {

                        if (!empty($_FILES['upload_csv']['name'])) {
                            $user_data = array();
                            $tmpName = $_FILES['upload_csv']['tmp_name'];
                            $csvAsArray = array_map('str_getcsv', file($tmpName));

                            foreach ($csvAsArray as $k => $v) {
                                if ($k !== 0) {
                                    $num_list[] = $v[0];
                                }
                            }

                            $user_id_list = $this->coupon_model->getUserByMobile($num_list);

                            foreach ($user_id_list as $key => $value) {
                                if (!empty($value['entity_id'])) {
                                    $user_data[] = array(
                                        'user_id' => $value['entity_id'],
                                        'coupon_id' => $entity_id
                                    );
                                }
                            }
                            if (!empty($user_data))
                                $this->coupon_model->insertBatch('coupon_user_map', $user_data, $entity_id);
                        } else if (!empty($this->input->post('user_id'))) {
                            $user_data = array();
                            foreach ($this->input->post('user_id') as $key => $value) {
                                if (!empty($value)) {

                                    $user_data[] = array(
                                        'user_id' => $value,
                                        'coupon_id' => $this->input->post('entity_id')
                                    );
                                }
                            }

                            if (!empty($user_data))
                                $this->coupon_model->insertBatch('coupon_user_map', $user_data, $this->input->post('entity_id'));
                        }
                    }
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    // redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                    echo "success";
                    exit;
                } else {
                    echo "failed";
                    exit;
                }
            } else {
                echo "failed";
                exit;
            }
        }
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->coupon_model->getEditDetail($entity_id);
        // $data['restaurant'] =  $this->coupon_model->restaurantMap($entity_id); //$this->coupon_model->getListData('restaurant',array('status'=>1));
        $data['restaurant_map'] = $this->coupon_model->getRestaurantMap($entity_id);
        $data['item_map'] = $data['edit_records']->coupon_type == "discount_on_items" || $data['edit_records']->coupon_type == "gradual" ? $this->coupon_model->getItemMap($entity_id) : new stdClass();
        $data['gradual_map'] = $this->coupon_model->getListData('coupon_gradual', array('coupon_id' => $entity_id));
        $data['type'] = 'edit';
        $data['user_map'] = $this->coupon_model->getUserMap($entity_id);
        // $data['user'] = $this->coupon_model->getListData('users', array('status' => 1, 'user_type' => 'User'));
        // echo '<pre>';
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/coupon_add', $data);
    }

    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'name', 2 => 'amount', 3 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->coupon_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $amount_type = ($val->amount_type == 'Percentage') ? '%' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                ($val->amount) ? number_format_unchanged_precision($val->amount) . $amount_type : '',
                ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
                ($this->lpermission->method('coupons', 'update')->access()
                    ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit')
                    : '') .
                    ($this->lpermission->method('coupons', 'delete')->access()
                        ? '</a> <button onclick="deleteDetail(' . $val->entity_id . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' :
                        '') .
                    ($this->lpermission->method('coupons', 'update')->access()
                        ? '<button onclick="disable_record(' . $val->entity_id . ',' . $val->status . ')"  title="' . $this->lang->line('click_for') . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->status ? 'times' : 'check') . '"></i> ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . '</button>'
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
    public function ajaxDisable()
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
        $this->coupon_model->deleteUser('coupon', $entity_id);
    }
    public function checkExist()
    {
        $coupon = ($this->input->post('coupon') != '') ? $this->input->post('coupon') : '';
        if ($this->input->post('amount')) {
            if ($coupon != '') {
                $check = $this->coupon_model->checkExist($coupon, $this->input->post('entity_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkExist', $this->lang->line('coupon_exist'));
                    return false;
                }
            }
        } else {
            if ($coupon != '') {
                $check = $this->coupon_model->checkExist($coupon, $this->input->post('entity_id'));
                echo $check;
            }
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

    public function getUsers()
    {
        $user_info   = $this->input->post('term', TRUE);
        $user_data   = $this->coupon_model->getUserData($user_info);

        if (!empty($user_data)) {
            foreach ($user_data as $user) {
                $user_json[] = array(
                    'label' => $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['mobile_number'] . ') ',
                    'id'    => $user['entity_id']
                );
            }
        } else {
            $user_json[] = 'No User Found';
        }
        echo json_encode($user_json);
    }

    public function getRestaurants()
    {
        $res_info   = $this->input->post('term', TRUE);
        $res_data   = $this->coupon_model->getRestaurantData($res_info);

        if (!empty($res_data)) {
            foreach ($res_data as $res) {
                $res_json[] = array(
                    'label' => $res['name'],
                    'id'    => $res['entity_id']
                );
            }
        } else {
            $res_json[] = 'No Restaurant Found';
        }
        echo json_encode($res_json);
    }

    public function getRestaurantMenu()
    {
        $menu_info   = $this->input->post('term', TRUE);
        $res_id   = $this->input->post('res_id', TRUE);
        $coupon_type = $this->input->post('coupon_type');
        $type = $this->input->post('type');
        $coupon_id = $this->input->post('coupon_id');

        $menu_data   = $this->coupon_model->getRestaurantMenuData($menu_info, $res_id, $coupon_type, $type, $coupon_id);

        if (!empty($menu_data)) {
            foreach ($menu_data as $menu) {
                $menu_json[$menu['restaurant_name']][] = array(
                    'label'       => $menu['name'],
                    'id'          => $menu['entity_id'],
                    // 'res_name'    => $menu['restaurant_name'],
                );
            }
        } else {
            $menu_json[] = 'No Menu Found';
        }

        echo json_encode($menu_json);
    }
}
