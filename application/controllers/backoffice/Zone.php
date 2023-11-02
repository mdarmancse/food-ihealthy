<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Zone extends CI_Controller
{
    public $controller_name = 'zone';
    public $prefix = 'zone';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/zone_model');
        $this->load->model(ADMIN_URL . '/restaurant_model');
        $this->load->model(ADMIN_URL . '/sub_dashboard_model');
        $this->load->model(ADMIN_URL . '/users_model');
    }
    //view data
    public function view()
    {
        $data['meta_title'] = ' Zone Area | ' . $this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/zone', $data);
    }


    //view data
    public function vehicle_view()
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $data['meta_title'] = ' Vehicle | ' . $this->lang->line('site_title');
        $data['vehicle'] = $this->Zone_model->all_vehicle();
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/vehicle', $data);
    }
    //view city data
    public function city_view()
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $data['meta_title'] = ' City | ' . $this->lang->line('site_title');
        $data['city'] = $this->Zone_model->all_city();
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/city', $data);
    }
    //edit data
    public function city_editdata($id)
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $data['city_update'] = $this->Zone_model->city_editdata($id);
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/city_add', $data);
    }
    //city status change
    public function change_status($id, $status)
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $result = $this->Zone_model->change_status($id, $status);
        if ($result) {
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/city_view');
        }
    }
    //delete City
    public function delete_city($id)
    {
        $this->load->model("Zone_model");
        $status = $this->Zone_model->delete_city($id);
        if ($status) {
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/city_view');
        }
    }
    //view data
    public function vehicle_editdata($id)
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $data['vehicle_update'] = $this->Zone_model->vehicle_editdata($id);
        $data['Languages'] = $this->common_model->getLanguages();
        $this->load->view(ADMIN_URL . '/add_vehicle', $data);
    }


    public function vehicle_status($id, $status)
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $result = $this->Zone_model->change_vehicle_status($id, $status);
        if ($result) {
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/vehicle_view');
        }
    }


    //delete Vehicle
    public function delete_vehicle($id)
    {
        $this->load->model("Zone_model");
        $status = $this->Zone_model->delete_vehicle($id);
        if ($status) {
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/vehicle_view');
        }
    }
    //view data
    public function update_city()
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $id = $this->input->post('entity_id', TRUE);
        $data = array(
            'id' => $id,
            'name'   => $this->input->post('name', TRUE),
        );

        $status = $this->Zone_model->update_city($data, $id);
        if ($status) {
            $this->session->set_flashdata('page_MSG', 'Successfully Updated');
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/city_view');
        } else {
            $this->session->set_flashdata('page_MSG', 'Update Error');
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/city_view');
        }
    }
    //view data
    public function update_vehicle()
    {
        $CI = &get_instance();
        $CI->load->model('Zone_model');
        $id = $this->input->post('entity_id', TRUE);
        $data = array(
            'entity_id' => $id,
            'name'   => $this->input->post('name', TRUE),
            'price'  => $this->input->post('price', TRUE)
        );

        $status = $this->Zone_model->update_vehicle($data, $id);
        if ($status) {
            $this->session->set_flashdata('page_MSG', 'Successfully Updated');
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/vehicle_view');
        } else {
            $this->session->set_flashdata('page_MSG', 'Update Error');
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/vehicle_view');
        }
    }


    //add data
    public function add()
    {
        $data['meta_title'] = ' Zone | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('area_name', 'Area Name', 'trim|required');
            $this->form_validation->set_rules('lat_long', 'Latitude/Longitude', 'trim|required');
            $this->form_validation->set_rules('restaurant_id[]', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('price_charge', 'Price Charge', 'trim|required');
            $this->form_validation->set_rules('price_charge_2', 'Price Charge_2', 'trim|required');
            $this->form_validation->set_rules('price_charge_3', 'Price Charge_3', 'trim|required');
            if ($this->form_validation->run()) {
                $add_data = array(
                    'area_name' => $this->input->post('area_name'),
                    'lat_long' => $this->input->post('lat_long'),
                    'city_id' => $this->input->post('city_id'),
                    'radius' => $this->input->post('radius'),
                    'created_by' => $this->session->userdata('UserID'),
                    'created_date' => date('Y-m-d H:i:s'),
                    'price_charge' => ($this->input->post('price_charge')) ? $this->input->post('price_charge') : NULL,
                    'price_charge_2' => ($this->input->post('price_charge_2')) ? $this->input->post('price_charge_2') : NULL,
                    'price_charge_3' => ($this->input->post('price_charge_3')) ? $this->input->post('price_charge_3') : NULL,
                    'status' => 1
                );

                $zone_id = $this->zone_model->addData('zone', $add_data);

                if (!empty($this->input->post('restaurant_id'))) {
                    $res_data = array();
                    foreach ($this->input->post('restaurant_id') as $key => $value) {
                        $res_data[] = array(
                            'restaurant_id' => $value,
                            'zone_id' => $zone_id,

                        );
                    }
                    $this->zone_model->insertBatch('zone_res_map', $res_data, $id = '');
                }
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
            }
        }
        $language_slug = ($this->uri->segment(4)) ? $this->uri->segment(4) : $this->session->userdata('language_slug');
        $data['restaurant'] = $this->zone_model->getListData('restaurant', $language_slug);
        $data['city'] = $this->zone_model->getcitylist('city');
        $this->load->view(ADMIN_URL . '/zone_add', $data);
    }
    //add City Data
    public function add_city()
    {
        $data['meta_title'] = ' city | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $add_data = array(
                'name' => $this->input->post('name'),
                'status' => 1
            );
            $city_id = $this->zone_model->addCity('city', $add_data);
            if ($city_id) {
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/city_view');
            }
        }
        $this->load->view(ADMIN_URL . '/city_add', $data);
    }
    //add Vehicle Data
    public function add_vehicle()
    {
        $data['meta_title'] = ' Vehicle | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            $add_data = array(
                'name' => $this->input->post('name'),
                'price' => $this->input->post('price'),
                'status' => 1
            );
            $result = $this->zone_model->addvehicle('vehicle_type', $add_data);
            if ($result) {
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/vehicle_view');
            }
        }
        $this->load->view(ADMIN_URL . '/add_vehicle', $data);
    }
    //edit data
    public function edit()
    {
        $data['meta_title'] = "Edit Zone" . ' | ' . $this->lang->line('site_title');
        //check add form is submit

        if ($this->input->post('submit_page') == "Submit") {
            $this->form_validation->set_rules('area_name', 'Area Name', 'trim|required');
            $this->form_validation->set_rules('city_id', 'City ID', 'required');
            $this->form_validation->set_rules('lat_long', 'Latitude/Longitude', 'trim|required');
            $this->form_validation->set_rules('restaurant_id[]', 'Restaurant', 'trim|required');
            $this->form_validation->set_rules('price_charge', 'Price Charge', 'trim|required');
            $this->form_validation->set_rules('price_charge_2', 'Price Charge_2', 'trim|required');
            $this->form_validation->set_rules('price_charge_3', 'Price Charge_3', 'trim|required');
            if ($this->form_validation->run()) {
                $updateData = array(
                    'area_name' => $this->input->post('area_name'),
                    'city_id' => $this->input->post('city_id'),
                    'lat_long' => $this->input->post('lat_long'),
                    'radius' => $this->input->post('radius'),
                    'price_charge' => ($this->input->post('price_charge')) ? $this->input->post('price_charge') : NULL,
                    'price_charge_2' => ($this->input->post('price_charge_2')) ? $this->input->post('price_charge_2') : NULL,
                    'price_charge_3' => ($this->input->post('price_charge_3')) ? $this->input->post('price_charge_3') : NULL,
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID')
                );

                // echo "<pre>";
                // print_r($updateData);
                // exit();
                $this->zone_model->updateData($updateData, 'zone', 'entity_id', $this->input->post('entity_id'));

                if (!empty($this->input->post('restaurant_id'))) {
                    $res_data = array();
                    foreach ($this->input->post('restaurant_id') as $key => $value) {
                        $res_data[] = array(
                            'restaurant_id' => $value,
                            'zone_id' => $this->input->post('entity_id'),

                        );
                    }
                    $this->zone_model->insertBatch('zone_res_map', $res_data, $this->input->post('entity_id'));
                }
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
            }
        }
        $language_slug = $this->session->userdata('language_slug');
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['restaurant'] = $this->zone_model->getListData('restaurant', $language_slug);
        $data['restaurant_map'] = $this->zone_model->getList($entity_id);
        $data['edit_records'] = $this->zone_model->getEditDetail($entity_id);
        $data['city'] = $this->zone_model->getcitylist('city');
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/zone_add', $data);
    }
    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'area_name', 2 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->zone_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            $records["aaData"][] = array(
                $nCount,
                $val->area_name,
                ($val->status == 1) ? $this->lang->line('active') : $this->lang->line('inactive'),
                ($this->lpermission->method('zone_area', 'update')->access()
                    ? '<a class="btn btn-sm danger-btn margin-bottom" title="' . $this->lang->line('edit') . '" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit') : '')
                    .
                    ($this->lpermission->method('zone_area', 'delete')->access()
                        ? '</a> <button onclick="deleteDetail(' . $val->entity_id . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' : '')
                    .
                    ($this->lpermission->method('zone_area', 'update')->access()
                        ? '<button onclick="disable_record(' . $val->entity_id . ',' . $val->status . ')"  title="' . $this->lang->line('click_for') . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->status ? 'times' : 'check') . '"></i> ' . ($val->status ? '' . $this->lang->line('inactive') . '' : '' . $this->lang->line('active') . '') . '</button>' : '')
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }

    // method for delete
    public function ajaxDeleteAll()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->zone_model->ajaxDeleteAll('zone', $entity_id);
    }
    // get restaurant lat long
    public function getResLatLong()
    {
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $reslatlong = $this->delivery_charge_model->getResLatLong($restaurant_id);
        echo json_encode($reslatlong);
    }

    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('zone_id') != '') ? $this->input->post('zone_id') : '';
        if ($entity_id != '') {
            $this->zone_model->UpdatedStatus('zone', $entity_id, $this->input->post('status'));
        }
    }
    // view restaurant menu
    public function rider_view($zone_id = null, $city_id = null)
    {
        $zone_id =  $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $zone_id));
        $data['meta_title'] = "Rider List";
        $data['Languages'] = $this->common_model->getLanguages();
        $data['restaurant'] = $this->zone_model->getDriverData();

        $city_data = $this->users_model->getcity();
        $zone_data = $this->users_model->getzone();
        $data['city_data'] = $city_data;
        $data['zone_data'] = $zone_data;

        if ($zone_id) {
            $data['zone_id'] = $zone_id;
        }
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/rider_list', $data);
    }
    // call for ajax data
    public function ajaxviewRider()
    {

        $city_id = $this->input->post('city_id', TRUE);
        $zone_id = $this->input->post('zone_id', TRUE);
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
        $grid_data = $this->zone_model->getDriverData($sortFieldName, $sortOrder, $displayStart, $displayLength, $city_id, $zone_id);
        //   echo "<pre>";
        // print_r($grid_data);
        // exit();
        $totalRecords = $grid_data['total'];
        //    echo "<pre>";
        // print_r($totalRecords);
        // exit();
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 1;
        //$edit_active_access = '<button onclick="deleteAll(' . $value['content_ids'] . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom red"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>';
        // $edit_active_access .= '<button onclick="disableAll(' . $value['content_id'] . ',' . $value['status'] . ')"  title="' . $this->lang->line('click_for') . ' ' . ($value['status'] ? $this->lang->line('inactive') : $this->lang->line('active')) . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($value['status'] ? 'times' : 'check') . '"></i> ' . ($value['status'] ? $this->lang->line('inactive') : $this->lang->line('active')) . '</button>';
        foreach ($grid_data['data'] as $key => $value) {
            $rider_id = $value->entity_id;
            $accept_rate = $this->sub_dashboard_model->average_accept_rate($zone_id, $rider_id, $city_id);
            $cancel_rate = $this->sub_dashboard_model->average_cancel_rate($zone_id, $rider_id, $city_id);
            $delivery_time = $this->sub_dashboard_model->average_delivery_time($zone_id, $rider_id, $city_id);
            $total_delivered = $this->sub_dashboard_model->average_delivery_time($zone_id, $rider_id, $city_id);
            $total_cancel = $this->sub_dashboard_model->average_cancel_rate($zone_id, $rider_id, $city_id);
            $onlineStatus = $this->sub_dashboard_model->online_riders(null, null, null, $rider_id);
            $records["aaData"][] = array(
                $cnt,
                $value->first_name,
                $value->mobile_number,
                ($onlineStatus ?
                    '<span class="text-success"><strong>Online</strong></span>'
                    : '<span class="text-danger"><strong>Offline</strong></span>'),
                $accept_rate['avg_ar'] . " %",
                $cancel_rate['avg_cr'] . " %",
                $delivery_time['avg_dt'] . " Minutes",

                // ($value->total_delivered ? $value->total_delivered : 0),
                $total_delivered['total_deliverd_order'],
                $total_cancel['total_cancelled_order']
                // ($value->total_cancel ? $value->total_cancel : 0),

            );
            $cnt++;
            //$nCount++;
        }



        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;

        //  echo "<pre>";
        // print_r($records);
        // exit();
        echo json_encode($records);
    }
}
