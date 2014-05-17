<?php
include 'get_value.php';
//include 'functions.php';
$main = [[50 , 0],
		 [0  , 100]];

$T1  =  [	[-1 ,0  ,-1 ,0  ,-1 , -1],
			[0  ,50 , 0 ,-1 ,-1 , -1],
			[-1 ,0  ,50 ,-1 , -1,  0],
			[0  ,-1 ,-1 ,50 ,0  , -1],
			[-1 ,-1 ,-1 ,0  ,50 ,  0],
			[-1 ,0  ,-1 ,-1 ,0  ,100]];


$T2 = 	[	[-1, 0,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
			[ 0,-1, 0,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
			[-1, 0,-1, 0,-1,-1, 0,-1,-1,-1,-1,-1,-1,-1,-1,-1],
			[-1,-1, 0,-1,-1,-1,-1, 0,-1,-1,-1,-1,-1,-1,-1,-1],
			[-1,-1,-1,-1,-1, 0,-1,-1, 0,-1,-1,-1,-1,-1,-1,-1],
			[-1,-1,-1,-1, 0,-1, 0,-1,-1, 0,-1,-1,-1,-1,-1,-1],
			[-1,-1 ,0,-1,-1, 0,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
			[-1,-1,-1, 0,-1,-1,-1,-1,-1,-1,-1, 0,-1,-1,-1,-1],
			[-1,-1,-1,-1, 0,-1,-1,-1,-1, 0,-1,-1, 0,-1,-1,-1],
			[-1,-1,-1,-1,-1, 0,-1,-1, 0,-1,-1,-1,-1, 0,-1,-1],
			[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1, 0,-1],
			[-1,-1,-1,-1,-1,-1,-1, 0,-1,-1,-1,100,-1,-1,-1,-1],
			[-1,-1,-1,-1,-1,-1,-1,-1, 0,-1,-1,-1,-1, 0,-1,-1],
			[-1,-1,-1,-1,-1,-1,-1,-1,-1, 0,-1,-1, 0,-1, 0,-1],
			[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1, 0,-1,-1, 0,-1, 0],
			[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1, 0,100]];


$actions = [0,1,2,3,4];

function translate_id($ID){
	switch ($ID){
		case 0:
			$name = 'main';
		break;
		default:
			$name = 'T'.$ID;
	}
	return $name;
}

function get_len($ID){
	global ${translate_id($ID)};
	$lenght = count(${translate_id($ID)},COUNT_NORMAL);
	return $lenght;
}

function translate_action($ID, $current_state, $action){
	$next_state = $current_state;
	switch ($action) {
		case 0:
			$next_state = $current_state;
		break;
		case 1:
			$next_state = $current_state - 1;
			break;
		case 2:
			$next_state = $current_state + 1;
			break;
		case 3:
			$next_state = $current_state - round(SQRT(get_len($ID)+0.6));
			break;	
		case 4:
			$next_state = $current_state + round(SQRT(get_len($ID)+0.4));
			break;
	}
	
	if ($next_state < 0) {
		$next_state = $current_state;
	}
	
	global ${translate_id($ID)};
	$a = count(${translate_id($ID)})-1;
	if ($next_state > $a) {
		$next_state = $current_state;
	}
	
	
	return $next_state;
}



function get_reward($ID, $current_state, $action) {
	/*
	 * Pabeigta
	 */
	
	$coordinates = [0,0];
	$coordinates[0] = $current_state;
	$coordinates[1] = translate_action($ID, $current_state, $action);

	
	global ${translate_id($ID)};
	$array = ${translate_id($ID)};

	$reward = get_value($array, $coordinates);
	
	
	if ($reward === ''){
		$reward = -1;
	}
		
	return $reward;
}


function apply_action($ID, $current_state, $action, $bag){
	/*
	 * Pabeigta
	 */
	$next_state = $current_state;
	
	$probability_to_succseed = 100;
	If(rand(0,100) <= $probability_to_succseed){
		$reward = get_reward($ID, $current_state, $action);
		$next_state = translate_action($ID, $current_state, $action);
	}
	
	if ($next_state < 0) {
		$next_state = $current_state;
	}

	IF($reward<0){
		$next_state = $current_state;
	}
	
	// balvas par somas saturu
	if ($ID == 2){
		if ( $next_state == 11) {
			// vçl nav atslçgas?
			if ($bag[0] != 1){
				$bag[0] = 1;
					
			}
			$reward = 100;
		}
	
		if ($next_state == 15) {
			// vçl nav izeja?
			if ($bag[1] != 1){
				$bag[1] = 1;
	
			}
			$reward = 100;
		}
	}
	

	if ($ID == 1){
		if ($next_state == 5){
			If($bag != [1,1]){
				$reward = -1;	
				$next_state = $current_state;	
			}
		}
		
		if ($next_state == 2){
			If($bag[0] != 1){
				$reward = -1;
				$next_state = $current_state;
			}
		}
		
		if ($next_state == 4){
			If($bag[1] != 1){
				$reward = -1;
				$next_state = $current_state;
			}
		}
	}
	
	if ($ID == 0){
		if ($next_state == 1){
			if ($bag[0] != 1 or $bag[1] != 1) {
				$reward = - 1;
				$next_state = $current_state;
			}
		}
	}
	
	
	$results = [];
	$results[0] = $next_state;
	$results[1] = $reward;
	$results[2] = $bag;
	return $results;
	
	//atgrieþam gan to kur viòð atradîsies, gan arî balvas izmçru
}


function print_Q1($ID){
	for ($i = 0; $i < get_len($ID)-1; $i++) {
		echo '<p>';
		for ($j = 0; $j < 5; $j++) {
			echo '  '.get_reward($ID, $i, $j);
		}
		echo '</p>';
	}
	
	
}

//print_r(apply_action(0, 0, 2, [1,1]));
?>