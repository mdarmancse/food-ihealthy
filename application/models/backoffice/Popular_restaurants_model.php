<?php
class Popular_restaurants_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		        
    }	
      //ajax view      
    public function getGridList($sortFieldName = '', $sortOrder = 'ASC', $displayStart = 0, $displayLength = 25)
    {
        if($this->input->post('page_title') != ''){
            $this->db->like('name', $this->input->post('page_title'));
        }
       
        if($this->input->post('Status') != ''){
            $this->db->like('status', $this->input->post('Status'));
        }
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));
        } 
        $this->db->select('entity_id,name,status,is_popular');
        $result['total'] = $this->db->count_all_results('restaurant');
        if($sortFieldName != '')
            $this->db->order_by($sortFieldName, $sortOrder);
        
        if($this->input->post('page_title') != ''){
                $this->db->like('name', $this->input->post('page_title'));
            }
           
        if($this->input->post('Status') != ''){
                $this->db->like('status', $this->input->post('Status'));
            }
        if($displayLength>1)
            $this->db->limit($displayLength,$displayStart);     
        if($this->session->userdata('UserType') == 'Admin'){
            $this->db->where('created_by',$this->session->userdata('UserID'));  
        }  
        $this->db->select('entity_id,name,status,is_popular');
        $result['data'] = $this->db->get('restaurant')->result();       
        return $result;
    }  
    //add to db
    public function addData($tblName,$Data)
    {   
        $this->db->insert($tblName,$Data);            
        return $this->db->insert_id();
    } 
   

    // updating the changed status
    public function UpdatedStatus($tblname,$entity_id,$is_popular){
        if($is_popular==0){
            $userData = array('is_popular' => 1);
        } else {
            $userData = array('is_popular' => 0);
        }        
        $this->db->where('entity_id',$entity_id);
        $this->db->update($tblname,$userData);
        return $this->db->affected_rows();
    }

}
?>