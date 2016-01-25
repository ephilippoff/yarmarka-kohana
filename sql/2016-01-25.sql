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