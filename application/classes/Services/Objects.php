<?php

	class Services_Objects {

		public function getObjects($select = 'object.*') {
			return DB::select($select)
				->from('object');
		}

		public function filterDataList($query, $values) {
			if (!is_array($values)) {
				$values = array( $values );
			}
			
			$query
				->select(array('data_list.value', 'data_list_value'))
				->join('data_list', 'inner')
				->on('data_list.object', '=', 'object.id')
				->where('data_list.value', 'in', $values);
		}

		public function selectMainImage($query, $require = false) {
			$query
				->select(array('object_attachment.filename', 'main_image_filename'))
				->join('object_attachment', $require ? 'inner' : 'left')
				->on('object_attachment.id', '=', 'object.main_image_id');
		}

		public function selectPublished($query, $value = 1) {
			$query
				->where('object.active', '=', 1)
				->where('object.is_published', '=', $value)
				->where('object.date_expired', '<=', DB::expr('NOW()'));
		}

		public function selectShowOnMain($query, $value = 0) {
			$query->where('object.not_show_on_index', '=', $value);
		}

		public function filterOnlyPremium($query) {
			$query
				->join('object_service_photocard', 'inner')
				->on('object_rating.object_id', '=', 'object.id')
				->where('object_rating.date_expiration', '>', DB::expr('NOW()'));
		}

		public function filterOnlyVip($query) {
			$query
				->join('object_rating', 'inner')
				->on('object_rating.object_id', '=', 'object.id')
				->where('object_rating.date_expiration', '>', DB::expr('NOW()'));
		}

		public function selectCategoryUrl($query) {
			$query
				->join('category', 'inner')
				->on('category.id', '=', 'object.category')
				->select(array('category.url', 'category_url'), array('category.title', 'category_title'));
		}

		public function filterPassedModeration($query) {
			$query->where('object.moder_state', '=', 1);
		}

		public function filterByCitySeoName($query, $citySeoName) {
			/*
			$query->where(DB::expr('(select id from city where seo_name = ' . Database::instance()->escape($citySeoName) . ' limit 1)'),
				'=', DB::expr('any(cities::int[])'));
			*/
			$this->filterByCityId($query, DB::expr('(select id from city where seo_name = ' . Database::instance()->escape($citySeoName) . ' limit 1)'));
		}

		public function filterByCityId($query, $cityId) {
			$query->where('city_id', '=', $cityId);
		}

		public function orderByCreated($query) {
			$query->order_by('date_expired', 'desc');
		}

		public function filterOnlyPriceExists($query) {
			$query->where('price', '>', 0);
		}

		public function withCategories($query, $categories) {
			$query->where('category', 'in', $categories);
		}

		public function withCategory($query, $categoryId) {
			$query->where('category', '=', $categoryId);
		}

		/* helpers */
		public function getUrl($item) {
			return '/' . $item['category_url'] . '/' . $item['seo_name'] 
				. '-' . $item['id'] . '.html';
		}

	}