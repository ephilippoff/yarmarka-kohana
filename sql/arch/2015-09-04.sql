ALTER TABLE public.category_banners
  ADD COLUMN menu_height SMALLINT;

ALTER TABLE public.category_banners
  ALTER COLUMN menu_height SET DEFAULT 0;