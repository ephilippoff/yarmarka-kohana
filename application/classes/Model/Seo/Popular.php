<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model_Service_Invoices 
 * 
 * @uses ORM
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Model_Seo_Popular extends ORM {

    protected $_table_name = 'seo_popular_query';

    public function with_city()
    {
        return $this->select(array( "city.title",  "city_title"))
                    ->join("city", "left")
                        ->on("seo_popular.city_id", "=", "city.id");
    }   


} // End Service_Invoices Model