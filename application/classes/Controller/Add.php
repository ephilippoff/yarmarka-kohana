<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Add extends Controller_Template {

	public function before()
	{
		parent::before();

		$user = Auth::instance()->get_user();
		if ($user)
		{
			$user->reload();
			if ($user->is_blocked == 1)
			{
				$this->redirect(URL::site('user/message?message=userblock'));
			}

			if (!$user->is_valid_orginfo()
					AND in_array(Request::current()->action(), array('index')))
			{
				if ($user->is_expired_date_validation())
					HTTP::redirect("/user/orginfo?from=another");
			}
		}else{
			$this->redirect(URL::site('user/login?return=add'));
		}
	}

	public function initAddForm($twig) {

		$twig->topMenuContainerClass = 'hidden-sm hidden-xs'; //hide on tablets and mobile
		$twig->showCatalogMenuButtonAfterLogo = true;
		$twig->catalogMenuAfterLogoButtonAdditionalClass = 'hidden-md hidden-lg'; //hide on desktops and laptops
		$twig->footerMenuAdditionalClass = 'hidden-xs'; //hide on mobile
		$twig->footerAdditionalClass = 'hidden-xs hidden-md hidden-lg hidden-sm'; //hide on all devices
	}

	public function action_index()
	{
		$this->use_layout   = FALSE;
		$this->auto_render  = FALSE;
		$twig = Twig::factory('user/add');
		$twig->params = new Obj();
		$twig->onPageFlag = 'add';
		$this->initAddForm($twig);
		
		$prefix = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
		$twig->data_file = $staticfile->jspath;

		$twig->crumbs = array(
			array("title" => "Создание объявления"),
		);
		
		$user = Auth::instance()->get_user();

		if ($user AND !Cookie::get('authautologin_s'))
				Auth::instance()->trueforcelogin($user);
		
		$prefix = (@$_SERVER['HTTP_HOST'] === 'c.yarmarka.biz') ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');

		$errors = new Obj();
		$token = NULL;
		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = $this->request->post();

		if ($is_post) {  
			$errors = new Obj(self::action_native_save_object($post_data));
			if ($errors->error)
				$errors = new Obj($errors->error);
			else {
				$object_id = $errors->object_id;
				if (Session::instance()->get('cv_mode') && $post_data['rubricid'] == 35 && isset($_GET['cv_mode']) && $_GET['cv_mode'] == 1) {
					$this->redirect('/detail/use_cv?object_id=' . $object_id);
				}
				$this->redirect('/detail/'.$object_id."?afteradd=1");
			}

			$params = $post_data;
			$token = (isset($post_data["csrf"])) ? $post_data["csrf"] : NULL;
		} 
		else
		{
			$token = Security::token();
			$params = array(
				'rubricid'		=> (int)$this->request->param('rubricid'),
				//'object_id'		=> (int)$this->request->query('object_id'),
				'city_id'		=> (int)$this->request->param('city_id')
			);
		}

		if (Session::instance()->get('cv_mode') == 1 && isset($_GET['cv_mode']) && $_GET['cv_mode'] == 1) {
			if (!isset($params)) {
				$params = array();
			}
			$params['rubricid'] = 35;
		}

		$form_data = new Form_Add($params, $is_post, $errors);

		$user = Auth::instance()->get_user();
		if (!$user)
			$form_data->Login();

		$form_data	->Category()
					->City()
					->Subject()
					->Text()
					->Photo()
					->Video()
					->Params()
					->Map()
					->Price()
					->Contacts()
					->Widgets()
					->Additional();
					
		if ($user AND $user->org_type == 2)
			$form_data->OrgInfo();
		elseif ($user AND $user->linked_to_user)
			$form_data->LinkedUser();



		if ( Acl::check("object.add.type") ) {
			$form_data ->AdvertType();
			$form_data ->UserType();
			$form_data ->OtherCities();
		}

		if ( Acl::check("object.add.dates") ) {
			$form_data ->Dates();
		}


		$twig->params->token = $token;
		
		//$twig->template->set_global('jspath', $staticfile->jspath);

		$twig->params->params 	= new Obj($params);
		$twig->params->form_data 	= $form_data->_data;
		$twig->params->errors = (array) $errors;
		$twig->params->assets = $this->assets;
		$twig->params->user = ($user AND $user->loaded()) ? $user->org_type : "undefined";
		$twig->params->allowCkEditor = \Yarmarka\Models\User::current()->isAdminOrModerator();
		$twig->allowCkEditor = $twig->params->allowCkEditor;

		$expired = NULL;
		if ($user AND !$user->is_valid_orginfo()
					AND !$user->is_expired_date_validation())
		{
			$settings = new Obj(ORM::factory('User_Settings')->get_group($user->id, "orginfo"));
			$expired =  $settings->{"date-expired"};
		}
		$twig->params->expired_orginfo = $expired;

		$twig->params = (array) $twig->params;
		$twig->block_name = "add/_index";
		$twig->cityTitle = $form_data->_data->city['city_title'];
		$this->response->body($twig);

		// echo "<pre>"; var_dump($twig); echo '</pre>'; die;

	}

	public function action_edit_ad()
	{
		$this->use_layout   = FALSE;
		$this->auto_render  = FALSE;
		$twig = Twig::factory('user/add');
		$twig->onPageFlag = 'add';
		$twig->isEdit = TRUE;
		$twig->params = new Obj();

		$this->initAddForm($twig);

		$user = Auth::instance()->get_user();
		
		$prefix = (Kohana::$environment == Kohana::PRODUCTION) ? "" : "dev_";
		$staticfile = new StaticFile("attributes", $prefix.'static_attributes.js');
		$twig->data_file = $staticfile->jspath;

		$twig->crumbs = array(
			array("title" => "Создание объявления"),
		);

		$errors = new Obj();
		$token = NULL;
		$object_id = (int)$this->request->param('object_id');
		$object = ORM::factory('Object', $object_id);
		if (!$object_id OR !$object->loaded())
			throw new HTTP_Exception_404;

		if (!$user OR ($object->author <> $user->id AND !in_array($user->role, array(1,9,3))) )
			throw new HTTP_Exception_404;

		$is_post = ($_SERVER['REQUEST_METHOD']=='POST');
		$post_data = $this->request->post();

		if ($is_post) {  
			$errors = new Obj(Object::default_save_object($post_data));
			if ($errors->error){
				$errors = new Obj($errors->error);
			}
			else 
			{
				$return_object_id = $errors->object_id;
				$this->redirect('/detail/'.$return_object_id);
			}	
		
			$params = $post_data;
			$token = (isset($post_data["csrf"])) ? $post_data["csrf"] : NULL;
		} 
		else
		{
			$token = Security::token();
			$params = array(
				'object_id'		=> $object_id
			);
		}

		$form_data = new Form_Add($params, $is_post, $errors);

		if (!$user)
			$form_data->Login();

		$form_data	->Category()
					->City()
					->Subject()
					->Text()
					->Photo()
					->Video()
					->Params()
					->Map()
					->Price()
					->Contacts()
					->Additional();

		if ( Acl::check("object.add.type") ) {
			$form_data ->AdvertType();
			$form_data ->UserType();
			$form_data ->OtherCities();
		}

		if ( Acl::check("object.add.dates") ) {
			$form_data ->Dates();
		}


		if ($user AND $user->org_type == 2)
			$form_data->OrgInfo();
		elseif ($user AND $user->linked_to_user)
			$form_data ->LinkedUser();

		$twig->params->token = $token;
		$twig->params->object  = $object;
		$twig->params->params 	= new Obj($params);
		$twig->params->form_data 	= $form_data->_data;
		$twig->params->errors = (array) $errors;
		$twig->params->assets = $this->assets;
		$twig->params->user = ($user AND $user->loaded()) ? $user->org_type : "undefined";
		$twig->params->allowCkEditor = \Yarmarka\Models\User::current()->isAdminOrModerator();
		$twig->allowCkEditor = $twig->params->allowCkEditor;

		$expired = NULL;
		if (!$user->is_valid_orginfo())
		{
			$settings = new Obj(ORM::factory('User_Settings')->get_group($user->id, "orginfo"));
			$expired =  $settings->{"date-expired"};

		}


		if ($object->is_bad) {
			$twig->params->moder_messages = ORM::factory('User_Messages')
			                        ->get_messages_from_admins($object_id)
			                        ->order_by("createdOn", "desc")
			                        ->limit(3)
			                        ->getprepared_all();
		} else {
			$twig->params->moder_messages = array();
		}

		$twig->params->expired_orginfo = $expired;
		$twig->params = (array) $twig->params;
		$twig->block_name = "add/_index";
		$this->response->body($twig);
	}

	public function action_native_save_object($params = NULL)
	{
		if (!$params)
			$params = $this->request->post();
		return Object::default_save_object($params);
	}

	public function action_save_object()
	{
		
		/*$this->auto_render = FALSE;
		$json = array();
		$user = Auth::instance()->get_user();

		if (!$user){
			$json['error'] = 'Ошибка авторизации. Необходимо очистить `cookie` (куки) браузера.';
			$this->response->body(json_encode($json));
			return;
		}

		if ( ! Request::current()->is_ajax())
		{
			//throw new HTTP_Exception_404('only ajax requests allowed');
		}

		if ($this->request->post("only_run_triggers") == 1)
		{
			$json = Object::PlacementAds_JustRunTriggers($this->request->post());
			$this->response->body(json_encode($json));
			return;
		}

		//если в локале работаем с подачей, ставим 1
		$local = Kohana::$config->load('common.is_local');

		if ($local == 1)
		{
			//подставляется дефолтный город 1947 не из кладра, чтоб кладр на локале не разворачивать
			//не сохраняется короткий урл
			$json = Object::PlacementAds_Local($this->request->post());
			$this->response->body(json_encode($json));
			return;
		} 


		if ($user->role == 9) 
			//убрана проверка контактов
			//убрана проверка на максимальное количество объяв в рубрику
			$json = Object::PlacementAds_ByModerator($this->request->post());
		else 
			//сохранение по дефолту
			$json = Object::PlacementAds_Default($this->request->post());

		$this->response->body(json_encode($json));*/
	}
	
	
	public function action_object_upload_file()
	{
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		$this->json['code'] = 200; // code by default
		
		try
		{
			if (empty($_FILES['userfile1']))
		{
			throw new Exception('Не загружен файл');
		}

		$filename = Uploads::make_thumbnail($_FILES['userfile1']);

		$check_image_similarity = Kohana::$config->load('common.check_image_similarity');

		if ($check_image_similarity)
		{
			$similarity = ORM::factory('Object_Attachment')->get_similarity(Uploads::get_full_path($filename));
			if ($similarity > Kohana::$config->load('common.max_image_similarity'))
			{
				throw new Exception('Фотография не уникальная, уже есть активное объявление с такой фотографией либо у вас , либо у другого пользователя');
			}
		}

		$this->json['filename'] = $filename;
		$this->json['filepaths'] = Imageci::getSitePaths($filename);

		$tmp_img = ORM::factory('Tmp_Img');
		$tmp_img->name = $filename;
		$tmp_img->save();
		}
			catch(Exception $e)
		{
			$this->json['error'] = $e->getMessage();
		}
		
		
		$this->response->body(json_encode($this->json));
	}

	public function action_set_order() {
		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		if (!isset($_REQUEST['fileName']) || !is_array($_REQUEST['fileName'])) {
			return;
		}
		$fileNames = $_REQUEST['fileName'];
		//select items
		$images = ORM::factory('Tmp_Img')
			->select('*')
			->where('name', 'in', $fileNames)
			->find_all();

		$data = array();
		foreach($images as $image) {
			$data[$image->name] = array_search($image->name, $fileNames);
			$image->delete();
		}
		asort($data);

		foreach($data as $key => $value) {
			$img = ORM::factory('Tmp_Img');
			$img->name = $key;
			$img->save();
		}

		$this->json['code'] = 200;
		$this->json['data'] = $data;
		$this->response->body(json_encode($this->json));
	}

	public function action_crop() {

		$this->use_layout = FALSE;
		$this->auto_render = FALSE;

		//create crop data structure
		$cropData = array();
		$cropDataKeys = array( 'x', 'y', 'width', 'height', 'rotate', 'scaleX', 'scaleY', 'fileName' );
		foreach($cropDataKeys as $key) {
			if (!array_key_exists($key, $_REQUEST)) {
				throw new Exception($key . ' required');
			}
			$value = $_REQUEST[$key];
			if ($key != 'fileName') {
				$value = (float) $value;
				if ($key == 'rotate') {
					//opposite css
					$value = -$value;
				}
			} else {
				$value = $initialValue = str_replace('..', '', $value);
				$value = $_SERVER['DOCUMENT_ROOT'] . Imageci::getOriginalSitePath($value);
				if (!is_file($value)) {
					throw new Exception('Image not exists');
				}
				$valuePathInfo = pathinfo($value);
				$newFileName = str_replace('./', '/', Imageci::getNewFileName('.' . $valuePathInfo['extension']));
				$newValue = $_SERVER['DOCUMENT_ROOT'] . $newFileName;

				//copy original
				if (!is_dir(dirname($newValue))) {
					mkdir(dirname($newValue), 0777, true);
				}
				copy($value, $newValue);

				//remove all others thumbnails
				$toRemove = Imageci::getSitePaths($initialValue);
				foreach($toRemove as $toRemoveKey => $toRemoveItem) {
					unlink($_SERVER['DOCUMENT_ROOT'] . $toRemoveItem);
				}
				$value = $newValue;
			}
			$cropData[$key] = $value;
		}

		//get image info
		$cropData['imageType'] = exif_imagetype($cropData['fileName']);
		$cropData['imageMime'] = image_type_to_mime_type($cropData['imageType']);
		$cropData['imageSize'] = getimagesize($cropData['fileName']);
		switch($cropData['imageType']) {
			case IMAGETYPE_GIF:
				$cropData['suffix'] = 'gif';
				break;
			case IMAGETYPE_JPEG:
				$cropData['suffix'] = 'jpeg';
				break;
			case IMAGETYPE_PNG:
				$cropData['suffix'] = 'png';
				break;
			default:
				throw new Exception('Image format ' . $cropData['imageType'] . ' is not supported');
		}

		//construct image
		$imgObj = NULL;
		$imgConstructor = 'imagecreatefrom' . $cropData['suffix'];
		$imgObj = $imgConstructor($cropData['fileName']);

		//validate new sizes - http://yarmarka.myjetbrains.com/youtrack/issue/yarmarka-316#comment=90-1078
		if (empty($_REQUEST['disableRectValidate']) || !$_REQUEST['disableRectValidate']) {
			if ($cropData['x'] < 0) {
				$cropData['width'] += $cropData['x'];
				$cropData['x'] = 0;
			}
			if ($cropData['y'] < 0) {
				$cropData['height'] += $cropData['y'];
				$cropData['y'] = 0;
			}
			if ($cropData['x'] + $cropData['width'] > $cropData['imageSize'][0]) {
				$cropData['width'] = $cropData['imageSize'][0] - $cropData['x'];
			}
			if ($cropData['y'] + $cropData['height'] > $cropData['imageSize'][1]) {
				$cropData['height'] = $cropData['imageSize'][1] - $cropData['y'];
			}
			if ($cropData['width'] > $cropData['imageSize'][0]) {
				$cropData['width'] = $cropData['imageSize'][0];
			}
			if ($cropData['height'] > $cropData['imageSize'][1]) {
				$cropData['height'] = $cropData['imageSize'][1];
			}
		}

		//create background color for rotate
		//transparent white color. Jpeg will have white background
		$bgColor = imagecolorallocatealpha($imgObj, 255, 255, 255, 127);

		//rotate
		if ($cropData['rotate'] !== 0) {
			$rotatedImg = imagerotate($imgObj, $cropData['rotate'], $bgColor);
			//get new sizes
			$rotatedSizeW = imagesx($rotatedImg);
			$rotatedSizeH = imagesy($rotatedImg);
			//replace image
			imagedestroy($imgObj);
			$imgObj = $rotatedImg;
			$cropData['imageSize'] = array(
					$rotatedSizeW,
					$rotatedSizeH
				);
		}

		//crop
		$croppedImage = imagecreatetruecolor($cropData['width'], $cropData['height']);
		imagefill($croppedImage, 0, 0, $bgColor);
		imagecopyresampled(
			$croppedImage, //dest image
			$imgObj, //source image
			0, 0, //dest start point
			$cropData['x'], $cropData['y'], //source start point
			$cropData['width'], $cropData['height'], //dest size
			$cropData['width'], $cropData['height'] //source size
		);
		//replace images
		imagedestroy($imgObj);
		$imgObj = $croppedImage;
		$cropData['imageSize'] = array(
				$cropData['width'],
				$cropData['height']
			);

		//debug output image
		//header('Content-Type: ' . $cropData['imageMime']);
		$imgOutput = 'image' . $cropData['suffix'];
		//$imgOutput($imgObj);
		//die;

		//recreate thumbnails
		$oldImageModule = new Imageci();
		$oldImageModule->setFileType($cropData['suffix']);
		$oldImageModule->setImageFileName(basename($cropData['fileName']));
		$oldImageModule->makeThumbnailByResource(
			$imgObj, $cropData['imageSize'][0], $cropData['imageSize'][1]);

		//save image
		$imgOutput($imgObj, $cropData['fileName']);

		//clear resources
		imagedestroy($imgObj);

		//die;

		//prepare answer
		$cropData['fileName'] = basename($cropData['fileName']);
		$cropData['thumbnails'] = Imageci::getSitePaths($cropData['fileName']);

		$this->json['code'] = 200;

		$this->response->body(json_encode(array_merge($this->json, $cropData)));
	}
}

/* End of file Add.php */
/* Location: ./application/classes/Controller/Add.php */
