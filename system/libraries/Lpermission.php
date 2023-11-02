<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CI_Lpermission
{
    protected $permission;
    protected $module;
    protected $redirect = "backoffice/dashboard";
    protected $ci;

    public function __construct()
    {
        $this->module = '';
        $this->ci = &get_instance();
    }
    public function role_view()
    {
        $CI = &get_instance();
        $CI->load->model('Permission_model');
        $user_count = $CI->Permission_model->user_count();
        $user_list  = $CI->Permission_model->user_list();
        $data = array(
            'title'      => 'Role List',
            'user_count' => $user_count,
            'user_list'  => $user_list
        );
        //$page = $CI->parser->parse('permission/role_view_form', $data, true);
        return $data;
    }

    //Permission Related Function

    public function access()
    {
        return $this->permission;
    }
    public function redirect()
    {
        if ($this->permission) {
            return $this->permission;
        } else {
            $this->ci->session->set_flashdata('exception', "You do not have permission to access. Please contact with administrator.");
            redirect($this->redirect);
        }
    }
    public function method($module = null, $method = null)
    {
        $module = (($module != null) ? strtolower($module) : $this->ci->uri->segment(1));
        $method = strtolower($method);

        if ($this->checkMethod($module, $method)) {
            $this->permission = true;
        } else {
            $this->permission = false;
        }
        return $this;
    }

    public function create()
    {

        if ($this->checkMethod($this->module, 'create')) {
            $this->permission = true;
        } else {
            $this->permission = false;
        }
        return $this;
    }


    public function read()
    {
        if ($this->checkMethod($this->module, 'read')) {
            $this->permission = true;
        } else {
            $this->permission = false;
        }
        return $this;
    }

    public function update()
    {
        if ($this->checkMethod($this->module, 'update')) {
            $this->permission = true;
        } else {
            $this->permission = false;
        }
        return $this;
    }

    public function delete()
    {
        if ($this->checkMethod($this->module, 'delete')) {
            $this->permission = true;
        } else {
            $this->permission = false;
        }
        return $this;
    }

    protected function checkMethod($module = null, $method = null)
    {
        $permission = $this->ci->session->userdata('permission');
        $isLogIn = $this->ci->session->userdata('isLogIn');
        $UserType = $this->ci->session->userdata('UserType');
        if ($UserType == 'MasterAdmin') {
            //action of administrator
            return true;
        } else if ($isLogIn) {

            if (($permission != null)) {
                $permission = json_decode($permission, true);
                //module list
                $modules = array_keys($permission);
                //check current module permission
                if (
                    in_array($module, $modules)
                ) {

                    //convert method to asoc
                    $methodList = $permission[$module];
                    $methods = array_keys($permission[$module]);

                    //check for each input
                    if (in_array(strtolower($method), $methods)) {
                        if ($methodList[$method] == 1) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
