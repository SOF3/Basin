<?php

namespace Basin;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Basin extends PluginBase implements Listener{
	private $opts;
	private $line = null;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$cp = $this->getDataFolder() . "config.yml";
		if(is_file($cp)) $this->opts = yaml_parse_file($cp);
		else{
			if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder(), 0777, true);
			$this->getLogger()->alert("Please configure Basin!");
			echo "[?] Please enter the IP address (can be \"localhost\") of a MySQL database ";
			$host = $this->readLine();
			echo "[?] Please enter the username to the MySQL database. ";
			$user = $this->readLine();
			echo "[?] Please enter the password to the MySQL database. ";
			$password = $this->readLine();
			echo "[?] Please enter the schema to use. ";
			$schema = $this->readLine();
			$db = new \mysqli($host, $user, $password);
			if($db->connect_error) throw new \RuntimeException("Could not connect to MySQL database: $db->connect_error");
			$db->query("CREATE SCHEMA `$schema` IF NOT EXISTS");
			$db->close();
			$this->opts = ["host" => $host, "user" => $user, "password" => $password, "schema" => $schema];
			yaml_emit_file($cp, $this->opts);
		}
	}
	private function readLine(){
		$this->el = true;
		while($this->line === null) $this->getServer()->checkConsole();
		$r = $this->line;
		$this->line = null;
		return $r;
	}
	/**
	 * @priority LOWEST
	 */
	public function onCmd(ServerCommandEvent $ev){
		if($this->el){
			$this->line = $ev->getMessage();
			$ev->setMessage("");
			$ev->setCancelled();
	}
}


