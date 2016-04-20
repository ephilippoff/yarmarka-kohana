
-- drop table subscription_surgut;
 create table subscription_surgut (
 	id serial primary key,
 	data text not null,
 	created timestamp not null,
 	user_id int not null,
 	query text,
 	path text,
 	enabled int not null default 0
 )

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
