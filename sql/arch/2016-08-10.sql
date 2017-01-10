CREATE TABLE public.seo_popular_query (
    id serial NOT NULL,
    query varchar(500),
    city_id int4,
    count int4 default 0,
    CONSTRAINT seo_popular_query_pkey PRIMARY KEY (id)
)
WITH (
    OIDS=FALSE
);


CREATE TABLE public.seo_popular_query_object (
    id serial NOT NULL,
    query_id int4,
    object_id int4,
    CONSTRAINT seo_popular_query_object_pkey PRIMARY KEY (id)
)
WITH (
    OIDS=FALSE
);

CREATE INDEX seo_popular_query_object_idx
  ON seo_popular_query_object
  USING btree
  (object_id);