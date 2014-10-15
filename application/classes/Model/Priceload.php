<?php defined('SYSPATH') OR die('No direct access allowed.');


class Model_Priceload extends ORM {

	protected $_table_name = 'priceload';

	protected $_belongs_to = array(
		'user'			=> array('model' => 'User', 'foreign_key' => 'user_id'),
	);

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