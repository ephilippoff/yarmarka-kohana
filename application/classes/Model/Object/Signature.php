<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Signature extends ORM
{
	protected $_table_name = 'object_signature';

	protected $_belongs_to = array(
		'object' => array(),
	);

	public function save(Validation $validation = NULL)
	{

		if ($this->signature_full AND is_array($this->signature_full))
		{
			$this->signature_full = '{'.join(',', $this->signature_full).'}';
		}

		parent::save($validation);
	}

	public function get_similarity($max_similarity, $array, $options_exlusive_union, $object_id = NULL,$user_id = NULL, $itis_massload = FALSE)
	{
		$user_id 	= (int) $user_id;

		$db = Database::instance();
		$db->begin();
			$query =DB::query(Database::SELECT, "select set_smlar_limit(:max_similarity);")
				->param(':max_similarity', $max_similarity)
				->execute();

			$array_str = "'{".join(',', $array)."}'::character varying[]";
			$query = DB::select(DB::expr("object_id, smlar($array_str, signature_full) AS sm"))
			->from($this->_table_name)
			->join('object')
				->on('object.id', '=', 'object_signature.object_id')
			->where('object.active', '=', 1)
			->where('object.is_published', '=', 1)
			->where("signature_full", '%', DB::expr($array_str))
			->order_by('sm', 'desc')
			->order_by('object_id', 'asc')
			->limit(1);
		$db->commit();

		if (!empty($options_exlusive_union))
			$query->where('options_exlusive_union', '=', $options_exlusive_union);

		if ($user_id)
			$query->where('object.author', '=', $user_id);

		if ($object_id AND !$itis_massload)
			$query->where('object.id', '<>', $object_id);

		$result = $query->execute();

		return Array ( "sm" => (float) $result->get('sm'), "object_id" => (int) $result->get('object_id'));
	}
}

/* End of file Signature.php */
/* Location: ./application/classes/Model/Object/Signature.php */