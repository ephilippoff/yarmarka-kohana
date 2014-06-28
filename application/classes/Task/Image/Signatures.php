<?php defined('SYSPATH') or die('No direct script access.');

class Task_Image_Signatures extends Minion_Task
{
	protected $_options = array(
		'limit'	=> 1000,
	);

	protected function _execute(array $params)
	{
		$limit 		= $params['limit'];
		$offset 	= 0;
		$total 		= ORM::factory('Object_Attachment')
			->join('object')
			->on('object.id', '=', 'object_attachment.object_id')
			->where('object.active', '=', 1)
			->limit($limit)
			->offset($offset)
			->count_all();

		Minion_CLI::write('Total:'.Minion_CLI::color($total, 'cyan'));

		$attachments = $this->get_attachments($limit, $offset);
		$i = 0;

		while (count($attachments))
		{
			foreach ($attachments as $attachment)
			{
				if ($signature = $attachment->generate_signature())
				{
					$attachment->signature = $signature;
					$attachment->save();
				}
			}

			Minion_CLI::write_replace('Processed: '.Minion_CLI::color($offset+count($attachments).'/'.$total, 'cyan'));

			$offset += $limit;
			$attachments = $this->get_attachments($limit, $offset);
		}
	}

	public function get_attachments($limit, $offset)
	{
		return ORM::factory('Object_Attachment')
			->join('object')
			->on('object.id', '=', 'object_attachment.object_id')
			->where('object.active', '=', 1)
			->limit($limit)
			->offset($offset)
			->order_by('id', 'desc')
			->find_all();
	}
}

/* End of file Signatures.php */
/* Location: ./application/classes/Task/Image/Signatures.php */