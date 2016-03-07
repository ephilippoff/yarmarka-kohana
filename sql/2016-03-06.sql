CREATE TABLE wiki
(
  id serial NOT NULL,
  url character varying(500),
  city character varying(500),
  CONSTRAINT wiki_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE wiki
  OWNER TO yarmarka_biz;