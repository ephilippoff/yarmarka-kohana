CREATE TABLE object_callback
(
  id serial NOT NULL,
  object_id integer NOT NULL,
  reason character varying (255),
  CONSTRAINT object_callback_pkey PRIMARY KEY (id),
  CONSTRAINT object_callback_fk FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE object_callback
  OWNER TO yarmarka_biz;
