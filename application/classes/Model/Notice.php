<?php

class Model_Notice extends Model_Database
{
	function GetHintsByController($controller)
	//Получаем все "подсказки" для контроллера
	{
	    	$query = DB::select()
			->from('hints')
			->where('controller', '=', $controller);
			
			$result = $query->as_object()->execute();

			return $result;
	}

	function getHintByCChar($cchar) { // $cchar - controller character
    	$query = DB::select()
    		->from('hints')
    		->where('controller', '=', $cchar)
    		->limit(1);
		
		$result = $query->as_object()->execute();

		return $result[0];
	}
}