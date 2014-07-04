<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Signature extends ORM
{
	protected $_table_name = 'object_signature';

	protected $_belongs_to = array(
		'object' => array(),
	);

	public function save(Validation $validation = NULL)
	{
		if ($this->signature AND is_array($this->signature))
		{
			$this->signature = '{'.join(',', $this->signature).'}';
		}

		parent::save($validation);
	}

	public function get_similarity($array, $user_id = NULL)
	{
		$user_id 	= (int) $user_id;

		$array_str = "'{".join(',', $array)."}'::character varying[]";
		$query = DB::select(DB::expr("MAX(smlar($array_str, signature)) AS sm"))
		->from($this->_table_name)
		->where('signature', 'IS', DB::expr('NOT NULL'));

		if ($user_id)
		{
			$query->join('object')
				->on('object.id', '=', 'object_signature.object_id')
				->where('object.author', '=', $user_id);

		}

		return (float) $query->execute()->get('sm');
	}
}

/* End of file Signature.php */
/* Location: ./application/classes/Model/Object/Signature.php */