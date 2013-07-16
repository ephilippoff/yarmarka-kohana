<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Complaint_Subject extends ORM {

	protected $_table_name = 'complaint_subjects';

	protected $_belongs_to = array(
		'complaint'	=> array('model' => 'Complaint', 'foreign_key' => 'subject_id'),
	);
}

/* End of file Subject.php */
/* Location: ./application/classes/Model/Complaint/Subject.php */