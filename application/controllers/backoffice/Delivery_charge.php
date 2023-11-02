<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Delivery_charge extends CI_Controller { 
    public $controller_name = 'delivery_charge';
    public $prefix = 'delivery';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/delivery_charge_model');
    }
    //view data
    public function view() {
        $data['meta_title'] = $this->lang->line('delivery_charge').' | '.$this->lang->line('site_title');
        $data['Languages'] = $this->common_model->getLanguages();     
        $this->load->view(ADMIN_URL.'/delivery_charge',$data);
    }
    //add data
    public function add() {
        $data['meta_title'] = $this->lang->line('delivery_charge_add').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('area_name', 'Area Name', 'trim|required');
            $this->form_validation->set_rules('lat_long', 'Latitude/Longitude', 'trim|required');
            $this->form_validation->set_rules('price_charge', 'Price Charge', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            if ($this->form_validation->run())
            {
                $add_data = array(                   
                    'restaurant_id'=>$this->input->post('restaurant_id'),
                    'area_name'=>$this->input->post('area_name'),
                    'lat_long'=>$this->input->post('lat_long'),
                    'price_charge'=>($this->input->post('price_charge'))?$this->input->post('price_charge'):NULL,
                    'created_by' => $this->session->userdata('UserID')
                ); 
                $this->delivery_charge_model->addData('delivery_charge',$add_data); 
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
            }
        }
        $language_slug = ($this->uri->segment(4))?$this->uri->segment(4):$this->session->userdata('language_slug');
        $data['restaurant'] = $this->delivery_charge_model->getListData('restaurant',$language_slug);
        $this->load->view(ADMIN_URL.'/delivery_charge_add',$data);
    }
    //edit data
    public function edit() {
        $data['meta_title'] = $this->lang->line('delivery_charge_edit').' | '.$this->lang->line('site_title');
        //check add form is submit
        if($this->input->post('submit_page') == "Submit")
        {
            $this->form_validation->set_rules('area_name', 'Area Name', 'trim|required');
            $this->form_validation->set_rules('lat_long', 'Latitude/Longitude', 'trim|required');
            $this->form_validation->set_rules('price_charge', 'Price Charge', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            if ($this->form_validation->run())
            {
                $updateData = array(     
                    'restaurant_id'=>$this->input->post('restaurant_id'),              
                    'area_name'=>$this->input->post('area_name'),
                    'lat_long'=>$this->input->post('lat_long'),
                    'price_charge'=>($this->input->post('price_charge'))?$this->input->post('price_charge'):NULL,
                    'updated_date'=>date('Y-m-d H:i:s'),
                    'updated_by' => $this->session->userdata('UserID')
                ); 
                $this->delivery_charge_model->updateData($updateData,'delivery_charge','charge_id',$this->input->post('charge_id'));
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                redirect(base_url().ADMIN_URL.'/'.$this->controller_name.'/view');          
                  
            }
        }    
        $language_slug = $this->session->userdata('language_slug');
        $charge_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['restaurant'] = $this->delivery_charge_model->getListData('restaurant',$language_slug);
        $data['edit_records'] = $this->delivery_charge_model->getEditDetail($charge_id);
        $this->load->view(ADMIN_URL.'/delivery_charge_add',$data);
    }
    //ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'name',2=>'area_name',3=>'price_charge',4=>'delivery_charge.created_date');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->delivery_charge_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $cnt = 0;
        foreach ($grid_data['data'] as $key => $val) {
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            $records["aaData"][] = array(
                $nCount,
                $val->name,
                $val->area_name,
                $currency_symbol->currency_symbol.number_format_unchanged_precision($val->price_charge,$currency_symbol->currency_code),
                '<a class="btn btn-sm danger-btn margin-bottom" title="'.$this->lang->line('edit').'" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->charge_id)).'"><i class="fa fa-edit"></i> '.$this->lang->line('edit').'</a> <button onclick="deleteDetail('.$val->charge_id.')"  title="'.$this->lang->line('click_delete').'" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '.$this->lang->line('delete').'</button>'
            );
            $nCount++;
        }   
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method for delete
    public function ajaxDeleteAll(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $this->delivery_charge_model->ajaxDeleteAll('delivery_charge',$entity_id);
    }
    // get restaurant lat long
    public function getResLatLong(){
        $restaurant_id = ($this->input->post('restaurant_id') != '')?$this->input->post('restaurant_id'):'';
        $reslatlong = $this->delivery_charge_model->getResLatLong($restaurant_id);
        echo json_encode($reslatlong);
    }
}