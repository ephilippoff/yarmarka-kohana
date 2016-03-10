create table user_object_stats (
	id serial primary key
	, object_id int not null
	, user_id int not null
	, date_start int
	, date_end int
)