<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Article extends ORM {
	public $level;

	protected $_created_column  = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column  = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');

	protected $_belongs_to = array(
		'article' => array('foreign_key' => 'parent_id'),
	);

	protected $_has_many = array(
		'articles' => array('foreign_key' => 'parent_id'),
	);

	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'),
			),
			'seo_name' => array(
				array('not_empty'),
				array(array($this, 'unique'), array('seo_name', ':value')),
			),
		);
	}

	public function filters()
	{
		return array(
			'title' => array(
				array('trim'),
			),
			'seo_name' => array(
				array('trim'),
			),
		);
	}

	/**
	 * Get flat articles array for html select
	 * 
	 * @param int $parent_id 
	 * @param int $level 
	 * @access public
	 * @return array
	 */
	public function get_articles_flat_list($parent_id = 0, $level = 0)
	{
		$result = array();
		$delimeter = '&rarr;';

		$articles = ORM::factory('Article')
			->where('parent_id', '=', intval($parent_id))
			->find_all();

		foreach ($articles as $article)
		{
			$result[$article->id] = str_repeat($delimeter, $level).$article->title;
			if ($article->articles->find_all()->count() > 0)
			{
				$result = Arr::merge($result, $this->get_articles_flat_list($article->id, $level+1));
			}
		}

		return $result;
	}

	public function get_articles_tree($parent_id = 0, $level = 0)
	{
		$result = array();

		$articles = ORM::factory('Article')
			->where('parent_id', '=', intval($parent_id))
			->find_all();

		foreach ($articles as $article)
		{
			$article_row_array = $article;
			$article_row_array->level = $level;

			$result[$article->id] = $article_row_array;
			if ($article->articles->find_all()->count() > 0)
			{
				$result = Arr::merge($result, $this->get_articles_tree($article->id, $level+1));
			}
		}

		return $result;
	}
}

/* End of file Article.php */
/* Location: ./application/classes/Model/Article.php */