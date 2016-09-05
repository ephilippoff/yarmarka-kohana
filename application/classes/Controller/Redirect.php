<?php

class Controller_Redirect extends Controller_Template
{
	//Редирект для category_banners
	public function action_ref_cb()
	{					
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;		
		
		if (!$id = (int)$_REQUEST['id']) $this->redirect();
			
		$banner = ORM::factory('Category_Banners', $id);

		if ($banner->loaded() and !empty($banner->href)) 
		{ 
			ORM::factory('Category_Banners')->increase_visits($id);
			ORM::factory('Category_Banners_Stats')->increase_visits($id);
			$this->redirect(URL::prep_url($banner->href));
		}
		
		$this->redirect();
	}

	public function action_maitenance()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;		

		echo "Сайт в режиме обслуживания. Подождите немного и мы станем еще лучше";
	}

	public function action_old_link()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$path = $this->request->param("category_path");
		$path_segments = explode("/", $path);
		array_shift($path_segments);

		$path_detail_segments = explode("-", $path);
		if ( $object_id = (int) $path_detail_segments[count($path_detail_segments)-1] ) {
			HTTP::redirect_to_object($object_id, 301);
			return;
		} 

		$query="";
		if (isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING']) {
			$query = "?".$_SERVER['QUERY_STRING'];
		}
		// HTTP::redirect("/".join("/",$path_segments).$query, 301);
		

	 	$uri = $this->request->uri();
        $route_params = $this->request->param();
        $query_params = $this->request->query();

        $category_path = $route_params['category_path'];
        $c = explode('/', $category_path);
        array_shift($c);

        $category_path = join('/',$c);

        $this->domain = new Domain();
        if ($proper_domain = $this->domain->is_domain_incorrect()) {
            HTTP::redirect("http://".$proper_domain, 301);
        }
        
        try {
            $searchuri = new Search_Url($category_path, $query_params, ($this->domain->get_city()) ? $this->domain->get_city()->id : FALSE);
        } catch (Kohana_Exception $e) {
            //TODO Log incorrect seo
            //HTTP::redirect("/", 301);
            
            if (@$query_params['k']) {
                
                $query_params = array(
                    'search' => $query_params['k']
                );
                HTTP::redirect("?" . http_build_query($query_params), 301);
                return;
            } else {
                throw new HTTP_Exception_404;
            }
        }

		try {
		    $searchuri->check_uri_segments();
		} catch (Kohana_Exception_Withparams $e) {
		    $error_params = $e->getParams();
		    HTTP::redirect($error_params["uri"], $error_params["code"]);
		}

		try {
		    $searchuri->check_query_params($query_params);
		} catch (Kohana_Exception_Withparams $e) {
		    $searchuri->incorrectly_query_params_for_seo = TRUE;
		}


		HTTP::redirect("/".join("/",$path_segments).$query, 301);
	}
	
}
