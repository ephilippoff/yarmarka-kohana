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

	public function action_sitemap_new() {
		$s = new Sitemap();
		$s->rebuild();
		echo 'OK';
		die;
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

		$path = $this->request->param("path");
		$path_segments = explode("/", $path);
		array_pop($path_segments);

		$articles_root = 'uploads/articles/';

		$filename = DOCROOT.$articles_root.$path.".html";

		$html = '';

		if (file_exists($filename)) {
			$html = file_get_contents($filename);
		} else {
			throw new HTTP_Exception_404;
		}

		$wiki = ORM::factory('Wiki')
					->where("url","=", $path)
					->getprepared_all();
		$access = (count(array_filter($wiki, function($item){
			return ($item->city == 'surgut' OR $item->city == '*');
		})) > 0);

		if (!$access) {
			throw new HTTP_Exception_404;
		}

		$templates_name = array(
			array("header.html", "header_template"), 
			array("footer.html", "footer_template")
		);
		
		$templates_info = array();
		foreach ($templates_name as $template_name) {
			$p = $this->get_template_path($articles_root, $path_segments, $template_name[0]);
			if ($p) {
				array_push($templates_info, 
					array( "path" =>$p, "name" => $template_name[0], "tmpl" => $template_name[1])
				);
			}
		}

		foreach ($templates_info as $template_info) {
			$tmpl = file_get_contents($template_info["path"]);
			$regexp = '/.*<'.$template_info['tmpl'].'><\/'.$template_info['tmpl'].'>.*/';
			$html = preg_replace($regexp, $tmpl, $html);
		}

		//<header_template></header_template>
		//<footer_template></footer_template>

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

	function get_template_path($articles_root, $segments, $name) {
		$header_path_segments = $segments;

		$header_filename = $articles_root.implode("/", $header_path_segments)."/".$name;
		while (!file_exists($header_filename) AND count($header_path_segments) > 0) {
			array_pop($header_path_segments);
			$header_filename = $articles_root.implode("/", $header_path_segments).$name;
			
		}

		if ( file_exists($header_filename) ) {
			return $header_filename;
		}

		return FALSE;
	}

	public function action_robots()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$domain = new Domain();
		if ($proper_domain = $domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}
		$subdomain = ($domain->get_city()) ? $domain->get_subdomain(): FALSE;

		$filename = DOCROOT.(($subdomain)?"robots.template.txt":"robots_main.template.txt");
		$robots_file = file_get_contents($filename);

		$robots_file = mb_ereg_replace ( '\{\$subdomain\}' , $subdomain , $robots_file);

		$this->response->headers('Content-Type', 'text/plain');
		$this->response->body($robots_file);
	}

	public function action_sitemaps()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$domain = new Domain();
		if ($proper_domain = $domain->is_domain_incorrect()) {
			HTTP::redirect("http://".$proper_domain, 301);
		}
		$subdomain = ($domain->get_city()) ? $domain->get_subdomain(): FALSE;
		$filename = DOCROOT.(($subdomain)?"sitemaps/".$subdomain."/index.xml":"sitemaps/index_main.xml");
		$robots_file = file_get_contents($filename);

		$this->response->headers('Content-Type', 'text/xml');
		$this->response->body($robots_file);
	}
}