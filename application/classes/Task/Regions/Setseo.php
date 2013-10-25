<?php defined('SYSPATH') or die('No direct script access.');

class Task_Regions_Setseo extends Minion_Task
{
	protected $_options = array(
	);

	protected function _execute(array $params)
	{
		$regions = ORM::factory('Region')->find_all();
		foreach ($regions as $region)
		{
			if ( ! $region->seo_name)
			{
				$region->seo_name = URL::title($region->title, '_', TRUE);
				$region->save();

				Minion_CLI::write('New seo name for region: '.Minion_CLI::color($region->title ."({$region->seo_name})", 'cyan'));
			}
		}

		Minion_CLI::write(Minion_CLI::color('Done!', 'green'));
	}
}