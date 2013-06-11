<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Invoice 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Invoice extends ORM {

	const CREATED = 0;
	const SUCCESS = 2;
	const REFUSED = 3;

	protected $_belongs_to = array(
		'user' => array(),
	);

	protected $_has_many = array(
		'services' => array('model' => 'Service_Invoices', 'foreign_key' => 'invoice_id'),
	);

	public function get_status_text()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		switch ($this->state) 
		{
			case self::CREATED:
				return 'Ожидает оплаты';
			break;

			case self::SUCCESS:
				return 'Оплачен';
			break;

			case self::REFUSED:
				return 'Отменен';
			break;
			
			default:
			break;
		}
	}

	public function created()
	{
		return $this->where('state', '=', self::CREATED);
	}

	public function success()
	{
		return $this->where('state', '=', self::SUCCESS);
	}

	public function refused()
	{
		return $this->where('state', '=', self::REFUSED);
	}

} // End Invoice Model
