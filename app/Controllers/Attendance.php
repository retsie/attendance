<?php

namespace App\Controllers;
use App\Models\AttendanceModel;

class Attendance extends BaseController
{

	var $session;
	public $attendance;
	
	function __construct() {
		// $this->session = \Config\Services::session();
		$this->attendance = new AttendanceModel();
		date_default_timezone_set('Asia/Singapore');

	}
	

	public function index()
	{

		$vars['user'] = $this->attendance->getUserByID(1);
		// print_r($vars['user']);
		// die();
		return view('attendance/main', $vars);
	}

	public function main() {
		$id = 1;
		$user = $this->attendance->getUserByID($id);
		print_r($user);
	}

}
