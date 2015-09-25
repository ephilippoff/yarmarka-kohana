
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

INSERT INTO module (name, class)
SELECT 'Купоны', 'kupon'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'kupon');

INSERT INTO module (name, class)
SELECT 'Reference', 'reference'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'reference');

INSERT INTO module (name, class)
SELECT 'Core redirect', 'coreredirect'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'coreredirect');

INSERT INTO module (name, class)
SELECT 'Invoice', 'invoice'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'invoice');

INSERT INTO module (name, class)
SELECT 'Seo pattern', 'seopattern'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'seopattern');

INSERT INTO module (name, class)
SELECT 'Object reason', 'object_reason'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'object_reason');

INSERT INTO module (name, class)
SELECT 'Sms', 'sms'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'sms');

INSERT INTO module (name, class)
SELECT 'Подписки', 'subscription'
WHERE NOT EXISTS (SELECT 1 FROM module WHERE class = 'subscription');

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'kupon') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'kupon'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'kupon') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'kupon'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'reference') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'reference'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'reference') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'reference'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'coreredirect') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'coreredirect'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'coreredirect') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'coreredirect'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'invoice') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'invoice'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'invoice') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'invoice'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'seopattern') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'seopattern'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'seopattern') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'seopattern'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'object_reason') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'object_reason'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'object_reason') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'object_reason'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'sms') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'sms'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'sms') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'sms'));

INSERT INTO role_module (role, module)
SELECT 1, (SELECT id FROM module WHERE class = 'subscription') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 1 and module = (SELECT id FROM module WHERE class = 'subscription'));

INSERT INTO role_module (role, module)
SELECT 3, (SELECT id FROM module WHERE class = 'subscription') as id
WHERE NOT EXISTS (SELECT 1 FROM role_module WHERE role = 3 and module = (SELECT id FROM module WHERE class = 'subscription'));