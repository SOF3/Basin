<?php

namespace SOFe\Basin;

use pocketmine\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;

class BalancePlayerEvent extends PlayerEvent implements Cancellable{
	public static $handlerList;
	private $basin;
	private $ip;
	private $port;
	public function __construct(Basin $basin, Player $player, $ip, $port){
		parent::__construct($player);
		$this->basin = $basin;
		$this->ip = $ip;
		$this->port = $port;
	}
	public function getPlugin(){
		return $this->basin;
	}
	public function getIp(){
		return $this->ip;
	}
	public function setIp($ip){
		$this->ip = $ip;
	}
	public function getPort(){
		return $this->port;
	}
	public function setPort($port){
		$this->port = $port;
	}
}

