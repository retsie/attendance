<?php 

namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RegisterModel;

class Register extends BaseController
{

	var $session;
	public $attendance;
	
	function __construct() {

		$this->session = \Config\Services::session();
		date_default_timezone_set('Asia/Singapore');
		$this->register = new RegisterModel();
	}
	

	public function index()
	{
		$vars['grades'] = $this->register->getAllGrades();
		$vars['sections'] = $this->register->getAllSections();
		$vars['strands'] = $this->register->getAllStrands();
		$vars['page_body'] = 'student/register';
		$vars['data'] = array('a', 'b');
		echo view('template/index', $vars);
		echo view('template/footer');
	}


	public function studentView($id) {
		
		$vars['student'] = $this->register->getStudentByID($id);
		$vars['grades'] = $this->register->getAllGrades();
		$vars['sections'] = $this->register->getAllSections();
		$vars['strands'] = $this->register->getAllStrands();

		$vars['page_body'] = 'student/view';

		echo view('template/index', $vars);
		echo view('template/footer');
	}

	public function studentEdit($id) {
		
		$vars['student'] = $this->register->getStudentByID($id);
		$vars['grades'] = $this->register->getAllGrades();
		$vars['sections'] = $this->register->getAllSections();
		$vars['strands'] = $this->register->getAllStrands();

		$vars['page_body'] = 'student/edit';

		echo view('template/index', $vars);
		echo view('template/footer');
	}


	public function ajax_signup() 
	{

		$validation =  \Config\Services::validation();
		helper(['form', 'url']);
		$request = service('request');

		$data =[];
		$msg = [];
		$other_errors = [];
		$field = [];

		$field['floatingLrn'] = ['label' => 'LRN', 'rules' => 'required|is_unique[student_tb.lrn]'];
		$field['floatingEmail'] = ['label' => 'Email', 
								   'rules' => 'required|valid_email|is_unique[student_tb.email]',
								   'errors' => [
								   				'is_not_unique' => 'This email is already registered as a student.'
								   			   ]
								 ];

		$field['floatingType'] = ['label' => 'Type', 'rules' => 'required'];
		$field['floatingFirstName'] = ['label' => 'First Name', 'rules' => 'required'];
		$field['floatingMiddleName'] = ['label' => 'Middle Name', 'rules' => 'required'];
		$field['floatingLastName'] = ['label' => 'Last Name', 'rules' => 'required'];
		$field['floatingAddress'] = ['label' => 'Address', 'rules' => 'required'];
		$field['floatingParents'] = ['label' => 'Parents', 'rules' => 'required'];
		$field['floatingNumber'] = ['label' => 'Contact Number', 'rules' => 'required'];
		$field['floatingBirth'] = ['label' => 'Birthday', 'rules' => 'required'];
		$field['floatingGrade'] = ['label' => 'Grade', 'rules' => 'required'];
		$field['floatingSection'] = ['label' => 'Section', 'rules' => 'required'];
		$field['floatingYear'] = ['label' => 'Class Year', 'rules' => 'required'];
		$field['floatingSemester'] = ['label' => 'Semester', 'rules' => 'required'];


		$valid =$this->validate($field);
		
		if($this->request->getMethod() == 'post') {
			if(!$valid) { // IF VALIDATION IS RETURN TO FALSE
				$msg = [
					'success' => false,
					'errors' => $validation->getErrors()
				];

			}  else { // IF DATA IS VALID

	
				$student_data = [
					'lrn' => $this->request->getVar('floatingLrn', FILTER_SANITIZE_STRING),	
					'email' => trim($this->request->getVar('floatingEmail', FILTER_SANITIZE_STRING)),	
					'type' => $this->request->getVar('floatingType', FILTER_SANITIZE_STRING),		
					'first_name' => $this->request->getVar('floatingFirstName', FILTER_SANITIZE_STRING),	
					'middle_name' => $this->request->getVar('floatingMiddleName', FILTER_SANITIZE_STRING),	
					'last_name' => $this->request->getVar('floatingLastName', FILTER_SANITIZE_STRING),
					'address' => $this->request->getVar('floatingAddress', FILTER_SANITIZE_STRING),
					'parents' => $this->request->getVar('floatingParents', FILTER_SANITIZE_STRING),
					'number' => $this->request->getVar('floatingNumber', FILTER_SANITIZE_STRING),
					'birthday' => $this->request->getVar('floatingBirth', FILTER_SANITIZE_STRING),
					'gender' => $this->request->getVar('gender', FILTER_SANITIZE_STRING),
					'status' =>  $this->request->getVar('switch-status', FILTER_SANITIZE_STRING)?1:0,
					'date_created' => date("Y-m-d H:i:s"),
					'date_updated' => date("Y-m-d H:i:s")
			
				];


				
				// SAVE MEMBER DATA TO DB
				$id = $this->register->addStudent($student_data);
				

				$student_data_2 = [
					'grade' => $this->request->getVar('floatingGrade', FILTER_SANITIZE_STRING),
					'section' => $this->request->getVar('floatingSection', FILTER_SANITIZE_STRING),
					'strand' => $this->request->getVar('floatingStrand', FILTER_SANITIZE_STRING),
					'date_created' => date("Y-m-d H:i:s"),
					'date_updated' => date("Y-m-d H:i:s"),
					'student_id' => $id,
					'start_year' => $this->request->getVar('floatingYear', FILTER_SANITIZE_STRING),
					'semester' => $this->request->getVar('floatingSemester', FILTER_SANITIZE_STRING),
					'is_current'=>1
				];
				
				$id2 = $this->register->addStudentSection($student_data_2);

			
				if($id) {
					
					$msg = [
						'success' => $id,
						'errors' => 0
					];

				} else {

					$msg = [
						'success' =>false,
						'errors' => 'Invalid Connection.'
					];

				}
	
				
			}
		}

		echo json_encode($msg); // RETURN MESSAGE TO AJAX

	}


