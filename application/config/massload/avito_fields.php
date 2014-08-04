<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
	'flat_resale'		=> array( 
									"Id" 			=> "external_id",
									"OperationType" => "tip-sdelki5", 
									"City" 			=> "city", 
									"Rooms" 		=> "flatrooms",
									"Floor" 		=> "etazh", 
									"Floors" 		=> "etazhnost",
									"Square"		=> "ploshchad",
									"Description" 	=> "user_text_adv",
									"ManagerName" 	=> "contact",
									"Price" 		=> "tsena",
									"EMail" 		=> "contact_0_value",
									"ContactPhone"  => "contact_1_value",
									"Street" 		=> "address",
									"Images" 		=> "images"
								),
	'house'				=> array( 
									"Id" 			=> "external_id",
									"OperationType" => "tip-sdelki3", 
									"City" 			=> "city", 
									"ObjectType" 	=> "dacha-chastnyi-dom",
									"Square" 		=> "ploshchad-doma", 
									"LandArea" 		=> "ploshchad-uchastka-v-sotkakh",
									"Description" 	=> "user_text_adv",
									"ManagerName" 	=> "contact",
									"Price" 		=> "tsena",
									"EMail" 		=> "contact_0_value",
									"ContactPhone"  => "contact_1_value",
									"Street" 		=> "address",
									"Images" 		=> "images"
								),
	'land'				=> array( 
									"Id" 			=> "external_id",
									"OperationType" => "tip-sdelki3", 
									"City" 			=> "city", 
									"ObjectType" 	=> "kategoriya-zemli",
									"LandArea" 		=> "ploshchad-uchastka-v-sotkakh",
									"Description" 	=> "user_text_adv",
									"ManagerName" 	=> "contact",
									"Price" 		=> "tsena",
									"EMail" 		=> "contact_0_value",
									"ContactPhone"  => "contact_1_value",
									"Street" 		=> "address",
									"Images" 		=> "images"
								)
);