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



ALTER TABLE reklama ADD COLUMN object_title character varying(500);

ALTER TABLE subscription_surgut ADD COLUMN filters text;


ALTER TABLE subscription_surgut ADD COLUMN last_object_id int4;
ALTER TABLE subscription_surgut ADD COLUMN empty_counter int4;
ALTER TABLE subscription_surgut ADD COLUMN sent_on timestamp;

ALTER TABLE public.subscription_surgut ALTER COLUMN created SET DEFAULT NOW();
ALTER TABLE public.subscription_surgut ALTER COLUMN sent_on SET DEFAULT NOW();