	public function ajax_editStudent() 
	{

		$validation =  \Config\Services::validation();
		helper(['form', 'url']);
		$request = service('request');

		$data =[];
		$msg = [];
		$other_errors = [];
		$field = [];

		$field['floatingLrn'] = ['label' => 'LRN', 'rules' => 'required|is_unique[student_tb.lrn,id,{id}]'];
		$field['floatingEmail'] = ['label' => 'Email', 
								   'rules' => 'required|valid_email|is_unique[student_tb.email,id,{id}]',
								   'errors' => [
								   				'is_not_unique' => 'This email is already registered as a student.'
								   			   ]
								 ];

		$field['floatingType'] = ['label' => 'Type', 'rules' => 'required'];
		$field['floatingFirstName'] = ['label' => 'First Name', 'rules' => 'required'];
		$field['floatingMiddleName'] = ['label' => 'Middle Name', 'rules' => 'required'];
		$field['floatingLastName'] = ['label' => 'Last Name', 'rules' => 'required'];
		$field['floatingAddress'] = ['label' => 'Address', 'rules' => 'required'];
		$field['floatingParents'] = ['label' => 'Parents', 'rules' => 'required'];
		$field['floatingNumber'] = ['label' => 'Contact Number', 'rules' => 'required'];
		
		if($this->request->getVar('additional', FILTER_SANITIZE_STRING)) {

			$field['floatingBirth'] = ['label' => 'Birthday', 'rules' => 'required'];
			$field['floatingGrade'] = ['label' => 'Grade', 'rules' => 'required'];
			$field['floatingSection'] = ['label' => 'Section', 'rules' => 'required'];

		}


		$valid =$this->validate($field);
		
		if($this->request->getMethod() == 'post') {
			if(!$valid) { // IF VALIDATION IS RETURN TO FALSE
				$msg = [
					'success' => false,
					'errors' => $validation->getErrors()
				];

			}  else { // IF DATA IS VALID

	
				$student_data = [
					'lrn' => $this->request->getVar('floatingLrn', FILTER_SANITIZE_STRING),	
					'email' => trim($this->request->getVar('floatingEmail', FILTER_SANITIZE_STRING)),	
					'type' => $this->request->getVar('floatingType', FILTER_SANITIZE_STRING),		
					'first_name' => $this->request->getVar('floatingFirstName', FILTER_SANITIZE_STRING),	
					'middle_name' => $this->request->getVar('floatingMiddleName', FILTER_SANITIZE_STRING),	
					'last_name' => $this->request->getVar('floatingLastName', FILTER_SANITIZE_STRING),
					'address' => $this->request->getVar('floatingAddress', FILTER_SANITIZE_STRING),
					'parents' => $this->request->getVar('floatingParents', FILTER_SANITIZE_STRING),
					'number' => $this->request->getVar('floatingNumber', FILTER_SANITIZE_STRING),
					'birthday' => $this->request->getVar('floatingBirth', FILTER_SANITIZE_STRING),
					'gender' => $this->request->getVar('gender', FILTER_SANITIZE_STRING),
					'status' =>  $this->request->getVar('switch-status', FILTER_SANITIZE_STRING)?1:0,
					'date_created' => date("Y-m-d H:i:s"),
					'date_updated' => date("Y-m-d H:i:s")	
			
				];


				
				// SAVE MEMBER DATA TO DB
				$student_id = $this->request->getVar('id', FILTER_SANITIZE_STRING);
				$id = $this->register->updateStudent($student_data, $student_id);
				
				if($this->request->getVar('additional', FILTER_SANITIZE_STRING)) {
					$this->register->disableAllStudentClass($student_id);
					$student_data_2 = [
						'grade' => $this->request->getVar('floatingGrade', FILTER_SANITIZE_STRING),
						'section' => $this->request->getVar('floatingSection', FILTER_SANITIZE_STRING),
						'strand' => $this->request->getVar('floatingStrand', FILTER_SANITIZE_STRING),
						'date_created' => date("Y-m-d H:i:s"),
						'date_updated' => date("Y-m-d H:i:s"),
						'student_id' => $student_id,
						'start_year' => $this->request->getVar('floatingYear', FILTER_SANITIZE_STRING),
						'semester' => $this->request->getVar('floatingSemester', FILTER_SANITIZE_STRING),
						'is_current'=>1
					];

					$id2 = $this->register->addStudentSection($student_data_2);
				}

				
			
				if($id) {
					
					$msg = [
						'success' => $id,
						'errors' => 0
					];

				} else {

					$msg = [
						'success' =>false,
						'errors' => 'Invalid Connection.'
					];

				}
	
				
			}
		}

		echo json_encode($msg); // RETURN MESSAGE TO AJAX

	}







