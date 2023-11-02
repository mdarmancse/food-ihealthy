<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sub_dashboard extends CI_Controller
{
    public $module_name = 'Sub Dashboard';
    public $controller_name = 'sub_dashboard';
    public $prefix = '_sub_dashboard';
    public $value;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(ADMIN_URL . '/sub_dashboard_model');
    }

    public function view()
    {
        $city_data = $this->sub_dashboard_model->getcity();
        $zone_data = $this->sub_dashboard_model->getzone();
        $data['city_data'] = $city_data;
        $data['meta_title'] = 'Zone Utilization' . ' | ' . $this->lang->line('site_title');

        $this->load->view(ADMIN_URL . '/sub_dashboard', $data);
    }

    public function ajaxView_overall()
    {
        $city_id = $this->input->post('city_id', TRUE);

        $online_rider_count    = array(
            'name'      => 'Online Riders',
            'number'    => $this->sub_dashboard_model->online_riders(null, $city_id)
        );

        $rider_with_1_order = array(
            'name'      => 'Rider with 1 order',
            'number'    => $this->sub_dashboard_model->get_total_rider_with_n_order(1, null, $city_id)
        );
        $rider_with_2_order = array(
            'name'      => 'Rider with 2 orders',
            'number'    => $this->sub_dashboard_model->get_total_rider_with_n_order(2, null, $city_id)
        );
        $rider_with_3_order = array(
            'name'      => 'Rider with 3 orders',
            'number'    => $this->sub_dashboard_model->get_total_rider_with_n_order(3, null, $city_id)
        );
        $total_active_order = array(
            'name'      => 'Total Active Order(s)',
            'number'    => $this->sub_dashboard_model->get_total_active_orders($city_id)
        );
        $total_unassigned_order = array(
            'name'      => 'Total Unassigned Order(s)',
            'number'    => $this->sub_dashboard_model->getUnassignedOrders($city_id)
        );

        $data['rider_stats']   =  array(
            $online_rider_count,
            $rider_with_1_order,
            $rider_with_2_order,
            $rider_with_3_order,
            $total_active_order,
            $total_unassigned_order,
        );



        $this->load->view(ADMIN_URL . '/rider_dashboard_data', $data);
    }

    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $city_id = $this->input->post('city_id', TRUE);

        $sortfields = array(1 => 'area_name', 2 => 'created_date');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->sub_dashboard_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $city_id);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            $records["aaData"][] = array(
                $nCount,
                $val['zone_name'],
                $val['open_rider'],
                $val['rider_1_order'],
                $val['rider_2_order'],
                $val['rider_3_order'],
                $val['total_deliverd_order'],
                $val['avg_dt'] . ' min',
                $val['avg_ar'] . '%',
                $val['total_cancelled_order'],
                $val['avg_cr'] . '%',
                $val['rider_cancel_order'],
                ($this->lpermission->method('zone_area', 'update')->access()
                    ? '<a class="btn btn-sm danger-btn margin-bottom" title="' . 'Rider List' . '" href="' . base_url() . ADMIN_URL . '/zone/rider_view/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val['zone_id'])) . '"><i class="fa fa-motocycle"></i> ' . 'Rider List'
                    : '')
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
}
