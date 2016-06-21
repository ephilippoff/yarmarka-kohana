
CREATE TABLE object_service_up
(
  id serial NOT NULL,
  object_id integer,
  date_created timestamp(6) without time zone DEFAULT now(),
  CONSTRAINT object_service_up_pkey PRIMARY KEY (id),
  CONSTRAINT object_service_up_fk FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE object_service_up
  OWNER TO yarmarka_biz;

