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

		public function getCategoryWithChilds($parent_id, $lvl = 5) {
			//select root level
			$query = DB::select(array('c0.title', 'c0_title'))
				->select(array('c0.id', 'c0_id'))
				->from(array('category', 'c0'));

			if (is_array($parent_id)) {
				$query->where('c0.id', 'in', $parent_id);
			} else {
				$query->where('c0.id', '=', $parent_id);
			}

			for($i = 1;$i < $lvl;$i++) {
				$query
					->join(array('category', 'c' . $i), 'left')
					->on('c' . $i . '.parent_id', '=', 'c' . ($i - 1) . '.id')
					->select(array('c' . $i . '.title', 'c' . $i . '_title'))
					->select(array('c' . $i . '.id', 'c' . $i . '_id'));
			}

			$rows = $query->execute();

			$res = array();
/*
			foreach($rows as $row) {
				$resPointer = &$res;
				for($i = 0; $i < $lvl;$i++) {
					$id = $row['c' . $i . '_id'];
					if (!$id) {
						break;
					}
					if (!array_key_exists($id, $resPointer)) {
						$resPointer[$id] = array(
								'id' => $id,
								'title' => $row['c' . $i . '_title'],
								'childs' => array()
							);
					}
					$resPointer = &$resPointer[$id]['childs'];
				}
			}
*/

			foreach($rows as $row) {
				for($i = 0; $i < $lvl;$i++) {
					$id = $row['c' . $i . '_id'];
					if (!$id) {
						break;
					}
					$res[$id] = true;
				}
			}


			return array_keys($res);
		}
	}