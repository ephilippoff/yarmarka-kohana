<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Article extends Controller_Template {

	public function before()
	{
		
		parent::before();

		$this->use_layout   = FALSE;
		$this->auto_render  = FALSE;
		

		$this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
        $this->city = $this->domain->get_city();
		

		if ($client_email = trim($this->request->query('em_client_email')) 
			AND $campaign_name = trim($this->request->query('em_campaign_name')))
		{
			$stat = ORM::factory('Email_Campaign_Statistic')
				->where('client_email', '=', $client_email)
				->where('campaign_name', '=', $campaign_name)
				->find();
			if ($stat->loaded())
			{
				$stat->visits += 1;
				$stat->save();
			}
			else
			{
				$stat->url 				= URL::site($_SERVER['REQUEST_URI'], 'http');
				$stat->client_email 	= $client_email;
				$stat->campaign_name 	= $campaign_name;
				if ($sended_on = $this->request->query('em_sendedon') AND strtotime($sended_on))
				{
					$stat->sended_on = date('Y-m-d H:i:s', strtotime($sended_on));
				}
				if ($campaign_id = $this->request->query('em_campaign_id') AND is_numeric($campaign_id))
				{
					$stat->campaign_id = intval($campaign_id);
				}
				$stat->save();
			}
		}
	}

	public function action_index()
	{
		$twig = Twig::factory('article/index');
		
		$article = ORM::factory('Article')
			->where('seo_name', '=', $this->request->param('seo_name'))
			->where('is_visible', '=', 1)
			->where('text_type', '=', 1)	
			->find();
		
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}	
				
		Seo::set_title($article->title.Seo::get_postfix());
		Seo::set_description($article->get_meta_description());

		$twig->articles = ORM::factory('Article')
				->where('is_visible', '=', 1)
				->where('text_type', '=', 1)
				->find_all();

		$twig->article = $article;

		$this->response->body($twig);
	}

	public function action_newsline()
	{
		$twig = Twig::factory('article/newsline');
		$date = $this->request->param('date');

		$date_from = date_create($date);
		$date_to = date_create($date);
		date_add($date_to, date_interval_create_from_date_string('1 day'));

		$articles = ORM::factory('Article')
			->where('start_date', '>=', date_format($date_from, 'Y-m-d'))
			->where('start_date', '<', date_format($date_to, 'Y-m-d'))
			->where('is_visible', '=', 1)
			->where('text_type', '=', 2)
			->where('parent_id', '<>', 0)
			->where('is_category', '=', 0)
			->order_by("start_date", "desc");

		$twig->city = $this->city;
		$city_id = ($this->city) ? $this->city->id : NULL;
		if ($city_id) {
			$articles = $articles->where(DB::expr($city_id), '=', DB::expr('ANY(cities)'));
		}
		$articles= $articles->getprepared_all();

		$twig->months = Date::get_months_names();
        $twig->lastnews  = ORM::factory('Article')
                                ->get_lastnews($city_id, NULL, 30)
                                ->getprepared_all();

		$twig->date = date_format($date_from, 'Y-m-d');
		$twig->articles = $articles;
		$this->response->body($twig);
	}
	
	public function action_ourservices()
	{
		$this->layout = 'article_ourservices';

		
		$article = ORM::factory('Article')
			->where('seo_name', '=', $this->request->param('seo_name'))
			->where('is_visible', '=', 1)
			->where('text_type', '=', 3)	
			->find();
		
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}
		
		//Определяем template
		if (Kohana::find_file('views/article/ourservices', trim($article->meta)))
			$this->template = View::factory('article/ourservices/'.trim($article->meta));
		else
			$this->template = View::factory('article/ourservices/ourservices_one');
		
		//Подбираем шаблон меню
		$this->template->menu = Kohana::find_file('views/article/ourservices', '_menu_'.trim($article->name)) 
			? $this->template->menu = View::factory('article/ourservices/'.'_menu_'.trim($article->name)) 
			: '';
				
		Seo::set_title($article->title.Seo::get_postfix());
		Seo::set_description($article->get_meta_description());

		$this->template->set_global('article_name', $article->name);
		$this->template->article = $article;		
	}
	
	public function action_newsone()
	{
		$twig = Twig::factory('article/index');

		$newsone = ORM::factory('Article')
			->where('id', '=', (int)$this->request->param('id'))
			->where('is_visible', '=', 1)
			->where('text_type', '=', 2)					
			->find();
	
		if ( ! $newsone->loaded())
		{
			throw new HTTP_Exception_404;
		}	
				
		Seo::set_title($newsone->title.Seo::get_postfix());
		Seo::set_description($newsone->get_meta_description());		
		
		$city_id = Region::get_current_city();
		$parent_name = $newsone->article->name;
	
		$photo = Imageci::getSavePaths($newsone->photo);												
		$real_photo = is_file($_SERVER['DOCUMENT_ROOT'].trim($photo['341x256'], '.')) ? trim($photo['341x256'], '.') : ''; 		
		$other_news = array();
		
		if ($newsone->is_category == 0)
		{
			$twig->months = Date::get_months_names();
			$other_news = ORM::factory('Article')
					->where('text_type', '=', 2)
					->where('is_category', '=', 0)
					->where('is_visible', '=', 1)
					->where('parent_id', '=', $newsone->parent_id)
					//->where('start_date', '<=', DB::expr('now()'))
					//->where('end_date', '>=', DB::expr('now()'))
					->where('id', '<>', $newsone->id)
					->order_by('created', 'desc')
					->limit(6);
			
			$city_id = ($this->city) ? $this->city->id : NULL;
			if ($city_id) {
				$other_news = $other_news->where(DB::expr($city_id), '=', DB::expr('ANY(cities)'));
			}
			$other_news = $other_news->getprepared_all();
		}
		
		$twig->set_global('is_news_page', 1);
		$twig->other_news = $other_news;
		$twig->count_other_news = count($other_news);
		$twig->real_photo = $real_photo;
		$twig->news_rubrics = ORM::factory('Article')->get_final_news_rubrics();			
		$twig->newsone = $newsone;
		$twig->parent_rubric = ORM::factory('Article')
			->where('id', '=', (int)$newsone->parent_id)				
			->find();
		$twig->parent_name = $parent_name;
		$this->response->body($twig);
	}	
	
	public function action_news()
	{	
		$this->template->news_rubrics = ORM::factory('Article')->get_news_rubrics();
	}
}

/* End of file Article.php */
/* Location: ./application/classes/Controller/Article.php */