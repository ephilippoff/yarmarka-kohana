-- Table: orders

-- DROP TABLE orders;

CREATE TABLE orders
(
  id serial NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  user_id integer,
  state integer NOT NULL,
  sum integer,
  comment text,
  params text,
  key character varying(255),
  payment_url character varying(1500),
  payment_date timestamp without time zone,
  cancel_date timestamp without time zone,
  CONSTRAINT orders_pkey PRIMARY KEY (id),
  CONSTRAINT orders_user_id_6854e156c3d9c61b_fk_users_id FOREIGN KEY (user_id)
      REFERENCES "user" (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION DEFERRABLE INITIALLY DEFERRED
)
WITH (
  OIDS=FALSE
);
ALTER TABLE orders
  OWNER TO yarmarka_biz;

-- Index: order_e8701ad4

-- DROP INDEX order_e8701ad4;

CREATE INDEX order_e8701ad4
  ON orders
  USING btree
  (user_id);


-- Table: order_item

-- DROP TABLE order_item;

CREATE TABLE order_item
(
  id serial NOT NULL,
  order_id integer NOT NULL,
  object_id integer,
  service_id integer,
  params text,
  CONSTRAINT order_item_pkey PRIMARY KEY (id),
  CONSTRAINT order_item_6854e156c3d9c61b_fk_object_id FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT order_item_6854e156c3d9c61b_fk_order_id FOREIGN KEY (order_id)
      REFERENCES orders (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT order_item_6854e156c3d9c61b_fk_service_id FOREIGN KEY (service_id)
      REFERENCES service (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED
)
WITH (
  OIDS=FALSE
);
ALTER TABLE order_item
  OWNER TO yarmarka_biz;


-- Table: order_item_temp

-- DROP TABLE order_item_temp;

CREATE TABLE order_item_temp
(
  id serial NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  object_id integer,
  service_id integer,
  params text,
  key character varying(255) NOT NULL,
  service_name character varying(255) NOT NULL,
  CONSTRAINT order_item_temp_pkey PRIMARY KEY (id),
  CONSTRAINT order_item_tem_6854e156c3d9c61b_fk_object_id FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED,
  CONSTRAINT order_item_tem_6854e156c3d9c61b_fk_service_id FOREIGN KEY (service_id)
      REFERENCES service (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY DEFERRED
)
WITH (
  OIDS=FALSE
);
ALTER TABLE order_item_temp
  OWNER TO yarmarka_biz;

-- Index: order_item_temp_key_idx

-- DROP INDEX order_item_temp_key_idx;

CREATE INDEX order_item_temp_key_idx
  ON order_item_temp
  USING btree
  (key COLLATE pg_catalog."default");



-- Table: kupon_group

-- DROP TABLE kupon_group;

CREATE TABLE kupon_group
(
  id serial NOT NULL,
  title character varying(500) NOT NULL,
  description character varying(500) NOT NULL,
  price double precision NOT NULL,
  object_id integer,
  contacts character varying(1000),
  address character varying(1000),
  address_details character varying(1000),
  support_info character varying(1000),
  expiration_date timestamp without time zone,
  CONSTRAINT kupon_group_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE kupon_group
  OWNER TO yarmarka_biz;
GRANT ALL ON TABLE kupon_group TO yarmarka_biz;

ALTER TABLE kupon ADD COLUMN state character varying(255);

ALTER TABLE kupon ADD COLUMN order_id integer;


ALTER TABLE kupon ADD COLUMN kupon_group_id integer;
ALTER TABLE kupon ALTER COLUMN kupon_group_id SET NOT NULL;
ALTER TABLE kupon ADD COLUMN access_key character varying(255);

NOT NULL снять

-- Table: object_movement

-- DROP TABLE object_movement;

CREATE TABLE object_movement
(
  id serial NOT NULL,
  begin_state character varying(255),
  end_state character varying(255),
  count integer,
  object_id integer,
  kupon_id integer,
  date timestamp without time zone DEFAULT now(),
  order_id integer,
  description character varying(500),
  CONSTRAINT object_movement_pkey PRIMARY KEY (id),
  CONSTRAINT object_movement_fk FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT object_movement_kupon_fk FOREIGN KEY (kupon_id)
      REFERENCES kupon (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);
ALTER TABLE object_movement
  OWNER TO yarmarka_biz;