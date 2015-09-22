
ALTER TABLE public.category_banners
  DROP CONSTRAINT category_banners_fk RESTRICT;
  
ALTER TABLE public.category_banners
  ADD COLUMN menu_name VARCHAR(15);
  
ALTER TABLE public.category_banners
  ALTER COLUMN menu_name SET DEFAULT 'main';

COMMENT ON COLUMN public.category_banners.menu_name
IS 'main - основное,
kupons - купоны';

update category_banners set menu_name = 'main' 

ALTER TABLE public.category_banners
  ADD COLUMN menu_height SMALLINT;
  
ALTER TABLE public.category_banners
  ALTER COLUMN menu_height SET DEFAULT 0;



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

ALTER TABLE kupon ADD COLUMN state character varying(255);
ALTER TABLE kupon ALTER COLUMN "number" SET NOT NULL;



CREATE TABLE kupon_group
(
  id serial NOT NULL,
  title character varying(500) NOT NULL,
  description character varying(500) NOT NULL,
  price decimal NOT NULL,
  CONSTRAINT kupon_group_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE kupon_group
  OWNER TO yarmarka_biz;
GRANT ALL ON TABLE kupon_group TO yarmarka_biz;

ALTER TABLE kupon ADD COLUMN kupon_group_id integer;

ALTER TABLE kupon
  ADD CONSTRAINT kupon_group_fk FOREIGN KEY (kupon_group_id)
      REFERENCES kupon_group (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

CREATE INDEX fki_kupon_group_fk
  ON kupon
  USING btree
  (kupon_group_id);

ALTER TABLE kupon ADD COLUMN access_key character varying(255);