<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP extends Kohana_HTTP {
 
   public static function redirect_to_object($object_id, $code = 302, $query = array())
   {
   		$object = ORM::factory('Object',$object_id);
   		$e = HTTP_Exception::factory($code);

   		if (!$object->loaded()) {
   			throw new HTTP_Exception_404;
   		} else {

            $query_str = (count(array_values($query)) > 0) ? '?'.http_build_query($query) : '';

   			throw $e->location($object->get_full_url().$query_str);
   		}

		if ( ! $e instanceof HTTP_Exception_Redirect)
			throw new Kohana_Exception('Invalid redirect code \':code\'', array(
				':code' => $code
			));

		throw $e->location($uri);
   }
}
