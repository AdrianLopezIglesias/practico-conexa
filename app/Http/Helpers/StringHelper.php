<?php

namespace App\Http\Helpers;


class StringHelper  {

	public static function prepare_array($string){

		$string = str_replace("[", "", $string); 
		$string = str_replace("]", "", $string); 
		$string = str_replace("\"", "", $string); 
		$string = str_replace("'", "", $string); 
		$string = str_replace(" ", "", $string); 
		$array = explode(",", $string);

		return $array;
	}

}