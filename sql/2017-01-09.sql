ALTER TABLE public.category ADD title_auto_if varchar(100) NULL;


CREATE TABLE public.object_statistic_all (
	id serial NOT NULL,
	period timestamp NOT NULL,
	statdata text NOT NULL,
	CONSTRAINT object_statistic_all_pkey PRIMARY KEY (id)
)
WITH (
	OIDS=FALSE
);
CREATE INDEX object_statistic_all_idx ON public.object_statistic_all(period);