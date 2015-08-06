<?php

namespace Basin;

use pocketmine\event\Listener;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\plugin\PluginBase;

class Basin extends PluginBase implements Listener{
	private $opts;
	private $line = null;
	private $ip, $port;
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
			echo "[?] What is the server slot limit? It must be smaller than or equal to max-players in server.properties. ";
			$slots = (int) $this->readLine();
			$db = new \mysqli($host, $user, $password);
			if($db->connect_error) throw new \RuntimeException("Could not connect to MySQL database: $db->connect_error");
			$db->query("CREATE SCHEMA IF NOT EXISTS `$schema`");
			$db->query("CREATE TABLE IF NOT EXISTS `$schema`.basin (sid CHAR(31) PRIMARY KEY, ip VARCHAR(63), port SMALLINT, online SMALLINT, max SMALLINT, laston TIMESTAMP)");
			$db->close();
			$this->opts = ["host" => $host, "user" => $user, "password" => $password, "schema" => $schema, "max" => $slots];
			yaml_emit_file($cp, $this->opts);
		}
	}
	private function readLine(){
		$this->el = true;
		while($this->line === null) $this->getServer()->checkConsole();
		$r = $this->line;
		$this->line = null;
		$this->el = false;
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
	public function getOpts(){
		return $this->opts;
	}
	public function setAltServer($ip, $port){
		$this->ip = $ip;
		$this->port = $port;
	}
	public function onPreLogin(PlayerPreLoginEvent $ev){
		if(count($this->getServer()->getOnlinePlayers()) < $this->opts["max"]) return;
		if(true){ // TODO fire event
			$this->getServer()->getPluginManager("FastTransfer")->transferPlayer($ev->getPlayer(), $this->ip, $this->port, "This server is full :(");
		}
	}
}

