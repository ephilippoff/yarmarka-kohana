CREATE TABLE data_additional
(
  id serial NOT NULL,
  object integer NOT NULL,
  value text,
  CONSTRAINT data_additional_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE data_additional
  OWNER TO yarmarka_biz;
