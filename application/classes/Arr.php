<?php defined('SYSPATH') OR die('No direct script access.');

class Arr extends Kohana_Arr {
	/**
	 * Recursively merge two or more arrays. Values in an associative array
	 * overwrite previous values with the same key. Values in an indexed array
	 * are appended, but only when they do not already exist in the result.
	 *
	 * FIX: don't merge numeric arrays
	 *
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = Arr::merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('jane'))
	 *
	 * @param   array  $array1      initial array
	 * @param   array  $array2,...  array to merge
	 * @return  array
	 */
	public static function merge_assoc($array1, $array2)
	{
		if (Arr::is_assoc($array2))
		{
			foreach ($array2 as $key => $value)
			{
				if (is_array($value)
					AND isset($array1[$key])
					AND is_array($array1[$key])
				)
				{
					$array1[$key] = Arr::merge_assoc($array1[$key], $value);
				}
				else
				{
					$array1[$key] = $value;
				}
			}
		}
		else
		{
			$array1 = $array2;
		}

		if (func_num_args() > 2)
		{
			foreach (array_slice(func_get_args(), 2) as $array2)
			{
				if (Arr::is_assoc($array2))
				{
					foreach ($array2 as $key => $value)
					{
						if (is_array($value)
							AND isset($array1[$key])
							AND is_array($array1[$key])
						)
						{
							$array1[$key] = Arr::merge_assoc($array1[$key], $value);
						}
						else
						{
							$array1[$key] = $value;
						}
					}
				}
				else
				{
					$array1 = $array2;
				}
			}
		}

		return $array1;
	}
}