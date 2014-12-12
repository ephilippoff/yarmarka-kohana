<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Static extends Controller_Template {

	function action_index()
	{
		$this->use_layout = FALSE;
		$this->template->data = Attribute::getData();
	}

	public function action_yandex_feed(){
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->response->headers('Content-Type', 'text/xml');

		$vakancies_query = ORM::factory('Object_Compiled')
						->select("title","date_created", "date_updated","author_company_id")
						->join("object")
							->on("object_id","=","object.id")
						->where("category","=",36)
						->where("active","=",1)
						->where("is_published","=",1)
						->order_by("id","desc")
						->limit(5)
						->find_all();
		$vakancies = array();
		$f = new Yfeed("vacancies");

		foreach ($vakancies_query as $vakancy_row) {
			$compile = unserialize($vakancy_row->compiled);
			$user = ORM::factory('User', $vakancy_row->author_company_id);
			$attributes = $vakancy_row->naming_attributes($compile["attributes"]);			
			
			$f->single("url", "http://yarmarka.biz/detail/".$vakancy_row->object_id );
			$f->single("creation-date", date("Y-m-d H:i:s", strtotime($vakancy_row->date_created))." GMT+5" );
			$f->single("update-date", ($vakancy_row->date_updated) ? date("Y-m-d H:i:s", strtotime($vakancy_row->date_updated))." GMT+5":NULL );
			$f->single("salary", $attributes["zarplata"] );
			$f->single("currency", "руб" );
			$f->multiple("category", array( 
										"industry" => $attributes["sfera-deyatelnosti"],
										"specialization" => NULL
									));
			$f->single("job-name", $attributes["professiya-dolzhnost"] );
			$f->single("employment", $attributes["grafik-raboty"] );
			$f->single("schedule", $attributes["tip-raboty"] );
			$f->single("description", $vakancy_row->title );
			$f->single("duty", $attributes["obyazannosti"]);
			$f->multiple("requirement", array( 
										"qualification" => $attributes["trebovaniya-k-kandidatu"] 
									));
			$f->multiple("term", array( 
										"text" => $attributes["usloviya-raboty"] 
									));
			$f->multiple("addresses", array( 
										"address" => array(
												"location" => $attributes["adres-raion"]
											)
									));
			$company_data = array( 
									"name" => $user->org_name,
									"description" => $user->about,
									"logo" => null,
									"site" => null,
									"email" => null,
									"phone" => null,
									"contact-name" => null,
									"hr-agency" => null,
								);

			foreach ($compile["contacts"] as $contact) {
				if ($contact["type"] == 5)
					$company_data["email"] = $contact["value"];
				else
					$company_data["phone"] = $contact["value"];
			}

			$f->multiple("company", $company_data);
			
			$f->compile();
			
			
		}
		echo $f->save();
		//echo Debug::vars($f->compile($vakancies));
	}
}