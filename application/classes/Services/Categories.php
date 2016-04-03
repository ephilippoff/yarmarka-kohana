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

		public function getCategoryWithChilds($parent_id, $lvl = 5, $cols = array(), $mode = 1) {

			//append default columns
			$defaultCols = array( 'id', 'title' );
			foreach($defaultCols as $defaultCol) {
				if (!in_array($defaultCol, $cols)) {
					$cols = array_merge($cols, array( $defaultCol ));
				}
			}

			//select root level
			$query = DB::select()->from(array('category', 'c0'));

			foreach($cols as $col) {
				$query->select(array('c0.' . $col, 'c0_' . $col));
			}

			if (is_array($parent_id)) {
				$query->where('c0.id', 'in', $parent_id);
			} else {
				$query->where('c0.id', '=', $parent_id);
			}

			for($i = 1;$i < $lvl;$i++) {
				$query
					->join(array('category', 'c' . $i), 'left')
					->on('c' . $i . '.parent_id', '=', 'c' . ($i - 1) . '.id');

				foreach($cols as $col) {
					$query->select(array('c' . $i . '.' . $col, 'c' . $i . '_' . $col));
				}
			}

			$rows = $query->execute();

			$res = array();

			if ($mode != 1) {
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
			} else {
				foreach($rows as $row) {
					for($i = 0; $i < $lvl;$i++) {
						$id = $row['c' . $i . '_id'];
						if (!$id) {
							continue;
						}
						if (array_key_exists($id, $res)) {
							continue;
						}
						$res[$id] = array( 'childs' => array() );
						foreach($cols as $col) {
							$res[$id][$col] = $row['c' . $i . '_' . $col];
						}

						for($j = $i - 1;$j >= 0;$j--) {
							$pId = $row['c' . $j . '_id'];
							$res[$pId]['childs'] []= $id;
						}
					}
				}
			}


			return $res;
		}
	}