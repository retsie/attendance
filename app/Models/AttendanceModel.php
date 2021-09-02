<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{

	public function __construct() {
        parent::__construct();
		$this->db = \Config\Database::connect();
    }	
   

     public function getUserByID($id) {

        $builder = $this->db->table('users');
        $builder->select('*');
        $builder->where('id', $id);
        $query = $builder->get(); 
        if($query->getNumRows()) {
            return $query->getRow();
        } else {
            return false;
        }
        

    }





}