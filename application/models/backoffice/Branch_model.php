<?php
class Branch_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    // method for getting all
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 10)
    {
        if ($this->input->post('page_title') != '') {
            $this->db->like('restaurant.name', $this->input->post('page_title'));
        }
        if ($this->input->post('restaurant') != '') {
            $this->db->like('resta.name', $this->input->post('restaurant'));
        }
        $this->db->select('restaurant.name,resta.name as rname');
        $this->db->join('restaurant as resta', 'restaurant.branch_entity_id = resta.entity_id', 'left');
        $this->db->where('restaurant.branch_entity_id !=', '');

        if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {

            if ($this->session->userdata('UserType') == 'Admin') {
                $this->db->where_in('resta.entity_id', $this->session->userdata('restaurant'));
            }
            if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                $this->db->where('resta.zonal_admin', $this->session->userdata('UserID'));
            }
            if ($this->session->userdata('UserType') == 'CentralAdmin') {
                $this->db->group_start();
                $this->db->where('resta.central_admin', $this->session->userdata('UserID'));
                $this->db->or_where('resta.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                $this->db->group_end();
            }
        }
        $this->db->group_by('restaurant.content_id');
        $result['total'] = $this->db->count_all_results('restaurant');
        if ($this->input->post('page_title') == "" && $this->input->post('restaurant') == '') {
            $this->db->select('content_general.content_general_id,restaurant.name,resta.name as rname');
            $this->db->join('restaurant', 'content_general.content_general_id = restaurant.content_id', 'left');
            $this->db->join('restaurant as resta', 'restaurant.branch_entity_id = resta.entity_id', 'left');

            if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('resta.zonal_admin', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where_in('resta.entity_id', $this->session->userdata('restaurant'));
                }
                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('resta.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('resta.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
            $this->db->where('content_type', 'branch');
            if ($displayLength > 1)
                $this->db->limit($displayLength, $displayStart);
            $dataCmsOnly = $this->db->get('content_general')->result();
            $content_general_id = array();
            foreach ($dataCmsOnly as $key => $value) {
                $content_general_id[] = $value->content_general_id;
            }
            if ($content_general_id) {
                $this->db->where_in('restaurant.content_id', $content_general_id);
            }
        } else {
            if ($this->input->post('page_title') != '') {
                $this->db->like('restaurant.name', $this->input->post('page_title'));
            }
            if ($this->input->post('restaurant') != '') {
                $this->db->like('resta.name', $this->input->post('restaurant'));
            }
            $this->db->select('restaurant.*,resta.name as rname');
            $this->db->join('restaurant as resta', 'restaurant.branch_entity_id = resta.entity_id', 'left');
            $this->db->where('restaurant.branch_entity_id !=', '');

            if (!($this->lpermission->method('full_restaurant_view', 'read')->access())) {
                if ($this->session->userdata('UserType') == 'ZonalAdmin') {
                    $this->db->where('resta.zonal_admin', $this->session->userdata('UserID'));
                }
                if ($this->session->userdata('UserType') == 'Admin') {
                    $this->db->where_in('resta.entity_id', $this->session->userdata('restaurant'));
                }
                if ($this->session->userdata('UserType') == 'CentralAdmin') {
                    $this->db->group_start();
                    $this->db->where('resta.central_admin', $this->session->userdata('UserID'));
                    $this->db->or_where('resta.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
                    $this->db->group_end();
                }
            }
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
                $this->db->order_by('FIELD ( restaurant.entity_id,' . trim($OrderByID, ',') . ') DESC');
                $this->db->where_in('restaurant.content_id', $ContentID);
            } else {
                if ($this->input->post('page_title') != '') {
                    $this->db->like('restaurant.name', trim($this->input->post('page_title')));
                }
                if ($this->input->post('restaurant') != '') {
                    $this->db->like('resta.name', $this->input->post('restaurant'));
                }
            }
        }
        $this->db->select('restaurant.*,resta.name as rname');
        $this->db->join('restaurant as resta', 'restaurant.branch_entity_id = resta.entity_id', 'left');
        $this->db->where('restaurant.branch_entity_id !=', '');
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where_in('resta.entity_id', $this->session->userdata('restaurant'));
        }
        if ($this->session->userdata('UserType') == 'CentralAdmin') {
            $this->db->group_start();
            $this->db->where('resta.central_admin', $this->session->userdata('UserID'));
            $this->db->or_where('resta.branch_entity_id in (SELECT res.entity_id FROM restaurant as res WHERE res.central_admin = ' . $this->session->userdata('UserID') . ')');
            $this->db->group_end();
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
                        'rname' => $value['rname'],
                        'status' => $value['status'],
                    );
                }
                $cmsLang[$value['content_id']]['translations'][$value['language_slug']] = array(
                    'translation_id' => $value['entity_id'],
                    'name' => $value['name'],
                    'status' => $value['status'],
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
    // method to get details by id
    public function getEditDetail($tblname, $entity_id)
    {
        $this->db->select('res.*,
        res_add.address,
        res_add.landmark,
        res_add.latitude,
        res_add.longitude,
        res_add.zipcode,
        res_add.country,
        res_add.city,
        ');
        $this->db->join('restaurant_address as res_add', 'res.entity_id = res_add.resto_entity_id', 'left');
        $this->db->where('res.entity_id', $entity_id);
        return $this->db->get('' . $tblname . ' as res')->first_row();
    }
    // delete
    public function ajaxDelete($tblname, $content_id, $entity_id)
    {
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
    public function ajaxDeleteAll($tblname, $content_id, $branch_id = null)
    {

        $this->db->where(array('content_general_id' => $content_id));
        $this->db->delete('content_general');

        $this->db->where('content_id', $content_id);
        $this->db->delete($tblname);

        if ($branch_id) {
            $this->db->where('restaurant_id', $branch_id);
            $this->db->delete('restaurant_menu_item');
        }
    }
    // update data common function
    public function updateData($Data, $tblName, $fieldName, $ID)
    {
        $this->db->where($fieldName, $ID);
        $this->db->update($tblName, $Data);
        return $this->db->affected_rows();
    }
    //get list
    public function getListData($tblname, $language_slug)
    {
        $this->db->select('name,entity_id');
        $this->db->where('status', 1);
        if ($this->session->userdata('UserType') == 'Admin') {
            $this->db->where('created_by', $this->session->userdata('UserID'));
        }
        $this->db->where('language_slug', $language_slug);
        return $this->db->get($tblname)->result();
    }
    // updating the changed status
    public function UpdatedStatus($tblname, $entity_id, $status)
    {
        if ($status == 0) {
            $userData = array('status' => 1);
        } else {
            $userData = array('status' => 0);
        }
        $this->db->where('entity_id', $entity_id);
        $this->db->update($tblname, $userData);
        return $this->db->affected_rows();
    }
    // get restaurant slug
    public function getRestaurantSlug($content_id)
    {
        $this->db->select('restaurant_slug');
        $this->db->where('content_id', $content_id);
        return $this->db->get('restaurant')->first_row();
    }
    // get content id
    public function getContentId($entity_id, $tblname)
    {
        $this->db->select('content_id');
        $this->db->where('entity_id', $entity_id);
        return $this->db->get($tblname)->first_row();
    }

    public function insertBatch($data, $table)
    {

        $this->db->trans_start();
        $this->db->insert_batch($table, $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $last_id;
    }
}
