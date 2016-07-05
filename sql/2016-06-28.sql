insert into attribute (title,seo_name,type)values('Цена за кв.м.','price-per-square','integer')

INSERT into reference (category, attribute, weight, is_required, is_main, is_seo_used)
	VALUES 
	(3, (select id from attribute where seo_name='price-per-square') , 0, 0, 0, 0),
	(96, (select id from attribute where seo_name='price-per-square') , 0, 0, 0, 0),
	(30, (select id from attribute where seo_name='price-per-square') , 0, 0, 0, 0),
	(34, (select id from attribute where seo_name='price-per-square') , 0, 0, 0, 0),
	(4, (select id from attribute where seo_name='price-per-square') , 0, 0, 0, 0);