<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Branch extends CI_Controller
{
    public $controller_name = 'branch';
    public $prefix = '_br';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/branch_model');
        $this->load->model(ADMIN_URL . '/restaurant_model');
    }
    // view branch
    public function view()
    {
        $data['meta_title'] = $this->lang->line('title_admin_branch') . ' | ' . $this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/branch', $data);
    }
    // add branch
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_branchadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Branch Name', 'trim|required');
            $this->form_validation->set_rules('branch_entity_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            // $this->form_validation->set_rules('capacity', 'Capacity', 'trim|required');
            // $this->form_validation->set_rules('no_of_table', 'No of table', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            // $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            // $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required');
            $this->form_validation->set_rules('currency_id', 'Currency', 'trim|required');
            $this->form_validation->set_rules('enable_hours', 'Enable Hours', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                if (!$this->input->post('content_id')) {
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                        'content_type' => $this->uri->segment('2'),
                        'created_by' => $this->session->userdata("UserID"),
                        'created_date' => date('Y-m-d H:i:s')
                    );
                    $ContentID = $this->branch_model->addData('content_general', $add_content);
                    $restaurant_slug = slugify($this->input->post('name'), 'restaurant', 'restaurant_slug');
                } else {
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->branch_model->getRestaurantSlug($this->input->post('content_id'));
                    $restaurant_slug = $slug->restaurant_slug;
                }

                $parent_restaurant_info = $this->restaurant_model->getRestaurantImages($this->input->post('branch_entity_id'));
                $parent_restaurant_menu = $this->restaurant_model->getRestaurantAllMenu($this->input->post('branch_entity_id'));

                // echo '<pre>';
                // print_r($parent_restaurant_menu);
                // exit();

                $add_data = array(
                    'name' => $this->input->post('name'),
                    'restaurant_slug' => $restaurant_slug,
                    'branch_entity_id' => $this->input->post('branch_entity_id'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    // 'capacity' =>$this->input->post('capacity'),
                    // 'no_of_table' =>$this->input->post('no_of_table'),
                    // 'no_of_hall' =>$this->input->post('no_of_hall'),
                    // 'hall_capacity' =>$this->input->post('hall_capacity'),
                    //'amount_type'=>$this->input->post("amount_type"),
                    //'amount'=>($this->input->post("amount"))?$this->input->post("amount"):'',
                    'enable_hours' => $this->input->post("enable_hours"),
                    'currency_id' => $this->input->post('currency_id'),
                    'is_veg' => ($this->input->post('is_veg') != '') ? $this->input->post('is_veg') : NULL,
                    'content_id' => $ContentID,
                    'language_slug' => $this->uri->segment('4') ? $this->uri->segment('4') : 'en',
                    'status' => 1,
                    'vat' => $this->input->post('vat'),
                    'sd' => $this->input->post('sd'),
                    'created_by' => $this->input->post('admin_user') ? $this->input->post('admin_user') : null,
                    'commission' => $this->input->post('commission'),
                    'price_range' => $this->input->post('start_range') . '-' . $this->input->post('end_range'),
                    'zonal_admin' => ($this->input->post('zonal_admin') ? $this->input->post('zonal_admin') : null),
                    'delivery_time' => $this->input->post('delivery_time') ? $this->input->post('delivery_time') : null
                );


                if (!empty($this->input->post('timings'))) {
                    $timingsArr = $this->input->post('timings');
                    $newTimingArr = array();
                    foreach ($timingsArr as $key => $value) {
                        if (isset($value['off'])) {
                            $newTimingArr[$key]['open'] = '';
                            $newTimingArr[$key]['close'] = '';
                            $newTimingArr[$key]['off'] = '0';
                        } else {
                            if (!empty($value['open']) && !empty($value['close'])) {
                                $newTimingArr[$key]['open'] = $value['open'];
                                $newTimingArr[$key]['close'] = $value['close'];
                                $newTimingArr[$key]['off'] = '1';
                            } else {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            }
                        }
                    }
                    $add_data['timings'] = serialize($newTimingArr);
                }

                if (!empty($this->input->post('break_timing'))) {
                    $timingsArr = $this->input->post('break_timing');
                    $newTimingArr = array();
                    foreach ($timingsArr as $t_key => $t_value) {
                        if (!isset($t_value['on'])) {
                            $newTimingArr[$t_key]['open'] = '';
                            $newTimingArr[$t_key]['close'] = '';
                            $newTimingArr[$t_key]['on'] = '0';
                        } else {
                            if (!empty($t_value['open']) && !empty($t_value['close'])) {
                                $newTimingArr[$t_key]['open'] = $t_value['open'];
                                $newTimingArr[$t_key]['close'] = $t_value['close'];
                                $newTimingArr[$t_key]['on'] = '1';
                            } else {
                                $newTimingArr[$t_key]['open'] = '';
                                $newTimingArr[$t_key]['close'] = '';
                                $newTimingArr[$t_key]['on'] = '0';
                            }
                        }
                    }
                    $add_data['break_timing'] = serialize($newTimingArr);
                }

                if (!empty($_FILES['Image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('Image')) {
                        $img = $this->upload_cloud->data();
                        $add_data['image'] = "restaurant/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                } else {
                    $add_data['image'] = $parent_restaurant_info->image;
                }

                if (!empty($_FILES['CoverImage']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('CoverImage')) {
                        $img = $this->upload_cloud->data();
                        $add_data['cover_image'] = "restaurant/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                } else {
                    $add_data['cover_image'] = $parent_restaurant_info->cover_image;
                }

                $entity_id = '';

                if (empty($data['Error'])) {
                    $entity_id = $this->branch_model->addData('restaurant', $add_data);

                    //for address
                    $add_data = array(
                        'resto_entity_id' => $entity_id,
                        'address' => $this->input->post('address'),
                        'landmark' => $this->input->post('landmark'),
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post("longitude"),
                        // 'state'=>$this->input->post("state"),
                        'country' => $this->input->post("country"),
                        'city' => $this->input->post("city"),
                        'zipcode' => $this->input->post("zipcode"),
                        'content_id' => $ContentID,
                        'language_slug' => $this->uri->segment('4'),
                    );
                    $branch_id = $this->branch_model->addData('restaurant_address', $add_data);


                    if ($parent_restaurant_menu) {
                        foreach ($parent_restaurant_menu as $menus) {

                            $menu_addon_data = $this->restaurant_model->getTableData('add_ons_master', 'menu_id', $menus['entity_id']);
                            $menu_variation_data = $this->restaurant_model->getTableData('variations', 'menu_id', $menus['entity_id']);

                            if (!$menus['menu_group_id'] || $menus['menu_group_id'] == '') {
                                $menu_group_id = mt_rand(1000, 999999);
                                $this->db->where('entity_id', $menus['entity_id']);
                                $this->db->update('restaurant_menu_item', array('menu_group_id' => $menu_group_id));
                                $menus['menu_group_id'] = $menu_group_id;
                            }

                            $add_content = array(
                                'content_type' => 'menu',
                                'created_by' => $this->session->userdata("UserID"),
                                'created_date' => date('Y-m-d H:i:s')
                            );
                            $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                            $menus['content_id'] = $ContentID;
                            $menus['created_by'] = $this->session->userdata("UserID");
                            $menus['created_date'] = date('Y-m-d H:i:s');

                            $item_slug = slugify($menus['name'], 'restaurant_menu_item', 'item_slug');
                            $menus['item_slug'] = $item_slug;
                            $menus['restaurant_id'] = $entity_id;

                            unset($menus['entity_id']);

                            $menu_id = $this->restaurant_model->addData('restaurant_menu_item', $menus);
                            foreach ($menu_addon_data as $each_add_on) {
                                $each_add_on['menu_id'] = $menu_id;
                                unset($each_add_on['add_ons_id']);

                                $this->restaurant_model->addData('add_ons_master', $each_add_on);
                            }

                            foreach ($menu_variation_data as $each_variation) {
                                $each_variation['menu_id'] = $menu_id;
                                unset($each_variation['entity_id']);

                                $this->restaurant_model->addData('variations', $each_variation);
                            }
                        }
                    }

                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['restaurant'] = $this->branch_model->getListData('restaurant', $language_slug);
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        $data['admin'] = $this->restaurant_model->getAdmins();
        $search = array('user_type' => 'ZonalAdmin');
        $data['zonalAdmin'] = $this->restaurant_model->getRecord('users', $search);
        if (!empty($this->uri->segment('5'))) {
            $getRestaurantCurrency = $this->common_model->getRestaurantCurrency($this->uri->segment('5'));
            $data['res_currency_id'] = $getRestaurantCurrency->currency_id;
        }
        $this->load->view(ADMIN_URL . '/branch_add', $data);
    }
    // edit branch
    public function edit()
    {
        $data['meta_title'] = $this->lang->line('title_admin_branchedit') . ' | ' . $this->lang->line('site_title');
        // check if form is submitted
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Branch Name', 'trim|required');
            $this->form_validation->set_rules('branch_entity_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            // $this->form_validation->set_rules('capacity', 'Capacity', 'trim|required|numeric');
            // $this->form_validation->set_rules('no_of_table', 'No of table', 'trim|required|numeric');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            // $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            // $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required');
            $this->form_validation->set_rules('currency_id', 'Currency', 'trim|required');
            $this->form_validation->set_rules('enable_hours', 'Enable Hours', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $content_id = $this->branch_model->getContentId($this->input->post('entity_id'), 'restaurant');
                $slug = $this->branch_model->getRestaurantSlug($this->input->post('content_id'));
                if (!empty($slug->restaurant_slug)) {
                    $restaurant_slug = $slug->restaurant_slug;
                } else {
                    $restaurant_slug = slugify($this->input->post('name'), 'restaurant', 'restaurant_slug', 'content_id', $content_id->content_id);
                }

                $edit_data = array(
                    'branch_entity_id' => $this->input->post('branch_entity_id'),
                    'name' => $this->input->post('name'),
                    'restaurant_slug' => $restaurant_slug,
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    // 'capacity' => $this->input->post('capacity'),
                    // 'no_of_table' => $this->input->post('no_of_table'),
                    // 'no_of_hall' => $this->input->post('no_of_hall'),
                    // 'hall_capacity' => $this->input->post('hall_capacity'),
                    //'amount_type'=>$this->input->post("amount_type"),
                    //'amount'=>($this->input->post("amount"))?$this->input->post("amount"):'',
                    'enable_hours' => $this->input->post("enable_hours"),
                    'currency_id' => $this->input->post('currency_id'),
                    'is_veg' => ($this->input->post('is_veg') != '') ? $this->input->post('is_veg') : NULL,
                    'status' => 1,
                    'created_by' => $this->input->post('admin_user') ? $this->input->post('admin_user') : null,
                    'commission' => $this->input->post('commission'),
                    'price_range' => $this->input->post('start_range') . '-' . $this->input->post('end_range'),
                    'zonal_admin' => ($this->input->post('zonal_admin') ? $this->input->post('zonal_admin') : null),
                    'delivery_time' =>  $this->input->post('delivery_time') ? $this->input->post('delivery_time') : null,
                    'vat' => $this->input->post('vat'),
                    'sd' => $this->input->post('sd')

                );

                if (!empty($this->input->post('timings'))) {
                    $timingsArr = $this->input->post('timings');
                    $newTimingArr = array();
                    foreach ($timingsArr as $key => $value) {
                        if (isset($value['off'])) {
                            $newTimingArr[$key]['open'] = '';
                            $newTimingArr[$key]['close'] = '';
                            $newTimingArr[$key]['off'] = '0';
                        } else {
                            if (!empty($value['open']) && !empty($value['close'])) {
                                $newTimingArr[$key]['open'] = $value['open'];
                                $newTimingArr[$key]['close'] = $value['close'];
                                $newTimingArr[$key]['off'] = '1';
                            } else {
                                $newTimingArr[$key]['open'] = '';
                                $newTimingArr[$key]['close'] = '';
                                $newTimingArr[$key]['off'] = '0';
                            }
                        }
                    }
                    $edit_data['timings'] = serialize($newTimingArr);
                }

                if (!empty($this->input->post('break_timing'))) {
                    $timingsArr = $this->input->post('break_timing');
                    $newTimingArr = array();
                    foreach ($timingsArr as $t_key => $t_value) {
                        if (!isset($t_value['on'])) {
                            $newTimingArr[$t_key]['open'] = '';
                            $newTimingArr[$t_key]['close'] = '';
                            $newTimingArr[$t_key]['on'] = '0';
                        } else {
                            if (!empty($t_value['open']) && !empty($t_value['close'])) {
                                $newTimingArr[$t_key]['open'] = $t_value['open'];
                                $newTimingArr[$t_key]['close'] = $t_value['close'];
                                $newTimingArr[$t_key]['on'] = '1';
                            } else {
                                $newTimingArr[$t_key]['open'] = '';
                                $newTimingArr[$t_key]['close'] = '';
                                $newTimingArr[$t_key]['on'] = '0';
                            }
                        }
                    }
                    $edit_data['break_timing'] = serialize($newTimingArr);
                }

                if (!empty($_FILES['Image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('Image')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "restaurant/" . $img['file_name'];
                        if ($this->input->post('uploaded_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }

                if (!empty($_FILES['CoverImage']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/restaurant';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/restaurant')) {
                        @mkdir('./uploads/restaurant', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('CoverImage')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['cover_image'] = "restaurant/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }

                if (empty($data['Error'])) {

                    // echo '<pre>';
                    // print_r($edit_data);
                    // exit();
                    $this->branch_model->updateData($edit_data, 'restaurant', 'entity_id', $this->input->post('entity_id'));
                    //Update data in Restaurant menu item
                    $update_res_menu =  array(
                        'vat' =>
                        $this->input->post('vat'),
                        'sd' =>
                        $this->input->post('sd'),
                    );
                    $this->restaurant_model->updateData($update_res_menu, 'restaurant_menu_item', 'restaurant_id', $this->input->post('entity_id'));
                    //for address
                    $edit_data = array(
                        'resto_entity_id' => $this->input->post('entity_id'),
                        'address' => $this->input->post('address'),
                        'landmark' => $this->input->post('landmark'),
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post("longitude"),
                        // 'state' => $this->input->post("state"),
                        'country' => $this->input->post("country"),
                        'city' => $this->input->post("city"),
                        'zipcode' => $this->input->post("zipcode"),
                    );

                    $this->branch_model->updateData($edit_data, 'restaurant_address', 'resto_entity_id', $this->input->post('entity_id'));
                    // exit();
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['restaurant'] = $this->branch_model->getListData('restaurant', $language_slug);
        $entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->branch_model->getEditDetail('restaurant', $entity_id);
        $data['admin'] = $this->restaurant_model->getAdmins();
        $search = array('user_type' => 'ZonalAdmin');
        $data['zonalAdmin'] = $this->restaurant_model->getRecord('users', $search);
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        $this->load->view(ADMIN_URL . '/branch_add', $data);
    }
    // method to change status
    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->branch_model->UpdatedStatus('restaurant', $entity_id, $this->input->post('status'));
        }
    }
    // method for delete
    public function ajaxDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->branch_model->ajaxDelete('restaurant', $this->input->post('content_id'), $entity_id);
    }
    public function ajaxDeleteAll()
    {
        $content_id = ($this->input->post('content_id') != '') ? $this->input->post('content_id') : '';
        $branch_id = ($this->input->post('branch_id') != '') ? $this->input->post('branch_id') : '';
        $this->branch_model->ajaxDeleteAll('restaurant', $content_id, $branch_id);
    }
    // call for ajax data
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(3 => 'restaurant.created_date', 4 => 'resta.name');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->branch_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        // echo '<pre>';
        // print_r($grid_data);
        // exit();
        $totalRecords = $grid_data['total'];
        $Languages = $this->common_model->getLanguages();
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access =
                $this->lpermission->method('branch', 'delete')->access()
                ? '<button onclick="deleteAll(' . $value['content_id'] . ',' . $value['entity_id'] . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>'
                : "";

            $deeplink = DEEP_LINK_BASE_URL . generateDeepLink('restaurant', $value['entity_id']);

            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                $value['rname'],
                '<button class="btn" onclick="copyToClipboard(\'' . $deeplink . '\')"><i class="fa fa-copy"></i></button>',
                $edit_active_access
            );
            $cusLan = array();
            foreach ($Languages as $lang) {
                if (array_key_exists($lang->language_slug, $value['translations'])) {
                    $cusLan[] =
                        ($this->lpermission->method('branch', 'update')->access()
                            ? '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . $lang->language_slug . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])) . '" title="' . $this->lang->line('click_edit') . '"><i class="fa fa-edit"></i> </a>'
                            : '') .
                        ($this->lpermission->method('branch', 'update')->access()
                            ? '<a style="cursor:pointer;" onclick="disablePage(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . $value['translations'][$lang->language_slug]['status'] . ')"  title="Click here to ' . ($value['translations'][$lang->language_slug]['status'] ? 'Inactive' : 'Activate') . '"><i class="fa fa-toggle-' . ($value['translations'][$lang->language_slug]['status'] ? 'on' : 'off') . '"></i> </a>'
                            : '') .
                        ($this->lpermission->method('branch', 'delete')->access()
                            ? '<a style="cursor:pointer;" onclick="deleteDetail(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . $value['content_id'] . ')"  title="' . $this->lang->line('click_delete') . '"><i class="fa fa-times"></i> </a>
                    ( ' . $value['translations'][$lang->language_slug]['name'] . ' )'
                            : '');
                } else {
                    $cusLan[] = $this->lpermission->method('branch', 'create')->access() ?  '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/add/' . $lang->language_slug . '/' . $value['content_id'] . '" title="' . $this->lang->line('click_add') . '"><i class="fa fa-plus"></i></a>' : '';
                }
            }
            // added to specific position
            array_splice($records["aaData"][$cnt], 2, 0, $cusLan);
            $cnt++;
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
}
