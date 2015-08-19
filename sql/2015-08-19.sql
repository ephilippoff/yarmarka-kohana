ALTER TABLE favorite DROP COLUMN userid;
ALTER TABLE favorite ADD COLUMN userid integer;

-- Table: object_service_up

-- DROP TABLE object_service_up;

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

-- Index: object_service_up_idx

-- DROP INDEX object_service_up_idx;

CREATE INDEX object_service_up_idx
  ON object_service_up
  USING btree
  (object_id);


DROP INDEX object_author;
DROP INDEX object_date_created;
DROP INDEX object_idx_compl;
DROP INDEX object_idx_dateandauthor;
DROP INDEX object_location;
DROP INDEX object_location_idx;
DROP INDEX object_number_idx;
DROP INDEX object_price;

CREATE INDEX object_author_active
  ON object
  USING btree
  (author, date_created DESC)
  WHERE active = 1;

CREATE INDEX object_author_company_id_active
  ON object
  USING btree
  (author_company_id, date_created DESC)
  WHERE active = 1;

CREATE INDEX object_date_created_active
  ON object
  USING btree
  (date_created DESC)
  WHERE active = 1;

CREATE INDEX object_date_created_published
  ON object
  USING btree
  (date_created DESC)
  WHERE is_published = 1 AND active = 1;

CREATE INDEX object_number_idx
  ON object
  USING btree
  (number COLLATE pg_catalog."default", author, category)
  WHERE is_published = 1 AND active = 1;

CREATE INDEX object_service_photocard_idx
  ON object_service_photocard
  USING btree
  (object_id);

CREATE INDEX object_rating_idx
  ON object_rating
  USING btree
  (object_id);
