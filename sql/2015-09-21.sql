
ALTER TABLE public.category_banners
  DROP CONSTRAINT category_banners_fk RESTRICT;
  
ALTER TABLE public.category_banners
  ADD COLUMN menu_name VARCHAR(15);
  
ALTER TABLE public.category_banners
  ALTER COLUMN menu_name SET DEFAULT 'main';

COMMENT ON COLUMN public.category_banners.menu_name
IS 'main - основное,
kupons - купоны';

update category_banners set menu_name = 'main' 

ALTER TABLE public.category_banners
  ADD COLUMN menu_height SMALLINT;
  
ALTER TABLE public.category_banners
  ALTER COLUMN menu_height SET DEFAULT 0;