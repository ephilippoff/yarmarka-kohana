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

		if ($this->signature_full AND is_array($this->signature_full))
		{
			$this->signature_full = '{'.join(',', $this->signature_full).'}';
		}

		parent::save($validation);
	}

	public function get_similarity($array, $options_exlusive_union, $object_id = NULL,$user_id = NULL, $full = '')
	{
		$user_id 	= (int) $user_id;

		$array_str = "'{".join(',', $array)."}'::character varying[]";
		$query = DB::select(DB::expr("object_id, MAX(smlar($array_str, signature".$full.")) AS sm"))
		->from($this->_table_name)
		->join('object')
		->on('object.id', '=', 'object_signature.object_id')
		->where('signature'.$full.'', 'IS', DB::expr('NOT NULL'))
		->where('object.active', '=', 1)
		->where('object.is_published', '=', 1)
		->group_by('object_id')
		->order_by('sm', 'desc')
		->order_by('object_id', 'asc')
		->limit(1);

		if (!empty($options_exlusive_union))
			$query->where('options_exlusive_union', '=', $options_exlusive_union);

		if ($user_id)
			$query->where('object.author', '=', $user_id);

		if ($object_id)
			$query->where('object.id', '<>', $object_id);

		$result = $query->execute();

		return Array ( "sm" => (float) $result->get('sm'), "object_id" => (int) $result->get('object_id'));
	}
}

/* End of file Signature.php */
/* Location: ./application/classes/Model/Object/Signature.php */