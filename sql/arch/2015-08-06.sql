DROP INDEX object_statistic1_idx_date_object;

CREATE INDEX object_statistic1_idx_date_object
  ON object_statistic1
  USING btree
  (date, object_id);