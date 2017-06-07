<?php

namespace SOFe\Basin;

use pocketmine\Player;


class TransferCommandExecutor implements CommandExecutor{
	private $server;
	private $main;
	
	public function __construct(Basin $main){
		$this->main = $main;
		$this->server = $main->getServer();
		parent::__construct($main);
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(!isset($args[0])){
			return false; #send Usage
		}
		if(($player = $this->server->getPlayerExact($args[0])) instanceof Player){
			if(isset($args[1])){
				if($this->transfer($player, $args[1]) !== true){
					$player->sendMessage("Failed to ")
				}
			}else{
				return false; #send Usage
			}
		}
		if($this->transfer($player))
	}
	
	private function transfer($player, string $ipPortArg): bool{
		
	}
	
	private static function getIpPortArray(string $ipPortArg): array{
		$arr = explode($ipPortArg, ":");
		if($a)
		return [$ip, $port];
	}
}

