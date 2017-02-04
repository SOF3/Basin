<?php

namespace SOFe\Basin;

class StandardTransferAgent extends TransferAgent{
	public function transferPlayer(Player $player, string $ip, int $port, string $message){
		parent::transferPlayer($player, $ip, $port, $message);
		$pk = new TransferPacket;
		$pk->address = $ip;
		$pk->port = $port;
		$player->dataPacket($pk);
	}
	public function getName(){
		return "StandardTransferAgent";
	}
}
