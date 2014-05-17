<?php
function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

function counter(){
	static $counter = 0;
	$counter++;
	return $counter;
	
}


?>

