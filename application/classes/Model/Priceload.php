<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Priceload extends ORM {

	protected $_table_name = 'priceload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

	function set_state($state = 0, $comment = NULL)
	{
		if (!$this->loaded())
			return;

		$this->state = $state;
		$this->comment = $comment;
		$this->update();
	}

	function get_states()
	{
		return array(
    			0 => "у оператора",
    			1 => "на модерации",
    			2 => "одобрено",
    			3 => "отклонено",
    			4 => "в обработке",
    			5 => "выполнено",
    			99 => "ошибка"
    		);
	}

	function get_active_states()
	{
		return array(1,2,3,4,99);
	}

	function _delete()
	{
		if (!$this->loaded())
			return FALSE;

			try {
				Temptable::delete_table($this->table_name);
			} catch (Exception $e)
			{
				return;
			}

		$this->delete();
	}

	/*
		state may be:

		0 - default
		1 - on_moderation (active)
		2 - true_moderation (active)
		3 - false_moderation (active, end state) 
		4 - in order/in proccess (active)
		5 - finished (end state)
		99 - error (active)
	*/

	/*
		-- Table: priceload

		-- DROP TABLE priceload;

		CREATE TABLE priceload
		(
		  id serial NOT NULL,
		  user_id integer,
		  created_on timestamp without time zone DEFAULT now(),
		  title character varying(255),
		  filepath character varying(255),
		  table_name character varying(255),
		  config text,
		  statistic character varying(255),
		  state integer DEFAULT 0, -- 0 - default...
		  comment text,
		  CONSTRAINT priceload_pkey PRIMARY KEY (id)
		)
		WITH (
		  OIDS=FALSE
		);
		ALTER TABLE priceload
		  OWNER TO yarmarka_biz;
		COMMENT ON COLUMN priceload.state IS '0 - default
		1 - on_moderation
		2 - true_moderation
		3 - false_moderation
		4 - in order 
		5 - finished';


	 */

}