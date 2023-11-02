<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Restaurant extends CI_Controller
{
    public $controller_name = 'restaurant';
    public $prefix = '_re';
    public $menu_prefix = '_menu';
    public $package_prefix = '_pac';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/restaurant_model');
        $this->load->model(ADMIN_URL . '/users_model');
    }
    // view restaurant
    public function view()
    {
        $data['allRestaurant'] = $this->restaurant_model->getAllRestaurant(1);
        $data['zone'] = $this->users_model->getzone();
        $data['meta_title'] = $this->lang->line('title_admin_restaurant') . ' | ' . $this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/restaurant', $data);
    }
    public function getAllRestaurant_Zonewise()
    {
        $zone_id = $this->input->post('zone_id');
        $this->db->select("restaurant.name,restaurant.entity_id");
        $this->db->join('zone_res_map', 'zone_res_map.restaurant_id = restaurant.entity_id', 'left');
        $this->db->where('zone_res_map.zone_id', $zone_id);
        $this->db->from("restaurant");
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
                $update = $this->restaurant_model->updateMenuSort($data, $id);
                $count++;
            }
        }

        return true;
    }

    // add restaurant
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Restaurant Name', 'trim|required');
            $this->form_validation->set_rules('currency_id', 'Currency', 'trim|required');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|callback_checkExist');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_checkEmailExist');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            // $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required');
            $this->form_validation->set_rules('enable_hours', 'Enable Hours', 'trim|required');
            $this->form_validation->set_rules('commission', 'Commission', 'trim|required');
            // $this->form_validation->set_rules('priceRange', 'Price Range', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                if (!$this->input->post('content_id')) {
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                        'content_type' => $this->uri->segment('2'),
                        'created_by' => $this->session->userdata("UserID"),
                        'created_date' => date('Y-m-d H:i:s')
                    );
                    $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                    $restaurant_slug = slugify($this->input->post('name'), 'restaurant', 'restaurant_slug');
                } else {
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                    $restaurant_slug = $slug->restaurant_slug;
                }
                //$currency_id = $this->common_model->getCurrencyID('Ariary');
                $add_data = array(
                    'name' => $this->input->post('name'),
                    'restaurant_slug' => $restaurant_slug,
                    //'currency_id' =>$currency_id->currency_id,
                    'currency_id' => $this->input->post('currency_id'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    'enable_hours' => $this->input->post("enable_hours"),
                    'status' => 1,
                    'content_id' => $ContentID,
                    'language_slug' => $this->uri->segment('4'),
                    'is_veg' => ($this->input->post('is_veg') != '') ? $this->input->post('is_veg') : NULL,
                    'price_range' => $this->input->post('start_range') . '-' . $this->input->post('end_range'),
                    'driver_commission' => $this->input->post('driver_commission'),
                    'created_by' => $this->input->post('admin_user') ? $this->input->post('admin_user') : NULL,
                    'zonal_admin' => $this->input->post('zonal_admin') ? $this->input->post('zonal_admin') : NULL,
                    'central_admin' => ($this->input->post('central_admin') != '') ? $this->input->post('central_admin') : null,
                    'commission' => $this->input->post('commission'),
                    'vat' => $this->input->post('vat'),
                    'sd' => $this->input->post('sd'),
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
                }


                //Cover Image

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
                }

                $entity_id = '';
                if (empty($data['Error'])) {
                    $entity_id = $this->restaurant_model->addData('restaurant', $add_data);
                    //for address
                    $add_data = array(
                        'resto_entity_id' => $entity_id,
                        'address' => $this->input->post('address'),
                        'landmark' => $this->input->post('landmark'),
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post("longitude"),
                        'country' => $this->input->post("country"),
                        'city' => $this->input->post("city"),
                        'zipcode' => $this->input->post("zipcode"),
                        'content_id' => $ContentID,
                        'language_slug' => $this->uri->segment('4'),
                    );
                    $this->restaurant_model->addData('restaurant_address', $add_data);
                    if ($this->session->userdata('adminemail')) {
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'new-restaurant-alert', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();

                        $arrayData = array('FirstName' => $this->session->userdata('adminFirstname'), 'restaurant_name' => $this->input->post('name'));
                        $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                        if (!empty($EmailBody)) {
                            $this->load->library('email');
                            $config['charset'] = 'iso-8859-1';
                            $config['wordwrap'] = TRUE;
                            $config['mailtype'] = 'html';
                            $this->email->initialize($config);
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                            $this->email->to(trim($this->session->userdata('adminemail')));
                            $this->email->subject($Emaildata->subject);
                            $this->email->message($EmailBody);
                            $this->email->send();
                        }
                    }
                    //get restaurant ans set in session
                    $restaurant = $this->common_model->getRestaurantinSession('restaurant', $this->session->userdata('UserID'));
                    if (!empty($restaurant)) {
                        $restaurant = array_column($restaurant, 'entity_id');
                        $this->session->set_userdata('restaurant', $restaurant);
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        $data['admin'] = $this->restaurant_model->getAdmins();
        $search = array('user_type' => 'ZonalAdmin');
        $data['zonalAdmin'] = $this->restaurant_model->getRecord('users', $search);
        $search = array('user_type' => 'CentralAdmin');
        $data['centralAdmin'] = $this->restaurant_model->getRecord('users', $search);
        if (!empty($this->uri->segment('5'))) {
            $getRestaurantCurrency = $this->common_model->getRestaurantCurrency($this->uri->segment('5'));
            $data['res_currency_id'] = $getRestaurantCurrency->currency_id;
        }
        $this->load->view(ADMIN_URL . '/restaurant_add', $data);
    }
    // edit restaurant
    public function edit()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantedit') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('name', 'Restaurant Name', 'trim|required');
            $this->form_validation->set_rules('currency_id', 'Currency', 'trim|required');
            $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            //$this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required');
            $this->form_validation->set_rules('enable_hours', 'Enable Hours', 'trim|required');
            $this->form_validation->set_rules('commission', 'Commission', 'trim|required');
            // $this->form_validation->set_rules('priceRange', 'Price Range', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $vat = $this->input->post('vat');
                $sd = $this->input->post('sd');
                $content_id = $this->restaurant_model->getContentId($this->input->post('entity_id'), 'restaurant');
                $slug = $this->restaurant_model->getRestaurantSlug($this->input->post('content_id'));
                if (!empty($slug->restaurant_slug)) {
                    $restaurant_slug = $slug->restaurant_slug;
                } else {
                    $restaurant_slug = slugify($this->input->post('name'), 'restaurant', 'restaurant_slug', 'content_id', $content_id->content_id);
                }
                $edit_data = array(
                    'name' => $this->input->post('name'),
                    'restaurant_slug' => $restaurant_slug,
                    'currency_id' => $this->input->post('currency_id'),
                    'phone_number' => $this->input->post('phone_number'),
                    'email' => $this->input->post('email'),
                    'enable_hours' => $this->input->post("enable_hours"),
                    'status' => $this->input->post("status"),
                    'updated_by' => $this->session->userdata('UserID'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'is_veg' => ($this->input->post('is_veg') != '') ? $this->input->post('is_veg') : NULL,
                    'price_range' => $this->input->post('start_range') . '-' . $this->input->post('end_range'),
                    'driver_commission' => $this->input->post('driver_commission'),
                    'created_by' => $this->input->post('admin_user') ? $this->input->post('admin_user') : null,
                    'zonal_admin' => $this->input->post('zonal_admin') ? $this->input->post('zonal_admin') : null,
                    'central_admin' => ($this->input->post('central_admin') != '') ? $this->input->post('central_admin') : null,
                    'commission' => $this->input->post('commission'),
                    'vat' => $vat,
                    'sd' => $sd,
                    'delivery_time' =>  $this->input->post('delivery_time') ? $this->input->post('delivery_time') : null
                );
                //Update data in Restaurant menu item
                $update_res_menu =  array(
                    'vat' => $vat,
                    'sd' => $sd
                );
                $this->restaurant_model->updateData($update_res_menu, 'restaurant_menu_item', 'restaurant_id', $this->input->post('entity_id'));
                $branches = $this->restaurant_model->checkHasBranch($this->input->post('entity_id'));
                if ($branches) {
                    foreach ($branches as $br) {
                        $branch_id = $br->entity_id;
                        $this->restaurant_model->updateData($update_res_menu, 'restaurant_menu_item', 'restaurant_id', $branch_id);
                        $this->restaurant_model->updateData($update_res_menu, 'restaurant', 'entity_id', $branch_id);
                    }
                }
                //Update data in Restaurant menu item
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
                //cover Image
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
                        if ($this->input->post('uploaded_cover_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_cover_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }

                if (empty($data['Error'])) {
                    $this->restaurant_model->updateData($edit_data, 'restaurant', 'entity_id', $this->input->post('entity_id'));
                    //for address
                    $edit_data = array(
                        'resto_entity_id' => $this->input->post('entity_id'),
                        'address' => $this->input->post('address'),
                        'landmark' => $this->input->post('landmark'),
                        'latitude' => $this->input->post('latitude'),
                        'longitude' => $this->input->post("longitude"),
                        'country' => $this->input->post("country"),
                        'city' => $this->input->post("city"),
                        'zipcode' => $this->input->post("zipcode"),
                    );
                    $this->restaurant_model->updateData($edit_data, 'restaurant_address', 'resto_entity_id', $this->input->post('entity_id'));
                    if ($this->session->userdata('adminemail')) {
                        $this->db->select('OptionValue');
                        $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

                        $this->db->select('OptionValue');
                        $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                        $this->db->select('subject,message');
                        $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'restaurant-details-update-alert', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
                        $arrayData = array('FirstName' => $this->session->userdata('adminFirstname'), 'restaurant_name' => $this->input->post('name'));
                        $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                        if (!empty($EmailBody)) {
                            $this->load->library('email');
                            $config['charset'] = 'iso-8859-1';
                            $config['wordwrap'] = TRUE;
                            $config['mailtype'] = 'html';
                            $this->email->initialize($config);
                            $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                            $this->email->to(trim($this->session->userdata('adminemail')));
                            $this->email->subject($Emaildata->subject);
                            $this->email->message($EmailBody);
                            $this->email->send();
                        }
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant', $entity_id);
        $data['admin'] = $this->restaurant_model->getAdmins();
        $search = array('user_type' => 'ZonalAdmin');
        $data['zonalAdmin'] = $this->restaurant_model->getRecord('users', $search);
        $search = array('user_type' => 'CentralAdmin');
        $data['centralAdmin'] = $this->restaurant_model->getRecord('users', $search);
        $data['currencies'] = $this->common_model->getCountriesCurrency();
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/restaurant_add', $data);
    }
    // call for ajax data
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'name', 2 => 'status', 3 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->restaurant_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $Languages = $this->common_model->getLanguages();
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $cnt = 0;
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $value) {
            $edit_active_access = ($this->lpermission->method('restaurant', 'delete')->access()) ? '<button onclick="deleteAll(' . $value['content_id'] . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' : '';
            $edit_active_access .= ($this->lpermission->method('restaurant', 'update')->access()) ? '<p onclick="disablePage(' . $value['translations']['en']['translation_id'] . ',' . !$value['translations']['en']['status'] . ')"  title="' . $this->lang->line('click_for') . ' ' . ($value['status'] ? $this->lang->line('inactive') : $this->lang->line('active')) . '"><button class="margin-bottom btn-sm btn-' . ($value['status'] ? 'success' : 'danger') . '"><i class="fa fa-' . ($value['status'] ? 'check' : 'times') . '"></i>' . ($value['status'] ? "On" : "Off") . '</button></p>' : '';
            $deeplink = DEEP_LINK_BASE_URL . generateDeepLink('restaurant', $value['entity_id']);
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                '<button class="btn" onclick="copyToClipboard(\'' . $deeplink . '\')"><i class="fa fa-copy"></i></button>',
                ($value['status'] == 1) ? "<span class='text-success'><strong>On</strong></span>" : "<span class='text-danger'><strong>Off</strong></span>",
                $edit_active_access
            );
            $cusLan = array();
            foreach ($Languages as $lang) {
                if (array_key_exists($lang->language_slug, $value['translations'])) {
                    $cusLan[] = ($this->lpermission->method('restaurant', 'update')->access()
                        ? '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . $lang->language_slug . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])) . '" title="' . $this->lang->line('click_edit') . '"><i class="fa fa-edit"></i> </a>'
                        : '') .
                        ($this->lpermission->method('restaurant', 'update')->access()
                            ? '<a style="cursor:pointer;" onclick="disablePage(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . !$value['translations'][$lang->language_slug]['status'] . ')"  title="Click here to turn ' . ($value['translations'][$lang->language_slug]['status'] ? 'Off' : 'On') . '"><i class="fa fa-toggle-' . ($value['translations'][$lang->language_slug]['status'] ? 'on' : 'off') . '"></i> </a>'
                            : '') .
                        ($this->lpermission->method('restaurant', 'delete')->access()
                            ? '<a style="cursor:pointer;" onclick="deleteDetail(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . $value['content_id'] . ')"  title="' . $this->lang->line('click_delete') . '"><i class="fa fa-times"></i> </a>
                        ( ' . $value['translations'][$lang->language_slug]['name'] . ' )'
                            : '');
                } else {
                    $cusLan[] = ($this->lpermission->method('restaurant', 'create')->access() ? '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/add/' . $lang->language_slug . '/' . $value['content_id'] . '" title="' . $this->lang->line('click_add') . '"><i class="fa fa-plus"></i></a>' : '');
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
    /*
     * Update status for Single
     */
    // method to change restaurant status
    public function ajaxDisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->restaurant_model->UpdatedStatus($this->input->post('tblname'), $entity_id, $this->input->post('status'));
        }
    }
    // method for deleting a restaurant
    public function ajaxDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->restaurant_model->ajaxDelete($this->input->post('tblname'), $this->input->post('content_id'), $entity_id);
    }
    public function ajaxDeleteAll()
    {
        $content_id = ($this->input->post('content_id') != '') ? $this->input->post('content_id') : '';
        $this->restaurant_model->ajaxDeleteAll($this->input->post('tblname'), $content_id);
    }
    // view restaurant menu
    public function view_menu()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenu') . ' | ' . $this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $this->session->userdata('language_slug'));
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/restaurant_menu', $data);
    }

    public function edit_res_menu()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenuedit') . ' | ' . $this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $this->session->userdata('language_slug'));
        $this->load->view(ADMIN_URL . '/restaurant_menu_edit', $data);
    }
    //add menu
    public function add_menu()
    {
        $error = 0;
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['addons_category'] = $this->restaurant_model->getListData('add_ons_category', $language_slug);
        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenuadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {

            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('category_id', 'Category', 'trim|required');
            $restaurant_details = $this->restaurant_model->getEditDetail('restaurant', $this->input->post('restaurant_id'));
            foreach ($this->input->post('menu-group') as $key => $value) {

                $menu_group_id = mt_rand(1000, 999999);

                if (!empty($this->input->post('content_id'))) {
                    $ContentID = $this->input->post('content_id');
                    $slug = $this->restaurant_model->getItemSlug($this->input->post('content_id'));
                    $item_slug = $slug->item_slug;
                } else {
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                        'content_type' => 'menu',
                        'created_by' => $this->session->userdata("UserID"),
                        'created_date' => date('Y-m-d H:i:s')
                    );
                    $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                    $item_slug = slugify($value['name'], 'restaurant_menu_item', 'item_slug');
                }

                $item_slug = slugify($value['name'], 'restaurant_menu_item', 'item_slug');
                $add_data = array(
                    'name' => $value['name'],
                    'item_slug' => $item_slug,
                    'restaurant_id' => $this->input->post('restaurant_id'),
                    'category_id' => $this->input->post('category_id'),
                    'price' => ($value['price']) ? $value['price'] : NULL,
                    'menu_detail' => $value['menu_details'],
                    // 'ingredients' => $this->input->post('ingredient'),
                    // 'recipe_detail' => $value['recipe_details'],
                    'recipe_time' => $value['recipe_time'] ? $value['recipe_time'] : null,
                    'popular_item' => ($value['popular_item']) ? $value['popular_item'][0] : 0,
                    // 'availability' => implode(',', $value['availability']),
                    'status' => 1,
                    'content_id' => $ContentID,
                    'language_slug' => $this->uri->segment('4'),
                    'created_by' => $this->session->userdata('UserID'),
                    'check_add_ons' => ($value['check_add_ons']) ? $value['check_add_ons'][0] : 0,
                    'vat' => $restaurant_details->vat,
                    'sd' => $restaurant_details->sd,
                    'menu_group_id' => $menu_group_id,

                );

                if (!empty($value['timings'])) {
                    $timingsArr = $value['timings'];
                    $newTimingArr = array();
                    foreach ($timingsArr as $t_key => $t_value) {
                        if (!isset($t_value['on'])) {
                            $newTimingArr = array();
                            // $newTimingArr[$t_key]['open'] = '';
                            // $newTimingArr[$t_key]['close'] = '';
                            // $newTimingArr[$t_key]['on'] = '0';
                        } else {
                            if (!empty($t_value['open']) && !empty($t_value['close'])) {
                                $newTimingArr[$t_key]['open'] = $t_value['open'];
                                $newTimingArr[$t_key]['close'] = $t_value['close'];
                                $newTimingArr[$t_key]['on'] = '1';
                            } else {
                                $newTimingArr = array();
                                // $newTimingArr[$t_key]['open'] = '';
                                // $newTimingArr[$t_key]['close'] = '';
                                // $newTimingArr[$t_key]['on'] = '0';
                            }
                        }
                    }
                    $add_data['availability'] = !empty($newTimingArr) ? serialize($newTimingArr) : null;
                }


                $img_url = '';

                if (!empty($_FILES['menu-group']['name'][$key]['Image'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '12288'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                        @mkdir('./uploads/menu', 0777, TRUE);
                    }
                    // $Image = array();
                    //$Image = array(
                    $_FILES['group']['name'] = $_FILES['menu-group']['name'][$key]['Image'];
                    $_FILES['group']['type'] = $_FILES['menu-group']['type'][$key]['Image'];
                    $_FILES['group']['tmp_name'] = $_FILES['menu-group']['tmp_name'][$key]['Image'];
                    $_FILES['group']['error'] = $_FILES['menu-group']['error'][$key]['Image'];
                    $_FILES['group']['size'] = $_FILES['menu-group']['size'][$key]['Image'];
                    //);
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('group')) {
                        $img = $this->upload_cloud->data();
                        $add_data['image'] = "menu/" . $img['file_name'];
                        $img_url = $add_data['image'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }

                $menu_id = $this->restaurant_model->addData('restaurant_menu_item', $add_data);
                //check if customized
                if ($value['check_add_ons'][0] == 1) {
                    if ($value['optradio'] == 1) {

                        foreach ($value['variation-group'] as $var_group) {
                            $variation_data = array(
                                'menu_id' => $menu_id,
                                'variation_name'    => $var_group['variation_name'],
                                'variation_price'   => $var_group['variation_price'],
                                'variation_add_on'   => $var_group['variation_add_on'] ? $var_group['variation_add_on'][0] : 0,
                                'status'            => 1,
                            );

                            $variation_id = $this->restaurant_model->addData('variations', $variation_data);
                            $addons = array();
                            if ($var_group['variation_add_on']) {
                                foreach ($var_group['add-on-parent-cat'] as $m => $v) {
                                    if (!empty($v['add_ons_list'])) {
                                        foreach ($v['add_ons_list'] as $keys => $values) {

                                            //foreach ($values as $k => $val) {
                                            if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                $addons[] = array(
                                                    'menu_id' => $menu_id,
                                                    'category_id' => $v['addons_category_id'],
                                                    'add_ons_name' => $values['add_ons_name'],
                                                    'add_ons_price' => $values['add_ons_price'],
                                                    'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                    'has_variation' => 1,
                                                    'variation_id'  => $variation_id,
                                                    'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                );
                                                // echo '<pre>';
                                                // print_r($addons);
                                                // exit();
                                            }
                                        }
                                    }
                                }

                                $this->restaurant_model->inserBatch('add_ons_master', $addons);
                            } else {
                                $addons = array(
                                    'menu_id' => $menu_id,
                                    'has_variation' => 1,
                                    'variation_id'  => $variation_id,
                                );
                                $this->restaurant_model->addData('add_ons_master', $addons);
                            }
                        }
                    } else {
                        $addons = array();

                        foreach ($value['only-add-on-parent-cat'] as $m => $v) {
                            if (!empty($v['only-add-on-add_ons_list'])) {

                                foreach ($v['only-add-on-add_ons_list'] as $keys => $values) {
                                    //foreach ($values as $k => $val) {
                                    if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                        $addons[] = array(
                                            'menu_id' => $menu_id,
                                            'category_id' => $v['addons_category_id'],
                                            'add_ons_name' => $values['add_ons_name'],
                                            'add_ons_price' => $values['add_ons_price'],
                                            'has_variation' => 0,
                                            'variation_id'  => null,
                                            'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                            'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                        );
                                    }
                                }
                            }
                        }

                        $this->restaurant_model->inserBatch('add_ons_master', $addons);
                    }
                }

                $branches = $this->restaurant_model->checkHasBranch($this->input->post('restaurant_id'));


                if ($branches) {
                    foreach ($branches as $br) {
                        $branch_id = $br->entity_id;

                        $add_content = array(
                            'content_type' => 'menu',
                            'created_by' => $this->session->userdata("UserID"),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                        $item_slug = slugify($value['name'], 'restaurant_menu_item', 'item_slug');
                        $add_data = array(
                            'name' => $value['name'],
                            'item_slug' => $item_slug,
                            'restaurant_id' => $branch_id,
                            'category_id' => $this->input->post('category_id'),
                            'price' => ($value['price']) ? $value['price'] : NULL,
                            'menu_detail' => $value['menu_details'],
                            // 'ingredients' => $this->input->post('ingredient'),
                            // 'recipe_detail' => $value['recipe_details'],
                            'recipe_time' => $value['recipe_time'],
                            'popular_item' => ($value['popular_item']) ? $value['popular_item'][0] : 0,
                            // 'availability' => implode(',', $value['availability']),
                            'status' => 1,
                            'content_id' => $ContentID,
                            'language_slug' => $this->uri->segment('4'),
                            'created_by' => $this->session->userdata('UserID'),
                            'check_add_ons' => ($value['check_add_ons']) ? $value['check_add_ons'][0] : 0,
                            'vat' => $restaurant_details->vat,
                            'sd' => $restaurant_details->sd,
                            'menu_group_id' => $menu_group_id,

                        );
                        $add_data['image'] = $img_url;

                        $menu_id = $this->restaurant_model->addData('restaurant_menu_item', $add_data);
                        if ($value['check_add_ons'][0] == 1) {
                            if ($value['optradio'] == 1) {

                                foreach ($value['variation-group'] as $var_group) {
                                    $variation_data = array(
                                        'variation_name'    => $var_group['variation_name'],
                                        'variation_price'   => $var_group['variation_price'],
                                        'menu_id' => $menu_id,
                                        'variation_add_on'   => $var_group['variation_add_on'] ? $var_group['variation_add_on'][0] : 0,
                                        'status'            => 1,
                                    );

                                    $variation_id = $this->restaurant_model->addData('variations', $variation_data);
                                    if ($var_group['variation_add_on']) {
                                        $addons = array();

                                        foreach ($var_group['add-on-parent-cat'] as $m => $v) {
                                            if (!empty($v['add_ons_list'])) {
                                                foreach ($v['add_ons_list'] as $keys => $values) {

                                                    //foreach ($values as $k => $val) {
                                                    if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                        $addons[] = array(
                                                            'menu_id' => $menu_id,
                                                            'category_id' => $v['addons_category_id'],
                                                            'add_ons_name' => $values['add_ons_name'],
                                                            'add_ons_price' => $values['add_ons_price'],
                                                            'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                            'has_variation' => 1,
                                                            'variation_id'  => $variation_id,
                                                            'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                        );
                                                    }
                                                }
                                            }
                                        }

                                        $this->restaurant_model->inserBatch('add_ons_master', $addons);
                                    } else {
                                        $addons = array(
                                            'menu_id' => $menu_id,
                                            'has_variation' => 1,
                                            'variation_id'  => $variation_id,
                                        );
                                        $this->restaurant_model->addData('add_ons_master', $addons);
                                    }
                                }
                            } else {
                                $addons = array();

                                foreach ($value['only-add-on-parent-cat'] as $m => $v) {
                                    if (!empty($v['only-add-on-add_ons_list'])) {

                                        foreach ($v['only-add-on-add_ons_list'] as $keys => $values) {
                                            //foreach ($values as $k => $val) {
                                            if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                $addons[] = array(
                                                    'menu_id' => $menu_id,
                                                    'category_id' => $v['addons_category_id'],
                                                    'add_ons_name' => $values['add_ons_name'],
                                                    'add_ons_price' => $values['add_ons_price'],
                                                    'has_variation' => 0,
                                                    'variation_id'  => null,
                                                    'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                    'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                );
                                            }
                                        }
                                    }
                                }

                                $this->restaurant_model->inserBatch('add_ons_master', $addons);
                            }
                        }
                    }
                }


                if (empty($data['Error'])) {
                    $error = 1;
                }
            }

            if ($error == 1) {
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view_menu');
            }
        }

        $not_a_branch = true;
        // $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $language_slug, $not_a_branch);
        // $data['category'] = $this->restaurant_model->getListData('category', $language_slug);


        $this->load->view(ADMIN_URL . '/restaurant_menu_add_multiple', $data);
    }
    //edit menu
    public function edit_menu()
    {

        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenuedit') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {


            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('category_id', 'Category', 'trim|required');


            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $restaurant_details = $this->restaurant_model->getEditDetail('restaurant', $this->input->post('restaurant_id'));
                $content_id = $this->restaurant_model->getContentId($this->input->post('menu-group[0][entity_id]'), 'restaurant_menu_item');
                $slug = $this->restaurant_model->getItemSlug($this->input->post('menu-group[0][content_id]'));
                if (!empty($slug->item_slug)) {
                    $item_slug = $slug->item_slug;
                } else {
                    $item_slug = slugify($this->input->post('menu-group[0][name]'), 'restaurant_menu_item', 'item_slug', 'content_id', $content_id->content_id);
                }

                $menu_group_id = $this->restaurant_model->getMenuGroup($this->input->post('menu-group[0][entity_id]'));
                $menu_id = $this->input->post('menu-group[0][entity_id]');

                $edit_data = array(
                    'name' => $this->input->post('menu-group[0][name]'),
                    'item_slug' => $item_slug,
                    'restaurant_id' => $this->input->post('restaurant_id'),
                    'category_id' => $this->input->post('category_id'),
                    'price' => ($this->input->post('menu-group[0][price]')) ? $this->input->post('menu-group[0][price]') : NULL,
                    'menu_detail' => $this->input->post('menu-group[0][menu_details]'),
                    'recipe_time' => $this->input->post('menu-group[0][recipe_time]') ? $this->input->post('menu-group[0][recipe_time]') : null,
                    'popular_item' => ($this->input->post('menu-group[0][popular_item]')) ? $this->input->post('menu-group[0][popular_item]')[0] : '0',
                    'updated_by' => $this->session->userdata('UserID'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'is_veg' => $this->input->post('menu-group[0][is_veg]'),
                    'check_add_ons' => ($this->input->post('menu-group[0][check_add_ons]')) ? $this->input->post('menu-group[0][check_add_ons][0]') : 0,
                    'vat' => $this->input->post('menu-group[0][vat]') ? $this->input->post('menu-group[0][vat]') : 0,
                    'sd' => $this->input->post('menu-group[0][sd]') ? $this->input->post('menu-group[0][sd]') : 0,
                    'need_modification' => 0

                );

                if (!empty($this->input->post('menu-group[0][timings]'))) {
                    $timingsArr = $this->input->post('menu-group[0][timings]');
                    $newTimingArr = array();
                    $exist_timing = 0;
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
                                $exist_timing = 1;
                            } else {
                                $newTimingArr[$t_key]['open'] = '';
                                $newTimingArr[$t_key]['close'] = '';
                                $newTimingArr[$t_key]['on'] = '0';
                            }
                        }
                    }
                    if ($exist_timing == 1) {
                        $edit_data['availability'] = serialize($newTimingArr);
                    } else {
                        $edit_data['availability'] = null;
                    }
                }

                $edit_data['image'] = $this->input->post('menu-group[0][uploaded_image]');
                $img_url = $edit_data['image'];
                if (!empty($_FILES['menu-group']['name'][0]['Image'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/menu';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '12288'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/menu')) {
                        @mkdir('./uploads/menu', 0777, TRUE);
                    }

                    $_FILES['menu_img']['name'] = $_FILES['menu-group']['name'][0]['Image'];
                    $_FILES['menu_img']['type'] = $_FILES['menu-group']['type'][0]['Image'];
                    $_FILES['menu_img']['tmp_name'] = $_FILES['menu-group']['tmp_name'][0]['Image'];
                    $_FILES['menu_img']['error'] = $_FILES['menu-group']['error'][0]['Image'];
                    $_FILES['menu_img']['size'] = $_FILES['menu-group']['size'][0]['Image'];
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('menu_img')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "menu/" . $img['file_name'];
                        $img_url = $edit_data['image'];
                        if ($this->input->post('menu-group[0][uploaded_image]')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('menu-group[0][uploaded_image]'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (empty($data['Error'])) {
                    //  echo "<pre>";
                    // print_r($edit_data);
                    // exit();
                    $this->restaurant_model->updateData($edit_data, 'restaurant_menu_item', 'entity_id', $this->input->post('menu-group[0][entity_id]'));
                    $addons = array();
                    $value = $this->input->post('menu-group[0]', TRUE);
                    if ($value['check_add_ons'][0] == 1) {
                        if ($value['optradio'] == 1) {

                            $this->restaurant_model->deleteData('variations', 'menu_id', $menu_id);
                            $addons = array();
                            foreach ($value['variation-group'] as $var_group) {
                                $variation_data = array(
                                    'menu_id' => $menu_id,
                                    'variation_name'    => $var_group['variation_name'],
                                    'variation_price'   => $var_group['variation_price'],
                                    'variation_add_on'   => $var_group['variation_add_on'] ? $var_group['variation_add_on'][0] : 0,
                                    'status'            => 1,
                                );

                                $variation_id = $this->restaurant_model->addData('variations', $variation_data);
                                if ($var_group['variation_add_on']) {

                                    foreach ($var_group['add-on-parent-cat'] as $m => $v) {
                                        if (!empty($v['add_ons_list'])) {
                                            foreach ($v['add_ons_list'] as $keys => $values) {

                                                //foreach ($values as $k => $val) {
                                                if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                    $addons[] = array(
                                                        'menu_id' => $menu_id,
                                                        'category_id' => $v['addons_category_id'],
                                                        'add_ons_name' => $values['add_ons_name'],
                                                        'add_ons_price' => $values['add_ons_price'],
                                                        'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                        'has_variation' => 1,
                                                        'variation_id'  => $variation_id,
                                                        'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                    );
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $addons[] = array(
                                        'menu_id' => $menu_id,
                                        'category_id' => null,
                                        'add_ons_name' => null,
                                        'add_ons_price' => null,
                                        'max_choice'    => null,
                                        'has_variation' => 1,
                                        'variation_id'  => $variation_id,
                                        'is_multiple' => 0

                                    );
                                    // $this->restaurant_model->addData('add_ons_master', $addons);
                                }
                            }

                            $this->restaurant_model->deleteinsertBatch('add_ons_master', $addons, $menu_id);
                        } else {
                            $addons = array();

                            foreach ($value['only-add-on-parent-cat'] as $m => $v) {
                                if (!empty($v['only-add-on-add_ons_list'])) {

                                    foreach ($v['only-add-on-add_ons_list'] as $keys => $values) {
                                        //foreach ($values as $k => $val) {
                                        if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                            $addons[] = array(
                                                'menu_id' => $menu_id,
                                                'category_id' => $v['addons_category_id'],
                                                'add_ons_name' => $values['add_ons_name'],
                                                'add_ons_price' => $values['add_ons_price'],
                                                'has_variation' => 0,
                                                'variation_id'  => null,
                                                'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                            );
                                        }
                                    }
                                }
                            }

                            $this->restaurant_model->deleteinsertBatch('add_ons_master', $addons, $menu_id);
                        }
                    }
                    $branches = $this->restaurant_model->checkHasBranch($this->input->post('restaurant_id'));
                    if ($branches) {

                        $edit_data = array();

                        $edit_data = array(
                            'name' => $value['name'],
                            'category_id' => $this->input->post('category_id'),
                            'price' => ($value['price']) ? $value['price'] : NULL,
                            'menu_detail' => $value['menu_details'],
                            'recipe_time' => $value['recipe_time'],
                            'popular_item' => ($value['popular_item']) ? $value['popular_item'][0] : 0,
                            'availability' => serialize($newTimingArr),
                            'language_slug' => $this->uri->segment('4'),
                            'created_by' => $this->session->userdata('UserID'),
                            'check_add_ons' => ($value['check_add_ons']) ? $value['check_add_ons'][0] : 0,
                            'vat' => $this->input->post('menu-group[0][vat]') ? $this->input->post('menu-group[0][vat]') : 0,
                            'sd' => $this->input->post('menu-group[0][sd]') ? $this->input->post('menu-group[0][sd]') : 0,
                            'need_modification' => 0
                        );
                        $edit_data['image'] = $img_url;

                        $this->restaurant_model->updateData($edit_data, 'restaurant_menu_item', 'menu_group_id', $menu_group_id[0]['menu_group_id']);

                        foreach ($branches as $br) {
                            $branch_id = $br->entity_id;
                            $menu_id = $this->restaurant_model->getBranchMenuID($branch_id, $menu_group_id[0]['menu_group_id']);
                            if (!$menu_id || $menu_id == '') {
                                $item_slug = slugify($this->input->post('menu-group[0][name]'), 'restaurant_menu_item', 'item_slug', 'content_id', $content_id->content_id);
                                $edit_data['restaurant_id'] = $branch_id;
                                $edit_data['status'] = 0;
                                $edit_data['menu_group_id'] = $menu_group_id[0]['menu_group_id'];
                                $edit_data['item_slug'] = $item_slug;
                                $menu_id = $this->restaurant_model->addData('restaurant_menu_item', $edit_data);
                            }
                            $addons = array();
                            $value = $this->input->post('menu-group[0]', TRUE);
                            if ($value['check_add_ons'][0] == 1) {
                                if ($value['optradio'] == 1) {

                                    $this->restaurant_model->deleteData('variations', 'menu_id', $menu_id);
                                    $addons = array();
                                    foreach ($value['variation-group'] as $var_group) {
                                        $variation_data = array(
                                            'menu_id' => $menu_id,
                                            'variation_name'    => $var_group['variation_name'],
                                            'variation_price'   => $var_group['variation_price'],
                                            'variation_add_on'   => $var_group['variation_add_on'] ? $var_group['variation_add_on'][0] : 0,
                                            'status'            => 1,
                                        );


                                        $variation_id = $this->restaurant_model->addData('variations', $variation_data);
                                        if ($var_group['variation_add_on']) {

                                            foreach ($var_group['add-on-parent-cat'] as $m => $v) {
                                                if (!empty($v['add_ons_list'])) {
                                                    foreach ($v['add_ons_list'] as $keys => $values) {

                                                        //foreach ($values as $k => $val) {
                                                        if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                            $addons[] = array(
                                                                'menu_id' => $menu_id,
                                                                'category_id' => $v['addons_category_id'],
                                                                'add_ons_name' => $values['add_ons_name'],
                                                                'add_ons_price' => $values['add_ons_price'],
                                                                'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                                'has_variation' => 1,
                                                                'variation_id'  => $variation_id,
                                                                'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                            );
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $addons[] = array(
                                                'menu_id' => $menu_id,
                                                'category_id' => null,
                                                'add_ons_name' => null,
                                                'add_ons_price' => null,
                                                'max_choice'    => null,
                                                'has_variation' => 1,
                                                'variation_id'  => $variation_id,
                                                'is_multiple' => 0

                                            );
                                            // $this->restaurant_model->addData('add_ons_master', $addons);
                                        }
                                    }

                                    $this->restaurant_model->deleteinsertBatch('add_ons_master', $addons, $menu_id);
                                } else {
                                    $addons = array();

                                    foreach ($value['only-add-on-parent-cat'] as $m => $v) {
                                        if (!empty($v['only-add-on-add_ons_list'])) {

                                            foreach ($v['only-add-on-add_ons_list'] as $keys => $values) {
                                                //foreach ($values as $k => $val) {
                                                if ($values['add_ons_name'] != '' && $values['add_ons_price'] != '') {
                                                    $addons[] = array(
                                                        'menu_id' => $menu_id,
                                                        'category_id' => $v['addons_category_id'],
                                                        'add_ons_name' => $values['add_ons_name'],
                                                        'add_ons_price' => $values['add_ons_price'],
                                                        'has_variation' => 0,
                                                        'variation_id'  => null,
                                                        'max_choice' => ($v['is_multiple'] && $v['max_choice'] != '' ? $v['max_choice'] : null),
                                                        'is_multiple' => ($v['is_multiple']) ? $v['is_multiple'][0] : 0
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    $this->restaurant_model->deleteinsertBatch('add_ons_master', $addons, $menu_id);
                                }
                            }
                        }
                    }
                    $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view_menu');
                }
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $not_a_branch = true;
        $data['isEdit'] = 1;
        // $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $language_slug, $not_a_branch);
        // $data['category'] = $this->restaurant_model->getListData('category', $language_slug);
        $entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_menu_item', $entity_id);

        $res_name = $this->restaurant_model->getMenuResName($data['edit_records']->restaurant_id);
        $cat_name = $this->restaurant_model->getMenuCatName($data['edit_records']->category_id);

        $data['res_name'] = $res_name->name;
        $data['cat_name'] = $cat_name->name;

        if ($data['edit_records']->check_add_ons == 1) {
            $data['has_variation']  = $this->restaurant_model->chechHasVariation($entity_id);
            if ($data['has_variation']) {
                $data['variation_detail'] = $this->restaurant_model->getVariationDetail($entity_id);
            } else {
                $data['add_ons_detail'] = $this->restaurant_model->getAddonsDetail('add_ons_master', $entity_id);
            }
        }
        $data['addons_category'] = $this->restaurant_model->getListData('add_ons_category', $language_slug);
        $this->load->view(ADMIN_URL . '/restaurant_menu_add_multiple', $data);
    }
    // call for ajax data ajaxviewRider
    public function ajaxviewMenu()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(4 => 'menu.price', 6 => 'res.name');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model

        $grid_data = $this->restaurant_model->getMenuGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);

        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        $Languages = $this->common_model->getLanguages();
        $ItemDiscount = $this->common_model->getItemDiscount(array('status' => 1, 'coupon_type' => 'discount_on_items'));
        $couponAmount = $ItemDiscount['couponAmount'];
        $ItemDiscount = (!empty($ItemDiscount['itemDetail'])) ? array_column($ItemDiscount['itemDetail'], 'item_id') : array();
        foreach ($grid_data['data'] as $key => $value) {
            $total = 0;
            if (in_array($value['entity_id'], $ItemDiscount)) {
                if (!empty($couponAmount)) {
                    if ($couponAmount[0]['max_amount'] < $value['price']) {
                        if ($couponAmount[0]['amount_type'] == 'Percentage') {
                            $total = $value['price'] - round(($value['price'] * $couponAmount[0]['amount']) / 100);
                        } else if ($couponAmount[0]['amount_type'] == 'Amount') {
                            $total = $value['price'] - $couponAmount[0]['amount'];
                        }
                    }
                }
            }
            $edit_active_access = ($this->lpermission->method('menu', 'delete')->access() ? '<button onclick="deleteAll(' . $value['content_id'] . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' : '');

            $edit_active_access .= ($this->lpermission->method('menu', 'update')->access() ? '<p onclick="disableAll(' . $value['content_id'] . ',' . $value['status'] . ')"  title="' . $this->lang->line('click_for') . ' ' . ($value['status'] ? $this->lang->line('inactive') : $this->lang->line('active')) . '"><button class="margin-bottom btn-sm btn-' . ($value['status'] ? 'success' : 'danger') . '"><i class="fa fa-' . ($value['status'] ? 'check' : 'times') . '"></i>' . ($value['status'] ? "On" : "Off") . '</button></p>' : '');

            $assigned_branch = $this->restaurant_model->getMenuAssignedBranches($value['entity_id']);

            $assigned_branches_list = '';
            if ($assigned_branch) {
                foreach ($assigned_branch as $branch) {
                    $assigned_branches_list .= $branch['name'] . ', ';
                }
            }

            $hasBranch = $this->restaurant_model->checkHasBranch($value['rid']);
            //$price = ($total && $total > 0)?"<strike>".number_format_unchanged_precision($value['price'])."</strike> ".number_format_unchanged_precision($total):number_format_unchanged_precision($value['price']);
            $currency_symbol = $this->common_model->getCurrencySymbol($value['currency_id']);
            $records["aaData"][] = array(
                $nCount,
                $value['name'],
                ($value['check_add_ons']) ? 'Customized' : ($currency_symbol->currency_symbol . number_format_unchanged_precision($value['price'], $currency_symbol->currency_code)),
                $hasBranch ?
                    ($assigned_branch
                        ? '<div class="col-sm-6">' . substr($assigned_branches_list, 0, -2) . '</div>'
                        : '<div class="col-sm-6"><span>Not assigned to any branch</span></div>') .
                    ($this->lpermission->method('menu', 'update')->access()
                        ? '<div class="col-sm-6"><button style="float:right" class="btn btn-sm btn-primary" onclick="menuBranchEdit(' . ($value['menu_group_id'] ? $value['menu_group_id'] : 0) . ' ,' . $value['entity_id'] . ' ,' . $value['rid'] . ')" title="' . $this->lang->line('click_edit') . '"><i class="fa fa-edit"></i> </button> </div>'
                        : '')

                    : "Not Available",
                $value['rname'],
                $edit_active_access
            );


            $cusLan = array();
            foreach ($Languages as $lang) {
                if (array_key_exists($lang->language_slug, $value['translations'])) {
                    $cusLan[] =
                        ($this->lpermission->method('menu', 'update')->access() ?
                            '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit_menu/' . $lang->language_slug . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($value['translations'][$lang->language_slug]['translation_id'])) . '" title="' . $this->lang->line('click_edit') . '"><i class="fa fa-edit"></i> </a>'
                            : '') .
                        // ($this->lpermission->method('menu', 'update')->access() ?
                        //     '<a style="cursor:pointer;" onclick="disable_record(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . $value['translations'][$lang->language_slug]['status'] . ')"  title="' . $this->lang->line('click_for') . ' ' . ($value['translations'][$lang->language_slug]['status'] ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . '"><i class="fa fa-toggle-' . ($value['translations'][$lang->language_slug]['status'] ? 'on' : 'off') . '"></i> </a>'
                        //     : '') .

                        ($this->lpermission->method('menu', 'delete')->access() ?
                            '<a style="cursor:pointer;" onclick="deleteDetail(' . $value['translations'][$lang->language_slug]['translation_id'] . ',' . $value['content_id'] . ')"  title="' . $this->lang->line('click_delete') . '"><i class="fa fa-times"></i> </a>
                    ( ' . $value['translations'][$lang->language_slug]['name'] . ' )'
                            : '');
                } else {
                    $cusLan[] = $this->lpermission->method('menu', 'create')->access() ? '<a href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/add_menu/' . $lang->language_slug . '/' . $value['content_id'] . '" title="' . $this->lang->line('click_add') . '"><i class="fa fa-plus"></i></a>' : '';
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
    /*
     * Update status for All
     */
    public function ajaxDisableAll()
    {
        $content_id = ($this->input->post('content_id') != '') ? $this->input->post('content_id') : '';
        if ($content_id != '') {
            $this->restaurant_model->UpdatedStatusAll($this->input->post('tblname'), $content_id, $this->input->post('status'));
        }
    }
    //add package
    public function add_package()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantPackageadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('price', 'Price', 'trim|required');
            $this->form_validation->set_rules('detail', 'Detail', 'trim|required');
            $this->form_validation->set_rules('availability[]', 'Availability', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                if (!$this->input->post('content_id')) {
                    //ADD DATA IN CONTENT SECTION
                    $add_content = array(
                        'content_type' => 'package',
                        'created_by' => $this->session->userdata("UserID"),
                        'created_date' => date('Y-m-d H:i:s')
                    );
                    $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                } else {
                    $ContentID = $this->input->post('content_id');
                }
                $add_data = array(
                    'name' => $this->input->post('name'),
                    'restaurant_id' => $this->input->post('restaurant_id'),
                    'price' => ($this->input->post('price')) ? str_replace('.', '', $this->input->post('price')) : NULL,
                    'detail' => $this->input->post('detail'),
                    'availability' => implode(',', $this->input->post("availability")),
                    'content_id' => $ContentID,
                    'language_slug' => $this->uri->segment('4'),
                    'status' => 1,
                    'created_by' => $this->session->userdata('UserID'),
                );
                $this->restaurant_model->addData('restaurant_package', $add_data);
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view_package');
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $language_slug);
        $this->load->view(ADMIN_URL . '/restaurant_package_add', $data);
    }
    //edit package
    public function edit_package()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantPackageEdit') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('price', 'Price', 'trim|required');
            $this->form_validation->set_rules('detail', 'Detail', 'trim|required');
            $this->form_validation->set_rules('availability[]', 'Availability', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $edit_data = array(
                    'name' => $this->input->post('name'),
                    'restaurant_id' => $this->input->post('restaurant_id'),
                    'price' => ($this->input->post('price')) ? str_replace('.', '', $this->input->post('price')) : NULL,
                    'detail' => $this->input->post('detail'),
                    'availability' => implode(',', $this->input->post("availability")),
                    'updated_by' => $this->session->userdata('UserID'),
                    'updated_date' => date('Y-m-d H:i:s'),
                );
                $this->restaurant_model->updateData($edit_data, 'restaurant_package', 'entity_id', $this->input->post('entity_id'));
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view_package');
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $language_slug);
        $entity_id = ($this->uri->segment('5')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(5))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->restaurant_model->getEditDetail('restaurant_package', $entity_id);
        $this->load->view(ADMIN_URL . '/restaurant_package_add', $data);
    }

    public function checkExist()
    {
        $phone_number = ($this->input->post('phone_number') != '') ? $this->input->post('phone_number') : '';
        if ($this->input->post('name')) {
            if ($phone_number != '') {
                $check = $this->restaurant_model->checkExist($phone_number, $this->input->post('entity_id'), $this->input->post('content_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkExist', $this->lang->line('phones_exist'));
                    return false;
                }
            }
        } else {
            if ($phone_number != '') {
                $check = $this->restaurant_model->checkExist($phone_number, $this->input->post('entity_id'), $this->input->post('content_id'));
                echo $check;
            }
        }
    }
    public function checkEmailExist()
    {
        $email = ($this->input->post('email') != '') ? $this->input->post('email') : '';
        if ($this->input->post('name')) {
            if ($email != '') {
                $check = $this->restaurant_model->checkEmailExist($email, $this->input->post('entity_id'), $this->input->post('content_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('email_exist'));
                    return false;
                }
            }
        } else {
            if ($email != '') {
                $check = $this->restaurant_model->checkEmailExist($email, $this->input->post('entity_id'), $this->input->post('content_id'));
                echo $check;
            }
        }
    }

    public function import_menu_status()
    {
        $data['meta_title'] = $this->lang->line('title_admin_restaurantMenu') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/import_menu_status', $data);
    }

    //import menu
    public function import_menu()
    {
        if ($this->input->post('submit_page') == 'Submit') {
            $this->form_validation->set_rules('import_tax', 'Menu File', 'trim|xss_clean');
            if ($this->form_validation->run()) {
                $test = $_FILES['import_tax']['name'];
                $this->load->library('Excel');
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/menu_import';
                $config['allowed_types'] = 'xlsx|xls|csv';
                $config['encrypt_name'] = TRUE;
                if (!@is_dir('uploads/menu_import')) {
                    @mkdir('./uploads/menu_import', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                // If upload failed, display error
                if (!$this->upload_cloud->do_upload('import_tax')) {
                    $this->session->set_flashdata('Import_Error', $this->upload_cloud->display_errors());
                    redirect(ADMIN_URL . '/' . $this->controller_name . '/view_menu');
                } else {
                    $file_data = $this->upload_cloud->data();
                    $file_path =  './uploads/menu_import/' . $file_data['file_name'];
                    // Start excel read
                    if ($file_data['file_ext'] == '.xlsx' || $file_data['file_ext'] == '.xls') {
                        //read file from path
                        $objPHPExcel = PHPExcel_IOFactory::load($file_path);
                        //get only the Cell Collection
                        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                        foreach ($cell_collection as $cell) {
                            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();

                            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                            $data_value = (string)$objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                            //header will/should be in row 1 only. of course this can be modified to suit your need.
                            if ($row == 2) {
                                $header[$row][$column] = $data_value;
                            } else if ($row > 2) {
                                $arr_data[$row][$column] = $data_value;
                            }
                        }
                        $row = 2;
                        $d = 2;
                        $Import = array();
                        $add_data = array();
                        $menu_language_arr = array();
                        $content_id_arr = array();
                        for ($rowcount = 1; $rowcount <= count($arr_data); $rowcount++) {
                            $d++;
                            $mandatoryColumnBlank = 1;
                            $getAddons = array();

                            // check for language
                            if (trim($arr_data[$d]['C']) != '') {
                                $add_data['language_slug'] = trim($arr_data[$d]['C']);
                                $getAddons = $this->restaurant_model->getAddons(trim($arr_data[$d]['C']));
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['C'] . ' is required.';
                            }

                            // check for restaurant
                            if (trim($arr_data[$d]['B']) != '' && trim($arr_data[$d]['C']) != '') {
                                $restaurant = $this->restaurant_model->getRestaurantId(trim($arr_data[$d]['B']), trim($arr_data[$d]['C']));
                                if (!empty($restaurant)) {
                                    $add_data['restaurant_id'] = $restaurant->entity_id;
                                } else {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['B'] . ' details not found';
                                }
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['B'] . ' is required.';
                            }

                            //check for Category
                            if (trim($arr_data[$d]['D']) != '' && trim($arr_data[$d]['C']) != '') {
                                $category = $this->restaurant_model->getCategoryId(trim($arr_data[$d]['D']), trim($arr_data[$d]['C']));
                                if (!empty($category)) {
                                    $add_data['category_id'] = $category->entity_id;
                                } else {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['D'] . ' details not found';
                                }
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['D'] . ' is required.';
                            }

                            // check for name
                            if (trim($arr_data[$d]['E']) != '') {
                                $add_data['name'] = trim($arr_data[$d]['E']);
                                $add_data['item_slug'] = slugify(trim($arr_data[$d]['E']), 'restaurant_menu_item', 'item_slug');
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['E'] . ' is required.';
                            }

                            // check for price
                            if (trim($arr_data[$d]['K']) != 'yes') {
                                if (trim($arr_data[$d]['F']) != '') {
                                    $add_data['price'] = trim($arr_data[$d]['F']);
                                } else {
                                    $mandatoryColumnBlank = 0;
                                    $Import[$rowcount][] = $header[2]['F'] . ' is required.';
                                }
                            }

                            // check for details
                            if (trim($arr_data[$d]['G']) != '') {
                                $add_data['menu_detail'] = trim($arr_data[$d]['G']);
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['G'] . ' is required.';
                            }

                            //check for the image
                            if (!empty($arr_data[$d]['H'])) {
                                $url = trim($arr_data[$d]['H']);
                                $fdata = file_get_contents($url);
                                $random_string = random_string('alnum', 12);
                                $new = 'uploads/menu/' . $random_string . '.png';
                                file_put_contents($new, $fdata);
                                $add_data['image'] = "menu/" . $random_string . '.png';
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['H'] . ' is required.';
                            }

                            //check for popular_item
                            if (trim($arr_data[$d]['I']) != '') {
                                $add_data['popular_item'] = (trim($arr_data[$d]['I']) == "yes") ? 1 : 0;
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['I'] . ' is required.';
                            }

                            //check for Food Type
                            if (trim($arr_data[$d]['J']) != '') {
                                $add_data['is_veg'] = (trim($arr_data[$d]['J']) == "veg") ? 1 : 0;
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['J'] . ' is required.';
                            }
                            $addons = array();
                            //check for check_add_ons
                            $addonsArray = array();
                            if (trim($arr_data[$d]['K']) != '') {
                                $add_data['check_add_ons'] = (trim($arr_data[$d]['K']) == "yes") ? 1 : 0;
                                $addonsArray = array_slice($header[2], 12);
                                $addonsArray = array_filter($addonsArray);
                                $lang_addons = array();
                                if (!empty($addonsArray)) {
                                    foreach ($addonsArray as $arrkey => $arrvalue) {
                                        if (in_array(trim($arrvalue), $getAddons)) {
                                            $lang_addons[$arrkey] = $arrvalue;
                                        }
                                    }
                                    foreach ($lang_addons as $Akey => $Avalue) {
                                        $category_id = $this->restaurant_model->getAddonsId(trim($Avalue), trim($arr_data[$d]['C']));
                                        if (in_array(trim($Avalue), $getAddons)) {
                                            $add_ons = explode(",", trim($arr_data[$d][$Akey]));
                                            if (!empty($add_ons)) {
                                                $addons[] = array(
                                                    'category_id' => $category_id->entity_id,
                                                    'add_ons_name' => trim($add_ons[1]),
                                                    'add_ons_price' => trim($add_ons[2]),
                                                    'is_multiple' => (trim($add_ons[0]) == "yes") ? 1 : 0
                                                );
                                            }
                                        } else {
                                            $mandatoryColumnBlank = 0;
                                            $Import[$rowcount][] = trim($Avalue) . ', such Add ons category does not exists for now.';
                                        }
                                    }
                                }
                            } else {
                                $mandatoryColumnBlank = 0;
                                $Import[$rowcount][] = $header[2]['K'] . ' is required.';
                            }
                            // add data to community_user_detail
                            if ($mandatoryColumnBlank == 1) {

                                // check for content id , if it is to be set same
                                //ADD DATA IN CONTENT SECTION
                                if (trim($arr_data[$d]['A']) != '') {
                                    if (!empty($menu_language_arr)) {
                                        if (in_array($arr_data[$d]['A'], $menu_language_arr)) {
                                            //name exists in the lang name as before so get the content id to add same menu item
                                            $Dkey = '';
                                            foreach ($menu_language_arr as $mkey => $mvalue) {
                                                if ($mvalue == $arr_data[$d]['A']) {
                                                    $Dkey = $mkey;
                                                }
                                            }
                                            if ($Dkey != '') {
                                                $ContentID = $content_id_arr[$Dkey];
                                            } else {
                                                $add_content = array(
                                                    'content_type' => 'menu',
                                                    'created_by' => $this->session->userdata("UserID"),
                                                    'created_date' => date('Y-m-d H:i:s')
                                                );
                                                $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                                                $content_id_arr[$d] = $ContentID;
                                            }
                                        } else {
                                            $add_content = array(
                                                'content_type' => 'menu',
                                                'created_by' => $this->session->userdata("UserID"),
                                                'created_date' => date('Y-m-d H:i:s')
                                            );
                                            $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                                            $content_id_arr[$d] = $ContentID;
                                        }
                                    } else {
                                        $add_content = array(
                                            'content_type' => 'menu',
                                            'created_by' => $this->session->userdata("UserID"),
                                            'created_date' => date('Y-m-d H:i:s')
                                        );
                                        $ContentID = $this->restaurant_model->addData('content_general', $add_content);
                                        $content_id_arr[$d] = $ContentID;
                                    }
                                    $menu_language_arr[$d] = $arr_data[$d]['A'];
                                }

                                $add_data['content_id'] = $ContentID;
                                $add_data['status'] = 1;
                                $add_data['created_by'] =  $this->session->userdata('UserID');
                                $menu_id = $this->restaurant_model->addData('restaurant_menu_item', $add_data);
                                if (!empty($addons)) {
                                    foreach ($addons as $key => $value) {
                                        $addons[$key]['menu_id'] = $menu_id;
                                    }
                                    $this->restaurant_model->inserBatch('add_ons_master', $addons);
                                }
                                $Import[$rowcount][] = "Success";
                            }
                        }
                        $import_data['arr_data'] = $arr_data;
                        $import_data['header'] = $header;
                        $import_data['Import'] = $Import;
                        $import_data['restaurant'] = $this->restaurant_model->getRestaurantName($this->input->post('restaurant_id'));
                        $this->session->set_userdata('import_data', $import_data);
                        redirect(base_url() . ADMIN_URL . '/restaurant/import_menu_status');
                    }
                }
            }
        }
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->restaurant_model->getListData('restaurant', $this->session->userdata('language_slug'));
        $this->load->view(ADMIN_URL . '/restaurant_menu', $data);
    }

    public function getMenuBranch()
    {

        $menu_group_id = $this->input->post('menu_group_id', TRUE);
        $menu_id = $this->input->post('menu_id', TRUE);
        $restaurant_id = $this->input->post('restaurant_id', TRUE);

        $branches = $this->restaurant_model->checkHasBranch($restaurant_id);

        if ($branches) {
            foreach ($branches as $br) {
                $br_menu_details = $this->restaurant_model->getSimpleMenuDetails($menu_group_id, $menu_id, $br->entity_id);
                $data['branches'][] = array(
                    'branch_id' => $br->entity_id,
                    'branch_name'   => $br->name,
                    'br_menu_id'    => $br_menu_details[0]['entity_id'],
                    'status'        => ($br_menu_details ? $br_menu_details[0]['menu_status'] : 0)
                );
            }
        }

        $data['restaurant_id'] = $restaurant_id;
        $data['menu_id'] = $menu_id;
        $data['menu_group_id'] = $menu_group_id;

        // echo '<pre>';
        // print_r($data);
        // exit();

        $this->load->view(ADMIN_URL . '/menuBranchesView', $data);

        // return $page;
    }

    public function updateBranchMenu()
    {
        $menu_id = $this->input->post('menu_id', TRUE);
        $restaurant_id = $this->input->post('restaurant_id', TRUE);
        $branch_ids = $this->input->post('branch_id', TRUE);
        $menu_group_id = $this->input->post('menu_group_id', TRUE);


        $branches = $this->restaurant_model->checkHasBranch($restaurant_id);

        foreach ($branches as $br) {
            if (in_array($br->entity_id, $branch_ids)) {
                $data = array(
                    'status' => 1
                );
            } else {
                $data = array(
                    'status' => 0
                );
            }
            $this->db->where('restaurant_id', $br->entity_id);
            $this->db->where('menu_group_id', $menu_group_id);

            $this->db->update('restaurant_menu_item', $data);
        }
    }

    public function getRestaurantNumber()
    {
        $restaurant_id = $this->input->post('restaurant_id', TRUE);

        $fetch = $this->restaurant_model->getRestaurantImages($restaurant_id);

        $data = array(
            'phone' => $fetch->phone_number,
            'email' => $fetch->email,
            'image' => image_url . $fetch->image,
            'cover_image' => image_url . $fetch->cover_image,
        );

        echo json_encode($data);
    }

    public function getAddonCatAddons()
    {
        $addon_cat_id = $this->input->post('addon_cat_id', TRUE);

        $cat_id_all_addons = $this->restaurant_model->getAddonCatAllAddons($addon_cat_id);

        $data['cat_id_all_addons'] = $cat_id_all_addons;

        // echo '<pre>';
        // print_r($data);
        // exit();

        return $this->load->view(ADMIN_URL . '/addonCatAddons', $data);
    }

    public function getAddonMultiple()
    {
        $addon_cat_id = $this->input->post('addon_cat_id', TRUE);

        $cat_id_all_addons = $this->restaurant_model->getAddonCatAllAddons($addon_cat_id);

        $data['is_multiple'] = $cat_id_all_addons['is_multiple'];
        $data['max_choice'] = $cat_id_all_addons['is_multiple'] == 1 ? $cat_id_all_addons['max_choice'] : null;

        echo json_encode($data);
    }

    public function getRestaurants()
    {
        $res_info   = $this->input->post('term', TRUE);
        $res_data   = $this->restaurant_model->getRestaurantData($res_info);

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
    public function get_restaurant_details()
    {
        $restaurant_id = $this->input->post('res_id');
        $this->db->select("restaurant.vat,restaurant.sd");
        $this->db->where('restaurant.entity_id', $restaurant_id);
        $result =  $this->db->get('restaurant')->result_array();
        echo json_encode($result);
    }

    public function getCategories()
    {
        $cat_info   = $this->input->post('term', TRUE);
        $cat_data   = $this->restaurant_model->getCategoriesData($cat_info);

        if (!empty($cat_data)) {
            foreach ($cat_data as $cat) {
                $cat_json[] = array(
                    'label' => $cat['name'],
                    'id'    => $cat['entity_id']
                );
            }
        } else {
            $cat_json[] = 'No categories found';
        }
        echo json_encode($cat_json);
    }
}
