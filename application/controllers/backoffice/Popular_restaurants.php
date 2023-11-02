<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Popular_restaurants extends CI_Controller
{
    public $controller_name = 'popular';
    public $prefix = '_pop';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/popular_restaurants_model');
    }
    // view coupon
    public function view()
    {
        $data['meta_title'] = ' Popular Restaurants | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/popular_restaurants', $data);
    }



    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'name');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->popular_restaurants_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($grid_data['data'] as $key => $val) {
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
                $this->lpermission->method('popular_restaurant', 'update')->access()
                    ? '<button onclick="disable_record(' . $val->entity_id . ',' . $val->is_popular . ')"  title="' . $this->lang->line('click_for') . ($val->is_popular ? ' Regular' : ' Popular') . ' " class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-' . ($val->is_popular ? 'check' : 'fire') . '"></i> ' . ($val->is_popular ? 'Regular' : 'Popular') . '</button>'
                    : '',
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
            $this->popular_restaurants_model->UpdatedStatus('restaurant', $entity_id, $this->input->post('is_popular'));
        }
    }
}
