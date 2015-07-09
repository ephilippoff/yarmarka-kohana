<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Special controller for internal sub requests (HMVC)
 * 
 * @uses Controller
 * @uses _Template
 * @package 
 * @copyright 2013
 * @author Mikhail Makeev <mihail.makeev@gmail.com> 
 * @version $id$
 */
class Controller_Block_Twig extends Controller_Block
{

    public function before()
    {
        parent::before();
        $this->auto_render = FALSE;
    }

    public function action_topline()
    {
        $twig = Twig::factory('block/header/topline');
        $twig->user = Auth::instance()->get_user();
        $this->response->body($twig);
    }

    public function action_logoline()
    {
        $twig = Twig::factory('block/header/logoline');
        $this->response->body($twig);
    }

    public function action_adslinkline()
    {
        $twig = Twig::factory('block/header/adslinkline');
        $twig->links = $this->adslinkline();
        $this->response->body($twig);
    }


    public function action_navigationline()
    {
        $twig = Twig::factory('block/header/navigationline');
        $this->response->body($twig);
    }

    ////// Реализация содержимого блоков

    public function adslinkline($city_id = null, $category_id = null)
    {
        $reklama = ORM::factory('Reklama')
                        ->where(DB::expr('CURRENT_DATE'), '>=', DB::expr('start_date') )
                        ->where(DB::expr('CURRENT_DATE'), '<=', DB::expr('end_date') )
                        ->where('active', '=',  1);
        if ($city_id) {
             $reklama =  $reklama->where((int) $city_id, "=", "ANY(cities)");
        }

        if ($category_id) {
             $reklama =  $reklama->where((int) $category_id, "=", "ANY(categories)");
        }

        return $reklama->getprepared_all();
    }

}