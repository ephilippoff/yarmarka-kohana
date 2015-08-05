<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rest extends Controller_Template 
{
    protected $json = array();

    public function before() 
    {
        //only ajax request is allowed
        if ( ! $this->request->is_ajax() AND Kohana::$environment !== Kohana::DEVELOPMENT)
        {
            throw new HTTP_Exception_404;
        }
        //disable global layout for this controller
        $this->use_layout = FALSE;
        $this->auto_render = FALSE;
        parent::before();

        $this->post = new Obj( (array) json_decode($this->request->body(), TRUE) );
        $this->get = new Obj( (array) $this->request->query() );
        $this->param = new Obj( (array) $this->request->param() );

        $this->json['code'] = 200; // code by default
    } 
     
    public function after() {
        $this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
        $this->response->headers('content-type', 'application/json'); 
        parent::after();
        $this->response->body(json_encode($this->json));
    } 
}