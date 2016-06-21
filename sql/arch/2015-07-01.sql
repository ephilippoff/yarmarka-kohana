-- Table: orders

-- DROP TABLE orders;

CREATE TABLE orders
(
  id serial NOT NULL,
  created timestamp without time zone NOT NULL DEFAULT now(),
  user_id integer NOT NULL,
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
