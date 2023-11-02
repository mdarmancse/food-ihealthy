<?php
class Restaurant_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    // method for getting all
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if ($this->input->post('page_title') != '') {
            $this->db->like('name', $this->input->post('page_title'));
        }
        if ($this->input->post('status') != '') {
            $this->db->like('restaurant.status', $this->input->post('status'));
        }
        $this->db->group_by('content_id');
        // $this->db->where('restaurant.branch_entity_id', '');

        if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }

            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
            }
        }

        $result['total'] = $this->db->count_all_results('restaurant');

        if ($this->input->post('page_title') == "") {
            if ($this->input->post('status') != '') {
                $this->db->like('restaurant.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,restaurant.*');
            $this->db->join('restaurant', 'restaurant.content_id = content_general.content_general_id', 'left');
            $this->db->group_by('restaurant.content_id');

            if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {

                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                }
            }
            $this->db->where('content_type', 'restaurant');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if ($content_general_id) {
                $this->db->where_in('content_id', $content_general_id);
            }
        } else {
            if ($this->input->post('page_title') != '') {
                $this->db->like('name', $this->input->post('page_title'));
            }
            if ($this->input->post('status') != '') {
                $this->db->like('restaurant.status', $this->input->post('status'));
            }
            $this->db->select('content_general_id,restaurant.*');
            $this->db->join('content_general', 'restaurant.content_id = content_general.content_general_id', 'left');

            if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {
                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
                }

                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                }
            }

            $this->db->where('content_type', 'restaurant');
            $this->db->group_by('restaurant.content_id');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $cmsData = $this->db->get('restaurant')->result();
            $ContentID = array();
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID . ',' . $value->entity_id;
                $ContentID[] = $value->content_id;
            }
            if ($OrderByID && $ContentID) {
                $this->db->order_by('FIELD ( entity_id,' . trim($OrderByID, ',') . ') DESC');
                $this->db->where_in('content_id', $ContentID);
            } else {
                if ($this->input->post('page_title') != '') {
                    $this->db->like('name', trim($this->input->post('page_title')));
                }
                if ($this->input->post('status') != '') {
                    $this->db->like('restaurant.status', $this->input->post('status'));
                }
            }
        }
        // $this->db->where('restaurant.branch_entity_id', '');

        if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {
            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where('restaurant.created_by', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('restaurant.zonal_admin', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->where('restaurant.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('restaurant.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
            }
        }
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('restaurant')->result_array();
        $cmsLang = array();
        if (!empty($cmdData)) {
            foreach ($cmdData as $key => $value) {
                if (!array_key_exists($value['content_id'], $cmsLang)) {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id' => $value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'status' => $value['status'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'status' => $value['status']
                );
            }
        }
        $result['data'] = $cmsLang;
        return $result;
    }
    // method for adding
    public function addData($tblName, $Data)
    {
        $this->db->insert($tblName, $Data);
        return $this->db->insert_id();
    }

    public function getAdmins()
    {
        $this->db->select('entity_id,first_name,last_name');
        $this->db->where('user_type', 'Admin');
        return $this->db->get('users')->result();
    }

    public function getAllRestaurant($limit = null)
    {
        $this->db->select("name,entity_id");
        $this->db->from("restaurant");
        $this->db->order_by('sort_value');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }


    public function updateMenu($data = array(), $id)
    {
        $this->db->where('entity_id', $id);
        $this->db->update('restaurant', $data);
        return $this->db->affected_rows();
    }

    public function updateMenuSort($data = array(), $id)
    {

        $this->db->where('entity_id', $id);
        $this->db->update('restaurant', $data);

        // for branches
        $this->db->where('branch_entity_id', $id);
        $this->db->update('restaurant', $data);
        return $this->db->affected_rows();
    }


    // method to get details by id
    public function getEditDetail($tblname, $entity_id)
    {
        $this->db->select('res.*,res_add.address,res_add.landmark,res_add.zipcode,res_add.country,res_add.city,res_add.latitude,res_add.longitude');
        $this->db->join('restaurant_address as res_add', 'res.entity_id = res_add.resto_entity_id', 'left');
        $this->db->where('res.entity_id', $entity_id);
        return $this->db->get('' . $tblname . ' as res')->first_row();
    }
    // delete
    public function ajaxDelete($tblname, $content_id, $entity_id)
    {


        if ($tblname == 'restaurant_menu_item') {
            $menu_group_id = $this->db->select('menu_group_id')->from('restaurant_menu_item')
                ->where('entity_id', $entity_id)
                ->get()
                ->first_row()
                ->menu_group_id;

            if ($menu_group_id && !empty($menu_group_id)) {
                $this->db->delete('restaurant_menu_item', array('menu_group_id' => $menu_group_id));
            }
        }
        // check  if last record
        if ($content_id) {
            $vals = $this->db->get_where($tblname, array('content_id' => $content_id))->num_rows();
            if ($vals == 1) {
                $this->db->where(array('content_general_id' => $content_id));
                $this->db->delete('content_general');
            }
        }
        $this->db->where('entity_id', $entity_id);
        $this->db->delete($tblname);
    }
    // delete all records
    public function ajaxDeleteAll($tblname, $content_id)
    {
        if ($tblname == 'restaurant_menu_item') {
            $menu_group_id = $this->db->select('menu_group_id')->from('restaurant_menu_item')
                ->where('content_id', $content_id)
                ->get()
                ->first_row()
                ->menu_group_id;

            if ($menu_group_id && !empty($menu_group_id)) {
                $this->db->delete('restaurant_menu_item', array('menu_group_id' => $menu_group_id));
            }
        }
        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');

        $this->db->where('content_id', $content_id);
        $this->db->delete($tblname);
    }
    // update data common function
    public function updateData($Data, $tblName, $fieldName, $ID)
    {
        $this->db->where($fieldName, $ID);
        $this->db->update($tblName, $Data);
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatus($tblname, $entity_id, $status)
    {
        if ($status == 0) {
            $userData = array('status' => 0);
        } else {
            $userData = array('status' => 1);
        }
        $this->db->where('entity_id', $entity_id);
        $this->db->update($tblname, $userData);
        return $this->db->affected_rows();
    }
    // updating the changed status
    public function UpdatedStatusAll($tblname, $ContentID, $Status)
    {
        if ($Status == 0) {
            $Data = array('status' => 1);
        } else {
            $Data = array('status' => 0);
        }

        $this->db->where('content_id', $ContentID);
        $this->db->update($tblname, $Data);
        return $this->db->affected_rows();
    }
    //get list
    public function getListData($tblname, $language_slug = NULL, $not_a_branch = 0)
    {
        $this->db->select('name,entity_id');
        $this->db->where('status', 1);
        if ($tblname == 'category') {
            $this->db->where('isactive', 1);
        }
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('created_by', $this->session->userdata('UserID'));
        }
        if ($tblname == 'restaurant' && $this->session->userdata('UserType') == 'ZonalAdmin') {
            $this->db->where('zonal_admin', $this->session->userdata('UserID'));
        }
        if (!empty($language_slug)) {
            $this->db->where('language_slug', $language_slug);
        }
        if ($not_a_branch) {
            // $this->db->where('branch_entity_id', 0);
        }
        // return $this->db->get('category')->result();
        return $this->db->get($tblname)->result();
    }
    //menu grid
    public function getMenuGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        // print_r($this->session->userdata('restaurant'));exit;
        if ($this->input->post('page_title') != '') {
            $this->db->like('menu.name', $this->input->post('page_title'));
        }
        if ($this->input->post('restaurant') != '') {
            $this->db->like('res.name', $this->input->post('restaurant'));
        }
        if ($this->input->post('price') != '') {
            $this->db->like('menu.price', $this->input->post('price'));
        }
        $this->db->select('menu.name as mname');
        $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');

        if (!($this->lpermission->method('full_menu_view', 'read')->access())) {
            if ($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('restaurant'))) {
                $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
            } elseif ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where_in('res.zonal_admin', $this->session->userdata('UserID'));
            } elseif ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('res.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('res.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            } elseif ($this->session->userdata('UserType') != 'MasterAdmin') {
                $this->db->where('res.created_by', $this->session->userdata('UserID'));
            }
        }
        if (!($this->session->userdata('UserType') == 'Admin' || $this->session->userdata('UserType') == 'CentralAdmin'))
            $this->db->where('res.branch_entity_id', 0);

        $this->db->group_by('menu.content_id');
        $result['total'] = $this->db->count_all_results('restaurant_menu_item as menu');

        $cmdData = array();
        if ($result['total'] > 0) {

            if ($this->input->post('page_title') == "" && $this->input->post('restaurant') == '' && $this->input->post('price') == '') {
                $this->db->select('content_general_id,menu.content_id,res.name as rname,res.currency_id');
                $this->db->join('restaurant_menu_item as menu', 'menu.content_id = content_general.content_general_id', 'left');
                $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
                if (!($this->session->userdata('UserType') == 'Admin' || $this->session->userdata('UserType') == 'CentralAdmin'))
                    $this->db->where('res.branch_entity_id', 0);

                if (!($this->lpermission->method('full_menu_view', 'read')->access())) {

                    if ($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('restaurant'))) {
                        $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
                    } elseif ($this->session->userdata('UserType') == 'ZonalAdmin') {
                        $this->db->where_in('res.zonal_admin', $this->session->userdata('UserID'));
                    } elseif ($this->session->userdata('UserType') == 'CentralAdmin') {
                        $this->db->group_start();
                        $this->db->where('res.central_admin', $this->session->userdata('UserID'));
                        $this->db->or_where('res.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                        $this->db->group_end();
                    } elseif ($this->session->userdata('UserType') != 'MasterAdmin') {
                        $this->db->where('res.created_by', $this->session->userdata('UserID'));
                    }
                }

                $this->db->where('content_type', 'menu');
                $this->db->group_by('menu.content_id');
                if ($displayLength > 1)
                    $this->db->limit($displayLength, $displayStart);
                $dataCmsOnly = $this->db->get('content_general')->result();

                $content_general_id = array();
                foreach ($dataCmsOnly as $key => $value) {
                    $content_general_id[] = $value->content_general_id;
                }
                if ($content_general_id) {
                    $this->db->where_in('menu.content_id', $content_general_id);
                }
            } else {
                if ($this->input->post('page_title') != '') {
                    $this->db->like('menu.name', $this->input->post('page_title'));
                }
                if ($this->input->post('restaurant') != '') {
                    $this->db->like('res.name', $this->input->post('restaurant'));
                }
                if ($this->input->post('price') != '') {
                    $this->db->like('menu.price', $this->input->post('price'));
                }
                $this->db->select('content_general_id,menu.content_id,res.name as rname,res.currency_id');
                $this->db->join('restaurant_menu_item as menu', 'menu.content_id = content_general.content_general_id', 'left');
                $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
                if (!($this->session->userdata('UserType') == 'Admin' || $this->session->userdata('UserType') == 'CentralAdmin'))
                    $this->db->where('res.branch_entity_id', 0);

                if (!($this->lpermission->method('full_menu_view', 'read')->access())) {

                    if ($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('restaurant'))) {
                        $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
                    } elseif ($this->session->userdata('UserType') == 'ZonalAdmin') {
                        $this->db->where_in('res.zonal_admin', $this->session->userdata('UserID'));
                    } elseif ($this->session->userdata('UserType') == 'CentralAdmin') {
                        $this->db->group_start();
                        $this->db->where('res.central_admin', $this->session->userdata('UserID'));
                        $this->db->or_where('res.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                        $this->db->group_end();
                    } elseif ($this->session->userdata('UserType') != 'MasterAdmin') {
                        $this->db->where('res.created_by', $this->session->userdata('UserID'));
                    }
                }

                $this->db->where('content_type', 'menu');
                $this->db->group_by('menu.content_id');
                if ($displayLength > 1)
                    $this->db->limit($displayLength, $displayStart);

                $dataCmsOnly = $this->db->get('content_general')->result();
                $content_general_id = array();
                foreach ($dataCmsOnly as $key => $value) {
                    $content_general_id[] = $value->content_general_id;
                }
                if ($content_general_id) {
                    $this->db->where_in('menu.content_id', $content_general_id);
                }
                // if ($cmsData) {
                //     foreach ($cmsData as $key => $value) {
                //         $OrderByID = $OrderByID . ',' . $value->entity_id;
                //         $ContentID[] = $value->content_id;
                //     }
                // }
                // if ($OrderByID && $ContentID) {
                //     $this->db->order_by('FIELD ( menu.entity_id,' . trim($OrderByID, ',') . ') DESC');
                //     $this->db->where_in('menu.content_id', $ContentID);
                // } else {
                // if ($this->input->post('page_title') != '') {
                //     $this->db->like('menu.name', trim($this->input->post('page_title')));
                // }
                // if ($this->input->post('restaurant') != '') {
                //     $this->db->like('res.name', $this->input->post('restaurant'));
                // }
                // if ($this->input->post('price') != '') {
                //     $this->db->like('menu.price', $this->input->post('price'));
                // }
                // }
            }
            $this->db->select('content_general_id,menu.*,res.name as rname,res.currency_id, res.entity_id as res_id');
            $this->db->join('content_general', 'menu.content_id = content_general.content_general_id', 'left');
            $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
            if (!($this->session->userdata('UserType') == 'Admin' || $this->session->userdata('UserType') == 'CentralAdmin'))
                $this->db->where('res.branch_entity_id', 0);

            if (!($this->lpermission->method('full_menu_view', 'read')->access())) {
                if ($this->session->userdata('UserType') == 'Admin' && !empty($this->session->userdata('restaurant'))) {
                    $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
                } elseif ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where_in('res.zonal_admin', $this->session->userdata('UserID'));
                } elseif ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('res.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('res.branch_entity_id in (SELECT restu.entity_id FROM restaurant as restu WHERE restu.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                } elseif ($this->session->userdata('UserType') != 'MasterAdmin') {
                    $this->db->where('res.created_by', $this->session->userdata('UserID'));
                }
            }
            if ($sortFieldName != '')
                $this->db->order_by($sortFieldName, $sortOrder);

            // $this->db->limit($displayLength, $displayStart);


            $cmdData = $this->db->get('restaurant_menu_item as menu')->result_array();
        }
        $cmsLang = array();
        if (!empty($cmdData)) {
            foreach ($cmdData as $key => $value) {
                if (!array_key_exists($value['content_id'], $cmsLang)) {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id' => $value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'menu_group_id' => $value['menu_group_id'],
                        'rname' => $value['rname'],
                        'rid'   => $value['res_id'],
                        'price' => $value['price'],
                        'check_add_ons' => $value['check_add_ons'],
                        'currency_id' => $value['currency_id'],
                        'status' => $value['status']
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' => $value['rname'],
                    'price' => $value['price'],
                    'status' => $value['status'],
                );
            }
        }
        $result['data'] = $cmsLang;

        return $result;
    }
    //package grid
    public function getPackageGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if ($this->input->post('page_title') != '') {
            $this->db->like('package.name', $this->input->post('page_title'));
        }
        if ($this->input->post('restaurant') != '') {
            $this->db->like('res.name', $this->input->post('restaurant'));
        }
        if ($this->input->post('price') != '') {
            $this->db->like('package.price', $this->input->post('price'));
        }
        $this->db->select('package.name as mname,res.name as rname,package.entity_id,package.status,res.currency_id');
        $this->db->join('restaurant as res', 'package.restaurant_id = res.entity_id', 'left');
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where_in('package.restaurant_id', $this->session->userdata('restaurant'));
        }
        $this->db->group_by('package.content_id');
        $result['total'] = $this->db->count_all_results('restaurant_package as package');

        if ($this->input->post('page_title') == "" && $this->input->post('restaurant') == '' && $this->input->post('price') == '') {
            $this->db->select('content_general_id,package.*,res.name as rname,res.currency_id');
            $this->db->join('restaurant_package as package', 'package.content_id = content_general.content_general_id', 'left');
            $this->db->join('restaurant as res', 'package.restaurant_id = res.entity_id', 'left');
            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where_in('res.created_by', $this->session->userdata('restaurant'));
            }
            $this->db->where('content_type', 'package');
            $this->db->group_by('package.content_id');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if ($content_general_id) {
                $this->db->where_in('package.content_id', $content_general_id);
            }
        } else {
            if ($this->input->post('page_title') != '') {
                $this->db->like('package.name', $this->input->post('page_title'));
            }
            if ($this->input->post('restaurant') != '') {
                $this->db->like('res.name', $this->input->post('restaurant'));
            }
            if ($this->input->post('price') != '') {
                $this->db->like('package.price', $this->input->post('price'));
            }
            $this->db->select('content_general_id,package.*,res.name as rname,res.currency_id');
            $this->db->join('restaurant_package as package', 'content_general.content_general_id = package.content_id', 'left');
            $this->db->join('restaurant as res', 'package.restaurant_id = res.entity_id', 'left');
            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where_in('package.restaurant_id', $this->session->userdata('restaurant'));
            }
            $this->db->group_by('package.content_id');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $cmsData = $this->db->get('content_general')->result();
            $ContentID = array();
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID . ',' . $value->entity_id;
                $ContentID[] = $value->content_id;
            }
            if ($OrderByID && $ContentID) {
                $this->db->order_by('FIELD ( package.entity_id,' . trim($OrderByID, ',') . ') DESC');
                $this->db->where_in('package.content_id', $ContentID);
            } else {
                if ($this->input->post('page_title') != '') {
                    $this->db->like('package.name', trim($this->input->post('page_title')));
                }
                if ($this->input->post('restaurant') != '') {
                    $this->db->like('res.name', $this->input->post('restaurant'));
                }
                if ($this->input->post('price') != '') {
                    $this->db->like('package.price', $this->input->post('price'));
                }
            }
        }
        $this->db->select('content_general_id,package.*,res.name as rname,res.currency_id');
        $this->db->join('content_general', 'package.content_id = content_general.content_general_id', 'left');
        $this->db->join('restaurant as res', 'package.restaurant_id = res.entity_id', 'left');
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where_in('package.restaurant_id', $this->session->userdata('restaurant'));
        }
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('restaurant_package as package')->result_array();
        $cmsLang = array();
        if (!empty($cmdData)) {
            foreach ($cmdData as $key => $value) {
                if (!array_key_exists($value['content_id'], $cmsLang)) {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id' => $value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' => $value['rname'],
                        'price' => $value['price'],
                        'check_add_ons' => $value['check_add_ons'],
                        'created_by' => $value['created_by'],
                        'currency_id' => $value['currency_id'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' => $value['rname'],
                );
            }
        }
        $result['data'] = $cmsLang;
        return $result;
    }
    public function checkExist($phone_number, $entity_id, $content_id)
    {
        $this->db->where('phone_number', $phone_number);
        $this->db->where('entity_id !=', $entity_id);
        $this->db->where('content_id !=', $content_id);
        return $this->db->get('restaurant')->num_rows();
    }
    public function checkEmailExist($email, $entity_id, $content_id)
    {
        $this->db->where('email', $email);
        $this->db->where('entity_id !=', $entity_id);
        $this->db->where('content_id !=', $content_id);
        return $this->db->get('restaurant')->num_rows();
    }
    //insert batch
    public function inserBatch($tblname, $data)
    {
        $this->db->insert_batch($tblname, $data);
        return $this->db->insert_id();
    }
    //get add ons detail
    public function getAddonsDetail($tblname, $menu_id)
    {
        $this->db->where('menu_id', $menu_id);
        $result = $this->db->get($tblname)->result();
        $addons = array();
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                if (!isset($addons[$value->category_id])) {
                    $addons[$value->category_id] = array();
                }
                if (isset($addons[$value->category_id])) {

                    $addons[$value->category_id]['category_id'] = $value->category_id;
                    $addons[$value->category_id]['is_multiple'] = $value->is_multiple;
                    $addons[$value->category_id]['max_choice'] = $value->max_choice;
                }

                if (!isset($addons[$value->category_id]['final_addon'])) {
                    $addons[$value->category_id]['final_addon'] = array();
                }

                array_push($addons[$value->category_id]['final_addon'], array(
                    $value->add_ons_name,
                    $value->add_ons_price
                ));
            }
        }
        return $addons;
    }
    //variation details
    public function getVariationDetail($menu_id)
    {
        $this->db->where('add_ons_master.menu_id', $menu_id)
            ->join('variations', 'variations.entity_id = add_ons_master.variation_id', 'left')
            ->join('add_ons_category', 'add_ons_category.entity_id = add_ons_master.category_id', 'left');
        $result = $this->db->get('add_ons_master')
            ->result();


        $variations = array();

        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $addons = array();
                if (!isset($variations[$value->variation_id])) {
                    $variations[$value->variation_id] = array();
                }
                if (isset($variations[$value->variation_id])) {
                    $variations[$value->variation_id]['variation_id'] = $value->variation_id;
                    $variations[$value->variation_id]['variation_name'] = $value->variation_name;
                    $variations[$value->variation_id]['variation_price'] = $value->variation_price;
                    $variations[$value->variation_id]['variation_add_on'] = $value->variation_add_on;

                    if ($variations[$value->variation_id]['variation_add_on'] == 1) {
                        if (!isset($variations[$value->variation_id]['addon_list'])) {
                            $variations[$value->variation_id]['addon_list'] = array();
                        }


                        if (!isset($variations[$value->variation_id]['addon_list'][$value->category_id])) {
                            $variations[$value->variation_id]['addon_list'][$value->category_id]['category_name'] = $value->name;
                            $variations[$value->variation_id]['addon_list'][$value->category_id]['category_id'] = $value->category_id;
                            $variations[$value->variation_id]['addon_list'][$value->category_id]['is_multiple'] = $value->is_multiple;
                            $variations[$value->variation_id]['addon_list'][$value->category_id]['max_choice'] = $value->max_choice;
                        }

                        if (!isset($variations[$value->variation_id]['addon_list'][$value->category_id]['final_addon'])) {
                            $variations[$value->variation_id]['addon_list'][$value->category_id]['final_addon'] = array();
                        }
                        array_push($variations[$value->variation_id]['addon_list'][$value->category_id]['final_addon'], array(

                            $value->add_ons_name,
                            $value->add_ons_price
                        ));
                    }
                }
            }
        }
        return $variations;
    }

    public function chechHasVariation($menu_id)
    {
        $query = $this->db->select('has_variation')
            ->from('add_ons_master')
            ->where('menu_id', $menu_id)
            ->get()
            ->row();

        return $query->has_variation;
    }
    //delete insert data
    public function deleteinsertBatch($tblname, $data, $menu_id)
    {

        $this->db->where('menu_id', $menu_id);
        $this->db->delete($tblname);
        if (!empty($data)) {
            $this->db->insert_batch($tblname, $data);
            // echo $this->db->insert_id();
        }
    }

    public function deleteData($tblname, $fieldName, $menu_id)
    {
        $this->db->where($fieldName, $menu_id);
        $this->db->delete($tblname);
    }
    //deal grid
    public function getDealGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if ($this->input->post('page_title') != '') {
            $this->db->like('menu.name', $this->input->post('page_title'));
        }
        if ($this->input->post('restaurant') != '') {
            $this->db->like('res.name', $this->input->post('restaurant'));
        }
        if ($this->input->post('price') != '') {
            $this->db->like('menu.price', $this->input->post('price'));
        }
        $this->db->select('menu.name as mname,res.name as rname,menu.entity_id,menu.status,res.currency_id');
        $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
        }
        $this->db->where('is_deal', 1);
        $this->db->group_by('menu.content_id');
        $result['total'] = $this->db->count_all_results('restaurant_menu_item as menu');

        if ($this->input->post('page_title') == "" && $this->input->post('restaurant') == '' && $this->input->post('price') == '') {
            $this->db->select('content_general_id,menu.*,res.name as rname,res.currency_id');
            $this->db->join('restaurant_menu_item as menu', 'menu.content_id = content_general.content_general_id', 'left');
            $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
            }
            $this->db->where('content_type', 'menu');
            $this->db->where('is_deal', 1);
            $this->db->group_by('menu.content_id');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();

            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if ($content_general_id) {
                $this->db->where_in('menu.content_id', $content_general_id);
            }
        } else {
            if ($this->input->post('page_title') != '') {
                $this->db->like('menu.name', $this->input->post('page_title'));
            }
            if ($this->input->post('restaurant') != '') {
                $this->db->like('res.name', $this->input->post('restaurant'));
            }
            if ($this->input->post('price') != '') {
                $this->db->like('menu.price', $this->input->post('price'));
            }
            $this->db->select('content_general_id,menu.*,res.name as rname,res.currency_id');
            $this->db->join('restaurant_menu_item as menu', 'menu.content_id = content_general.content_general_id', 'left');
            $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
            }
            $this->db->where('content_type', 'menu');
            $this->db->where('is_deal', 1);
            $this->db->group_by('menu.content_id');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();
            $ContentID = array();
            foreach ($cmsData as $key => $value) {
                $OrderByID = $OrderByID . ',' . $value->entity_id;
                $ContentID[] = $value->content_id;
            }
            if ($OrderByID && $ContentID) {
                $this->db->order_by('FIELD ( menu.entity_id,' . trim($OrderByID, ',') . ') DESC');
                $this->db->where_in('menu.content_id', $ContentID);
            } else {
                if ($this->input->post('page_title') != '') {
                    $this->db->like('menu.name', trim($this->input->post('page_title')));
                }
                if ($this->input->post('restaurant') != '') {
                    $this->db->like('res.name', $this->input->post('restaurant'));
                }
                if ($this->input->post('price') != '') {
                    $this->db->like('menu.price', $this->input->post('price'));
                }
            }
        }
        $this->db->select('content_general_id,menu.*,res.name as rname,res.currency_id');
        $this->db->join('content_general', 'menu.content_id = content_general.content_general_id', 'left');
        $this->db->join('restaurant as res', 'menu.restaurant_id = res.entity_id', 'left');
        $this->db->where('is_deal', 1);
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where_in('menu.restaurant_id', $this->session->userdata('restaurant'));
        }
        if ($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        $cmdData = $this->db->get('restaurant_menu_item as menu')->result_array();
        $cmsLang = array();
        if (!empty($cmdData)) {
            foreach ($cmdData as $key => $value) {
                if (!array_key_exists($value['content_id'], $cmsLang)) {
                    $cmsLang[$value['content_id']] = array(
                        'entity_id' => $value['entity_id'],
                        'content_id' => $value['content_id'],
                        'name' => $value['name'],
                        'rname' => $value['rname'],
                        'price' => $value['price'],
                        'check_add_ons' => $value['check_add_ons'],
                        'created_by' => $value['created_by'],
                        'currency_id' => $value['currency_id'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'rname' => $value['rname'],
                    'price' => $value['price'],
                );
            }
        }
        $result['data'] = $cmsLang;
        return $result;
    }
    //gewt deals details
    // public function getDealDetail($menu_id){
    //     $this->db->select('deal_category.deal_category_name,add_ons_master.*');
    //     $this->db->join('deal_category','add_ons_master.deal_category_id = deal_category.deal_category_id','left');
    //     $this->db->where('menu_id',$menu_id);
    //     $result = $this->db->get('add_ons_master')->result();
    //     $items = array();
    //     if(!empty($result)){
    //         foreach ($result as $key => $value) {
    //             if(!isset($items[$value->deal_category_id])){
    //                 $items[$value->deal_category_id] = array();
    //             }
    //             array_push($items[$value->deal_category_id], $value);
    //         }
    //     }
    //     return $items;
    // }

    //delete category
    // public function deleteDealCategory($category_id){
    //     $this->db->where('deal_category_id',$category_id);
    //     $this->db->delete('deal_category');
    // }

    //get deal category
    // public function dealCategory($tblname,$language_slug)
    // {
    //     $this->db->where('language_slug',$language_slug);
    //     $this->db->where('deal_category',1);
    //     if($this->session->userdata('UserType') == 'Admin'){
    //         $this->db->where('created_by',$this->session->userdata('UserID'));
    //     }
    //     return $this->db->get($tblname)->result();
    // }
    // get restaurant slug
    public function getRestaurantSlug($content_id)
    {
        $this->db->select('restaurant_slug');
        $this->db->where('content_id', $content_id);
        return $this->db->get('restaurant')->first_row();
    }
    // get item slug
    public function getItemSlug($content_id)
    {
        $this->db->select('item_slug');
        $this->db->where('content_id', $content_id);
        return $this->db->get('restaurant_menu_item')->first_row();
    }
    // get restaurants name
    public function getRestaurantName($entity_id)
    {
        $this->db->select('name');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get('restaurant')->first_row();
    }
    // get content id
    public function getContentId($entity_id, $tblname)
    {
        $this->db->select('content_id');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get($tblname)->first_row();
    }
    // get category id
    public function getCategoryId($name, $lang_slug)
    {
        $this->db->select('entity_id');
        $this->db->where('name', $name);
        $this->db->where('language_slug', $lang_slug);
        return $this->db->get('category')->first_row();
    }
    // get addons for language
    public function getAddons($lang_slug)
    {
        $this->db->select('name');
        $this->db->where('language_slug', $lang_slug);
        $addons = $this->db->get('add_ons_category')->result_array();
        return array_column($addons, 'name');
    }
    // check addons category exist or not
    public function getAddonsId($name, $lang_slug)
    {
        $this->db->select('entity_id');
        $this->db->where('name', $name);
        $this->db->where('language_slug', $lang_slug);
        return $this->db->get('add_ons_category')->first_row();
    }
    // check addons category exist or not
    public function getRestaurantId($name, $lang_slug)
    {
        $this->db->select('entity_id');
        $this->db->where('name', $name);
        $this->db->where('language_slug', $lang_slug);
        return $this->db->get('restaurant')->first_row();
    }

    public function getRecord($tblName, $search)
    {
        return  $this->db->get_where($tblName, $search)->result();
    }

    public function getMenuAssignedBranches($menu_id)
    {
        $menu_group_id = $this->db->select('menu_group_id')
            ->from('restaurant_menu_item')
            ->where('entity_id', $menu_id)
            ->get()
            ->result_array()[0]['menu_group_id'];


        $query = $this->db->select('b.name')
            ->from('restaurant_menu_item a')
            ->join('restaurant b', 'b.entity_id = a.restaurant_id')
            ->where('a.menu_group_id', $menu_group_id)
            ->where('a.status', 1)
            ->where('b.branch_entity_id !=', 0)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function checkHasBranch($restaurant_id)
    {
        $this->db->select('entity_id, name')
            ->from('restaurant')
            ->where('branch_entity_id', $restaurant_id);

        $query = $this->db->get();

        // echo '<pre>';
        // print_r($query->result());
        // exit();
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }


    public function getSimpleMenuDetails($menu_group_id, $menu_id, $restaurant_id)
    {
        $this->db->select('a.entity_id, a.status as menu_status')
            ->from('restaurant_menu_item a')
            ->join('restaurant b', 'b.entity_id = a.restaurant_id', 'left')
            ->where('a.menu_group_id', $menu_group_id)
            ->where('b.entity_id', $restaurant_id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function getMenuGroup($menu_id)
    {
        $query = $this->db->select('menu_group_id, content_id')
            ->from('restaurant_menu_item')
            ->where('entity_id', $menu_id)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function getBranchMenu_entity_id($menu_group_id, $branch_id)
    {
        $query =  $this->db->select('entity_id, content_id')
            ->from('restaurant_menu_item')
            ->where('menu_group_id', $menu_group_id)
            ->where('restaurant_id', $branch_id)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function getRestaurantAllMenu($restaurant_id)
    {
        $query =  $this->db->select('*')
            ->from('restaurant_menu_item')
            ->where('restaurant_id', $restaurant_id)
            ->get();

        return $query->result_array();
    }

    public function getRestaurantImages($restaurant_id)
    {
        $query =  $this->db->select('phone_number, email, image, cover_image')
            ->from('restaurant')
            ->where('entity_id', $restaurant_id)
            ->get();

        return $query->row();
    }

    public function getTableData($table_name, $column, $filter)
    {
        return $this->db->select('*')
            ->from($table_name)
            ->where($column, $filter)
            ->get()
            ->result_array();
    }

    public function getAddonCatAllAddons($addon_cat_id)
    {
        $result = $this->db->select('add_ons_category.*, add_ons_category.entity_id as cat_entity_id, preset_addons.*, preset_addons.entity_id as addon_id, preset_addons.status as addon_status')
            ->from('add_ons_category')
            ->where('add_ons_category.entity_id', $addon_cat_id)
            ->join('preset_addons', 'preset_addons.addon_category_id = add_ons_category.entity_id')
            ->get()
            ->result();

        $addons = array();

        if (!empty($result)) {
            foreach ($result as $key => $value) {

                $addons['is_multiple'] = $value->cat_is_multiple;
                $addons['max_choice'] = $value->cat_max_choice;


                if (!isset($addons['addons'])) {
                    $addons['addons'] = array();
                }

                array_push($addons['addons'], array(
                    $value->addon_name,
                    $value->addon_price
                ));
            }
        }


        return $addons;
    }

    public function getBranchMenuID($branch_id, $menu_group_id)
    {
        $res = $this->db->select('entity_id')
            ->from('restaurant_menu_item')
            ->where('restaurant_id', $branch_id)
            ->where('menu_group_id', $menu_group_id)
            ->get()
            ->result();

        return $res[0]->entity_id;
    }

    public function getRestaurantData($res_info)
    {
        $data = $this->db->select('entity_id, name')
            ->from('restaurant')
            ->where('status', 1)
            ->group_start()
            ->like('name', $res_info, 'both')
            ->or_like('phone_number', $res_info, 'both')
            ->group_end()
            ->limit(15)
            ->get()
            ->result_array();

        return $data;
    }

    public function getCategoriesData($cat_info)
    {
        $data = $this->db->select('entity_id, name')
            ->from('category')
            ->where('status', 1)
            ->where('isactive', 1)
            ->group_start()
            ->like('name', $cat_info, 'both')
            ->group_end()
            ->limit(15)
            ->get()
            ->result_array();

        return $data;
    }

    public function getMenuResName($res_id)
    {
        return $this->db->select('name')
            ->from('restaurant')
            ->where('entity_id', $res_id)
            ->get()
            ->first_row();
    }

    public function getMenuCatName($cat_id)
    {
        return $this->db->select('name')
            ->from('category')
            ->where('entity_id', $cat_id)
            ->get()
            ->first_row();
    }
}
