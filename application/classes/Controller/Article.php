<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Article extends Controller_Template {

	public function before()
	{
		parent::before();
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
		//$this->assets->js('jquery.treeview.js');
		
		$article = ORM::factory('Article')
			->where('seo_name', '=', $this->request->param('seo_name'))
			->where('is_visible', '=', 1)
//			->where('text_type', '=', 1)	
			->find();
		
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}	
				
		Seo::set_title($article->title.Seo::get_postfix());
		Seo::set_description($article->get_meta_description());

		$this->template->articles = ORM::factory('Article')
				->where('is_visible', '=', 1)
				->where('text_type', '=', 1)
				->find_all();

		$this->template->article = $article;
	}
	
	public function action_newsone()
	{
		//$this->assets->js('jquery.treeview.js');
		$newsone = ORM::factory('Article')
			->where('seo_name', '=', $this->request->param('seo_name'))
			->where('is_visible', '=', 1)
			->where('text_type', '=', 2)					
			->find();
	
		if ( ! $newsone->loaded())
		{
			throw new HTTP_Exception_404;
		}	
				
		Seo::set_title($newsone->title.Seo::get_postfix());
		Seo::set_description($newsone->get_meta_description());		
	
		$this->template->other_news = ORM::factory('Article')
				->where('text_type', '=', 2)
				->where('is_category', '=', 0)
				->where('is_visible', '=', 1)
				->where('parent_id', '=', $newsone->parent_id)
				->where('start_date', '<', DB::expr('now()'))
				->where('end_date', '>', DB::expr('now()'))
				->where('id', '<>', $newsone->id)
				->order_by('created', 'desc')
				->limit(6)
				->find_all();

		$this->template->news_rubrics = ORM::factory('Article')->get_news_rubrics();			
		$this->template->newsone = $newsone;
	}	
	
	public function action_news()
	{	
		$this->template->news_rubrics = ORM::factory('Article')->get_news_rubrics();
	}
}

/* End of file Article.php */
/* Location: ./application/classes/Controller/Article.php */