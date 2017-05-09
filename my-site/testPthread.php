<?php
class testThread extends Thread{
	private $arg;
	private $index;

	public function __construct($arg){
		$this->arg=$arg;
		$this->index=0;
	}

	public function run(){
		if($this->arg){
			while($this->index<5){
				echo  microtime(true)."--".$this->arg."->ID:".(Thread::getCurrentThreadId())."---index=".$this->index."<br/>";
    	 $this->index++;
			}
		}
	}
}

$threadA = new testThread("WokerA");
$threadB = new testThread("WokerB");
if($threadA->start() && $threadB->start())
 {
 	$threadA->join();
	$threadB->join();
 	$index = 0;
 	while($index<5){
 	    echo "<font color='red'>".microtime(true)."---主线程ID".(Thread::getCurrentThreadId())."--index=".$index."</font><br/>";
 	    $index++;
 	}
 }else{
 	 echo "failed";
 }