<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Barcode extends Controller_Template {

	public function before()
	{
		parent::before();
		
		$this->use_layout = false;
		$this->auto_render === false;
		
	}
	
	public function action_ean13() 
	{
		$code = $this->request->param('code');
		$barcode = new Image_Barcode_ean13();
		$barcode->draw($code, 'png');	
	}
}