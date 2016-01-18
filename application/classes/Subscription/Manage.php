<?php

	class Subscription_Manage {

		public static function get_subscription_info_by_url($path, $query) {
			$path = ltrim($path, '/');
			$search_url = new Search_Url($path, $query);
			$clean_query_params = array_merge($search_url->get_clean_query_params(), $search_url->get_seo_filters());
			$domain = new Domain();
			$reserved_query_params = $search_url->get_reserved_query_params();

			//var_dump($clean_query_params);
			//var_dump($search_url);
			//var_dump($reserved_query_params);
			//die;

			$seo_data = Seo::get_seo_attributes($path, $clean_query_params, $search_url->get_category(), $domain->get_city());

			$category = $search_url->get_category();
			$title = $seo_data['h1'] ? $seo_data['h1'] : $category->title;
			$attributes_meta = self::get_attribites_meta($clean_query_params);
			$search_text = array_key_exists('search', $reserved_query_params)
				? $reserved_query_params['search'] 
				: '';
			$with_photo = array_key_exists('photo', $reserved_query_params) && $reserved_query_params['photo'];
			$only_private = array_key_exists('private', $reserved_query_params) && $reserved_query_params['private'];
			$user_email = Auth::instance()->get_user()->email;

			$res = new Obj();
			$res->attributes = $attributes_meta;
			$res->category_id = $category->id;
			$res->title = $title;
			$res->search_text = $search_text;
			$res->with_photo = $with_photo;
			$res->only_private = $only_private;
			$res->email = $user_email;

			return $res;
		}

		public static function get_attribites_meta($attribites) {

			/* get attributes */
			$attributesRaw = ORM::factory('Attribute')
				->where('seo_name', 'in', array_keys($attribites))
				->find_all();
			$attributes = array();
			foreach($attributesRaw as $attribute) {
				$attributesMeta[$attribute->seo_name] = array(
						'title' => $attribute->title,
						'type' => $attribute->type,
						'value' => $attribites[$attribute->seo_name]
					);
			}

			/* get attributes list values */
			$elementsIdsToGet = array();
			foreach($attributesMeta as $key => $value) {
				if ($value['type'] != 'list' || $value === NULL) {
					continue;
				}

				if (is_array($value['value'])) {
					$elementsIdsToGet = array_merge( $elementsIdsToGet, $value['value'] );
				} else {
					$elementsIdsToGet []= $value['value'];
				}
			}

			$elementsRaw = ORM::factory('Attribute_Element')
				->where('id', 'in', $elementsIdsToGet)
				->find_all();
			$elements = array();
			foreach($elementsRaw as $element) {
				$elements[$element->id] = $element->title;
			}

			/* replace ids with values */
			foreach($attributesMeta as $key => &$value) {
				if ($value['type'] != 'list' || $value === NULL) {
					continue;
				}

				if (is_array($value['value'])) {
					foreach($value['value'] as $index => $item) {
						$value['value'][$index] = array(
								'id' => $value['value'][$index],
								'title' => $elements[$value['value'][$index]]
							);
					}
				} else {
					$value['value'] = array(
							'id' => $value['value'],
							'title' => $elements[$value['value']]
						);
				}
			}

			return $attributesMeta;

		}

	}