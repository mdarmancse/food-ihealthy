<?php
class Permission_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function permission_list()
    {
        // echo "hello";
        // exit();
        $this->db->select('*');
        $this->db->from('module');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function insert_user_entry($data = array())
    {
        $this->db->insert('sec_role', $data);
        return $this->db->insert_id();
    }

    public function create($data = array())
    {
        $this->db->where('role_id', $data[0]['role_id'])->delete('role_permission');
        return $this->db->insert_batch('role_permission', $data);
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
        $page = $CI->parser->parse('permission/role_view_form', $data, true);
        return $page;
    }
    public function user_count()
    {
        $query = $this->db->query('select * from sec_role');
        return $query->num_rows();
    }

    public function user_list()
    {
        $this->db->select('*');
        $this->db->from('sec_role');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function role($id = null)
    {
        return  $data = $this->db->select('*')
            ->from('sec_role')
            ->where('id', $id)
            ->get()
            ->result();
    }
    public function role_edit($id = null)
    {
        return $roleAcc = $this->db->select('role_permission.*,sub_module.name')
            ->from('role_permission')
            ->join('sub_module', 'sub_module.id=role_permission.fk_module_id')
            ->where('role_permission.role_id', $id)
            ->get()
            ->result();
    }
    public function module()
    {
        return $modules = $this->db->select('*')->from('module')->get()->result();
    }

    public function role_update($data, $id)
    {

        $this->db->where('id', $id);
        $this->db->update('sec_role', $data);
        return true;
    }

    public function delete_role($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('sec_role');
        return true;
    }
    public function delete_role_permission($id)
    {
        $this->db->where('role_id', $id);
        $this->db->delete('role_permission');
        return true;
    }
    public function store_module($data = array())
    {
        return
            $this->db->insert('module', $data);
    }
    public function module_list()
    {
        $this->db->select('*');
        $this->db->from('module');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function store_sub_module($data = array())
    {
        return $this->db->insert('sub_module', $data);
    }

    public function userslist()
    {
        $this->db->select('user_type');
        $this->db->from('users');
        $this->db->where('user_type !=', 'User');
        $this->db->where('user_type !=', 'Driver');
        $this->db->group_by('user_type');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function store_assign_role($data = array())
    {
        $checkExist = $this->db->select('id')
            ->from('role')
            ->where('user_type', $data['user_type'])
            ->get()
            ->result_array();

        if (count($checkExist) > 0) {
            $this->db->where('id',  $checkExist[0]['id']);
            return $this->db->update('role', $data);
        } else {
            return $this->db->insert('role', $data);
        }
    }

    public function userPermissionadmin($role_id = null)
    {

        return $this->db->select("
            sub_module.name,
            role_permission.fk_module_id,
            role_permission.create,
            role_permission.read,
            role_permission.update,
            role_permission.delete
            ")
            ->from('role_permission')
            ->join('sub_module', 'sub_module.id = role_permission.fk_module_id', 'full')
            ->where('role_permission.role_id', $role_id)
            // ->where('sub_module.status', 1)
            ->group_start()
            ->where('create', 1)
            ->or_where('read', 1)
            ->or_where('update', 1)
            ->or_where('delete', 1)
            ->group_end()
            ->get()
            ->result();
        // return true;
    }
}
