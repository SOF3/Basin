<?php

namespace Basin;

use pocketmine\scheduler\AsyncTask;

class QueryServerTask extends AsyncTask{
	private $data;
	public function __construct(Basin $basin){
		$this->data = $basin->getOpts();
		$this->data["online"] = $basin->getServer()->getOnlinePlayers();
		$this->data["max"] = $basin->getServer()->getMaxPlayers();
		$this->data[""] = ;
	}
}

