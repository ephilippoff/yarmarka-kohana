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
		
		$news = ORM::factory('Article')->where('text_type', '=', 2);
		
		$clone_to_count = clone $news;
		$count_all = $clone_to_count->count_all();
		
		$this->template->news = $news->limit($limit)->offset($offset)->order_by('created', 'DESC')->find_all();
		
		$this->template->pagination	= Pagination::factory(array(
				'current_page'   => array('source' => 'query_string', 'key' => 'page'),
				'total_items'    => $count_all,
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

		$this->template->articles = ORM::factory('Article')->get_articles_flat_list();

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;
				
				$redirect_to = 'index';
				//Если новость
				if ($this->request->post('text_type') == 2)
				{
					$post['parent_id'] = 0;
					$redirect_to = 'news';
					if (!empty($post['photo'])) $post['photo'] = Uploads::save($_FILES['photo']);
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

		$this->template->articles = ORM::factory('Article')->get_articles_flat_list();

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
				
				if ($article->text_type == 2)
				{
					$post['parent_id'] = 0;
					$redirect_to = 'news';
					$post['photo'] = Uploads::save($_FILES['photo']);
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
}

/* End of file Articles.php */
/* Location: ./application/classes/Controller/Admin/Articles.php */