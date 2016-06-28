INSERT into reference (category, attribute, weight, is_required, is_main, is_seo_used)
	VALUES 
	(3, 360, 2, 1, 1, 0),
	(96, 360, 2, 1, 1, 0),
	(30, 360, 2, 1, 1, 0),
	(34, 360, 2, 1, 1, 0),
	(4, 360, 2, 1, 1, 0);

INSERT into attribute_relation (parent_id, category_id, parent_element_id, reference_id, weight)
	VALUES	
		(20, 3, 3250, 1004, 2),
		(20, 3, 3252, 1004, 2),
		(51, 3, 3240, 1005, 2),
		(70, 30, 3242, 1006, 2),
		(70, 30, 3240, 1006, 2),
		(115, 34, 3244, 1007, 2),
		(115, 34, 3242, 1007, 2),
		(102, 34, 3242, 1008, 2),
		(102, 34, 3244, 1008, 2);