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

		public function selectMainImage($query) {
			$query
				->select(array('object_attachment.filename', 'main_image_filename'))
				->join('object_attachment', 'left')
				->on('object_attachment.id', '=', 'object.main_image_id');
		}

		public function selectPublished($query, $value = 1) {
			$query
				->where('object.is_published', '=', $value)
				->where('object.date_expired', '<=', DB::expr('NOW()'));
		}

		public function filterOnlyPremium($query) {
			$query
				->join('object_rating', 'inner')
				->on('object_rating.object_id', '=', 'object.id')
				->where('object_rating.date_expiration', '>', DB::expr('NOW()'));
		}

		public function selectCategoryUrl($query) {
			$query
				->join('category', 'inner')
				->on('category.id', '=', 'object.category')
				->select(array('category.url', 'category_url'));
		}

		/* helpers */
		public function getUrl($item) {
			return '/' . $item['category_url'] . '/' . $item['seo_name'] 
				. '-' . $item['id'] . '.html';
		}

	}