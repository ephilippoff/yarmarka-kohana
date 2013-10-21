<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Конвертация таблицы contacts в новый формат, поле contact_clear делаем уникальным, 
 * удаляем дубли и не правильные контакты (сотовые телефоны как городские), удаляем скайпы и аськи
 */
class Task_Contacts extends Minion_Task
{
	protected $_options = array(
		'limit' => 1000,
	);

	protected static $_deleted_rows = 0;

	protected function _execute(array $params)
	{

		// удаляем старые типы контактов
		Minion_CLI::write('Delete old contact types(skype, icq)');
		$affected_rows = DB::delete('contacts')
			->where('contact_type_id', 'NOT IN', array(Model_Contact_Type::PHONE, Model_Contact_Type::MOBILE, Model_Contact_Type::EMAIL))
			->execute();
		Minion_CLI::write('Affected rows:'.Minion_CLI::color($affected_rows, 'cyan'));
		Minion_CLI::write(Minion_CLI::color('Done!', 'green'));

		$total = ORM::factory('Contact')->count_all();
		Minion_CLI::write('Total contacts:'.Minion_CLI::color($total, 'cyan'));

		$offset = 0;
		$limit = $params['limit'];

		Minion_CLI::write_replace(
			'Processed:'.Minion_CLI::color($offset, 'cyan').
			'('.round($offset/($total/100)).'%)'.
			' Deleted:'.Minion_CLI::color(self::$_deleted_rows, 'cyan')
		);

		while ($contacts = $this->get_contacts($offset))
		{
			foreach ($contacts as $contact)
			{
				Database::instance()->begin();

				// проверяем правильность типа контактов
				switch (intval($contact->contact_type_id))
				{
					case Model_Contact_Type::PHONE : // домашний телефон
					case Model_Contact_Type::MOBILE : // мобильный телефон
						$number = trim($contact->contact_clear) ? trim($contact->contact_clear) : Text::clear_phone_number($contact->contact);
						// проверяем есть ли в начале семерка
						if (strpos($number, '7') !== 0)
						{
							$number = '7'.$number;
							$contact->contact_clear = $number;
							$contact->contact = '+7'.$contact->contact;
						}

						// проверяем тип контакта (домашний или мобильный)
						$contact->contact_type_id = (strpos($number, '79') === 0)
							? Model_Contact_Type::MOBILE : Model_Contact_Type::PHONE;

						// удаляем номера короче 6 цифр
						if (strlen($number) < 6)
						{
							$this->delete_contact($contact->id);
						}
						else
						{
							// ищем дубли контакта
							$doubles = ORM::factory('Contact')
								->where('id', '!=', $contact->id)
								->where_open()
									->where('contact_clear', '=', $number)
									->or_where('contact_clear', '=', substr($number, 1))
								->where_close()
								->find_all();

							// объединяем дубли с текущим контактом
							foreach ($doubles as $d)
							{
								$this->relink_contact($d->id, $contact->id);
								$d->delete();
							}
							
							$contact->update();
						}

					break;

					case Model_Contact_Type::EMAIL :
						$email = trim($contact->contact);
						// удаляем не валидные email
						if ( ! Valid::email($email))
						{
							$this->delete_contact($contact->id);
						}
						else
						{
							// ищем дубли контакта
							$doubles = ORM::factory('Contact')
								->where('id', '!=', $contact->id)
								->where_open()
									->where('contact', '=', $email)
									->or_where('contact_clear', '=', $email)
								->where_close()
								->find_all();

							// объединяем дубли с текущим контактом
							foreach ($doubles as $d)
							{
								$this->relink_contact($d->id, $contact->id);
								$d->delete();
							}

							$contact->contact_clear = $email;
							$contact->update();
						}
					break;
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

	public function get_contacts($offset = 0)
	{
		return ORM::factory('Contact')->limit(1000)
			->offset($offset)
			->order_by('id')
			->find_all()
			->as_array();
	}

	public function relink_contact($old_contact_id, $new_contact_id)
	{
		$query = DB::query(Database::UPDATE, 
			"UPDATE user_contacts SET contact_id = :new_contact_id WHERE contact_id = :old_contact_id"
		);
		$query->param(':old_contact_id', $old_contact_id);
		$query->param(':new_contact_id', $new_contact_id);
		$query->execute();

		$query = DB::query(Database::UPDATE, 
			"UPDATE object_contacts SET contact_id = :new_contact_id WHERE contact_id = :old_contact_id"
		);
		$query->param(':old_contact_id', $old_contact_id);
		$query->param(':new_contact_id', $new_contact_id);
		$query->execute();
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