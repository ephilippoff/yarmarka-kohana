<?php

	class Services_Attributes {

		public function getAttribiteValuesQuery($attribiteId) {

			return ORM::factory('Attribute_Element')
				->where('attribite', '=', $attribiteId);

		}

		public function getElementsByAttributeSeoNameQuery($seoName) {
			return ORM::factory('Attribute_Element')
				->join('attribute', 'inner')
				->on('attribute.id', '=', 'attribute_element.attribute')
				->where('attribute.seo_name', '=', $seoName);
		}

		private static $_types = array(
					'boolean',
					'date',
					'integer',
					'list',
					'numeric',
					'text'
				);
		public function getTypes() {
			return self::$_types; 
		}

		public function getTypeTable($type) {
			return 'data_' . $type;
		}

		public function joinValues($query, $joinKey1, $joinKey2, $join = 'inner', $types = NULL) {
			if ($types === NULL) {
				$types = $this->getTypes();
			}

			foreach($types as $type) {
				$ttn = $this->getTypeTable($type);
				$query
					->join($ttn, $join)
					->on($joinKey1, '=', ($ttn . '.') . $joinKey2);
			}

			return $query;
		}

	}