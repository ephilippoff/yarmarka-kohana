<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Object_Attachment extends ORM
{
	protected $_table_name = 'object_attachment';
	
	protected $_belongs_to = array(
		'object' => array(),
	);

	public function generate_signature()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		$filepath = Uploads::get_full_path($this->filename);

		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		$image_diff = new Image_Diff;
		return $image_diff->generate_array($filepath);
	}

	public function save(Validation $validation = NULL)
	{	


		if ($this->signature)
		{
			$filepath = Uploads::get_full_path($this->filename);

			if ( ! file_exists($filepath))
			{
				return FALSE;
			}

			$image_diff = new Image_Diff;
			$array =  $image_diff->generate_array($filepath);
			$this->signature = "{".join(',', $array)."}";		
		}

		parent::save($validation);
	}

	public function get_similarity($filepath)
	{
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		$img_diff = new Image_Diff;
		$array = $img_diff->generate_array($filepath);

		$array_str = "'{".join(',', $array)."}'::int[]";
		$query = DB::select(DB::expr("MAX(smlar($array_str, signature)) AS sm"))
			->from($this->_table_name)
			->join('object', 'left')
			->on('object.id', '=', 'object_id')
			->where('object.active', '=', 1)
			->where('object.is_published', '=', 1)
			->where('signature', 'IS', DB::expr('NOT NULL'));

		return (float) $query->execute()->get('sm');
	}

	public function generate_filenames_signature($object_id)
	{
		$result = array();
		$attachments = $this->where("object_id","=",$object_id)->order_by("id","asc");
		foreach ($attachments as $attachment) {
			$result[] = $attachment->filename;
		}
		return join(",", $result);
	}

	/**
	 * [fdelete - Delete attachement in DB and linked files]
	 * @return void
	 */
	public function fdelete()
	{
		foreach ($this->find_all() as $attachment) {
			foreach (Imageci::getSitePaths($attachment->filename) as $key => $_filename) {
				$filename = "./".$_filename;
				if ( file_exists($filename) AND !is_dir($filename) ) {
					unlink($filename);
				}
			}
			$attachment->delete();
		}
	}
	/*
		install smlar
		
		sudo apt-get install postgresql-server-dev-all
		git clone git://sigaev.ru/smlar
		cd smlar
		sudo make USE_PGXS=1 install
	*/
}

/* End of file Attachment.php */
/* Location: ./application/classes/Model/Object/Attachment.php */