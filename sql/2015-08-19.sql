ALTER TABLE favorite DROP COLUMN userid;
ALTER TABLE favorite ADD COLUMN userid integer;

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


CREATE INDEX object_service_up_idx
  ON object_service_up
  USING btree
  (object_id);

CREATE INDEX object_rating_idx
  ON object_rating
  USING btree
  (object_id);
