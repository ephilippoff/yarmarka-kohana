create table pretty_url
(
	id serial
	, pretty varchar(255) not null
	, ugly varchar(255) not null
	, title varchar(255)
	, h1 varchar(255)
	, description text
	, footer text
	, keywords text
)