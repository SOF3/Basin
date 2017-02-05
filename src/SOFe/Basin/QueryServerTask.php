<?php

namespace SOFe\Basin;

use pocketmine\scheduler\AsyncTask;

class QueryServerTask extends AsyncTask{
	private $data;
	
	public function __construct(Basin $basin){
		$this->data = $basin->getOpts();
		$this->data["online"] = count($basin->getServer()->getOnlinePlayers());
		$this->data["sid"] = $basin->getServer()->getServerUniqueId();
		$this->data["port"] = $basin->getServer()->getPort();
	}
	
	public function onRun(){
		$db = $this->getDb();
		extract($this->data);
		$ip = Utils::getIP();
		$db->query("INSERT INTO basin (sid, ip, port, online, max, laston) VALUES ('{$db->escape_string($sid)}', '{$db->escape_string($ip)}', $port, $online, $max, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE ip='{$db->escape_string($ip)}', port=$port, online=$online, max=$max, laston=CURRENT_TIMESTAMP");
		$alt = $db->query("SELECT ip,port WHERE unix_timestamp()-unix_timestamp(laston)<5 AND sid != '{$db->escape_string($sid)}' AND max>online ORDER BY max-online DESC LIMIT 1");
		$r = $alt->fetch_assoc();
		$alt->close();
		$this->setResult($r);
	}
	
	public function getDb(){
		$r = $this->getFromThreadStore("basin.mysqli");
		if(!($r instanceof \mysqli)){
			$r = new \mysqli($this->data["host"], $this->data["user"], $this->data["password"], $this->data["schema"]);
			if($r->connect_error) throw new \RuntimeException("Failed to connect to MySQL: $r->connect_error");
			$this->saveToThreadStore("basin.mysqli", $r);
		}
		return $r;
	}
}