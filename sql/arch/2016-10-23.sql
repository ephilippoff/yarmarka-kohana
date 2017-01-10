ALTER TABLE public.order_item ALTER COLUMN service_id DROP NOT NULL;

ALTER TABLE public.order_item ALTER COLUMN service_id DROP DEFAULT;

ALTER TABLE order_item ADD COLUMN service_name character varying(255);