-- Table: search_url_cache

-- DROP TABLE search_url_cache;

CREATE TABLE search_url_cache
(
  id serial NOT NULL,
  hash character varying(40),
  url character varying,
  params text,
  created_on timestamp without time zone DEFAULT now(),
  sql character varying,
  hash_sql character varying(40),
  count integer,
  canonical_url character varying,
  CONSTRAINT search_url_cache_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE search_url_cache
  OWNER TO yarmarka_biz;

-- Index: search_url_cache_idx

-- DROP INDEX search_url_cache_idx;

CREATE INDEX search_url_cache_idx
  ON search_url_cache
  USING btree
  (hash COLLATE pg_catalog."default");

-- Index: search_url_cache_sql_idx

-- DROP INDEX search_url_cache_sql_idx;

CREATE INDEX search_url_cache_sql_idx
  ON search_url_cache
  USING btree
  (hash_sql COLLATE pg_catalog."default");


-- Table: seo

-- DROP TABLE seo;

CREATE TABLE seo
(
  id serial NOT NULL,
  hash character varying(40) NOT NULL,
  url character varying NOT NULL,
  city_id integer DEFAULT 0,
  category_id integer,
  pattern_id integer DEFAULT 0,
  params text,
  CONSTRAINT seo_pk PRIMARY KEY (id),
  CONSTRAINT seo_hash UNIQUE (hash)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE seo
  OWNER TO yarmarka_biz;

-- Index: seo_hash_idx

-- DROP INDEX seo_hash_idx;

CREATE INDEX seo_hash_idx
  ON seo
  USING btree
  (hash COLLATE pg_catalog."default");
