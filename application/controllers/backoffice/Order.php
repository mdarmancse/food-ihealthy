<?php error_reporting(1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH . "/third_party/excelclasses/PHPExcel.php";
require_once APPPATH . "/third_party/excelclasses/PHPExcel/IOFactory.php";
class Order extends CI_Controller
{
    public $module_name = 'Order';
    public $controller_name = 'order';
    public $prefix = '_order';
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL . '/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL . '/order_model');
        $this->load->model(ADMIN_URL . '/users_model');
        $this->load->model(ADMIN_URL . '/Sub_dashboard_model');
        $this->load->model('v1/api_model');
        $this->load->model(ADMIN_URL . '/systemoption_model');
    }
    public function FilterDriver()
    {

        $city_id = $this->input->post('city_id', TRUE);
        $zone_id = $this->input->post('zone_id', TRUE);
        $from_date = $this->input->post('start_date', TRUE);
        $to_date = $this->input->post('end_date', TRUE);

        if (is_array($zone_id)) {
            if (empty($zone_id[array_key_last($zone_id)])) {
                $zone_id = null;
            } else {
                $zone_id = explode(',', $zone_id[array_key_last($zone_id)]);
            }
        }
        $data['total_online_rider'] = $this->Sub_dashboard_model->online_riders(1, $city_id, $zone_id, null, null);
        $data['unassigned_order'] = $this->Sub_dashboard_model->getUnassignedOrders($from_date, $to_date, $city_id, $zone_id);
        $data['cancelled_order'] = $this->Sub_dashboard_model->average_cancel_rate($zone_id, null, $city_id, $from_date, $to_date);
        $data['total_delivered'] = $this->Sub_dashboard_model->average_delivery_time($zone_id, null, $city_id, $from_date, $to_date);
        $data['accepted_order'] = $this->Sub_dashboard_model->average_accept_rate($zone_id, null, $city_id, $from_date, $to_date);
        $data['inactive_rider'] = $this->Sub_dashboard_model->inactive_rider(null, $zone_id, $city_id, null, null);
        echo json_encode($data);
    }
    // view order
    public function view()
    {
        $data['meta_title'] = "Dispatch Panel" . ' | ' . $this->lang->line('site_title');
        $data['restaurant'] = $this->order_model->getRestaurantList();
        $data['drivers'] = $this->order_model->getDrivers();
        $city_data = $this->users_model->getcity();
        $zone_data = $this->users_model->getzone();
        $data['zone_data'] = $zone_data;
        $data['city_data'] = $city_data;
        $data['total_online_rider'] = $this->Sub_dashboard_model->online_riders(1);
        $data['unassigned_order'] = $this->Sub_dashboard_model->getUnassignedOrders();
        $data['cancelled_order'] = $this->Sub_dashboard_model->average_cancel_rate();
        $data['total_delivered'] = $this->Sub_dashboard_model->average_delivery_time();
        $data['accepted_order'] = $this->Sub_dashboard_model->average_accept_rate();
        $data['total_inactive_rider'] = $this->Sub_dashboard_model->inactive_rider();
        $data['auto_refresh_time'] = $this->systemoption_model->getValue('auto_refresh_time');
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/order', $data);
    }
    public function GetDriver()
    {
        $order_id = $this->input->post('order_id') ? $this->input->post('order_id') : '';

        $detailData = $this->order_model->getZoneDrivers($order_id);
        // echo "<pre>";
        // print_r($detailData);
        // exit();

        $this->db->select('OptionValue');
        $max_orders = $this->db->get_where('system_option', array('OptionSlug' => 'rider_max_order'))->first_row();
        foreach ($detailData as $k => $v) {
            $currOrder = $this->Sub_dashboard_model->order_count($v->user_id);
            if ($currOrder == $max_orders->OptionValue) {
                unset($detailData[$k]);
            }
        }
        $data['latestOrder'] = array_values($detailData);

        echo json_encode($data);
    }
    public function GetDriverDetails()
    {
        $status = $this->input->post('status') ? $this->input->post('status') : '';
        $city_id = $this->input->post('city_id') ? $this->input->post('city_id') : '';
        $zone_id = $this->input->post('zone_id') ? $this->input->post('zone_id') : '';

        if ($status == 1 || $status == 2) {
            $data['driver_information'] = $this->Sub_dashboard_model->online_riders(null, $city_id, $zone_id, null, $status);
        }
        if ($status == 3) {
            $data['driver_information'] = $this->Sub_dashboard_model->inactive_rider(null, null, null, $status);
        }
        // echo "<pre>";
        // print_r($data);
        // exit();
        echo json_encode($data);
    }
    // add order
    public function add()
    {
        $data['meta_title'] = $this->lang->line('title_admin_orderadd') . ' | ' . $this->lang->line('site_title');
        if ($this->input->post('submit_page') == "Submit") {
            // $this->form_validation->set_rules('user_id', 'User', 'trim|required');
            $this->form_validation->set_rules('restaurant_id', 'Restaurant', 'trim|required');
            // $this->form_validation->set_rules('address_id','Address', 'trim|required');
            $this->form_validation->set_rules('order_status', 'Order Status', 'trim|required');
            $this->form_validation->set_rules('order_date', 'Date Of Order', 'trim|required');
            $this->form_validation->set_rules('total_rate', 'Total', 'trim|required');
            //check form validation using codeigniter

            if ($this->input->post('first_name') && $this->input->post('last_name') && $this->input->post('mobile_number')) {
                $userdata = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'mobile_number' => $this->input->post('mobile_number'),
                    'user_type' => 'User',
                    'status' => 1,
                    'active' => 1,
                    'password' => md5(SALT . $this->input->post('password')),
                    'created_by' => $this->session->userdata("UserID")
                );

                $user_id = $this->order_model->addData('users', $userdata);
            }

            if ($this->input->post('address') && $this->input->post('landmark')) {
                $add_address = array(
                    'address' => $this->input->post('address'),
                    'landmark' => $this->input->post('landmark'),
                    'user_entity_id' => $user_id,
                    'latitude' => $this->input->post('latitude'),
                    'longitude' => $this->input->post('longitude'),
                );

                $address_id = $this->order_model->addData('user_address', $add_address);
            }

            if ($this->form_validation->run()) {
                $total = $this->input->post('total_rate');
                $max_order_verification = $this->db->get_where('system_option', array('OptionSlug' => 'max_order_verification'))->first_row();
                if ($total > $max_order_verification->OptionValue) {
                    $notify_value = 0;
                } else {
                    $notify_value = 1;
                }
                $add_data = array(
                    'user_id' => $this->input->post('user_id') ? $this->input->post('user_id') : $user_id,
                    'restaurant_id' => $this->input->post('restaurant_id'),
                    'address_id' => ($this->input->post('address_id')) ? $this->input->post('address_id') : $address_id,
                    'coupon_id' => ($this->input->post('actual_coupon_type') == 'discount_on_items') ? '-100' : $this->input->post('coupon_id'),
                    'order_status' => $this->input->post('order_status'),
                    'order_date' => date('Y-m-d H:i:s', strtotime($this->input->post('order_date'))),
                    'subtotal' => ($this->input->post('subtotal')) ? $this->input->post('subtotal') : '',
                    'tax_rate' => ($this->input->post('tax_rate')) ? $this->input->post('tax_rate') : '',
                    'tax_type' => $this->input->post('tax_type'),
                    'total_rate' => ($this->input->post('total_rate')) ? $this->input->post('total_rate') : '',
                    'coupon_type' => $this->input->post('coupon_type'),
                    'coupon_discount' => ($this->input->post('coupon_discount')) ? $this->input->post('coupon_discount') : '',
                    'coupon_amount' => ($this->input->post('coupon_amount')) ? $this->input->post('coupon_amount') : '',
                    'created_by' => $this->session->userdata("UserID"),
                    'status' => 1,
                    'order_delivery' => 'PickUp',
                    'coupon_name' => $this->input->post('coupon_name'),
                    'sd' => $this->input->post('sd'),
                    'vat' => $this->input->post('vat'),
                    'delivery_charge' => ($this->input->post('deliveryCharge')) ? $this->input->post('deliveryCharge') : '',
                    'preorder_mode' => ($this->input->post('order_status') == 'preorder') ? 1 : 0,
                    'preorder_date' => ($this->input->post('order_status') == 'preorder') ? date('Y-m-d H:i:s', strtotime($this->input->post('order_date'))) : NULL,
                    'zone_id' => ($this->input->post('zone_id')) ? $this->input->post('zone_id') : NULL,
                    'verify_order' => $notify_value

                );
                $order_id = $this->order_model->addData('order_master', $add_data);

                if ($this->input->post('actual_coupon_type') == 'discount_on_items') {
                    $userID = $this->input->post('user_id') ? $this->input->post('user_id') : $user_id;
                    $coupon_id = $this->input->post('coupon_id');
                    $check_previous = $this->api_model->checkRecords('gradual_coupon_track', $userID, $coupon_id);
                    if (empty($check_previous)) {
                        $getHighestSequence = $this->api_model->getHighestSequence($coupon_id);

                        $track_detail = array(
                            'user_id' => $userID,
                            'coupon_id' => $coupon_id,
                            'count' => $getHighestSequence->sequence - 1,
                            'last_applied' => 1
                        );
                        $this->api_model->addRecord('gradual_coupon_track', $track_detail);
                    } else {
                        $decrement = array('user_id' => $userID, 'coupon_id' => $coupon_id);
                        $this->db->set('count', 'count-1', FALSE);
                        $this->db->set('last_applied', 'last_applied+1', FALSE);
                        $this->db->where($decrement);
                        $this->db->update('gradual_coupon_track');
                    }
                }
                //add items
                $items = $this->input->post('item_id');
                $add_item = array();
                if (!empty($items)) {
                    foreach ($items as $key => $value) {
                        $itemName = $this->order_model->getItemName($this->input->post('item_id')[$key]);
                        $add_item[] = array(
                            "item_id" => $this->input->post('item_id')[$key],
                            "item_name" => $itemName->name,
                            "qty_no" => $this->input->post('qty_no')[$key],
                            "rate" => ($this->input->post('rate')[$key]) ? $this->input->post('rate')[$key] : '',
                            "order_id" => $order_id
                        );
                    }
                }
                //  echo "<pre>";
                // print_r($add_item);
                // exit();
                //get user detail
                $addressID = ($this->input->post('address_id')) ? $this->input->post('address_id') : $address_id;
                $address = $this->api_model->getAddress('user_address', 'entity_id', $addressID);
                $tokenres = $this->order_model->checkToken($userID);
                $user_detail = array(
                    'first_name' => $tokenres->first_name,
                    'last_name' => ($tokenres->last_name) ? $tokenres->last_name : '',
                    'address' => ($address) ? $address[0]->address : '',
                    'landmark' => ($address) ? $address[0]->landmark : '',
                    'zipcode' => ($address) ? $address[0]->zipcode : '',
                    'city' => ($address) ? $address[0]->city : '',
                    'latitude' => ($address) ? $address[0]->latitude : '',
                    'longitude' => ($address) ? $address[0]->longitude : '',
                );
                //get restaurant detail
                $rest_detail = $this->order_model->getRestaurantDetail($this->input->post('restaurant_id'));
                $order_detail = array(
                    'order_id' => $order_id,
                    'item_detail' => serialize($add_item),
                    'user_detail' => serialize($user_detail),
                    'restaurant_detail' => serialize($rest_detail),
                );

                $this->order_model->addData('order_detail', $order_detail);
                $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                // send invoice to user
                $data['order_records'] = $this->order_model->getEditDetail($order_id);
                $data['menu_item'] = $this->order_model->getInvoiceMenuItem($order_id);
                $html = $this->load->view('backoffice/order_invoice', $data, true);
                if (!@is_dir('uploads/invoice')) {
                    @mkdir('./uploads/invoice', 0777, TRUE);
                }
                $filepath = 'uploads/invoice/' . $order_id . '.pdf';
                $this->load->library('M_pdf');
                $mpdf = new mPDF('', 'Letter');
                $mpdf->SetHTMLHeader('');
                $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="' . base_url() . '/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
                $mpdf->AddPage(
                    '', // L - landscape, P - portrait
                    '',
                    '',
                    '',
                    '',
                    0, // margin_left
                    0, // margin right
                    10, // margin top
                    23, // margin bottom
                    0, // margin header
                    0 //margin footer
                );
                $mpdf->autoScriptToLang = true;
                $mpdf->SetAutoFont();
                $mpdf->WriteHTML($html);
                $mpdf->output($filepath, 'F');

                //send invoice as email
                $user = $this->db->get_where('users', array('entity_id' => $this->input->post('user_id')))->first_row();
                $FromEmailID = $this->db->get_where('system_option', array('OptionSlug' => 'From_Email_Address'))->first_row();
                $this->db->select('OptionValue');
                $FromEmailName = $this->db->get_where('system_option', array('OptionSlug' => 'Email_From_Name'))->first_row();
                $this->db->select('subject,message');
                $Emaildata = $this->db->get_where('email_template', array('email_slug' => 'new-order-invoice', 'language_slug' => $this->session->userdata('language_slug'), 'status' => 1))->first_row();
                $arrayData = array('FirstName' => $user->first_name, 'Order_ID' => $order_id);
                $EmailBody = generateEmailBody($Emaildata->message, $arrayData);
                if (!empty($EmailBody)) {
                    $this->load->library('email');
                    $config['charset'] = 'iso-8859-1';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    $this->email->from($FromEmailID->OptionValue, $FromEmailName->OptionValue);
                    $this->email->to(trim($user->email));
                    $this->email->subject($Emaildata->subject);
                    $this->email->message($EmailBody);
                    $this->email->attach($filepath);
                    $this->email->send();
                }
                redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
            }
        }
        $data['restaurant'] = $this->order_model->getListData('restaurant');
        $data['user'] = $this->order_model->getListData('users');
        $data['coupon'] = $this->order_model->getListData('coupon');
        if ($this->session->userdata('UserType') == 'Admin') {
            $data['adminRestaurantName'] = $this->order_model->getResName($this->session->userdata('restaurant')[0]);
            $data['menu'] = $this->order_model->getItem($this->session->userdata('restaurant')[0]);
            $data['gradual'] = $this->api_model->getActiveGradual($this->session->userdata('restaurant')[0]);
        }
        $this->load->view(ADMIN_URL . '/order_add', $data);
    }
    //ajax view
    public function ajaxview()
    {
        $displayLength = ($this->input->post('iDisplayLength') != '') ? intval($this->input->post('iDisplayLength')) : '';
        $displayStart = ($this->input->post('iDisplayStart') != '') ? intval($this->input->post('iDisplayStart')) : '';
        $sEcho = ($this->input->post('sEcho')) ? intval($this->input->post('sEcho')) : '';
        $sortCol = ($this->input->post('iSortCol_0')) ? intval($this->input->post('iSortCol_0')) : '';
        $sortOrder = ($this->input->post('sSortDir_0')) ? $this->input->post('sSortDir_0') : 'ASC';
        $order_status = ($this->uri->segment('4')) ? $this->uri->segment('4') : '';
        $sortfields = array('1' => 'o.entity_id', '2' => 'restaurant.name', '4' => ' u.first_name', '5' => 'o.total_rate', '8' => 'driver.first_name', '9' => 'o.order_status', '10' => 'o.created_date', '11' => 'o.order_delivery', '12' => 'o.status');
        $sortFieldName = '';
        if (array_key_exists($sortCol, $sortfields)) {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->order_model->getGridList($sortFieldName, $sortOrder, $displayStart, $displayLength, $order_status);
        $totalRecords = $grid_data['total'];
        $records = array();
        $records["aaData"] = array();
        $nCount = ($displayStart != '') ? $displayStart + 1 : 1;
        // echo "<pre>";
        // print_r($grid_data);
        // exit();
        foreach ($grid_data['data'] as $key => $val) {
            $payable_ammount = $val->sub_total - $val->commission_value + $val->vat + $val->sd;
            if ($this->session->userdata('UserType') == 'Admin' || $this->session->userdata('UserType') == 'CentralAdmin') {
                $order_total = $val->rate - $val->delivery_charge;
            } else {
                $order_total = $val->rate;
            }
            // $payable_ammount = ($val->rate + $val->vat + $val->sd) - $val->commission_value;
            $currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id);
            $disabled = ($val->ostatus == 'delivered' || $val->ostatus == 'cancel' || $val->ostatus == 'not_delivered') ? 'disabled' : '';
            $assignDisabled = ($val->order_delivery != "Delivery" || $val->ostatus == 'delivered' || $val->ostatus == 'not_delivered' || $val->ostatus == 'cancel') ? 'disabled' : '';
            $trackDriver = (($val->first_name != '' || $val->last_name != '') && $val->order_delivery == "Delivery" && $val->ostatus == 'onGoing') ? '<a target="_blank" href="' . base_url() . ADMIN_URL . '/order/track_order/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)) . '" title="Click here to view driver live position" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-eye"></i> ' . $this->lang->line('track_driver') . '</a>' : '';
            $assignDisabledStatus = ($val->status != 1) ? 'disabled' : '';
            $ostatus = ($val->ostatus) ? "'" . $val->ostatus . "'" : '';
            //Reassign Driver
            $r = (($val->cancel || $val->accept) ? "Reassign Driver" : "Assign Driver");
            $restaurant = ($val->restaurant_detail) ? unserialize($val->restaurant_detail) : '';
            $accept = ($val->status != 1 && $val->restaurant_id && $val->ostatus != 'delivered' && $val->ostatus != 'cancel') ? '<button onclick="disableDetail(' . $val->entity_id . ',' . $val->restaurant_id . ',' . $val->entity_id . ',' . $ostatus . ')"
               title="' . $this->lang->line('accept') . '" class="delete btn btn-sm danger-btn margin-bottom"' . (($val->verify_order == 0) ? "disabled=disabled" : "") . '><i class="fa fa-check"></i> '
                . $this->lang->line('accept') . '</button>' : '';



            if (($val->ostatus == 'placed' || ($val->ostatus != 'delivered' && $val->ostatus != 'cancel' && $val->status != 1))) {
                $reject =
                    '<button onclick="rejectOrder(' . $val->user_id . ',' . $val->restaurant_id . ',' . $val->entity_id . ')"
             title="' . $this->lang->line('reject') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '
                    . $this->lang->line('reject') . '</button>';
            } else if (($val->ostatus == 'onGoing' || $val->ostatus == 'preparing')) {
                $reject =
                    '<button onclick="rejectOrder(' . $val->user_id . ',' . $val->restaurant_id . ',' . $val->entity_id . ',' . '1' . ')"
             title="' . "Cancel" . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '
                    . "Cancel" . '</button>';
            }

            if (($val->ostatus == 'placed' || $val->ostatus == "preorder" || $val->ostatus == 'preparing')) {
                $update_cart_button = '<button onclick="update_cart_item(' . $val->entity_id . ',' . $val->restaurant_id . ')"  title="' . "Click here to edit cart item" . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-edit"></i> ' . "Edit cart" . '</button>';
            } else {
                $update_cart_button = '';
            }

            if ($val->ostatus == "not_delivered" || $val->ostatus == "delivered" || $val->ostatus == "cancel") {
                $reject = '';
            }

            $invoice = "Rider's Invoice";
            $order_date =  ($val->preorder_mode == 1) ? date('d-m-Y g:i A', strtotime($val->preorder_date)) : date('d-m-Y g:i A', strtotime($val->created_date));
            $updateStatus = ($val->status == 1 || $val->status == 0) ? '<button onclick="updateStatus(' . $val->entity_id . ',' . $ostatus . ',' . $val->user_id . ')" ' . ' title="Click here for update status" class="update_status delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-edit"></i> ' . $this->lang->line('change_status') . '</button>' : '';
            $viewComment = ($val->extra_comment != '') ? '<button onclick="viewComment(' . $val->entity_id . ')" title="Click here to view comment" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-eye"></i> ' . $this->lang->line('view_comment') . '</button>' : '';
            $riderInvoice = ($val->accept == 1) ? '<button onclick="riderInvoice(' . $val->entity_id . ')"  title="Click here for update status" class="delete btn btn-sm btn-rider margin-bottom"><i class="fa fa-edit"></i> Rider\'s Invoice </button>' : '<button disabled class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-edit"></i>' . $invoice . ' </button>';

            if ($val->ostatus == "placed") {
                if ($val->status == 1) {
                    $ostatuslng = "Accepted";
                } else {
                    $ostatuslng = $this->lang->line('placed');
                }
            }
            if ($val->ostatus == "preorder") {

                // if($val->status == 1)
                // {
                //     $ostatuslng = $this->lang->line('placed');
                // }

                //else{
                $ostatuslng = "Pre-Order";
                // }

            }
            if ($val->ostatus == "delivered") {
                $ostatuslng = $this->lang->line('delivered');
            }
            if ($val->ostatus == "onGoing") {
                $ostatuslng = $this->lang->line('onGoing');
            }
            if ($val->ostatus == "cancel") {
                $ostatuslng = $this->lang->line('cancel');
            }
            if ($val->ostatus == "preparing") {
                $ostatuslng = "Preparing - Accepted by Rider";
            }
            if ($val->ostatus == "pending") {
                $ostatuslng = $this->lang->line('pending');
            }
            // if ($val->ostatus == "accepted_by_restaurant") {
            //     $ostatuslng = "Accepted By Restaurant";
            // }
            if ($val->order_delivery == "Delivery") {
                $order_delivery = $this->lang->line('delivery');
            }
            if ($val->order_delivery == "PickUp") {
                $order_delivery = $this->lang->line('pickup');
            }
            //Get Not Delivered Status
            $order_curr_status = $this->order_model->statusHistory($val->entity_id, 'latest_two');
            if ($order_curr_status[0]->order_status == 'cancel' && $order_curr_status[1]->order_status == 'onGoing' && $val->not_delivered == 0) {
                $ostatuslng = '<button onclick="not_delievered('  . $val->user_id . ',' . $val->restaurant_id . ',' . $val->entity_id . ')"
                 title="Click here to update status" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '
                    . "Not Delivered" . '</button>' . '<br>' . '<button onclick="cancel_order('  . $val->user_id . ',' . $val->restaurant_id . ',' . $val->entity_id . ',' . "1" . ')"
                 title="Click here to cancel the Order" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> '
                    . "Cancel" . '</button>';
            }
            if ($val->ostatus == "not_delivered") {
                $ostatuslng = "Not Delivered";
            }

            //End Of Delivered status Checking

            $records["aaData"][] = array(
                '<input type="checkbox" name="ids[]" value="' . $val->entity_id . '">',
                $val->entity_id,
                ($restaurant) ? $restaurant->name . " <br>" . $restaurant->phone_number . "<br>" . $restaurant->address : $val->name,
                ($val->area_name),
                ($val->fname || $val->lname) ? $val->fname . ' ' . $val->lname . "<br>" . $val->u_mobile_number . "<br>" . $val->user_address : 'Order by Restaurant',
                ($val->rate) ? $currency_symbol->currency_symbol . number_format_unchanged_precision($order_total, $currency_symbol->currency_code) : '',
                ($payable_ammount) ? $currency_symbol->currency_symbol . number_format_unchanged_precision($payable_ammount, $currency_symbol->currency_code) : '',
                $val->payment_option,
                ($val->first_name) ? $val->first_name . ' (' . $val->driver_mobile_number . ')' . ' ' . $val->last_name : 'No response from riders',
                $ostatuslng ? $ostatuslng : "",
                $order_date,
                $order_delivery,
                ($val->status)
                    ? $this->lang->line('active')
                    : $this->lang->line('inactive'),
                ' '
                    .
                    ($this->lpermission->method('order_verification', 'update')->access() && ($val->verify_order == 0 && $val->status == 0)
                        ? '<button onclick="verify_order(' . $val->entity_id . ')" title="Click here for verify this order" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-history"></i> ' . "Verify Order" . '</button>'
                        : '')
                    .
                    ($this->lpermission->method('orders', 'update')->access()
                        ? $accept . $reject
                        : '')
                    .
                    ($this->lpermission->method('orders', 'delete')->access() ?
                        '<button onclick="deleteDetail(' . $val->entity_id . ')"  title="' . $this->lang->line('click_delete') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('delete') . '</button>'
                        : '') .
                    ($this->lpermission->method('orders', 'read')->access()
                        ? ' <button onclick="getInvoice(' . $val->entity_id . ')"  title="' . $this->lang->line('download_invoice') . '" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> ' . $this->lang->line('invoice') . '</button>'
                        : '')
                    .
                    ($this->lpermission->method('update_cart', 'update')->access()
                        ? $update_cart_button
                        : '') .
                    ($this->lpermission->method('orders', 'update')->access() && !($this->session->userdata('UserType') == 'Admin')
                        ? (($this->lpermission->method('update_status_special', 'update')->access()
                            || ($val->ostatus != "delivered"
                                && $val->ostatus != "cancel"
                                && $val->ostatus != "not_delivered")
                        ) ? $updateStatus : "")
                        : '')
                    .
                    ($this->lpermission->method('orders', 'read')->access()
                        ? '<button onclick="statusHistory(' . $val->entity_id . ')" title="Click here for view status history" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-history"></i> ' . $this->lang->line('status_history') . '</button>'
                        : '')
                    .

                    ($this->lpermission->method('orders', 'update')->access() && !($this->session->userdata('UserType') == 'Admin')
                        ? $viewComment : '')
                    .
                    ($this->lpermission->method('assign_driver', 'update')->access() ?
                        '<button onclick="updateDriver(' . $val->entity_id . ')" ' . $assignDisabled . ' ' . $assignDisabledStatus .
                        ' title="Click here to assign driver" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-user"></i> '
                        . $r . '</button>'
                        : '')
                    .
                    ($this->lpermission->method('orders', 'update')->access() && !($this->session->userdata('UserType') == 'Admin') ?
                        $trackDriver
                        : '')
                    .
                    ($this->lpermission->method('riders_invoice', 'read')->access() ?
                        $riderInvoice :
                        '')

            );
            $nCount++;
        }
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }

    public function track_order()
    {
        $data['meta_title'] = $this->lang->line('track_order') . ' | ' . $this->lang->line('site_title');
        $order_id = ($this->uri->segment('4')) ? $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment('4'))) : '';
        if (!empty($order_id)) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($order_id);
            $data['order_id'] = $order_id;
            $this->load->view(ADMIN_URL . '/track_order', $data);
        }
    }
    // ajax track user's order
    public function ajax_track_order()
    {
        $data['meta_title'] = $this->lang->line('track_order') . ' | ' . $this->lang->line('site_title');
        $data['latestOrder'] = array();
        if (!empty($this->input->post('order_id'))) {
            $data['latestOrder'] = $this->order_model->getLatestOrder($this->input->post('order_id'));
        }
        $data['order_id'] = $this->input->post('order_id');
        $this->load->view(ADMIN_URL . '/ajax_track_order', $data);
    }
    // updating status to reject a order
    public function ajaxReject()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $order_id = ($this->input->post('order_id') != '') ? $this->input->post('order_id') : '';

        $not_delivered = $this->input->post('not_delivered', TRUE);
        if ($user_id && $restaurant_id && $order_id) {
            $this->db->set('order_status', 'cancel')->where('entity_id', $order_id)->update('order_master');

            if ($not_delivered == 1)
                $this->db->set('not_delivered', 0)->where('entity_id', $order_id)->update('order_master');
            $addData = array(
                'order_id' => $order_id,
                'order_status' => 'cancel',
                'time' => date('Y-m-d H:i:s'),
                'status_created_by' => 'Admin',
                'updated_by' => $this->session->userdata('UserID')


            );
            $this->order_model->addData('order_status', $addData);
            $this->order_model->updateEngage($order_id);
            $userdata = $this->order_model->getUserDate($user_id);
            $message = $this->lang->line('order_canceled');
            $device_id = $userdata->device_id;

            $this->sendFCMRegistration($device_id, $message, 'cancel', $restaurant_id, $order_id, '');
        }
    }
    public function map_driver()
    {

        $order_id = ($this->input->post('order_id') != '') ? $this->input->post('order_id') : 10;
        if ($order_id) {
            $result = $this->order_model->getZoneDrivers($order_id);
            return $result;
        }
    }
    // assign driver
    public function assignDriver()
    {

        if (!empty($this->input->post('order_entity_id')) && !empty($this->input->post('driver_id'))) {
            //  print_r($this->input->post('order_entity_id'));
            $distance = $this->order_model->getOrderDetails($this->input->post('order_entity_id'));
            //echo $distance[0]->distance;
            $comsn = 0;
            if ($distance[0]->distance > 3) {
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_more'))->first_row();
            } else {
                $this->db->select('OptionValue');
                $comsn = $this->db->get_where('system_option', array('OptionSlug' => 'driver_commission_less'))->first_row();
            }
            //Check Where User Already Exists
            $order_id = $this->input->post('order_entity_id');
            $isexists = $this->order_model->get_order_map($order_id);
            $order_detail = array(
                'driver_commission' => $comsn->OptionValue,
                'commission' => $comsn->OptionValue,
                'distance' => $distance[0]->distance,
                'driver_id' => $this->input->post('driver_id'),
                'order_id' => $this->input->post('order_entity_id'),
                'is_accept' => 1
            );
            // print_r($isexists[0]->driver_map_id);
            // $driver_map_id = $this->order_model->addData('order_driver_map', $order_detail);
            if ($isexists) {

                //printf($this->input->post('driver_id'));
                $driver_map_id = $this->order_model->update_order_driver($isexists[0]->driver_map_id, 'order_driver_map', $order_detail);
                $driver_map_id = $this->order_model->addData('order_driver_map', $order_detail);
                //  printf($driver_map_id);
            } else {
                $driver_map_id = $this->order_model->addData('order_driver_map', $order_detail);
                //printf("exist");
            }
            // $updateData = array(
            //     'engage' => 1,
            // );
            //Check Active Order of a Rider

            $updateData = array(
                'engage' => 1,
            );

            $this->order_model->updateData($updateData, 'users', 'entity_id', $this->input->post('driver_id'));
            if (!empty($driver_map_id)) {
                // after assigning a driver need to update the order status
                $order_status = "preparing";
                $this->db->set('order_status', $order_status)->where('entity_id', $this->input->post('order_entity_id'))->update('order_master');
                $addData = array(
                    'order_id' => $this->input->post('order_entity_id'),
                    'order_status' => $order_status,
                    'time' => date('Y-m-d H:i:s'),
                    'status_created_by' => 'Admin',
                    'updated_by' => $this->session->userdata('UserID')

                );
                $order_id = $this->order_model->addData('order_status', $addData);
                // adding notification for website
                $order_status = 'order_preparing';
                if ($order_status != '') {
                    $order_detail = $this->common_model->getSingleRow('order_master', 'entity_id', $this->input->post('order_entity_id'));
                    $notification = array(
                        'order_id' => $this->input->post('order_entity_id'),
                        'user_id' => $order_detail->user_id,
                        'notification_slug' => $order_status,
                        'view_status' => 0,
                        'datetime' => date("Y-m-d H:i:s"),
                    );
                    $this->common_model->addData('user_order_notification', $notification);
                }
                //notification to user
                $device = $this->order_model->getDevice($order_detail->user_id);
                $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                $message = $this->lang->line($order_status);
                $device_id = $device->device_id;
                $restaurant = $this->order_model->orderDetails($this->input->post('order_entity_id'));
                $this->sendFCMRegistration($device_id, $message, 'preparing', $restaurant[0]->restaurant_id, $this->input->post('order_entity_id'), $this->input->post('driver_id'));

                //notification to driver
                $device = $this->order_model->getDevice($this->input->post('driver_id'));
                //print_r($device);
                if ($device->device_id) {
                    //get langauge
                    $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    #prep the bundle
                    $fields = array();
                    $message = $this->lang->line('order_assigned');
                    $fields['to'] = $device->device_id; // only one user to send push notification
                    $fields['notification'] = array('body'  => $message, 'sound' => 'default');
                    $fields['data'] = array('screenType' => 'order');

                    $headers = array(
                        'Authorization: key=' . FCM_KEY,
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
                echo 'success';
            }
        }
    }
    // view comment
    public function viewComment()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id) {
            $comment = $this->order_model->getOrderComment($entity_id);
            echo $comment->extra_comment;
        }
    }
    // updating status and send request to driver
    public function ajaxdisable()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $order_id = ($this->input->post('order_id') != '') ? $this->input->post('order_id') : '';
        $order_status = ($this->input->post('order_status') != '') ? $this->input->post('order_status') : '';
        if ($entity_id != '' && $restaurant_id != '' && $order_id != '' && $order_status != '') {
            $order_curr_status = $this->order_model->statusHistory($order_id, 'latest');
            if ($order_curr_status[0]->order_status == 'accepted_by_restaurant') {
                echo 0;
            }

            $this->order_model->UpdatedStatus('order_master', $entity_id, $restaurant_id, $order_id, $order_status);
            // adding order status

            $addData = array(
                'order_id' => $order_id,
                'order_status' => 'accepted_by_restaurant',
                'time' => date('Y-m-d H:i:s'),
                'status_created_by' => 'Admin',
                'updated_by' => $this->session->userdata('UserID')

            );
            $status_id = $this->order_model->addData('order_status', $addData);
            // adding notification for website
            $order_detail = $this->common_model->getSingleRow('order_master', 'entity_id', $order_id);
            $notification = array(
                'order_id' => $order_id,
                'user_id' => $order_detail->user_id,
                'notification_slug' => 'order_accepted',
                'view_status' => 0,
                'datetime' => date("Y-m-d H:i:s"),
            );
            $this->common_model->addData('user_order_notification', $notification);
        }
    }
    // method for deleting
    public function ajaxDelete()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $this->order_model->ajaxDelete('order_master', $entity_id);
    }

    //get item of restro
    public function getItem()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $res_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        if ($res_id != '') {
            $entity_id = $res_id;
        }
        // $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        if ($entity_id) {
            $result =  $this->order_model->getItem($entity_id);
            $html = '<option value="">' . $this->lang->line('select') . '</option>';
            foreach ($result as $key => $value) {
                $html .= '<option value="' . $value->entity_id . '" data-id="' . $value->price . '" data-id-addons="' . $value->check_add_ons . '">' . $value->name . '</option>';
            }
        }
        if ($res_id) {
            return $result;
        } else {
            echo $html;
        }
    }

    //get address
    public function getAddress()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        echo $entity_id;
        if ($entity_id) {
            $result =  $this->order_model->getAddress($entity_id);
            if (!empty($result)) {
                $html = '<option value="">' . $this->lang->line('select') . '</option>';
                foreach ($result as $key => $value) {
                    $html .= '<option value="' . $value->entity_id . '">' . $value->address . ' , ' . $value->landmark . ' , '  . $value->city . ' , ' . $value->state . ' , ' . $value->country . ' ' . $value->zipcode . '</option>';
                }
            }
        }
        echo $html;
    }
    //pending
    public function pending()
    {
        $data['meta_title'] = $this->lang->line('title_admin_pending') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/pending_order', $data);
    }
    //preorder
    public function preorder()
    {
        $data['meta_title'] = $this->lang->line('title_admin_pending') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/preorder_order', $data);
    }
    //delivered
    public function delivered()
    {
        $data['meta_title'] = $this->lang->line('title_admin_delivered') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/delivered_order', $data);
    }
    //on going
    public function on_going()
    {
        $data['meta_title'] = $this->lang->line('title_admin_ongoing') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/ongoing_order', $data);
    }
    //cancel
    public function cancel()
    {
        $data['meta_title'] = $this->lang->line('title_admin_cancel') . ' | ' . $this->lang->line('site_title');
        $this->load->view(ADMIN_URL . '/cancel_order', $data);
    }
    public function update_item()
    {
        $entity_id = ($this->input->post('entity_id')) ? $this->input->post('entity_id') : '';
        $res_id = ($this->input->post('restaurant_id')) ? $this->input->post('restaurant_id') : '';
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
        $data['menu'] = $this->getItem($res_id);
        $data['order_details'] = $this->order_model->orderDetails($entity_id);
        $data['order_id'] = $this->input->post('entity_id');
        // echo "<pre>";
        // print_r($data);
        // exit();

        $this->load->view(ADMIN_URL . '/update_order', $data);
    }
    public function get_coupon()
    {
        $subtotal = $this->input->post('subtotal');
        $user_id = $this->input->post('user_id');
        $res_id = $this->input->post('restaurant_id');
        $order = 'placed';
        $get_coupon_data = $this->order_model->getcouponList($subtotal, $res_id, $order, $user_id);
        return json_encode($get_coupon_data);
    }
    public function get_res_item_data()
    {
        $menu_id = ($this->input->post('menu_id')) ? $this->input->post('menu_id') : '';
        $data['menu'] = $this->order_model->getMenuItem($menu_id);
    }
    public function update_cart_data()
    {

        $item_id = $this->input->post('item_id');
        $user_id = $this->input->post('user_id');
        $sub_total = $this->input->post('subtotal');
        $coupon_id = $this->input->post('coupon_id');
        $coupon_name = $this->input->post('coupon_name');
        $coupon_type = $this->input->post('coupon_type');
        $coupon_amount = $this->input->post('coupon_amount');
        $coupon_discount = $this->input->post('coupon_discount');
        $total = $this->input->post('total_rate');
        $restaurant_id = $this->input->post('restaurant_id');
        $items = array_values($item_id);
        $order_id = $items[0]['order_id'];
        $vat =  $this->input->post('vat');
        $sd =  $this->input->post('sd');
        foreach ($items as $key => $i) {
            $items[$key]['qty_no'] = $i['quantity'];
            unset($items[$key]['quantity']);
            $items[$key]['item_id'] = $i['menu_id'];
            unset($items[$key]['menu_id']);
            $items[$key]['item_name'] = $i['menu_name'];
            unset($items[$key]['menu_name']);

            if ($i['is_customize'] && $i['is_customize'] == 1) {


                if ($i['has_variation'] == 1 && !empty($i['variation_list'])) {
                    $temp_addons_category_list = array_values($i['variation_list'][0]['addons_category_list']);

                    if (!empty($i['variation_list'][0]['addons_category_list'])) {
                        $items[$key]['variation_list'][0]['addons_category_list'] = $temp_addons_category_list;
                    }
                    foreach ($items[$key]['variation_list'][0]['addons_category_list'] as $k => $value) {
                        $temp_addons_list = array_values($value['addons_list']);
                        $items[$key]['variation_list'][0]['addons_category_list'][$k]['addons_list'] = $temp_addons_list;
                    }
                } else {
                    $temp_addons_category_list = array_values($i['addons_category_list']);
                    $items[$key]['addons_category_list'] = $temp_addons_category_list;
                    foreach ($items[$key]['addons_category_list'] as $k => $value) {
                        $temp_addons_list = array_values($value['addons_list']);
                        $items[$key]['addons_category_list'][$k]['addons_list'] = $temp_addons_list;
                    }
                }
            }
        }


        //Add Previous Data to cart_update_history table
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($order_id);

        $order_detail = array(
            'order_id' => $order_id,
            'item_detail' => $data['menu_item']->item_detail,
            'prev_total'     =>  $data['menu_item']->total_rate ? $data['menu_item']->total_rate  : 0,
            'new_total'     =>  $total ? $total : 0,
            'date' => date('Y-m-d H:i:s'),
        );

        $this->order_model->add_update_cart_history('cart_update_history', $order_detail);
        // End of this section
        // Add Data To Order Master
        $res_data['restaurant_details'] = $this->order_model->getRestaurantDetail($restaurant_id);
        $comission_rate = $res_data['restaurant_details']->commission;
        $comission_value = ($sub_total * ($comission_rate / 100));
        $order_master_detail = array(
            'coupon_id' => $coupon_id ? $coupon_id : 0,
            'coupon_discount' => $coupon_discount ? $coupon_discount : 0,
            'coupon_name' => $coupon_name ? $coupon_name : null,
            'coupon_type' => $coupon_type ? $coupon_type : null,
            'coupon_amount' => $coupon_amount ? $coupon_amount : 0,
            'commission_rate' => $comission_rate ? $comission_rate : 0,
            'commission_value' => $comission_value ? $comission_value : 0,
            'subtotal' => $sub_total ? $sub_total : 0,
            'total_rate' => $total ? $total : 0,
            'vat' => $vat,
            'sd' => $sd,
        );
        $this->order_model->update_cart_data($order_master_detail, 'order_master', $order_id);
        $serialize_data = serialize($items);
        $order_detail = array(
            'item_detail' => $serialize_data
        );

        $result = $this->order_model->update_cart($serialize_data, 'order_detail', $order_id);

        if ($result) {
            $userdata = $this->order_model->getUserDate($user_id);
            $message = "Your cart have been updated. Please check the order page.";
            $device_id = $userdata->device_id;

            $this->sendFCMRegistration($device_id, $message, '', $restaurant_id, $order_id, '');
        }

        redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
    }
    //create invoice array_replace
    public function getInvoice()
    {
        $entity_id = ($this->input->post('entity_id')) ? $this->input->post('entity_id') : '';
        $data['order_records'] = $this->order_model->getEditDetail($entity_id);
        $data['menu_item'] = $this->order_model->getInvoiceMenuItem($entity_id);
        //   echo '<pre>';
        // print_r($data);
        // exit();
        $html = $this->load->view('backoffice/order_invoice', $data, true);
        if (!@is_dir('uploads/invoice')) {
            @mkdir('./uploads/invoice', 0777, TRUE);
        }
        $filepath = 'uploads/invoice/' . $entity_id . '.pdf';
        $this->load->library('M_pdf');
        $mpdf = new mPDF('', 'Letter');
        $mpdf->SetHTMLHeader('');
        $mpdf->SetHTMLFooter('<div style="padding:30px" class="endsign">Signature ____________________</div><div class="page-count" style="text-align:center;font-size:12px;">Page {PAGENO} out of {nb}</div><div class="pdf-footer-section" style="text-align:center;background-color: #000000;"><img src="http://restaura.evdpl.com/~restaura/assets/admin/img/logo.png" alt="" width="80" height="40"/></div>');
        $mpdf->AddPage(
            '', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            0, // margin_left
            0, // margin right
            10, // margin top
            23, // margin bottom
            0, // margin header
            0 //margin footer
        );
        $mpdf->autoScriptToLang = true;
        $mpdf->SetAutoFont();
        $mpdf->WriteHTML($html);
        $mpdf->output($filepath, 'F');
        echo $filepath;
    }
    //Change Status Not Delivered
    public function not_delievered()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $order_id = ($this->input->post('order_id') != '') ? $this->input->post('order_id') : '';
        if ($user_id && $restaurant_id && $order_id) {
            $addData = array(
                'order_id' => $order_id,
                'order_status' => 'not_delivered',
                'time' => date('Y-m-d H:i:s'),
                'status_created_by' => 'Admin'
            );
            $entity_id = $this->order_model->addData('order_status', $addData);
            $this->db->set('order_status', 'not_delivered')->where('entity_id', $order_id)->update('order_master');
            $this->db->set('not_delivered', 1)->where('entity_id', $order_id)->update('order_master');

            $userdata = $this->order_model->getUserDate($user_id);
            $message = $this->lang->line('order_not_deliverd');
            $device_id = $userdata->device_id;

            $this->sendFCMRegistration($device_id, $message, 'not_delivered', $restaurant_id, $order_id, '');
        }
    }
    //Change Status Not Delivered
    public function verify_order()
    {
        $order_id = ($this->input->post('order_id')) ? $this->input->post('order_id') : '';
        if ($order_id) {
            $user_id = $this->session->userdata("UserID");
            $this->db->set('verified_by', $user_id)->where('entity_id', $order_id)->update('order_master');
            $this->db->set('verify_order', 1)->where('entity_id', $order_id)->update('order_master');

            $this->api_model->updateDriver($order_id);
        }
    }
    public function order_cancelled()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $order_id = ($this->input->post('order_id') != '') ? $this->input->post('order_id') : '';
        if ($user_id && $restaurant_id && $order_id) {
            $this->db->set('not_delivered', 1)->where('entity_id', $order_id)->update('order_master');

            $userdata = $this->order_model->getUserDate($user_id);
            $message = $this->lang->line('order_user_cancled');
            $device_id = $userdata->device_id;

            $this->sendFCMRegistration($device_id, $message, 'cancel', $restaurant_id, $order_id, '');
        }
    }
    //add status
    public function updateOrderStatus()
    {
        $entity_id = ($this->input->post('entity_id')) ? $this->input->post('entity_id') : '';
        $order_status = ($this->input->post('order_status')) ? $this->input->post('order_status') : '';
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if ($entity_id && $order_status) {
            $this->db->set('order_status', $this->input->post('order_status'))->where('entity_id', $entity_id)->update('order_master');
            $addData = array(
                'order_id' => $entity_id,
                'order_status' => $this->input->post('order_status'),
                'time' => date('Y-m-d H:i:s'),
                'status_created_by' => 'Admin',
                'updated_by' => $this->session->userdata('UserID')
            );
            $order_id = $this->order_model->addData('order_status', $addData);
            $orderDetails = $this->order_model->orderDetails($entity_id);
            // adding notification for website
            $order_status = '';
            $driver_id = '';
            if ($this->input->post('order_status') == "complete") {
                $this->common_model->deleteData('user_order_notification', 'order_id', $entity_id);
            } else if ($this->input->post('order_status') == "preparing") {
                $order_status = 'order_preparing';
            } else if ($this->input->post('order_status') == "onGoing") {
                $order_status = 'order_ongoing';
            } else if ($this->input->post('order_status') == "delivered") {
                //Foodi CRM
                $reward_value = $this->systemoption_model->getRewardValue('Earn 1 Point For');
                $order_detail = $this->common_model->getSingleRow('order_master', 'entity_id', $entity_id);
                $total_rate = $order_detail->total_rate;
                $user_id = $order_detail->user_id;
                // echo "<pre>";
                // print_r($order_detail);
                // exit();
                $addData = array(
                    'points' => floor($total_rate / $reward_value),
                    'cost' => 1,
                    'date' => date('Y-m-d H:i:s'),
                    'order_id' => $entity_id,
                    'user_id' => $orderDetails[0]->user_id
                );
                $reward_point_id = $this->order_model->addData('reward_point', $addData);

                //Foodi Crm
                $order_status = 'order_delivered';
                //update rider's engage value
                $driver_id = $this->order_model->updateEngage($entity_id);
            } else if ($this->input->post('order_status') == "cancel") {
                $order_status = 'order_canceled';
            }
            if ($order_status != '') {
                $order_detail = $this->common_model->getSingleRow('order_master', 'entity_id', $entity_id);
                $notification = array(
                    'order_id' => $entity_id,
                    'user_id' => $order_detail->user_id,
                    'notification_slug' => $order_status,
                    'view_status' => 0,
                    'datetime' => date("Y-m-d H:i:s"),
                );
                $this->common_model->addData('user_order_notification', $notification);
            }

            $userdata = $this->order_model->getUserDate($user_id);
            //get langauge
            $device = $this->order_model->getDevice($user_id);
            $languages = $this->db->select('*')->get_where('languages', array('language_slug' => $device->language_slug))->first_row();
            $this->lang->load('messages_lang', $languages->language_directory);
            $message = $this->lang->line($order_status);
            $device_id = $userdata->device_id;
            $restaurant = $this->order_model->orderDetails($entity_id);
            $this->sendFCMRegistration($device_id, $message, $this->input->post('order_status'), $restaurant[0]->restaurant_id, $entity_id, $driver_id);
        }
    }
    // Send notification
    function sendFCMRegistration($registrationIds, $message, $order_status, $restaurant_id, $order_id, $driver_id)
    {
        if ($registrationIds) {
            #prep the bundle
            $fields = array();

            $fields['to'] = $registrationIds; // only one user to send push notification
            $fields['notification'] = array('body'  => $message, 'sound' => 'default');
            if ($order_status == "delivered") {
                $fields['data'] = array('screenType' => 'delivery', 'restaurant_id' => $restaurant_id, 'order_id' => $order_id, 'rider_id' => $driver_id);
            } else {
                $fields['data'] = array('screenType' => 'order');
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
    }
    public function deleteMultiOrder()
    {
        $orderId = ($this->input->post('arrayData')) ? $this->input->post('arrayDatas') : "";
        if ($orderId) {
            $order_id = explode(',', $orderId);
            $data = $this->order_model->deleteMultiOrder($order_id);
            echo json_encode($data);
        }
    }
    //get status history
    public function statusHistory()
    {
        $entity_id = ($this->input->post('order_id')) ? $this->input->post('order_id') : '';
        if ($entity_id) {
            $data['history'] = $this->order_model->statusHistory($entity_id);
            $this->load->view(ADMIN_URL . '/view_status_history', $data);
        }
    }
    //generate report
    public function generate_report()
    {
        $restaurant_id = $this->input->post('restaurant_id');
        $order_type = $this->input->post('order_delivery');
        $order_date = $this->input->post('order_date');
        $results = $this->order_model->generate_report($restaurant_id, $order_type, $order_date);
        if (!empty($results)) {
            // export as an excel sheet
            $this->load->library('excel');
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Reports');
            $headers = array("Restaurant", "User Name", "Order Total", "Order Delivery", "Order Date", "Order Status", "Status");

            for ($h = 0, $c = 'A'; $h < count($headers); $h++, $c++) {
                $this->excel->getActiveSheet()->setCellValue($c . '1', $headers[$h]);
                $this->excel->getActiveSheet()->getStyle($c . '1')->getFont()->setBold(true);
            }
            $row = 2;
            for ($r = 0; $r < count($results); $r++) {
                $status = ($results[$r]->status) ? 'Active' : 'Deactive';
                $this->excel->getActiveSheet()->setCellValue('A' . $row, $results[$r]->name);
                $this->excel->getActiveSheet()->setCellValue('B' . $row, $results[$r]->first_name . ' ' . $results[$r]->last_name);
                $this->excel->getActiveSheet()->setCellValue('C' . $row, number_format_unchanged_precision($results[$r]->total_rate, $results[$r]->currency_code));
                $this->excel->getActiveSheet()->setCellValue('D' . $row, $results[$r]->order_delivery);
                $this->excel->getActiveSheet()->setCellValue('E' . $row, $results[$r]->order_date);
                $this->excel->getActiveSheet()->setCellValue('F' . $row, ucfirst($results[$r]->order_status));
                $this->excel->getActiveSheet()->setCellValue('G' . $row, $status);
                $row++;
            }
            $filename = 'report-export.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');

            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
            exit;
        } else {
            $this->session->set_flashdata('not_found', $this->lang->line('not_found'));
            redirect(base_url() . ADMIN_URL . '/' . $this->controller_name . '/view');
        }
    }


    public function getDelivery()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $deliveryCharge = $this->order_model->deliveryCharge($entity_id);
        header('Content-Type: application/json');

        echo json_encode($deliveryCharge->price_charge);
    }


    public function getVatSd()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $data['vat_sd'] = $this->order_model->getVatSd($entity_id);
        //  echo "<pre>";
        // print_r($data);
        // exit();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function customAddOns()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';

        $data['result'] = $this->order_model->getAddOnsDetails($entity_id);
        $this->load->view(ADMIN_URL . '/add_order_addons', $data);
    }
    public function UpdateCartAddOns()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $data['result'] = $this->order_model->getMenuItem($entity_id);
        $data['sl_no'] = $this->input->post('sl_no');
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view(ADMIN_URL . '/update_order_addons', $data);
    }


    public function getCouponRestaurant()
    {
        $entity_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $res_id = ($this->input->post('res_id') != '') ? $this->input->post('res_id') : '';

        if (!empty($entity_id) && !empty($res_id)) {
            $result =  $this->order_model->getCouponRestaurant($entity_id, $res_id);
        }
        echo $result;
    }

    public function checkUserCountCoupon()
    {

        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $result = $this->order_model->checkUserCountCoupon($user_id);
        echo $result;
    }

    public function addOns()
    {
        $add_ons_details = $this->session->userdata('add_ons');

        $add_ons = ($this->input->post('add_ons_array') != '') ? $this->input->post('add_ons_array') : '';
        foreach ($add_ons as $key => $value) {
            array_push($add_ons_details, $value['add_ons_id']);
        }
        $subtotal = ($this->input->post('subTotal') != '') ? $this->input->post('subTotal') : '';
        $this->session->set_userdata('add_ons', $add_ons_details);
        // echo "<pre>";
        // print_r($add_ons_details);
        // exit();

        // $this->session->set_userdata('subtotal', $subtotal);

    }


    public function discountedItem()
    {
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';

        if ($restaurant_id) {
            $result =  $this->api_model->discountedItem($restaurant_id);
        }
        echo json_encode($result);
    }


    public function checkPreviousOrder()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $coupon_id = ($this->input->post('coupon_id') != '') ? $this->input->post('coupon_id') : '';
        $data['gradual'] = $this->order_model->checkPreviousOrder($user_id, $coupon_id);
        $data['sequence'] = $this->order_model->getSequence($coupon_id);
        $data['gradual_item'] = $this->order_model->gradualItem($coupon_id);
        $data['checkRecords'] = $this->order_model->checkRecords('gradual_coupon_track', $user_id, $coupon_id);
        echo json_encode($data);
    }

    public function checkCouponUser()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $coupon_id = ($this->input->post('coupon_id') != '') ? $this->input->post('coupon_id') : '';

        $result = $this->order_model->checkCouponUser($coupon_id, $user_id);

        echo json_encode($result);
    }

    public function getLatLongs()
    {
        $address_id = ($this->input->post('entity_id') != '') ? $this->input->post('entity_id') : '';
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $result = $this->order_model->getUserAddLatLong($address_id, $restaurant_id);
        echo json_encode($result);
    }


    public function getArea()
    {
        $restaurant_id = ($this->input->post('restaurant_id') != '') ? $this->input->post('restaurant_id') : '';
        $lat = ($this->input->post('lat') != '') ? $this->input->post('lat') : '';
        $long = ($this->input->post('long') != '') ? $this->input->post('long') : '';
        $result = $this->order_model->getRestaurantArea($lat, $long, $restaurant_id);
        echo json_encode($result);
    }

    public function checkMaximumUsage()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $coupon_id = ($this->input->post('coupon_id') != '') ? $this->input->post('coupon_id') : '';

        $result = $this->order_model->checkMaximumUsage($user_id, $coupon_id);
        echo json_encode($result);
    }

    public function checkUsability()
    {
        $user_id = ($this->input->post('user_id') != '') ? $this->input->post('user_id') : '';
        $coupon_id = ($this->input->post('coupon_id') != '') ? $this->input->post('coupon_id') : '';

        $result = $this->order_model->checkOneTimeUser($user_id, $coupon_id);
        echo json_encode($result);
    }


    public function checkExist()
    {
        $mobile_number = ($this->input->post('mobile_number') != '') ? $this->input->post('mobile_number') : '';

        if ($mobile_number != '') {
            $check = $this->order_model->checkExist($mobile_number);
            echo $check;
        }
    }

    public function riderInvoice()
    {
        $order_id = ($this->input->post('order_id')) ? $this->input->post('order_id') : '';
        if ($order_id) {
            $data['history'] = $this->order_model->order_master($order_id);
            $data['menu_item'] = $this->order_model->getInvoiceMenuItem($order_id);
            $data['vehicle_charge'] = $this->order_model->getRiderVehicleCharge($order_id);
            $data['currency'] = $this->order_model->getCurrency();
            // $type = $this->order_model->rider_type($order_id);
            // $data['commission'] = $this->order_model->getSystemOptoin($type->rider_types);
            $this->load->view(ADMIN_URL . '/riders_invoice', $data);
        }
    }

    public function itemCoupon()
    {

        $item_id = $this->input->post('item_id');
        $sub_total = $this->input->post('subtotal');
        $total = $this->input->post('total_rate');
        $user_id = $this->input->post('user_id');
        $zone_id = $this->input->post('zone_id');
        $coupon_name = $this->input->post('coupon_name');
        $user_number = $this->order_model->getUserNumber($user_id)->mobile_number;
        $restaurant_id = $this->input->post('restaurant_id');
        $items = array_values($item_id);

        $order_id = $items[0]['order_id'];
        foreach ($items as $key => $i) {
            if ($i['is_customize'] && $i['is_customize'] == 1) {

                if ($i['has_variation'] == 1 && !empty($i['variation_list'])) {
                    $temp_addons_category_list = array_values($i['variation_list'][0]['addons_category_list']);

                    if (!empty($i['variation_list'][0]['addons_category_list'])) {
                        $items[$key]['variation_list'][0]['addons_category_list'] = $temp_addons_category_list;
                    }
                    foreach ($items[$key]['variation_list'][0]['addons_category_list'] as $k => $value) {
                        $temp_addons_list = array_values($value['addons_list']);
                        $items[$key]['variation_list'][0]['addons_category_list'][$k]['addons_list'] = $temp_addons_list;
                    }
                } else {
                    $temp_addons_category_list = array_values($i['addons_category_list']);
                    $items[$key]['addons_category_list'] = $temp_addons_category_list;
                    foreach ($items[$key]['addons_category_list'] as $k => $value) {
                        $temp_addons_list = array_values($value['addons_list']);
                        $items[$key]['addons_category_list'][$k]['addons_list'] = $temp_addons_list;
                    }
                }
            }
        }

        $headers = array(
            'Content-Type: application/json'
        );

        $item_parent = array(
            'resId' => $restaurant_id,
            'zoneId' => $zone_id,
            'items' => $items,
            'coupon_name'   => '',
            'cart_id'   => 0
        );

        $fields = array(
            'language_slug'    => 'en',
            'user_id'    => $user_id,
            'token'    => $user_number,
            'restaurant_id'    => $restaurant_id,
            'zoneId'        => $zone_id,
            'items'     => $item_parent,
            "cart_id" => 0,
            "coupon" => $coupon_name,
            "order_delivery" => "Delivery",
            'latitude' => 0,
            'longitude' => 0,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base_url('v1/api/addToCart'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $res_data = json_decode($result);


        $discount = $res_data->coupon_discount ? $res_data->coupon_discount : 0;
        $subtotal = $res_data->subtotal ? $res_data->subtotal : 0;
        $vat = $res_data->vat ? $res_data->vat : 0;
        $sd = $res_data->sd ? $res_data->sd : 0;
        $total = $res_data->total ? $res_data->total : 0;
        $delivery_charge = $res_data->delivery_charge ? $res_data->delivery_charge : 0;
        $coupon_name = $res_data->coupon_name ? $res_data->coupon_name : null;
        $is_apply = $res_data->is_apply ? $res_data->is_apply : null;
        $coupon_id = $res_data->coupon_id ? $res_data->coupon_id : 0;
        $coupon_amount = $res_data->coupon_amount ? $res_data->coupon_amount : 0;

        $fields['items'] = (array) $res_data;

        $response = array(
            'discount'  => $discount,
            'subtotal'  => $subtotal,
            'vat'  => $vat,
            'sd'  => $sd,
            'total'  => $total,
            'delivery_charge'  => $delivery_charge,
            'coupon_name'  => $coupon_name,
            'is_apply'  => $is_apply,
            'coupon_id'  => $coupon_id,
            'coupon_amount'  => $coupon_amount,
            'res_data_sring'  => json_encode($fields)
        );

        $headers = array(
            'Content-Type: application/json'
        );

        $fields = array(
            'user_id'    => $user_id,
            'token'    => $user_number,
            'restaurant_id'    => $restaurant_id,
            "order_delivery" => "Delivery",
            'subtotal' => $subtotal,
            'is_admin' => 1,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base_url('v1/api/couponList'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $list_result = curl_exec($ch);
        curl_close($ch);

        $list_res_data = json_decode($list_result);


        $coupon_list_html = '';
        $coupon_list_html .= '<option value="">Select Coupon</option>';

        foreach ($list_res_data->coupon_list as $coupon) {
            $coupon_list_html .= '<option data-coupon-name="' . htmlspecialchars($coupon->name) . '" value="' . htmlspecialchars($coupon->coupon_id) . '">' . ($coupon->name) . '</option>';
        }


        $response['coupon_list'] = $coupon_list_html;


        echo json_encode($response);
    }

    public function couponApply()
    {

        $fields = $this->input->post('data_string');

        $parsedField = json_decode($fields);

        $parsedField->coupon = $this->input->post('coupon_name');

        $headers = array(
            'Content-Type: application/json'
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, base_url('v1/api/addToCart'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parsedField));
        $result = curl_exec($ch);
        curl_close($ch);

        $res_data = json_decode($result);
        // echo "<pre>";
        // print_r($res_data);
        // exit();
        $discount = $res_data->coupon_discount ? $res_data->coupon_discount : 0;
        $coupon_type = $res_data->coupon_type ? $res_data->coupon_type : '';
        $subtotal = $res_data->subtotal ? $res_data->subtotal : 0;
        $vat = $res_data->vat ? $res_data->vat : 0;
        $sd = $res_data->sd ? $res_data->sd : 0;
        $total = $res_data->total ? $res_data->total : 0;
        $delivery_charge = $res_data->delivery_charge ? $res_data->delivery_charge : 0;
        $coupon_name = $res_data->coupon_name ? $res_data->coupon_name : 0;
        $coupon_amount = $res_data->coupon_amount ? $res_data->coupon_amount : 0;
        $coupon_id = $res_data->coupon_id ? $res_data->coupon_id : 0;
        $is_apply = $res_data->is_apply ? $res_data->is_apply : null;


        $response = array(
            'discount'  => $discount,
            'coupon_type' => $coupon_type,
            'subtotal'  => $subtotal,
            'vat'  => $vat,
            'sd'  => $sd,
            'total'  => $total,
            'delivery_charge'  => $delivery_charge,
            'coupon_name'  => $coupon_name,
            'coupon_amount'  => $coupon_amount,
            'coupon_id'  => $coupon_id,
            'is_apply'  => $is_apply,
        );

        echo json_encode($response);
    }
}
