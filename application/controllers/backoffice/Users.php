<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Users extends CI_Controller
{
    public $full_module = 'User Management System';
    public $module_name = 'User';
    public $controller_name = 'users';
    public $prefix = '_us';
    public $ad_prefix = '_ad';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/users_model');
    }
    // view users
    public function view()
    {
        $data['meta_title'] = $this->lang->line('title_admin_users') . ' | ' . $this->lang->line('site_title');
        $data['selected'] = '';
        $this->load->view(ADMIN_URL . '/users', $data);
    }
    // view users
    public function driver()
    {
        $data['meta_title'] = $this->lang->line('title_admin_users') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/driver', $data);
    }
    // add users
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_usersadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            if ($this->input->post('user_type') != 'Driver') {
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
                $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
            } else {
                $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
                $this->form_validation->set_rules('mobile_number', 'Mobile Number', 'trim|required');
                $this->form_validation->set_rules('present_address', 'Present Address', 'trim|required');
                $this->form_validation->set_rules('permanent_address', 'Permanent Address', 'trim|required');
                $this->form_validation->set_rules('v_type', 'Vehicle Type', 'trim|required');
                $this->form_validation->set_rules('city_id', 'City Name', 'trim|required');
                $this->form_validation->set_rules('zone_id', 'Zone Name', 'trim|required');
                if (empty($_FILES['nid']['name'])) {
                    $this->form_validation->set_rules('nid', 'NID Front Image', 'required');
                }
                if (empty($_FILES['nid_back']['name'])) {
                    $this->form_validation->set_rules('nid_back', 'NID Back Image', 'required');
                }

                if (empty($_FILES['gnid_back']['name'])) {
                    $this->form_validation->set_rules('gnid_back', 'Guardian NID Back Image', 'required');
                }
                if (empty($_FILES['gnid']['name'])) {
                    $this->form_validation->set_rules('gnid', 'Guardian NID Front Image', 'required');
                }
            }
            // if (empty($_FILES['gnid']['name'])) {
            //     $this->form_validation->set_rules('gnid', 'Guardian NID Front Image', 'required');
            // }
            $this->form_validation->set_rules('user_type', 'User Type', 'trim|required');
            $this->form_validation->set_rules('mobile_number', 'Phone Number', 'trim|required|numeric|is_unique[users.mobile_number]');
            //$this->form_validation->set_rules('phone_number','Phone Number', 'trim|required|numeric|is_unique[users.phone_number]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $add_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'email' => strtolower($this->input->post('email')),
                    'mobile_number' => $this->input->post('mobile_number'),
                    'user_type' => $this->input->post('user_type'),
                    'city_id' => $this->input->post('city_id'),
                    'zone_id' => $this->input->post('zone_id'),
                    'status' => 1,
                    'active' => 1,
                    'password' => md5(SALT . $this->input->post('password')),
                    'created_by' => $this->session->userdata("UserID")
                );
                if (!empty($_FILES['Image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/users';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/users')) {
                        @mkdir('./uploads/users', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('Image')) {
                        $img = $this->upload_cloud->data();
                        $add_data['image'] = "users/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                //Nid
                if (!empty($_FILES['nid']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/nid';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/nid')) {
                        @mkdir('./uploads/nid', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('nid')) {
                        $img = $this->upload_cloud->data();
                        $add_data['nid'] = "nid/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                //Nid BAck
                if (!empty($_FILES['nid_back']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/nid_back';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/nid_back')) {
                        @mkdir('./uploads/nid_back', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('nid_back')) {
                        $img = $this->upload_cloud->data();
                        $add_data['nid_back'] = "nid_back/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }



                $rider_id = $this->users_model->addData('users', $add_data);
                $rider_info = array(
                    'rider_id' => $rider_id,
                    'present_address' => $this->input->post('present_address'),
                    'permanent_address' => $this->input->post('permanent_address'),
                    'v_type' => $this->input->post('v_type'),
                    'bkash_no' => $this->input->post('bkash_no'),
                    'nagad_no' => $this->input->post('nagad_no'),
                    // 'created_by' => $this->session->userdata("UserID")
                );
                if (!empty($_FILES['gnid']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/gnid';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/gnid')) {
                        @mkdir('./uploads/gnid', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('gnid')) {
                        $img = $this->upload_cloud->data();
                        $rider_info['gnid_front'] = "gnid/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (!empty($_FILES['gnid_back']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/gnid_back';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/gnid_back')) {
                        @mkdir('./uploads/gnid_back', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('gnid_back')) {
                        $img = $this->upload_cloud->data();
                        $rider_info['gnid_back'] = "gnid_back/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (!empty($_FILES['ebill']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/ebill';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/ebill')) {
                        @mkdir('./uploads/ebill', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('ebill')) {
                        $img = $this->upload_cloud->data();
                        $rider_info['electricity_bill'] = "ebill/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if (!empty($_FILES['nameplate']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/nameplate';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/nameplate')) {
                        @mkdir('./uploads/nameplate', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('nameplate')) {
                        $img = $this->upload_cloud->data();
                        $rider_info['nameplate'] = "nameplate/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }

                if ($this->input->post('user_type') == 'Driver')
                    $this->users_model->addrider('rider_information', $rider_info);

                if ($this->input->post('email')) {
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                    $this->db->select('subject,message');
                    $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'user-added', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
                    $arrayData = array('FirstName' => $this->input->post('first_name'), 'LoginLink' => base_url() . ADMIN_URL, 'Email' => $this->input->post('email'), 'Password' => $this->input->post('password'));
                    $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                    if (!empty($EmailBody)) {
                        $this->load->library('email');
                        $config['charset'] = 'iso-8859-1';
                        $config['wordwrap'] = TRUE;
                        $config['mailtype'] = 'html';
                        $this->email->initialize($config);
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                        $this->email->to(trim($this->input->post('email')));
                        $this->email->subject($Emaildata->subject);
                        $this->email->message($EmailBody);
                        $this->email->send();
                    }
                }
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                if ($this->input->post('user_type') == 'Driver') {
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/driver');
                } else {
                    redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
                }
            }
        }
        $vehicle_data = $this->users_model->getvehicle();
        $city_data = $this->users_model->getcity();
        $zone_data = $this->users_model->getzone();
        $data['vehicle_data'] = $vehicle_data;
        $data['city_data'] = $city_data;
        //  $data['zone_data'] = $zone_data;
        //  echo "<pre>";
        //         print_r($data);
        //         exit();
        $this->load->view(ADMIN_URL . '/users_add', $data);
    }
    // edit users
    public function edit()
    {
        $data['meta_title'] = $this->lang->line('title_admin_usersedit') . ' | ' . $this->lang->line('site_title');
        // check if form is submitted
        if ($this->input->post('submit_page') == "Submit") {
            if ($this->input->post('user_type') != 'Driver') {
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
                $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|callback_checkEmailExist');
            } else {
                $this->form_validation->set_rules('first_name', 'Name', 'trim|required');
            }
            $this->form_validation->set_rules('user_type', 'User Type', 'trim|required');
            $this->form_validation->set_rules('mobile_number', 'Phone Number', 'trim|numeric|callback_checkExist');
            //$this->form_validation->set_rules('phone_number','Phone Number', 'trim|numeric|callback_checkExistPhone');
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', 'Password', 'trim|required');
                $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[password]');
            }
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $data_edited = $this->users_model->getEditDetail('users', $this->input->post('entity_id'));

                $edit_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'email' => strtolower($this->input->post('email')),
                    'mobile_number' => $this->input->post('mobile_number'),
                    'user_type' => $this->input->post('user_type'),
                    'status' => 1,
                    'updated_by' => $this->session->userdata("UserID"),
                    'city_id' => $this->input->post('city_id'),
                    'zone_id' => $this->input->post('zone_id'),
                    'updated_date' => date('Y-m-d h:i:s')
                );
                if (!empty($_FILES['Image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/users';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/users')) {
                        @mkdir('./uploads/users', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('Image')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "users/" . $img['file_name'];
                        if ($this->input->post('uploaded_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                //Nid
                if (!empty($_FILES['nid']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/nid';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/nid')) {
                        @mkdir('./uploads/nid', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('nid')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['nid'] = "nid/" . $img['file_name'];
                        if ($this->input->post('uploaded_nid_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_nid_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                //Nid BAck
                if (!empty($_FILES['nid_back']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/nid_back';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/nid_back')) {
                        @mkdir('./uploads/nid_back', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('nid_back')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['nid_back'] = "nid_back/" . $img['file_name'];
                        if ($this->input->post('uploaded_nid_back_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_nid_back_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                if ($this->input->post('password')) {
                    $edit_data['password'] = md5(SALT . $this->input->post('password'));
                }
                $this->users_model->updateData($edit_data, 'users', 'entity_id', $this->input->post('entity_id'));

                if ($this->input->post('email') != $data_edited->email) {
                    $this->db->select('OptionValue');
                    $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

                    $this->db->select('OptionValue');
                    $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                    $this->db->select('subject,message');
                    $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'email-update-alert', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
                    $arrayData = array('FirstName' => $this->input->post('first_name'), 'Email' => $this->input->post('email'), 'Sender_Email' => $data_edited->email);
                    $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                    if (!empty($EmailBody)) {
                        $this->load->library('email');
                        $config['charset'] = 'iso-8859-1';
                        $config['wordwrap'] = TRUE;
                        $config['mailtype'] = 'html';
                        $this->email->initialize($config);
                        $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                        $this->email->to(trim($this->input->post('email')));
                        $this->email->subject($Emaildata->subject);
                        $this->email->message($EmailBody);
                        $this->email->send();
                    }
                }
            }

            //Edit Rider Information
            $rider_info = array(
                'rider_id' => $this->input->post('entity_id'),
                'present_address' => $this->input->post('present_address'),
                'permanent_address' => $this->input->post('permanent_address'),
                'v_type' => $this->input->post('v_type'),
                'bkash_no' => $this->input->post('bkash_no'),
                'nagad_no' => $this->input->post('nagad_no'),
            );
            //Guardian Nid Front
            if (!empty($_FILES['gnid']['name'])) {
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/gnid';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '5120'; //in KB
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/gnid')) {
                    @mkdir('./uploads/gnid', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('gnid')) {
                    $img = $this->upload_cloud->data();
                    $rider_info['gnid_front'] = "gnid/" . $img['file_name'];
                    if ($this->input->post('uploaded_gnid_image')) {
                        @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_gnid_image'));
                    }
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }
            //Guardian Nid Back Image
            if (!empty($_FILES['gnid_back']['name'])) {
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/gnid_back';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '5120'; //in KB
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/gnid_back')) {
                    @mkdir('./uploads/gnid_back', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('gnid_back')) {
                    $img = $this->upload_cloud->data();
                    $rider_info['gnid_back'] = "gnid_back/" . $img['file_name'];
                    if ($this->input->post('uploaded_gnid_back_image')) {
                        @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_gnid_back_image'));
                    }
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }
            //Electricity Bill
            if (!empty($_FILES['ebill']['name'])) {
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/ebill';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '5120'; //in KB
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/ebill')) {
                    @mkdir('./uploads/ebill', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('ebill')) {
                    $img = $this->upload_cloud->data();
                    $rider_info['electricity_bill'] = "ebill/" . $img['file_name'];
                    if ($this->input->post('uploaded_ebill_image')) {
                        @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_ebill_image'));
                    }
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }
            //Nameplate
            if (!empty($_FILES['nameplate']['name'])) {
                $this->load->library('upload_cloud');
                $config['upload_path'] = './uploads/nameplate';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size'] = '5120'; //in KB
                $config['encrypt_name'] = TRUE;
                // create directory if not exists
                if (!@is_dir('uploads/nameplate')) {
                    @mkdir('./uploads/nameplate', 0777, TRUE);
                }
                $this->upload_cloud->initialize($config);
                if ($this->upload_cloud->do_upload('nameplate')) {
                    $img = $this->upload_cloud->data();
                    $rider_info['nameplate'] = "nameplate/" . $img['file_name'];
                    if ($this->input->post('uploaded_nameplate_image')) {
                        @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_nameplate_image'));
                    }
                } else {
                    $data['Error'] = $this->upload_cloud->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                }
            }

            $data['records'] = $this->users_model->getEditDetail('users', $this->input->post('entity_id'));
            if ($this->input->post('user_type') == 'Driver') {
                if (!$this->users_model->updateRider($rider_info, 'rider_information', 'id', $data['records']->id)) {
                    $this->users_model->addrider('rider_information', $rider_info);
                }
            }

            $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
            if ($this->input->post('user_type') == 'Driver') {
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/driver');
            } else {
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
            }
        }
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->users_model->getEditDetail('users', $entity_id);

        $vehicle_data = $this->users_model->getvehicle();
        $city_data = $this->users_model->getcity();
        $zone_data = $this->users_model->getzone();
        $data['vehicle_data'] = $vehicle_data;
        $data['city_data'] = $city_data;
        $data['zone_data'] = $zone_data;
        //  echo "<pre>";
        //     print_r($data);
        //     exit();
        $this->load->view(ADMIN_URL . '/users_add', $data);
    }

    //get zone for secific city
    public function getzone()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id) {
            $result =  $this->users_model->get_zone_by_city($entity_id);
            // echo "<pre>";
            // print_r($result);
            // exit();
            $html = '<option value="">' . $this->lang->line('select') . '</option>';
            foreach ($result as $key => $value) {
                $html .= '<option value="' . $value->entity_id . '">' . $value->area_name . '</option>';
            }
        }
        echo $html;
    }
    // call for ajax data
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'first_name', 2 => 'status', 3 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_type = '');
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;

        foreach ($grid_data['data'] as $key => $val) {

            if ($this->lpermission->method('users', 'update')->access()) {
                $edit_access = '<a class="btn btn-sm danger-btn margin-bottom" title="' . $this->lang->line('edit') . '" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit') . '</a> <button onclick="disableDetail(' . $val->entity_id . ',' . $val->status . ')"  title="' . $this->lang->line('click_for') . ' ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->status ? 'times' : 'check') . '"></i> ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . '</button>';
            } else {
                $edit_access = '';
            }
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                $val->mobile_number,
                $val->user_type,
                ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
                $edit_access
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change user status
    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $emailData = $this->users_model->getEditDetail('users', $entity_id);
            $this->users_model->UpdatedStatus($entity_id, $this->input->post('status'));
            if ($emailData->email != '') {
                if ($this->input->post('status') == 0) {
                    $status = 'activated';
                } else {
                    $status = 'deactivated';
                }
                $this->db->select('OptionValue');
                $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();

                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                $this->db->select('subject,message');
                $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'change-status-alert', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
                $arrayData = array('FirstName' => $emailData->first_name, 'Status' => $status);
                $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                if (!empty($EmailBody)) {
                    $this->load->library('email');
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                    $this->email->to(trim($emailData->email));
                    $this->email->subject($Emaildata->subject);
                    $this->email->message($EmailBody);
                    $this->email->send();
                }
            }
        }
    }
    // method for deleting a user
    public function ajaxDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->users_model->deleteUser($this->input->post('table'), $entity_id);
    }
    // add address
    public function add_address()
    {
        $data['meta_title'] = $this->lang->line('title_admin_userAddressAdd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('user_entity_id', 'User', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required|numeric');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $add_data = array(
                    'user_entity_id' =>  $this->input->post('user_entity_id'),
                    'address' => $this->input->post('address'),
                    'landmark' => $this->input->post('landmark'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                    'zipcode' => $this->input->post('zipcode'),
                    'country' => $this->input->post('country'),
                    'city' => $this->input->post('city'),
                    'state' => $this->input->post('state'),
                    'saved_status' => ($this->input->post('saved_status')) ? $this->input->post('saved_status') : ''
                );
                $this->users_model->addData('user_address', $add_data);
                $this->session->set_flashdata('add_page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view/user_address');
            }
        }
        $data['selected'] = 'user_address';
        $data['user_data'] = $this->users_model->getUsers();
        $this->load->view(ADMIN_URL . '/users_address_add', $data);
    }
    // edit address
    public function edit_address()
    {
        $data['meta_title'] = $this->lang->line('title_admin_userAddressEdit') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('user_entity_id', 'User', 'trim|required');
            $this->form_validation->set_rules('address', 'Address', 'trim|required');
            $this->form_validation->set_rules('landmark', 'Landmark', 'trim|required');
            $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
            $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');
            $this->form_validation->set_rules('zipcode', 'Zipcode', 'trim|required|numeric');
            $this->form_validation->set_rules('country', 'Country', 'trim|required');
            $this->form_validation->set_rules('state', 'State', 'trim|required');
            $this->form_validation->set_rules('city', 'City', 'trim|required');
            //check form validation using codeigniter
            if ($this->form_validation->run()) {
                $edit_data = array(
                    'user_entity_id' =>  $this->input->post('user_entity_id'),
                    'address' => $this->input->post('address'),
                    'landmark' => $this->input->post('landmark'),
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                    'zipcode' => $this->input->post('zipcode'),
                    'country' => $this->input->post('country'),
                    'city' => $this->input->post('city'),
                    'state' => $this->input->post('state'),
                    'saved_status' => ($this->input->post('saved_status')) ? $this->input->post('saved_status') : ''
                );
                $this->users_model->updateData($edit_data, 'user_address', 'entity_id', $this->input->post('entity_id'));
                $this->session->set_flashdata('add_page_MSG', $this->lang->line('success_update'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view/user_address');
            }
        }
        $data['user_data'] = $this->users_model->getUsers();
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['edit_records'] = $this->users_model->getEditDetail('user_address', $entity_id);
        $this->load->view(ADMIN_URL . '/users_address_add', $data);
    }
    // call for ajax data
    public function ajaxViewAddress()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'first_name', 2 => 'address', 3 => 'status');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getAddressGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;

        foreach ($grid_data['data'] as $key => $val) {
            $edit_access = $this->lpermission->method('users', 'update')->access() ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit_address/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit') : '';
            $delete_access = $this->lpermission->method('users', 'delete')->access() ? '</a> <button onclick="deleteAddress(' . $val->entity_id . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->first_name . ' ' . $val->last_name,
                $val->address,
                $edit_access . $delete_access,
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function checkExist()
    {
        $mobile_number = ($this->input->post('mobile_number') != '') ? $this->input->post('mobile_number') : '';
        if ($this->input->post('first_name')) {
            if ($mobile_number != '') {
                $check = $this->users_model->checkExist($mobile_number, $this->input->post('entity_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkExist', $this->lang->line('phone_exist'));
                    return false;
                }
            }
        } else {
            if ($mobile_number != '') {
                $check = $this->users_model->checkExist($mobile_number, $this->input->post('entity_id'));
                echo $check;
            }
        }
    }

    public function checkExistPhone()
    {
        $phone_number = ($this->input->post('phone_number') != '') ? $this->input->post('phone_number') : '';
        if ($this->input->post('first_name')) {
            if ($phone_number != '') {
                $check = $this->users_model->checkExistPhone($phone_number, $this->input->post('entity_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkExistPhone', $this->lang->line('phone_exist'));
                    return false;
                }
            }
        } else {
            if ($phone_number != '') {
                $check = $this->users_model->checkExistPhone($phone_number, $this->input->post('entity_id'));
                echo $check;
            }
        }
    }

    public function checkEmailExist()
    {
        $email = ($this->input->post('email') != '') ? $this->input->post('email') : '';
        if ($this->input->post('first_name')) {
            if ($email != '') {
                $check = $this->users_model->checkEmailExist($email, $this->input->post('entity_id'));
                if ($check > 0) {
                    $this->form_validation->set_message('checkEmailExist', $this->lang->line('alredy_exist'));
                    return false;
                }
            }
        } else {
            if ($email != '') {
                $check = $this->users_model->checkEmailExist($email, $this->input->post('entity_id'));
                echo $check;
            }
        }
    }
    //driver view
    public function ajaxdriverview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'first_name', 2 => 'restaurant.name', 3 => 'phone_number', 3 => 'status', 4 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_type = 'Driver');
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $edit = $this->lpermission->method('riders', 'update')->access() ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '/driver" title=' . $this->lang->line('edit') . '><i class="fa fa-edit"></i> ' . $this->lang->line('edit') . '</a><button onclick="disableDetail(' . $val->entity_id . ',' . $val->status . ')"  title="' . $this->lang->line('click_for') . ' ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->status ? 'times' : 'check') . '"></i> ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . '</button> ' : '';
            $commission = $this->lpermission->method('riders', 'read')->access() ? (($val->user_type == 'Driver') ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/commission/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-money"></i> ' . $this->lang->line('commission') . '</a>' : '') : '';
            $review = $this->lpermission->method('riders', 'read')->access() ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/review/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-star"></i> ' . $this->lang->line('review') . '</a>' : '';
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                //$val->name,
                $val->mobile_number,
                ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
                $edit . ''
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function commission()
    {
        $data['meta_title'] = $this->lang->line('title_admin_commission') . ' | ' . $this->lang->line('site_title');
        $data['entity_id'] = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : '';
        $this->load->view(ADMIN_URL . '/commission', $data);
    }
    //ajax view
    public function ajaxcommission()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(1 => 'first_name', 2 => 'last_name', 3 => 'date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getCommissionDetail($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_id);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $restaurant = unserialize($val->restaurant_detail);
            $disableCheckbox = ($val->commission_status == 'Paid') ? 'disabled' : '';
            $records["aaData"][] = array(
                '<input type="checkbox" ' . $disableCheckbox . ' name="ids[]" value="' . $val->driver_map_id . '">',
                $val->first_name . ' ' . $val->last_name,
                ($restaurant) ? $restaurant->name : '',
                $val->commission,
                ($val->date) ? date('m-d-Y', strtotime($val->date)) : '',
                ($val->commission_status) ? $val->commission_status : ''
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    //commission view
    public function review()
    {
        $data['meta_title'] = $this->lang->line('title_admin_review') . ' | ' . $this->lang->line('site_title');
        $data['entity_id'] = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : '';
        $this->load->view(ADMIN_URL . '/driver_review', $data);
    }
    //ajax view
    public function ajaxDriverReview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
        $user_id = $this->uri->segment(4);
        $sortfields = array(1 => 'first_name', 2 => 'review', 3 => 'rating', 4 => 'review.created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getDriverReviewDetail($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_id);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $restaurant = unserialize($val->restaurant_detail);
            $records["aaData"][] = array(
                $nCount,
                $val->first_name . ' ' . $val->last_name,
                $val->review,
                $val->rating,
                ($val->created_date) ? date('m-d-Y', strtotime($val->created_date)) : '',
                '-'
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    /*
    * Multiple commission pay
    */
    public function commission_pay()
    {
        $commisionIDs = @explode(",", $this->input->post('arrayData'));
        if (!empty($commisionIDs)) {
            $count = $this->users_model->payCommision($commisionIDs);
        }
    }
    public function active_driver()
    {
        // $this->load->view(ADMIN_URL . '/active_driver', $data);
    }


    public function getActiveDriver()
    {
        $this->load->model('Users_model');
        $postData = $this->input->post();
        $data = $this->report_model->getActiveDriverList();
        //echo "<pre>";print_r($data);exit();
        echo json_encode($data);
    }

    public function dtAllCsv()
    {
        $_POST['user_type'] = $_GET['user_type'];
        $_POST['page_title'] = $_GET['page_title'];
        $_POST['phone'] = $_GET['phone'];
        $_POST['Status'] = $_GET['Status'];
        $_POST['restaurant_name'] = $_GET['restaurant_name'];

        $displayLength = '';
        $displayStart = '';
        $sEcho = ($this->input->get('sEcho')) ? intval($this->input->get('sEcho')) : '';
        $sortCol = ($this->input->get('iSortCol_0')) ? intval($this->input->get('iSortCol_0')) : '';
        $sortOrder = ($this->input->get('sSortDir_0')) ? $this->input->get('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'first_name', 2 => 'status', 3 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_type = '');
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;

        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                $val->mobile_number,
            );
            $nCount++;
        }
        $file_name = 'user_list_' . date("YmdHis") . '.csv';
        // header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Type: application/csv;");

        $file = fopen('php://output', 'w');

        $header = array("SL", "Name", "Mobile Number");
        fputcsv($file, $header);

        foreach ($records["aaData"] as $key => $value) {
            fputcsv($file, $value);
        }

        fclose($file);
        exit;
    }

    public function dtAllDriverCsv()
    {

        $_POST['user_type'] = $_GET['user_type'];
        $_POST['page_title'] = $_GET['page_title'];
        $_POST['phone'] = $_GET['phone'];
        $_POST['Status'] = $_GET['Status'];
        $_POST['restaurant_name'] = $_GET['restaurant_name'];

        $displayLength =  '';
        $displayStart = '';
        $sortCol = ($this->input->get('iSortCol_0')) ? intval($this->input->get('iSortCol_0')) : '';
        $sortOrder = ($this->input->get('sSortDir_0')) ? $this->input->get('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'first_name', 2 => 'restaurant.name', 3 => 'phone_number', 3 => 'status', 4 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->users_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $user_type = 'Driver');
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->first_name,
                //$val->name,
                $val->mobile_number,
            );
            $nCount++;
        }

        $file_name = 'rider_list_' . date("YmdHis") . '.csv';
        // header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Type: application/csv;");

        $file = fopen('php://output', 'w');

        $header = array("SL", "Name", "Mobile Number");
        fputcsv($file, $header);

        foreach ($records["aaData"] as $key => $value) {
            fputcsv($file, $value);
        }

        fclose($file);
        exit;
    }
}
