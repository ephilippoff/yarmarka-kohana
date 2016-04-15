<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Commercial extends Controller_Template {

	protected $json = array();

	public function before()
	{
		parent::before();

		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->domain = new Domain();
		//$this->assets->css('bs_grid.css');
	}

	public function action_index()
	{

		$twig = Twig::factory('commercial/index');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям"
			)
		);

		$this->response->body($twig);
	}

	public function action_site()
	{

		$twig = Twig::factory('commercial/site');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте"
			)
		);

		$this->response->body($twig);
	}

	public function action_target()
	{

		$twig = Twig::factory('commercial/target');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Точно в цель"
			)
		);

		$this->response->body($twig);
	}

	public function action_view()
	{

		$twig = Twig::factory('commercial/view');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Баннерная реклама"
			)
		);

		$this->response->body($twig);
	}

	public function action_number_one()
	{

		$twig = Twig::factory('commercial/number-one');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Номер один"
			)
		);

		$this->response->body($twig);
	}

	public function action_tri_slona()
	{

		$twig = Twig::factory('commercial/tri-slona');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Три слона"
			)
		);

		$this->response->body($twig);
	}

	public function action_personal()
	{

		$twig = Twig::factory('commercial/personal');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Создание персональной страницы"
			)
		);

		$this->response->body($twig);
	}

	public function action_leader()
	{

		$twig = Twig::factory('commercial/leader');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Размещение фотообъявления"
			)
		);

		$this->response->body($twig);
	}

	public function action_premium()
	{

		$twig = Twig::factory('commercial/premium');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Премиум-объявление"
			)
		);

		$this->response->body($twig);
	}

	public function action_full()
	{

		$twig = Twig::factory('commercial/full');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Загрузка прайс-листа на персональную страницу"
			)
		);

		$this->response->body($twig);
	}

	public function action_conveer()
	{

		$twig = Twig::factory('commercial/conveer');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Реклама на сайте",
				"url" => "commercial/site"
			),
			array(
				"title" => "Массовая загрузка объявлений на сайт"
			)
		);

		$this->response->body($twig);
	}

	public function action_outdoor()
	{

		$twig = Twig::factory('commercial/outdoor');

		$twig->crumbs = array(
			array(
				"title" => "Рекламодателям",
				"url" => "commercial"
			),
			array(
				"title" => "Наружная реклама в городе Сургут"
			)
		);

		$this->response->body($twig);
	}

}