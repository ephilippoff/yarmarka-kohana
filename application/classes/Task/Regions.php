<?php defined('SYSPATH') or die('No direct script access.');

class Task_Regions extends Minion_Task
{
	protected $_options = array(
		'limit' => 1000,
	);

	protected function _execute(array $params)
	{
		$limit 	= $params['limit'];
		$offset = 0;

		$total = ORM::factory('Region')->count_all();
		Minion_CLI::write('Total regions:'.Minion_CLI::color($total, 'cyan'));

		Minion_CLI::write_replace(
			'Processed:'.Minion_CLI::color($offset, 'cyan').
			'('.round($offset/($total/100)).'%)'
		);

		$unique_regions = array();
		$regions = $this->get_regions($limit, $offset);
		while(count($regions))
		{
			foreach ($regions as $region) 
			{
				if ( ! isset($unique_regions[$region->kladr_id]))
				{
					$unique_regions[$region->kladr_id] = $region->id;
				}
				else // дубль
				{
					$affected_rows = $this->relink_cities($region->id, $unique_regions[$region->kladr_id]);
					$region->delete();
				}

			}

			$offset += $limit;
			Minion_CLI::write_replace('Processed:'.Minion_CLI::color(count($regions), 'cyan'));
			$regions = $this->get_regions($limit, $offset);
		}

		Minion_CLI::write();
		Minion_CLI::write(Minion_CLI::color('Done!', 'green'));
	}

	public function get_regions($limit, $offset)
	{
		return ORM::factory('Region')
			->offset($offset)
			->limit($limit)
			->order_by('id')
			->group_by('kladr_id', 'id')
			->find_all();
	}

	public function relink_cities($old_region_id, $new_region_id)
	{
		$query = DB::query(Database::UPDATE, "UPDATE city SET region_id = :new_region_id WHERE region_id = :old_region_id");
		$query->param(':new_region_id', $new_region_id);
		$query->param(':old_region_id', $old_region_id);
		return $query->execute();
	}
}