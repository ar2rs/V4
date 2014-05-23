<?php

include 'get_value.php';


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

function test_logic($ID, $current_state, $next_state, &$bag, &$reward){	
	
	if ($ID == 0){
		If($next_state == 0){
			If($bag[0] == 1 and $bag[1] == 1){
				$reward = 0;
			}
		}
	
		If($next_state == 1){
			If($bag[0] != 1 or $bag[1] != 1){
				$reward = 0;
			}
		}
	}
	
	if ($ID == 1) {
		If($next_state == 1){
			If($bag[0] == 1){
				$reward = 0;
			}
		}
	
		If($next_state == 2){
			If($bag[1] == 1){
				$reward = 0;
			}
		}
	
		If($next_state == 3){
			If($bag[1] == 1){
				$reward = 0;
			}
		}
	
		If($next_state == 4){
			If($bag[0] == 1){
				$reward = 0;
			}
		}
	
		If($next_state == 5){
			If($bag[0] == 1 and $bag[1] ==1){
				$reward = 100;
			}
		}
	}
	
	
	
	if ($ID == 2){
		If($next_state == 11){
			If($bag == [1,1]){
				If($current_state == 11){
					$reward = 100;
				}
			}
			If($bag[0] != 1){
				$bag[0] = 1;
				$reward = 100;	
			}
		
		}
	
		If($next_state == 15){
			If($bag == [1,1]){
				If($current_state == 15){
					$reward = 100;
				}
			}
			If($bag[1] != 1){
				$bag[1] = 1;
				$reward = 100;
			}
			
		}
	
	}


}

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

function get_reward($ID, $current_state, $action) {

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
	
	$next_state = $current_state;
	
	$probability_to_succseed = 100;
	If(rand(0,100) <= $probability_to_succseed){
		$reward = get_reward($ID, $current_state, $action);
		$next_state = translate_action($ID, $current_state, $action);
	}
	if($reward > -1){
	test_logic($ID, $current_state, $next_state, $bag, $reward);
	}
	IF($reward<0){
		$next_state = $current_state;
	}
	
	$results = [];
	$results[0] = $next_state;
	$results[1] = $reward;
	$results[2] = $bag;
	return $results;
	
	
}