	public function studentList() {
		$vars['page_body'] = 'student/student_list';
		echo view('template/index', $vars);
		echo view('template/footer');
	}


	public function ajax_studentList(){

	
		$params = $_REQUEST;

		//define index of column
		$columns = array(
	
			
			0 => 'lrn',
			1 => 'first_name', 
			2 => 'middle_name',
			3 => 'last_name',
			4 => 'email',
			5 => 'gender',
			6 => 'type',
			7 => 'grade',
			8 => 'section',
			9 => 'strand'
			// 6 => 'member_subscription_type'
		);

		$sql_params = array();
		$sql_params['column'] = $columns[$params['order'][0]['column']]; //member_name default
		$sql_params['dir'] = $params['order'][0]['dir']; //asc default
		$sql_params['start'] = $params['start'];
		$sql_params['length'] = $params['length'];
		$sql_params['search'] = $params['search']['value'];

		if(isset($params['grade']) && $params['grade'] !='') {
			$sql_params['grade'] = $params['grade'];
		}
		if(isset($params['section']) && $params['section'] !='') {
			$sql_params['section'] = $params['section'];
		}
		if(isset($params['strand']) && $params['strand'] !='') {
			$sql_params['strand'] = $params['strand'];
		}
		if(isset($params['type']) && $params['type'] !='') {
			$sql_params['type'] = $params['type'];
		}

		$all_data = $this->register->getAllStudent($sql_params);
		$total = $this->register->getAllStudentCount($sql_params);
		
	
		$data = array();

		if($all_data) {
			
		

			foreach($all_data as $student) {
			
				$data[] = array(
					'lrn'			=>	$student->lrn,
					'first_name'	=>	$student->first_name,
					'middle_name'	=>	$student->middle_name,
					'last_name'		=>	$student->last_name,
					'email'			=>	$student->email,
					'gender'		=>	$student->gender==1?'Male':'Female',
					'type'		=>	$student->type==1?'Local':'International',
					'grade'			=>	$student->grade,
					'section'		=>	$student->section,
					'strand'		=>	$student->strand,
					'action'		=>  '<a href="'.base_url().'/student/view/'.$student->id.'" title="View"><i class="material-icons">remove_red_eye</i></a> &nbsp; <a href="'.base_url().'/student/edit/'.$student->id.'" title="Edit"><i class="material-icons">mode_edit</i></a>'
					);
			}


		} 
			

		
		
	
		$json_data = array(
				"draw"            => intval($params['draw']), 
				"recordsTotal"    => intval($total),   
				"recordsFiltered" => intval($total), 
				"data"            => $data   
				);

		echo json_encode($json_data);

	}


}

 ?>