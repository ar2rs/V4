<?php
include 'functions.php';
include 'level_defs.php';
include 'backup.php';



class Agent_smith{

	private $ID;
	private $default_starting_point = 0;
	private $gamma = 0.8;
	private $default_actions = [0,1,2,3,4];
	private $expeditions = 1;
	private $unique_id = 0;
	private $bag = [0,0];
	private $Q = [];
	private $R = [];
	
	private $current_state = 0;
	private $next_state = 0;
	private $action = 0;
	
	private $bullets = 1;
	private $revard = -1;
	private $backup = [];
	private $agents_sent = [];
	
	private $target = 0;
	private $experiments = [];


function __construct($ID, &$bag){
		$this->ID = $ID+1;
		$this->uniq_id = counter();
		$this->bag = &$bag;
		$this->Learn();
	}
	
	function Learn(){
		echo 'Hello here is my full name -> '.$this->ID.'   '.$this->unique_id;
			print_r2($this->bag);
			
			$reset_bag = $this->bag;
		for ($i = 0; $i < $this->expeditions; $i++) {
			$this->bag = $reset_bag;
			
			//sagatavojam mainÄ«gos;
			$this->current_state = 0;
			$this->target = 0;
			$this->next_state = 0;
			
			//dodamies ekspedÄ«cijÄ�
			$this->goto_expedition();

		}
	//apmÄ�cÄ«bas beigas - saglabÄ�jam/izvadam rezultÄ�tus Q un R;
	$this->save_results();
	}
	
	
function goto_expedition(){
	
	// Iet pa soÄ¼iem uz priekÅ�u kamÄ“r atrod mÄ“rÄ·i
	while ( $this->target == 0){
		
		//speram vienu soli	
		$this->take_one_step();
		
		//apskatamies, kas no Å�Ä« soÄ¼a ir iznÄ�cis
		$this->look_at_results();
		
	}
}



// ---------------------------------------------------------------- papildfunkcijas
	function save_results(){//staadaa korekti;
		echo 'I have learned only  so many things:';
			print_r2($this->Q);
			print_r2($this->R);
				
		echo '<p> I have in my bag : </p>';
			print_r2($this->bag);
	}
	
	function take_one_step(){//strdaa korekti
		//izvÄ“lamies darbÄ«bu
		$this->choose_an_action();

		//eksperimentÄ“jam ar darbÄ«bu
		$this->experiments = [];
		
		for($i = 0; $i<$this->bullets; $i++){
			//katrÄ� experimentÄ� mÄ“s iegÅ«stam jauno stÄ�vokli, balvu par darbÄ«bu un somas saturu;
			$exp_result = apply_action($this->ID, $this->current_state, $this->action, $this->bag);
			array_push($this->experiments, $exp_result);

		}		
		
	}
	
	function choose_an_action(){ //straadaa korekti
		//izvÄ“lamies nejauÅ�u darbÄ«bu
		$this->action = mt_rand(0,4);
	}
	
	function look_at_results(){
		//apskatam visus izdarÄ«tos eksperimentus Å�ajÄ� solÄ«;
		/*
		/	| NR. | Jaunais_stavoklis | balva | somas saturs |
		*/
		//meklÄ“jam lÄ«meÅ†us, jauno stÄ�vokli, pÄ�rejas varbÅ«tÄ«bu
		$backup_needed = [];
		$probability = 0;
		$hit = 0;

		foreach($this->experiments as $experiment){
			//skaitam pozitÄ«vos iznÄ�kumus
			$hit += ($experiment[1] > -1 ? 1 : 0);
			
			echo '<br>';
			echo $this->current_state;
			echo $this->action;
			
			//pieprasam jaunu klasi jaunam lÄ«menim
			if ($experiment[1] == 50){
				$report = [];
				$report[0] = $this->ID;
				$report[1] = $this->current_state;
				$report[2] = $experiment;
				
				
				array_push($backup_needed, $report);
			}
			if ($experiment[1] > -1){
				//jaunais stÄ�voklis
					$this->next_state = ($this->current_state != $experiment[0] ? $experiment[0]:$this->current_state);
				
			}
			
			//mÄ“rÄ·is sasniegts
			$this->target = ($experiment[1] > 50 ? 1 : 0);
			
			
			$this->bag[0] = ($this->bag[0] < $experiment[2][0] ? $experiment[2][0] : $this->bag[0]);
			$this->bag[1] = ($this->bag[1] < $experiment[2][1] ? $experiment[2][1] : $this->bag[1]);

		}
		
		$backup_needed = array_map("unserialize", array_unique(array_map("serialize", $backup_needed)));
		
		echo $this->next_state;
		echo '</br>';
		
		// pÄ�rejam uz jauno stÄ�vokli
		$this->current_state = $this->next_state;
		
		//sÅ«tam papildspÄ“ku pieprasÄ«jumu
		
		$this->Q_and_R($hit);
		
		$this->send_backup($backup_needed);
			
		
	}
	
	function Q_and_R($hit){
		$probability = $hit/$this->bullets;
	}
	
	
	
	function send_backup($request_list){
	
		global $agent_list;
		global $global_bag;
	
		$agents_in_duty = count($agent_list);
		
	
		if(count($request_list) != 0){
			array_push($agent_list, $request_list);
		}
	
		$agent_list = array_map("unserialize", array_unique(array_map("serialize", $agent_list)));
	
		if (count($agent_list) > $agents_in_duty) {
			echo '<br> new agent needed @'.$agent_list[count($agent_list)-1][0][0].$agent_list[count($agent_list)-1][0][1];
			echo '</br>';
			print_r2($agent_list);
			new Agent_smith($this->ID, $global_bag);
		}
	}
	
}
