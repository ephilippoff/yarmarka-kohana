-- Index: object_fast_select_for_main_page

-- DROP INDEX object_fast_select_for_main_page;

CREATE INDEX idx_object_fast_select_for_main_page
  ON object
  USING btree
  (is_published, moder_state, not_show_on_index, city_id, date_expired, category);

-- Index: city_seo_name

-- DROP INDEX city_seo_name;

CREATE INDEX idx_city_seo_name
  ON city
  USING btree
  (seo_name COLLATE pg_catalog."default");

-- Index: data_list_value

-- DROP INDEX data_list_value;

CREATE INDEX idx_data_list_value
  ON data_list
  USING btree
  (value);


-- Index: data_list_object_value

-- DROP INDEX data_list_object_value;

CREATE INDEX data_list_object_value
  ON data_list
  USING btree
  (object, value);

