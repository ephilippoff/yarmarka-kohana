<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Articles extends Controller_Admin_Template {

	protected $module_name = 'articles';

	public function action_index()
	{
		$this->template->articles = ORM::factory('Article')->get_articles_tree();
	}
	
	public function action_news()
	{
		$limit  = Arr::get($_GET, 'limit', 30);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;		
			
		$news = DB::select()
				->from('articles')
				->join('email_campaign_statistic', 'LEFT')
				->on(DB::expr('(\'newsone_\' || articles.id)'), '=', 'email_campaign_statistic.campaign_name')
				->where('text_type', '=', 2);
		
		$clone_to_count = clone $news;
		$count_all = $clone_to_count->select(array(DB::expr('COUNT(*)'), 'total'))->execute();
	
		$news->select('articles.*', 'email_campaign_statistic.visits')->limit($limit)->offset($offset)->order_by('is_category', 'DESC')->order_by('created', 'DESC');		
	
		$this->template->news = $news->execute();
		
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all[0]['total'],
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'articles',
				'action'     => 'news',
			));		
	}	

	public function action_add()
	{
		$this->template->errors = array();

		$this->template->articles = ORM::factory('Article')->get_articles_flat_list(0, 0, 1);
		$this->template->news	  = ORM::factory('Article')->get_articles_flat_list(0, 0, 2, true);
		$this->template->cities	  = ORM::factory('City')
										->where('is_visible', '=', 1)
										->where('region_id', '=', kohana::$config->load('common.default_region_id'))
										->order_by('title')->find_all()
										->as_array('id','title');
			
		//Умолчательный вариант типа текста: 1 - статья, 2 - новость
		$this->template->text_type_default = 1;

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;
				
				$redirect_to = 'index';
				//Если новость
				if ($this->request->post('text_type') == 2)
				{
					$post['parent_id'] = isset($post['news_parent_id']) ? $post['news_parent_id'] : 0;
					$redirect_to = 'news';
					if (!empty($_FILES['photo']['tmp_name'])) $post['photo'] = @Uploads::save($_FILES['photo']);				
				}	
				elseif ($this->request->post('text_type') == 1)
				{
					$post['parent_id'] = isset($post['article_parent_id']) ? $post['article_parent_id'] : 0;
				}
				
				if ($this->request->post('cities'))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}
				
				ORM::factory('Article')->values($post)
				->save();				

				$this->redirect('khbackend/articles/'.$redirect_to);
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
	}

	public function action_edit()
	{
		$this->template->errors = array();

		$this->template->articles = ORM::factory('Article')->get_articles_flat_list(0, 0, 1);
		$this->template->news	  = ORM::factory('Article')->get_articles_flat_list(0, 0, 2, true);
		$this->template->cities	  = ORM::factory('City')
										->where('is_visible', '=', 1)
										->where('region_id', '=', kohana::$config->load('common.default_region_id'))
										->order_by('title')->find_all()
										->as_array('id','title');		

		$article = ORM::factory('Article', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try
			{
				if (empty($_POST['is_category']))
				{
					$_POST['is_category'] = 0;
				}
				
				if (empty($_POST['is_visible']))
				{
					$_POST['is_visible'] = 0;
				}	
				
				$post = $_POST;
				
				$redirect_to = 'index';
				
				if ($article->text_type == 2)
				{
					$post['parent_id'] = isset($post['news_parent_id']) ? $post['news_parent_id'] : 0;
					$redirect_to = 'news';
					if (!empty($_FILES['photo']['tmp_name'])) $post['photo'] = @Uploads::save($_FILES['photo']);					
				}	
				elseif ($article->text_type == 1)
				{
					$post['parent_id'] = isset($post['article_parent_id']) ? $post['article_parent_id'] : 0;
				}
				
				if ($this->request->post('cities'))
				{
					$post['cities'] = '{'.join(',', $post['cities']).'}';	
				}	
				
				$article->values($post)
				->save();

				$this->redirect('khbackend/articles/'.$redirect_to);
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->article = $article;
	}

	public function action_delete()
	{
		$this->auto_render = FALSE;

		$article = ORM::factory('Article', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$article->delete();

		$this->response->body(json_encode(array('code' => 200)));
	}

	public function action_wiki()
	{
		$limit  = Arr::get($_GET, 'limit', 30);
		$page   = $this->request->query('page');
		$offset = ($page AND $page != 1) ? ($page-1)*$limit : 0;		
			
		$wiki = DB::select()
				->from('wiki');
		
		$clone_to_count = clone $wiki;
		$count_all = $clone_to_count->select(array(DB::expr('COUNT(*)'), 'total'))->execute();
		
		$wiki->select('*')->limit($limit)->offset($offset)->order_by('id', 'DESC');		
		
		$this->template->wiki = $wiki->execute();
		
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all[0]['total'],
				'items_per_page' => $limit,
				'auto_hide'      => TRUE,
				'view'           => 'pagination/bootstrap',
			))->route_params(array(
				'controller' => 'articles',
				'action'     => 'news',
			));
	}

	public function action_wiki_add()
	{
		$this->template->errors = array();


		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;
				
				$redirect_to = 'wiki';
				
				ORM::factory('Wiki')->values($post)->save();				

				$this->redirect('khbackend/articles/'.$redirect_to);
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
	}

	public function action_wiki_edit()
	{
		$this->template->errors = array();



		$article = ORM::factory('Wiki', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try
			{			
				$post = $_POST;
				
				$redirect_to = 'wiki';
				
				ORM::factory('Wiki')->values($post)->save();				

				$this->redirect('khbackend/articles/'.$redirect_to);
			}
			catch(ORM_Validation_Exception $e)
			{
				$this->template->errors = $e->errors('validation');
			}
		}

		$this->template->article = $article;
	}

	public function action_delete_wiki()
	{
		$this->auto_render = FALSE;

		$article = ORM::factory('Wiki', $this->request->param('id'));
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		$article->delete();

		$this->response->body(json_encode(array('code' => 200)));
	}



	
}

/* End of file Articles.php */
/* Location: ./application/classes/Controller/Admin/Articles.php */