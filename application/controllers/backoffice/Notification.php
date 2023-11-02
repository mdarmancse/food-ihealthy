<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Notification extends CI_Controller
{
    public $full_module = 'Notification';
    public $module_name = 'Notification';
    public $controller_name = 'notification';

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/notification_model');
    }
    public function view()
    {
        $data['meta_title'] = $this->lang->line('title_admin_notification') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/notification', $data);
    }
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_notificationadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submitNotification') == "Submit") {

            $this->form_validation->set_rules('notification_title', 'Notification Title', 'trim|required');
            $this->form_validation->set_rules('users', 'User Type', 'required');
            if ($this->form_validation->run()) {
                $addNotificationData = array(
                    'notification_title' => $this->input->post('notification_title'),
                    'notification_description' => $this->input->post('notification_description'),
                    'created_by' => $this->session->userdata("UserID"),
                    'selection_type' => $this->input->post('users')
                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/Notifications';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/Notifications')) {
                        @mkdir('./uploads/Notifications', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $addNotificationData['image'] = "Notifications/" . $img['file_name'];
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                }
                $NotificationID = $this->notification_model->addData('notifications', $addNotificationData);

                if ($this->input->post('users') == "all_users") {
                    $ids = $this->notification_model->getUserNotification();
                    $UserIds = array_column($ids, 'entity_id');
                }
                if ($this->input->post('users') == "specific_users") {
                    $UserIds = $this->input->post('user_id');
                }




                $NotificationDetail = array();
                if ($this->input->post('save') == 1) {
                    for ($u = 0; $u < count($UserIds); $u++) {
                        $NotificationDetail[] = array('notification_id' => $NotificationID, 'user_id' => $UserIds[$u]);
                    }
                    $this->notification_model->addRecordBatch('notifications_users', $NotificationDetail);
                }

                // START Push Notification
                $DeviceIds = $this->notification_model->getUserDevices($UserIds);
                $registrationIds = array_column($DeviceIds, 'device_id');
                $return = array_chunk($registrationIds, 800);
                foreach ($return as $key => $registrationId) {
                    #prep the bundle
                    $fields = array();
                    if (is_array($registrationId) && count($registrationId) > 1) {
                        $fields['registration_ids'] = $registrationId; // multiple user to send push notification
                    } else {
                        $fields['to'] = $registrationId[0]; // only one user to send push notification
                    }
                    $fields['notification']['title'] = $this->input->post('notification_title');
                    $fields['notification']['body'] = $this->input->post('notification_description');
                    $fields['notification']['sound'] = 'default';
                    $fields['notification']['image'] = image_url . "Notifications/" . $img['file_name'];
                    if ($this->input->post('save') == 1) {
                        $fields['data'] = array('screenType' => 'noti');
                    } else {
                        $fields['data'] = array('screenType' => 'noti');
                    }
                    $headers = array(
                        'Authorization: key=' . Driver_FCM_KEY,
                        'Content-Type: application/json'
                    );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }

                // END Push Notification
                $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_add'));
                redirect(base_url() . ADMIN_URL . '/notification/view');
            }
        }
        // $data['users'] =  $this->notification_model->getUserNotification();
        $this->load->view(ADMIN_URL . '/notification_add', $data);
    }
    public function edit()
    {
        $data['meta_title'] = $this->lang->line('title_admin_notificationedit') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submitNotification') == "Submit") {
            $this->form_validation->set_rules('notification_title', 'Notification Title', 'trim|required');
            $this->form_validation->set_rules('users', 'User Type', 'required');
            if ($this->form_validation->run()) {
                $edit_data = array(
                    'notification_title' => $this->input->post('notification_title'),
                    'notification_description' => $this->input->post('notification_description'),
                    'selection_type' => $this->input->post('users')
                );
                if (!empty($_FILES['image']['name'])) {
                    $this->load->library('upload_cloud');
                    $config['upload_path'] = './uploads/Notifications';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $config['max_size'] = '5120'; //in KB
                    $config['encrypt_name'] = TRUE;
                    // create directory if not exists
                    if (!@is_dir('uploads/Notifications')) {
                        @mkdir('./uploads/Notifications', 0777, TRUE);
                    }
                    $this->upload_cloud->initialize($config);
                    if ($this->upload_cloud->do_upload('image')) {
                        $img = $this->upload_cloud->data();
                        $edit_data['image'] = "Notifications/" . $img['file_name'];
                        // code for delete existing image
                        if ($this->input->post('uploaded_image')) {
                            @unlink(FCPATH . 'uploads/' . $this->input->post('uploaded_image'));
                        }
                    } else {
                        $data['Error'] = $this->upload_cloud->display_errors();
                        $this->form_validation->set_message('upload_invalid_filetype', 'Error Message');
                    }
                } else {
                    $IM = true;
                    // $IMG="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Image_created_with_a_mobile_phone.png/220px-Image_created_with_a_mobile_phone.png";
                    $data = $this->notification_model->getEditNotificationDetail($this->input->post('entity_id'));

                    $IMG1 = $data->image;
                    $IMG = image_url . $IMG1;
                }
                $this->notification_model->updateData($edit_data, 'notifications', 'entity_id', $this->input->post('entity_id'));

                if ($this->input->post('users') == "all_users") {
                    $ids = $this->notification_model->getUserNotification();
                    $UserIds = array_column($ids, 'entity_id');
                }
                if ($this->input->post('users') == "specific_users") {
                    $UserIds = $this->input->post('user_id');
                }

                $NotificationDetail = array();
                if ($this->input->post('save') == 1) {
                    for ($u = 0; $u < count($UserIds); $u++) {
                        $NotificationDetail[] = array('notification_id' => $this->input->post('entity_id'), 'user_id' => $UserIds[$u]);
                    }
                    $this->notification_model->deleteInsertRecord('notifications_users', 'notification_id', $this->input->post('entity_id'), $NotificationDetail);
                }
                // START Push Notification
                $DeviceIds = $this->notification_model->getUserDevices($UserIds);
                $registrationIds = array_column($DeviceIds, 'device_id');
                $return = array_chunk($registrationIds, 800);
                foreach ($return as $key => $registrationId) {
                    #prep the bundle
                    $fields = array();
                    if (is_array($registrationId) && count($registrationId) > 1) {
                        $fields['registration_ids'] = $registrationId; // multiple user to send push notification
                    } else {
                        $fields['to'] = $registrationId[0]; // only one user to send push notification
                    }
                    $fields['notification']['title'] = $this->input->post('notification_title');
                    $fields['notification']['body'] = $this->input->post('notification_description');
                    $fields['notification']['sound'] = 'default';
                    $fields['notification']['image'] = $IM ? $IMG : image_url . "Notifications/" . $img['file_name'];
                    if ($this->input->post('save') == 1) {
                        $fields['data'] = array('screenType' => 'noti');
                    } else {
                        $fields['data'] = array('screenType' => 'noti');
                    }
                    $headers = array(
                        'Authorization: key=' . Driver_FCM_KEY,
                        'Content-Type: application/json'
                    );
                    #Send Reponse To FireBase Server
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }

                // END Push Notification
                $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_update'));
                redirect(base_url() . ADMIN_URL . '/notification/view');
            }
        }
        // $data['users'] =  $this->notification_model->getUserNotification();
        $entity_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))) : $this->input->post('entity_id');
        $data['editNotificationDetail'] = $this->notification_model->getEditDetail('notifications', $entity_id);
        $data['NotificationUsers'] = array();
        if (!empty($data['editNotificationDetail']) && $data['editNotificationDetail']->selection_type == 'specific_users') {
            if ($data['editNotificationDetail']->entity_id) {
                $data['NotificationUsers'] = $this->notification_model->getNotificationUsers($data['editNotificationDetail']->entity_id);
                // $data['NotificationUsers'] = array_column($data['NotificationUsers'], "user_id");
            }
        }

        $this->load->view(ADMIN_URL . '/notification_add', $data);
    }
    public function ajaxview()
    {
        $searchTitleName = ($this->input->post('pageTitle') != '') ? $this->input->post('pageTitle') : '';
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';

        $sortfields = array(1 => 'notification_title', 2 => 'Status', 3 => 'CreatedDate');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $NotificationData = $this->notification_model->getNotificationList($searchTitleName, $sortFieldName, $sortOrder, $displayStart, $displayLength);
        $totalRecords = $NotificationData['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        foreach ($NotificationData['data'] as $key => $notificationDetails) {
            $records["aaData"][] = array(
                $nCount,
                $notificationDetails->notification_title,
                ($this->lpermission->method('notifications', 'update')->access()
                    ? '<a class="btn btn-sm danger-btn margin-bottom" href="' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($notificationDetails->entity_id)) . '"><i class="fa fa-edit"></i> ' . $this->lang->line('edit') . '</a>' : '') .
                    ($this->lpermission->method('notifications', 'delete')->access()
                        ? '<button onclick="deleteNotification(' . $notificationDetails->entity_id . ')"  title="Click here for Delete" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>' : '')
                //<a class="btn btn-sm danger-btn margin-bottom" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($notificationDetails->entity_id)).'"><i class="fa fa-edit"></i> Edit</a>
            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    public function ajaxdeleteNotification()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id != '') {
            $this->notification_model->deleteRecord($entity_id);
        }
    }

    public function getUsers()
    {
        $user_info   = $this->input->post('term', TRUE);
        $user_data   = $this->notification_model->getUserData($user_info);

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
}
