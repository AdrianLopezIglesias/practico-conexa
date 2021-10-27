<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helpers\StringHelper;
use Illuminate\Http\Request;

class MovementController extends Controller {

	public static function generate_movements(){
		$options = ['Down', 'Right', 'Up', 'Left'];
		$amount = rand(5,7);
		$final_resoult = [];
		for($i=0; $i<$amount; $i++){
			shuffle($options);
			array_push($final_resoult, $options[0]);
		}
		return $final_resoult;
	}

	public function earth_response() {
		return response()->json(MovementController::generate_movements());
	}

	public function calculate_position(Request $request){
		$current_position = $request->initial;
		$itmes = $request->itmes;
		$movements = []; 
		
		// $current_position = [0, 0];
		// $current_position = StringHelper::prepare_array($current_position);
		// $itmes = StringHelper::prepare_array($itmes);


		$current_position[2] = 'Initial position';
		// $itmes = ['D', 'R', 'L', 'L', 'D', 'D'];

		$options = [
			'Down' => [1, 0],
			'Right' => [0, 1],
			'Up' => [-1, 0],
			'Left' => [0, -1]
		];
		
		array_push($movements, $current_position);
		foreach($itmes as $order){
			$vertical_movement = $current_position[0] + $options[$order][0];
			$horizontal_movement = $current_position[1] + $options[$order][1];

			if ($vertical_movement >= 0 && $horizontal_movement >= 0 && $vertical_movement <= 10 && $horizontal_movement <= 10 ){
				$current_position[0] = $vertical_movement;
				$current_position[1] = $horizontal_movement;
				$current_position[2] = "Moving";
			}else{
				$current_position[2] = "Can't move";
			}
			$current_position[3] = uniqid();
			
			array_push($movements, $current_position);
		}
		return $movements; 

	}


}
