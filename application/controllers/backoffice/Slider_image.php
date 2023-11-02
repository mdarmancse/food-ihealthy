<?php
$id = 0;

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
class Slider_image extends CI_Controller
{
  public $controller_name = 'slider-image';
  public $prefix = '_slider';

  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata('is_admin_login')) {
      redirect('home');
    }
    $this->load->library('form_validation');
    $this->load->model(ADMIN_URL . '/slider_image_model');
  }
  //view
  public function view()
  {
    $data['meta_title'] = $this->lang->line('title_slider_image') . ' | ' . $this->lang->line('site_title');
    $this->load->view(ADMIN_URL . '/slider_images', $data);
  }
  // add slider images
  public function add()
  {
    $data['meta_title'] = $this->lang->line('title_slider_image_add') . ' | ' . $this->lang->line('site_title');
    if ($this->input->post('submit_page') == "Submit" /*|| $this->input->post('restaurantId') == "resId"*/) {
      /*if (isset($data['selectedRestaurant'])) {
        $id = $data['selectedRestaurant'];
      }*/

      if (!empty($_FILES['Slider_image']['name'])) {
        $this->load->library('upload_cloud');
        $config['upload_path'] = './uploads/slider-images';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '5120'; //in KB
        $config['encrypt_name'] = TRUE;
        // create directory if not exists
        if (!@is_dir('uploads/slider-images')) {
          @mkdir('./uploads/slider-images', 0777, TRUE);
        }
        $this->upload_cloud->initialize($config);
        if ($this->upload_cloud->do_upload('Slider_image')) {

          $img = $this->upload_cloud->data();
          $add_data['image'] = "slider-images/" . $img['file_name'];
          $add_data['restaurant_id'] = $this->input->post("showAllRestaurant");
          // = $id;
          $add_data['action_type'] = $this->input->post('actionType');
          $add_data['url'] = $this->input->post('link');

          $add_data['item_id'] = $this->input->post('selectedItemId');

          $this->slider_image_model->addData('slider_image', $add_data);
          $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
        } else {
          $data['Error'] = $this->upload_cloud->display_errors();
          $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
        }
      }

      redirect(base_url() . ADMIN_URL . '/slider_image/view');
    }


    $data['allRestaurant'] = $this->slider_image_model->allRestaurant();
    $data['selectedRestaurant'] = $this->input->post("showAllRestaurant");
    $data['allItems'] = $this->slider_image_model->showItems($data);

    $this->load->view(ADMIN_URL . '/slider_image_add', $data);
  }
  // edit user insurance
  public function edit()
  {
    $data['meta_title'] = $this->lang->line('title_slider_image_edit') . ' | ' . $this->lang->line('site_title');
    // check if form is submitted
    if ($this->input->post('submit_page') == "Submit") {
      if (!empty($_FILES['Slider_image']['name'])) {
        $this->load->library('upload_cloud');
        $config['upload_path'] = './uploads/slider-images';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '5120'; //in KB
        $config['encrypt_name'] = TRUE;
        // create directory if not exists
        if (!@is_dir('uploads/slider-images')) {
          @mkdir('./uploads/slider-images', 0777, TRUE);
        }
        $this->upload_cloud->initialize($config);
        if ($this->upload_cloud->do_upload('Slider_image')) {
          $img = $this->upload_cloud->data();
          $add_data['image'] = "slider-images/" . $img['file_name'];

          $add_data['action_type'] = $this->input->post('actionType');
          $add_data['url'] = $this->input->post('link');
          $add_data['item_id'] = $this->input->post('selectedItemId');
          // code for delete existing image
          if ($this->input->post('uploadedSliderImage')) {
            @unlink(FCPATH . 'uploads/' . $this->input->post('uploadedSliderImage'));
          }


          $this->slider_image_model->updateData($add_data, 'slider_image', 'entity_id', $this->input->post('entity_id'));
          $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
        } else {
          $data['Error'] = $this->upload_cloud->display_errors();
          $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
        }
      } else {
        $add_data['action_type'] = $this->input->post('actionType');
        $add_data['url'] = $this->input->post('link');
        $add_data['item_id'] = $this->input->post('selectedItemId');

        $this->slider_image_model->updateData($add_data, 'slider_image', 'entity_id', $this->input->post('entity_id'));
        $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
      }
      redirect(base_url() . ADMIN_URL . '/slider_image/view');
    }
    $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
    $data['edit_records'] = $this->slider_image_model->getEditDetail($entity_id);
    $data['allRestaurant'] = $this->slider_image_model->allRestaurant();
    //$data['selectedRestaurant'] = $this->input->post("showAllRestaurant");
    //$data['allItems'] = $this->slider_image_model->showItems($data);
    $this->load->view(ADMIN_URL . '/slider_image_add', $data);
  }
  //ajax view
  public function ajaxview()
  {
    $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
    $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
    $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
    $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
    $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

    $sortfields = array();
    $sortFieldName = '';
    if (array_key_exists($sortCol, $sortfields)) {
      $sortFieldName = $sortfields[$sortCol];
    }
    //Get Recored from model
    $grid_data = $this->slider_image_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength);
    $totalRecords = $grid_data['total'];
    $records = array();
    $records["aaData"] = array();
    $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
    foreach ($grid_data['data'] as $key => $val) {
      $doc = "'" . $val->image . "'";
      $records["aaData"][] = array(
        $nCount,
        '<img id="oldpic" class="sliderimg" width="70" height="50" src="' . image_url . $val->image . '">',
        ($val->status) ? $this->lang->line('active') : $this->lang->line('inactive'),
        ($this->lpermission->method('slider', 'create')->access()
          ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit')
          : '') .
          ($this->lpermission->method('slider', 'create')->access()
            ? '</a> <button onclick="deleteDetail(' . $val->entity_id . ',' . $doc . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>'
            : '')
      );
      $nCount++;
    }
    $records["sEcho"] = $sEcho;
    $records["iTotalRecords"] = $totalRecords;
    $records["iTotalDisplayRecords"] = $totalRecords;
    echo json_encode($records);
  }
  // method to change status
  public function ajaxdisable()
  {
    $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
    if ($entity_id != '') {
      $this->slider_image_model->UpdatedStatus('slider_image', $entity_id, $this->input->post('status'));
    }
  }
  // method for deleting
  public function ajaxDelete()
  {
    $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
    $Image = ($this->input->post('image') != '') ? $this->input->post('image') : '';
    $this->slider_image_model->ajaxDelete('slider_image', $entity_id);
    @unlink(FCPATH . 'uploads/' . $Image);
  }
}
