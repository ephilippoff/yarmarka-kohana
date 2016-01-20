<?php

	class Services_Categories {

		public function getCategories() {
			return DB::select('category.*')->from('category');
		}

		public function selectTopLevel($query) {
			$query->where('category.parent_id', '=', 1);
		}

		public function filterByParentIds($query, $parentIds) {
			if (!is_array($parentIds)) {
				$parentIds = array( $parentIds );
			}

			$query->where('parent_id', 'in', $parentIds);
		}
	}