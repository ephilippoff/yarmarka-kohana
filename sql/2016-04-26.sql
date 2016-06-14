CREATE TABLE settings
(
  id serial NOT NULL,
  name character varying(255),
  value character varying(255),
  CONSTRAINT settings_id_primary PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE settings
  OWNER TO yarmarka_biz;