<?php

namespace SOFe\Basin;

use pocketmine\scheduler\PluginTask;

class FireQueryTask extends PluginTask{
	private $last = null;
	
	public function onRun($t){
		if($this->last !== null and $this->last->hasResult()){
			$ret = $this->last->getResult();
			if(is_array($ret)){
				extract($ret);
				$this->getOwner()->setAlt($ip, $port);
			}
			$this->last = null;
		}
		if($this->last === null) $this->last = new QueryServerTask($this->getOwner());
	}
}