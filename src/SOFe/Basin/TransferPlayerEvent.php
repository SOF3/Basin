<?php

namespace SOFe\Basin;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;

class TransferPlayerEvent extends PlayerEvent implements Cancellable{
	public static $handlerList;
	
	const CAUSE_UNKNOWN = -1;
	const CAUSE_BALANCE = 0;
	const CAUSE_COMMAND = 1;
	const CAUSE_CUSTOM = 2;
	
	private $basin;
	private $ip;
	private $port;
	private $message;
	private $cause;
	
	public function __construct(Basin $basin, Player $player, string $ip, int $port, int $cause = self::CAUSE_UNKNOWN, string $message = ""){
		parent::__construct($player);
		$this->basin = $basin;
		$this->ip = $ip;
		$this->port = $port;
		$this->message = $mesage;
		$this->cause = $cause;
	}
	
	public function getPlugin(){
		return $this->basin;
	}
	
	public function getIp(){
		return $this->ip;
	}
	public function setIp(string $ip){
		$this->ip = $ip;
	}
	
	public function getPort(){
		return $this->port;
	}
	public function setPort(int $port){
		$this->port = $port;
	}
	
	public function getMessage(){
		return $this->message;
	}
	public function setMessage(string $message){
		$this->message = $message;
	}
	
	public function getCause(){
		return $this->cause;
	}
}

