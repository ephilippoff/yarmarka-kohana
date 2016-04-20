-- Table: favorite

-- DROP TABLE favorite;

CREATE TABLE favorite
(
  id serial NOT NULL,
  userid integer NOT NULL,
  objectid integer NOT NULL,
  code character varying(40) NOT NULL,
  CONSTRAINT favorite_pkey PRIMARY KEY (id),
  CONSTRAINT favorite_fk FOREIGN KEY (userid)
      REFERENCES "user" (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT favorite_fk1 FOREIGN KEY (objectid)
      REFERENCES object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE favorite
  OWNER TO yarmarka_biz;
GRANT ALL ON TABLE favorite TO yarmarka_biz;

-- Index: favorite_code2_idx

-- DROP INDEX favorite_code2_idx;

CREATE INDEX favorite_code2_idx
  ON favorite
  USING btree
  (code COLLATE pg_catalog."default");

-- Index: favorite_code_idx

-- DROP INDEX favorite_code_idx;

CREATE INDEX favorite_code_idx
  ON favorite
  USING btree
  (code COLLATE pg_catalog."default", objectid);

-- Index: favorite_idx

-- DROP INDEX favorite_idx;

CREATE INDEX favorite_idx
  ON favorite
  USING btree
  (userid, objectid);
