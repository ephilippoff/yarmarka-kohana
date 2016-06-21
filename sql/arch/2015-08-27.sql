ALTER TABLE object_rating ADD COLUMN count integer;
ALTER TABLE object_rating ALTER COLUMN count SET DEFAULT 1;

ALTER TABLE object_rating ADD COLUMN activated integer;
ALTER TABLE object_rating ALTER COLUMN activated SET DEFAULT 1;


ALTER TABLE object_service_up ADD COLUMN count integer;
ALTER TABLE object_service_up ALTER COLUMN count SET DEFAULT 1;

ALTER TABLE object_service_up ADD COLUMN activated integer;
ALTER TABLE object_service_up ALTER COLUMN activated SET DEFAULT 1;


ALTER TABLE object_service_photocard ADD COLUMN count integer;
ALTER TABLE object_service_photocard ALTER COLUMN count SET DEFAULT 1;

ALTER TABLE object_service_photocard ADD COLUMN activated integer;
ALTER TABLE object_service_photocard ALTER COLUMN activated SET DEFAULT 1;

update object_service_photocard set count = 1, activated = 1;
update object_service_up set count = 1, activated = 1;
update object_rating set count = 1, activated = 1;

ALTER TABLE object_service_photocard ADD COLUMN cities integer[];
ALTER TABLE object_service_photocard ADD COLUMN categories integer[];

CREATE INDEX object_service_photocard_categories_idx
  ON object_service_photocard
  USING gin
  (categories, cities)
  WHERE active = 1;
