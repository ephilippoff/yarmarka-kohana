CREATE TABLE public.object_service_email (
    id serial not null,
    object_id int4 not NULL,
    date_expiration timestamp not NULL,
    CONSTRAINT object_service_email_pkey PRIMARY KEY (id),
    CONSTRAINT object_service_email_fk FOREIGN KEY (object_id) REFERENCES public."object"(id) ON DELETE CASCADE ON UPDATE CASCADE
)
WITH (
    OIDS=FALSE
);

CREATE INDEX object_service_email_idx ON public.object_service_email (object_id);
