<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Permission extends CI_Controller
{

    public function add_role()
    {

        $CI = &get_instance();
        $CI->load->model('Permission_model');

        $account = $CI->Permission_model->permission_list();

        $data = array(
            'title'    => 'Create Role Name',
            'accounts' => $account,
        );
        // $account = $CI->parser->parse('backoffice/Permission/add_role', $data, true);
        // return $account;
        $this->load->view('backoffice/Permission/add_role', $data);
    }
    public function add_module()
    {
        $this->load->view('backoffice/Permission/add_module');
    }

    public function storerole()

    {
        // echo "Hello";
        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $data['title'] = "Add Role Permission";
        $data = array(
            'type' => $this->input->post('role_id', true),
        );
        $insert_id = $CI->Permission_model->insert_user_entry($data);

        $fk_module_id = $this->input->post('fk_module_id', true);
        $create = $this->input->post('create', true);
        $read = $this->input->post('read', true);
        $update = $this->input->post('update', true);
        $delete = $this->input->post('delete', true);

        // echo '<pre>';
        // print_r($fk_module_id);
        // print_r($create);
        // print_r($read);
        // exit();


        $new_array = array();
        for ($m = 0; $m < sizeof($fk_module_id); $m++) {
            for ($i = 0; $i < sizeof($fk_module_id[$m]); $i++) {
                for ($j = 0; $j < sizeof($fk_module_id[$m][$i]); $j++) {
                    $dataStore = array(
                        'role_id' => $insert_id,
                        'fk_module_id' => $fk_module_id[$m][$i][$j],
                        'create' => (!empty($create[$m][$i][$j]) ? $create[$m][$i][$j] : 0),
                        'read'   => (!empty($read[$m][$i][$j]) ? $read[$m][$i][$j] : 0),
                        'update' => (!empty($update[$m][$i][$j]) ? $update[$m][$i][$j] : 0),
                        'delete' => (!empty($delete[$m][$i][$j]) ? $delete[$m][$i][$j] : 0),
                    );
                    array_push($new_array, $dataStore);
                }
            }
        }
        /*-----------------------------------*/
        if ($this->Permission_model->create($new_array)) {
            $id = $this->db->insert_id();
            $this->session->set_flashdata('message', "Roles Permission added successfully");
        } else {
            $this->session->set_flashdata('exception', "Please try again");
        }
        redirect("backoffice/Permission/add_role");
    }


    public function store_module()

    {
        // echo "Hello";
        // exit();
        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $name = $this->input->post('module_name', true);
        $description = $this->input->post('module_description', true);

        $data = array(
            'name' => $name,
            'description' => $description,
            'status'    => 1

        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        /*-----------------------------------*/
        if ($this->Permission_model->store_module($data)) {
            $this->session->set_flashdata('message', "module added successfully");
        } else {
            $this->session->set_flashdata('exception', "Please try again");
        }
        redirect("backoffice/Permission/add_module");
    }
    public function role_list()
    {
        //    echo "Test";
        //    exit();
        // $content = $this->Lpermission->role_view();
        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $user_count = $CI->Permission_model->user_count();
        $user_list  = $CI->Permission_model->user_list();
        $data = array(
            'title'      => 'Role List',
            'user_count' => $user_count,
            'user_list'  => $user_list
        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view('backoffice/Permission/role_list', $data);
    }
    public function edit_role($id)
    {
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        // echo "test";
        $CI = &get_instance();
        $CI->load->model('Permission_model');

        $role = $CI->Permission_model->role($id);
        $module = $CI->Permission_model->module();
        $role_detail = $CI->Permission_model->role_edit($id);

        $data = array(
            'role'             => $role,
            'title'         => 'Edit Role',
            'module'         => $module,
            'role_detail'   => $role_detail
        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view('backoffice/Permission/editroleform', $data);
    }

    public function updaterole()
    {

        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $id = $this->input->post('rid', TRUE);

        $data = array(
            'type' => $this->input->post('role_id', TRUE),
            'id'   => $this->input->post('rid', TRUE),
        );

        $CI->Permission_model->role_update($data, $id);


        $fk_module_id = $this->input->post('fk_module_id', true);
        $create       = $this->input->post('create', true);
        $read         = $this->input->post('read', true);
        $update       = $this->input->post('update', true);
        $delete       = $this->input->post('delete', true);


        $new_array = array();
        for ($m = 0; $m < sizeof($fk_module_id); $m++) {
            for ($i = 0; $i < sizeof($fk_module_id[$m]); $i++) {
                for ($j = 0; $j < sizeof($fk_module_id[$m][$i]); $j++) {
                    $dataStore = array(
                        'role_id' => $this->input->post('rid', TRUE),
                        'fk_module_id' => $fk_module_id[$m][$i][$j],
                        'create' => (!empty($create[$m][$i][$j]) ? $create[$m][$i][$j] : 0),
                        'read'   => (!empty($read[$m][$i][$j]) ? $read[$m][$i][$j] : 0),
                        'update' => (!empty($update[$m][$i][$j]) ? $update[$m][$i][$j] : 0),
                        'delete' => (!empty($delete[$m][$i][$j]) ? $delete[$m][$i][$j] : 0),
                    );
                    array_push($new_array, $dataStore);
                }
            }
        }
        if ($this->Permission_model->create($new_array)) {
            $id = $this->db->insert_id();
            $this->session->set_flashdata('message', "role_permission_updated_successfully");
        } else {
            $this->session->set_flashdata('exception', 'please_try_again');
        }
        redirect("backoffice/Permission/role_list");
    }

    public function role_delete($id)
    {
        $this->load->model('Permission_model');
        $role = $this->Permission_model->delete_role($id);
        $role_per = $this->Permission_model->delete_role_permission($id);

        $data = array(
            'role'     => $role,
            'role_per' => $role_per
        );

        if ($data) {
            $this->session->set_userdata(array('message' => 'successfully_delete'));
        } else {
            $this->session->set_flashdata('exception', 'please_try_again');
        }
        redirect("backoffice/Permission/role_list");
    }
    public function add_sub_module()
    {

        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $module_list = $CI->Permission_model->module_list();

        $data = array(
            'title'     => 'Add Sub Module',
            'module_list' => $module_list,
        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view('backoffice/Permission/add_sub_module', $data);
    }

    public function store_submodule()

    {
        // echo "Hello";
        // exit();strtolower($module)
        $CI = &get_instance();
        $CI->load->model('Permission_model');

        $sub_module = $this->input->post('sub_name', true);
        $category = $sub_module;
        $sub_module = strtolower($sub_module);
        $sub_module_name = url_title($sub_module, '_');
        $module_id = $this->input->post('module_id', true);

        $data = array(
            'name' => $sub_module_name,
            'category' => $category,
            'mid' => $module_id,
            'status' => 1,
        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        /*-----------------------------------*/
        if ($this->Permission_model->store_sub_module($data)) {
            $this->session->set_flashdata('page_MSG', "Sub module added successfully");
        } else {
            $this->session->set_flashdata('page_MSG', "Something went wrong. Please try again");
        }
        redirect("backoffice/Permission/add_sub_module");
    }

    public function assign_rule()
    {

        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $roles_list = $CI->Permission_model->user_list();
        $users_list = getUserTypeList($this->session->userdata('language_slug'));

        $data = array(
            'title'     => 'Assign Role',
            'users_list' => $users_list,
            'roles_list' => $roles_list,
        );
        // echo "<pre>";
        // print_r($data);
        // exit();
        $this->load->view('backoffice/Permission/assign_rule', $data);
    }

    public function store_assignrole()
    {
        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $user_type = $this->input->post('user_id', true);
        $role_id = $this->input->post('role_id', true);

        $data = array(
            'user_type' => $user_type,
            'roleid' => $role_id,
            'createby' => $this->session->userdata('UserID'),
            'createdate' => date('Y-m-d H:i:s'),
        );

        if ($this->Permission_model->store_assign_role($data)) {
            $this->session->set_flashdata('page_MSG', "Role Assigned successfully");
        } else {
            $this->session->set_flashdata('page_MSG', "Please try again");
        }
        redirect("backoffice/Permission/assign_rule");
    }

    public function select_to_rol($uType)
    {
        $role_reult = $this->db->select('sec_role.*,role.*')
            ->from('role')
            ->join('sec_role', 'role.roleid=sec_role.id')
            ->where('role.user_type', $uType)
            ->group_by('sec_role.type')
            ->get()
            ->result();
        if ($role_reult) {
            $html = "";
            $html .= "<table class=\"table table-bordered table-striped table-hover\">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Role Name</th>
                            </tr>
                        </thead>
                       <tbody>";
            $i = 1;
            foreach ($role_reult as $key => $role) {
                $html .= "<tr>
                                <td>$i</td>
                                <td>$role->type</td>
                            </tr>";
                $i++;
            }
            $html .= "</tbody>
                    </table>";
        }
        echo json_encode($html);
    }
}
