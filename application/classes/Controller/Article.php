<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Article extends Controller_Template {

	public function before()
	{
		parent::before();
	}

	public function action_index()
	{
		$article = ORM::factory('Article')
			->where('seo_name', '=', $this->request->param('seo_name'))
			->find();
		if ( ! $article->loaded())
		{
			throw new HTTP_Exception_404;
		}

		Seo::set_title($article->title.Seo::get_postfix());
		Seo::set_description($article->get_meta_description());

		$this->template->article = $article;
	}
}

/* End of file Article.php */
/* Location: ./application/classes/Controller/Article.php */