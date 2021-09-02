<?php

namespace App\Models;

use CodeIgniter\Model;

class RegisterModel extends Model
{

	public function __construct() {
        parent::__construct();
		$this->db = \Config\Database::connect();
    }	
   

     public function getStudentByID($id) {

        $builder = $this->db->table('student_tb s');
        $builder->select('s.*, ss.id student_section_id, ss.grade, ss.section, ss.strand, ss.start_year, ss.semester');
        $builder->where('s.id', $id);
        $builder->where('ss.is_current', 1);
        $builder->join('student_section_tb ss', 'ss.student_id=s.id');
        $query = $builder->get(); 
       
        if($query->getNumRows()) {
       
            return $query->getRow();
       
        } else {
       
            return false;
       
        }  

    }


    public function addStudent($data) {
         
        $this->db->table('student_tb')->insert($data);
        
        if ( $this->db->affectedRows() >= 1) {
        
            return  $this->db->insertID();
        
        } else {
           
            return false;
        
        }

    }


    public function updateStudent($data, $id) {
         
        $this->db->table('student_tb')->where('id', $id)->update($data);

        if ($this->db->affectedRows() >= 1) {
            return true;
        } else {
            return false;
        }

    }


    public function addStudentSection($data) {
         
        $this->db->table('student_section_tb')->insert($data);
        
        if ( $this->db->affectedRows() >= 1) {
        
            return  $this->db->insertID();
        
        } else {
           
            return false;
        
        }

    }



    public function updateStudentSection($data, $id) {
         
        $this->db->table('student_section_tb')->where('id', $id)->update($data);

        if ($this->db->affectedRows() >= 1) {
            return true;
        } else {
            return false;
        }

    }


    public function disableAllStudentClass($id) {
         
        $this->db->table('student_section_tb')->where('student_id', $id)->update(array('is_current'=>0));

        if ($this->db->affectedRows() >= 1) {
            return true;
        } else {
            return false;
        }

    }



    public function getAllStudent($sql_params) {
        $builder = $this->db->table('student_tb s');

        $builder->select('s.*, gt.name grade, st.name section, str.name strand');
        $builder->join('student_section_tb ss', 'ss.student_id=s.id');
        $builder->join('section_tb st', 'st.id=ss.section');
        $builder->join('grade_tb gt', 'gt.id=ss.grade');
        $builder->join('strand_tb str', 'str.id=ss.strand', 'left');

        $builder->where('ss.is_current', 1); 
        

        if(isset($sql_params['grade']) && $sql_params['grade'] !='') {
             $builder->where('ss.grade', $sql_params['grade']); 
        }

        if(isset($sql_params['section']) && $sql_params['section'] !='') {
             $builder->where('ss.section', $sql_params['section']); 
        }

        if(isset($sql_params['strand']) && $sql_params['strand'] !='') {
             $builder->where('ss.strand', $sql_params['strand']); 
        }

        if(isset($sql_params['type']) && $sql_params['type'] !='') {
             $builder->where('s.type', $sql_params['type']); 
        }

        if(isset($sql_params['search']) && $sql_params['search'] != '') {
            $builder->like('lrn', $sql_params['search']); 
            $builder->orLike('email', $sql_params['search']);
            $builder->orLike('first_name', $sql_params['search']);
            $builder->orLike('middle_name', $sql_params['search']);
            $builder->orLike('last_name', $sql_params['search']);
        }


        if(isset($sql_params['column'])) {
          $builder->orderBy($sql_params['column'], $sql_params['dir']);
        }

        $builder->limit($sql_params['length'], $sql_params['start']);


        $query = $builder->get();
        log_message('error', "LAST QUERY HERE: ".print_r($this->db->getLastQuery(),true));
        $rows = $query->getResult();


        if($query->getNumRows()) {
            return $rows;
        } else {
            return false;
        }
    }


    public function getAllStudentCount($sql_params) {
        $builder = $this->db->table('student_tb s');

        $builder->select('s.id');
        $builder->join('student_section_tb ss', 'ss.student_id=s.id');
        $builder->join('section_tb st', 'st.id=ss.section');
        $builder->join('grade_tb gt', 'gt.id=ss.grade');
        $builder->join('strand_tb str', 'str.id=ss.strand', 'left');

        $builder->where('ss.is_current', 1); 

        if(isset($sql_params['grade']) && $sql_params['grade'] !='') {
             $builder->where('ss.grade', $sql_params['grade']); 
        }

        if(isset($sql_params['section']) && $sql_params['section'] !='') {
             $builder->where('ss.section', $sql_params['section']); 
        }

        if(isset($sql_params['strand']) && $sql_params['strand'] !='') {
             $builder->where('ss.strand', $sql_params['strand']); 
        }

        if(isset($sql_params['type']) && $sql_params['type'] !='') {
             $builder->where('s.type', $sql_params['type']); 
        }

        if(isset($sql_params['search']) && $sql_params['search'] != '') {
            $builder->like('lrn', $sql_params['search']); 
            $builder->orLike('email', $sql_params['search']);
            $builder->orLike('first_name', $sql_params['search']);
            $builder->orLike('middle_name', $sql_params['search']);
            $builder->orLike('last_name', $sql_params['search']);
        }

        return $builder->countAllResults(); // Produces an integer, like 17
    }



    public function getAllGrades() {
        $builder = $this->db->table('grade_tb');

        $builder->select('*');

        $builder->orderBy('id', 'asc');
     
        $query = $builder->get();
 
        $rows = $query->getResult();


        if($query->getNumRows()) {
            return $rows;
        } else {
            return false;
        }
    }


    public function getAllSections() {
        $builder = $this->db->table('section_tb');

        $builder->select('*');

        $builder->orderBy('id', 'asc');
     
        $query = $builder->get();
 
        $rows = $query->getResult();


        if($query->getNumRows()) {
            return $rows;
        } else {
            return false;
        }
    }


    public function getAllStrands() {
        $builder = $this->db->table('strand_tb');

        $builder->select('*');

        $builder->orderBy('id', 'asc');
     
        $query = $builder->get();
 
        $rows = $query->getResult();


        if($query->getNumRows()) {
            return $rows;
        } else {
            return false;
        }
    }





}