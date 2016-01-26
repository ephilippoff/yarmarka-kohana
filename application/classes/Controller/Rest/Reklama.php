<?php
	
	class Controller_Rest_Reklama extends Controller_Rest {

		public function action_click() {

			if (!isset($this->post->id)) {
				throw new Exception('id');
			}

			//load model
			$reklama = ORM::factory('Reklama', $this->post->id);
			if (!$reklama->loaded()) {
				throw new Exception('Not found!');
			}

			//increase clicks count
			$reklama->clicks_count++;
			$reklama->save();

			//load linkstat
			$linkstat = ORM::factory('Reklama_Linkstats')
				->where('reklama_id', '=', $reklama->id)
				->where('date', '=', DB::expr('CURRENT_DATE'))
				->find();

			if (!$linkstat->loaded()) {
				$linkstat->reklama_id = $reklama->id;
				$linkstat->date = date('Y-m-d');
			}

			$linkstat->clicks_count = !$linkstat->clicks_count ? 1 : ($linkstat->clicks_count + 1);
			$linkstat->save();

		}

	}