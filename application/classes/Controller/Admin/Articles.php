<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Articles extends Controller_Admin_Template {

    protected $module_name = 'articles';

    public function action_index()
    {
        $this->template->articles = ORM::factory('Article')->get_articles_tree();
    }

    public function action_add()
    {
        $this->template->errors = array();

        $this->template->articles = ORM::factory('Article')->get_articles_flat_list();

        if (HTTP_Request::POST === $this->request->method()) 
        {
            try 
            {
                ORM::factory('Article')->values($_POST)
                    ->save();

                $this->redirect('khbackend/articles/index');
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
	    		$article->values($_POST)
	    			->save();

	    		$this->redirect('khbackend/articles/index');
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