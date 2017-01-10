
CREATE TABLE object_notice
(
  id serial NOT NULL,
  object_id integer,
  name character varying(255),
  noticed boolean,
  CONSTRAINT object_notice_pkey PRIMARY KEY (id),
  CONSTRAINT object_notice_fk FOREIGN KEY (object_id)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE object_notice
  OWNER TO yarmarka_biz;


CREATE INDEX object_notice_idx
  ON object_notice
  USING btree
  (object_id);