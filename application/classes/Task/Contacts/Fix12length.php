<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Удаляет и мержит 12ти символьные телефоны, вида 789028556975, +78-909-182-16-05 
 *
 * Параметры:
 * --limit - какими порциями обрабатыватьк контакты
 */
class Task_Contacts_Fix12length extends Minion_Task
{
	protected $_options = array(
		'limit' => 1000,
	);

	protected static $_deleted_rows = 0;

	protected function _execute(array $params)
	{
		$offset = 0;
		$limit 	= $params['limit'];

		$total = ORM::factory('Contact')
			->where(DB::expr('char_length(contact_clear)'), '=', 12)
			->where('contact_type_id', 'IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE))
			->where('contact_clear', 'LIKE', '78%');
		$total = $total->count_all();

		Minion_CLI::write('Total contacts:'.Minion_CLI::color($total, 'cyan'));


		Minion_CLI::write_replace(
			'Processed:'.Minion_CLI::color($offset, 'cyan').
			'('.round($offset/($total/100)).'%)'.
			' Deleted:'.Minion_CLI::color(self::$_deleted_rows, 'cyan')
		);

		while ($contacts = $this->get_contacts($limit))
		{
			foreach ($contacts as $contact)
			{
				// приводим контакт в нормальный вид
				$good_contact = preg_replace('/^(78)(.*)/', '7$2', $contact->contact_clear);
				if (strlen($good_contact) != 11)
				{
					Minion_CLI::write(Minion_CLI::color('something wrong with contact '.$good_contact, 'red'));
					continue;
				}

				// есть ли такой контакт в базе
				$exists_contact = ORM::factory('Contact')
					->where('contact_clear', '=', $good_contact)
					->find();


				Database::instance()->begin();
				if ($exists_contact->loaded())
				{
					// если есть - удаляем
					$this->delete_contact($contact->id);
				}
				else
				{
					// иначе сохраняем исправленный
					$contact->contact = $contact->contact_clear = $good_contact;
					$contact->save();
				}

				Database::instance()->commit();
			}

			$offset += $limit;

			Minion_CLI::write_replace(
				'Processed:'.Minion_CLI::color($offset, 'cyan').
				'('.round($offset/($total/100)).'%)'.
				' Deleted:'.Minion_CLI::color(self::$_deleted_rows, 'cyan')
			);
		}

		Minion_CLI::write();
		Minion_CLI::write(Minion_CLI::color('Done!', 'green'));
	}

	public function get_contacts($limit)
	{
		$query = ORM::factory('Contact')
			->where(DB::expr('char_length(contact_clear)'), '=', 12)
			->where('contact_type_id', 'IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE))
			->where('contact_clear', 'LIKE', '78%')
			->limit($limit);

		return $query->find_all()
			->as_array();
	}

	public function delete_contact($contact_id)
	{
		DB::delete('user_contacts')->where('contact_id', '=', $contact_id)
			->execute();
		DB::delete('object_contacts')->where('contact_id', '=', $contact_id)
			->execute();
		DB::delete('contacts')->where('id', '=', $contact_id)
			->execute();

		self::$_deleted_rows++;
	}
}