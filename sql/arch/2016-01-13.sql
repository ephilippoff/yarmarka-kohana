CREATE TABLE structure
(
  id serial NOT NULL,
  title character varying(255) NOT NULL,
  url character varying(1000),
  parent_id integer,
  weight smallint NOT NULL DEFAULT (500)::smallint,  
  description character varying(2000),
  image character varying(250),
  for_admin boolean,
  CONSTRAINT structure_pkey PRIMARY KEY (id),
  CONSTRAINT structure_fk FOREIGN KEY (parent_id)
      REFERENCES structure (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE structure
  OWNER TO yarmarka_biz;


CREATE INDEX idx_structure
  ON structure
  USING btree
  (parent_id);