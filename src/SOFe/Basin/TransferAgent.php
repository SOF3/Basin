<?php

namespace SOFe\Basin;

abstract class TransferAgent{
	public function transferPlayer(Player $player, string $ip, int $port, string $message){
		$player->sendMessage($message);
	}
	abstract public function getName();
	public function isReady(){
		return true;
	}
}
