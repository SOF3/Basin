<?php

namespace SOFe\Basin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\plugin\PluginBase;

class Basin extends PluginBase implements Listener{
	private $opts;
	private $line = null, $el = false;
	private $ip = null, $port = null;
	
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
			$db = @new \mysqli($host, $user, $password);
			if($db->connect_error) throw new \RuntimeException("Could not connect to MySQL database: $db->connect_error");
			$db->query("CREATE SCHEMA IF NOT EXISTS `$schema`");
			$db->query("CREATE TABLE IF NOT EXISTS `$schema`.basin (
				sid CHAR(31) PRIMARY KEY,
				ip VARCHAR(63),
				port SMALLINT,
				online SMALLINT,
				max SMALLINT,
				laston TIMESTAMP
			)");
			$db->query("INSERT INTO `$schema`.basin (sid, ip, port, online, max, laston) VALUES
				('{$db->escape_string($this->getServer()->getUniqueId())}', '{$db->escape_string($host)}', {$this->getServer()->getPort()},
				0, {$this->getServer()->getMaxPlayers()}, unix_timestamp())");
			$db->close();
			$db->query("CREATE TABLE IF NOT EXISTS `$schema`.droplets (
				target INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				name CHAR(31),
				ip VARCHAR(63),
				source CHAR(31) REFERENCES `$schema`.basin(sid),
				target CHAR(31) REFERENCES `$schema`.basin(sid),
				updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
			)");
			$this->opts = ["host" => $host, "user" => $user, "password" => $password, "schema" => $schema, "max" => $slots];
			yaml_emit_file($cp, $this->opts);
		}
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new FireQueryTask($this), 1);
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
	public function onServerCmd(ServerCommandEvent $ev){
		if($this->el){
			$this->line = $ev->getCommand();
			$ev->setCommand("");
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
		$this->getServer()->getPluginManager()->callEvent($tpev = new TransferPlayerEvent($this, $ev->getPlayer(), $this->ip, $this->port, "This server is full :("));
		if(!$ev->isCancelled()){
			if($tpev->getIp() === null or $tpev->getPort() === null){
				$ev->getPlayer()->kick("%disconnectScreen.serverFull", false);
			}else{
				$this->rawTransfer($ev->getPlayer(), $tpev->getIp(), $tpev->getPort(), $tpev->getMessage(), false);
			}
		}
	}
	
	public function transferPlayer(Player $player, string $ip, int $port, string $message = "", int $cause = TransferPlayerEvent::CAUSE_CUSTOM): bool{
		$this->getServer()->getPluginManager()->callEvent($tpev = new TransferPlayerEvent($this, $player, $ip, $port, $message, $cause));
		if($ev->isCancelled()){
			return false:
		}
		$this->rawTransfer($player, $ip, $port, $message);
		return true;
	}
	private function rawTransfer(Player $player, string $ip, int $port, string $message = ""){
		$pk = new TransferPacket;
		$pk->address = $ip;
		$pk->port = $port;
		$player->dataPacket($pk);
	}
}
