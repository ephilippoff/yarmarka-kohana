<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Complaint extends ORM {

	protected $_table_name = 'complaints';

	protected $_belongs_to = array(
		'object'	=> array('model' => 'Object', 'foreign_key' => 'object_id'),
		'user'		=> array('model' => 'User', 'foreign_key' => 'user_id'),
		'subject'	=> array('model' => 'Complaint_Subject', 'foreign_key' => 'subject_id'),
	);
}

/* End of file Complaint.php */
/* Location: ./application/classes/Model/Complaint.php */