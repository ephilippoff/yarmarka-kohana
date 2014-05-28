<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Reklama extends Controller_Admin_Template {

	protected $module_name = 'reklama';

	public function action_index()
	{
		$this->template->ads_list = ORM::factory('Reklama')->order_by('id')->find_all();		
	}
	
	public function action_add()
	{
		$this->template->errors = array();

		if (HTTP_Request::POST === $this->request->method()) 
		{
			try 
			{				
				$post = $_POST;			

				if (isset($_FILES['image']))
				{
					$post['image'] = $this->_save_image($_FILES['image']);
				}				
				
				ORM::factory('Reklama')->values($post)->save();				

				$this->redirect('khbackend/reklama/index');
			} 
			catch (ORM_Validation_Exception $e) 
			{
				$this->template->errors = $e->errors('validation');
			}
		}
	}
	
	public function action_delete()
	{
		$this->auto_render = FALSE;

		$ads_element = ORM::factory('Reklama', $this->request->param('id'));

		if ( ! $ads_element->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if (is_file(DOCROOT.'uploads/banners/'.$ads_element->image))
			unlink (DOCROOT.'uploads/banners/'.$ads_element->image);
		
		$ads_element->delete();
				
		$this->redirect('khbackend/reklama/index');

		//$this->response->body(json_encode(array('code' => 200)));
	}
	
    protected function _save_image($image)
    {
        if (
            ! Upload::valid($image) OR
            ! Upload::not_empty($image) OR
            ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif')))
        {
            return FALSE;
        }

        $directory = DOCROOT.'uploads/banners/';
 
        if ($file = Upload::save($image, NULL, $directory))
        {
            $filename = strtolower(Text::random('alnum', 20)).'.jpg';
 
            Image::factory($file)->save($directory.$filename);
 
            // Delete the temporary file
            unlink($file);
 
            return $filename;
        }
 
        return FALSE;
    }	

	
}

/* End of file Articles.php */
/* Location: ./application/classes/Controller/Admin/Articles.php */