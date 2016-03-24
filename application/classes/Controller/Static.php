<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Static extends Controller_Template {

	public function action_index()
	{
		$this->use_layout = FALSE;
		$this->template->data = Attribute::getData();
	}

	public function action_sitemap()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$domain = new Domain();
		if ($proper_domain = $domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}
		$subdomain = ($domain->get_city()) ? $domain->get_subdomain(): FALSE;
		
		if ($subdomain) {
			$filename = APPPATH."../sitemaps/$subdomain.sitemap.xml";
			if (!file_exists($filename)) {
				Task_Clear::sitemap_by_city($subdomain);
			}
		} else {
			$subtitle = $this->request->param("subtitle") ? $this->request->param("subtitle")."." : "";
			$filename = APPPATH."../sitemaps/".$subtitle."sitemap.xml";
			if (!file_exists($filename)) {
				Task_Clear::sitemap();
			}
		}
		$this->response->headers('Content-Type', 'text/xml');
		echo file_get_contents($filename);
	}

	public function action_yandex_feed(){
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->response->headers('Content-Type', 'text/xml');

		$vakancies_query = ORM::factory('Object_Compiled')
						->select("title","date_created", "date_updated","author_company_id", "contact", "location_id")
						->join("object")
							->on("object_id","=","object.id")
						->where("category","=",36)
						->where("active","=",1)
						->where("is_published","=",1)
						->order_by("id","desc")
						->limit(70)
						->find_all();
		$vakancies = array();
		$f = new Yfeed("vacancies");

		foreach ($vakancies_query as $vakancy_row) {
			$compile = unserialize($vakancy_row->compiled);
			$user = ORM::factory('User', $vakancy_row->author_company_id);
			$location = ORM::factory('Location',$vakancy_row->location_id);
			$attributes = $vakancy_row->naming_attributes($compile["attributes"]);			
			
			$f->single("url", "http://yarmarka.biz/detail/".$vakancy_row->object_id );
			$f->single("creation-date", date("Y-m-d H:i:s", strtotime($vakancy_row->real_date_created))." GMT+5" );
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
			
			$f->multiple("term", array( 
										"text" => $attributes["usloviya-raboty"] 
									));
			$f->multiple("requirement", array( 
										"qualification" => $attributes["trebovaniya-k-kandidatu"] 
									));
			$f->multiple("addresses", array( 
										"address" => array(
												"location" => $location->city.", ".$location->address,
												"lng" => $location->lon,
												"lat" => $location->lat
											)
									));
			$logo = null;
			if ($user->filename)
			{
				$logo = Imageci::getSitePaths($user->filename);
				$logo = URL::base("http").$logo["120x90"];
			}
			$company_data = array( 
									"name" => $user->org_name,
									"description" => $user->about,
									"logo" => $logo,
									"site" => null,
									"email" => null,
									"phone" => null,
									"contact-name" => $vakancy_row->contact,
									"hr-agency" => "false",
								);

			foreach ($compile["contacts"] as $contact) {
				if ($contact["type"] == 5)
					$company_data["email"] = $contact["value"];
				else
					$company_data["phone"] = $contact["value"];
			}

			if ($user->org_type == 2 and $user->org_name and $user->about)
			{
				$f->multiple("company", $company_data);
				$f->compile();
			}
			elseif ($user->about)
			{
				$f->multiple("anonymous-company", $company_data);
				$f->compile();
			}
			
			$f->reset();
			
		}
		echo $f->save();
		//echo Debug::vars($f->compile($vakancies));
	}

	public function action_reklamodatelyam() {
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$articles_root = 'uploads/articles/';

		$filename = DOCROOT.$articles_root.$this->request->param("path").".html";

		$html = '';

		if (file_exists($filename)) {
			$html = file_get_contents($filename);
		} else {
			throw new HTTP_Exception_404;
		}
		
		$this->response->body($html);
	}

	public function action_reklamodatelyam_static() { 
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$articles_root = 'uploads/articles/';

		$filename = DOCROOT.$articles_root.$this->request->param("path");

		$path_parts = pathinfo($filename);
		$mime_type =File::mime_by_ext($path_parts['extension']);

		$this->response->headers('Content-Type', $mime_type);
		$this->response->body(file_get_contents($filename));

	}
}