<?php

namespace test;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\plugin\PluginBase;

class PacketLogger extends PluginBase implements Listener{
	public $isJoined = [];

	public $enablePacketLogger = false;
	public $enableBatchPacketLogger = false;

	/*
	public $enablePacketLogger = false;
	public $enableBatchPacketLogger = false;
	 */

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function join(PlayerJoinEvent $event){
		$this->isJoined[$event->getPlayer()->getName()] = true;
	}

	public function Quit(PlayerQuitEvent $event){
		unset($this->isJoined[$event->getPlayer()->getName()]);
	}

	public function PacketReceive(DataPacketReceiveEvent $event){
		if(!$this->enablePacketLogger){
			return;
		}
		if(!$event->getPacket() instanceof BatchPacket&&!$event->getPacket() instanceof TextPacket&&!$event->getPacket() instanceof MovePlayerPacket&&!$event->getPacket() instanceof MoveActorAbsolutePacket&&!$event->getPacket() instanceof SetTimePacket){
			$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($event->getPacket()));
			$this->getLogger()->info("!! ".$name);
			if(isset($this->isJoined[$event->getPlayer()->getName()])){
				$event->getPlayer()->sendMessage("!! ".$name);
			}
		}

		if(!$this->enableBatchPacketLogger){
			return;
		}

		if($event->getPacket() instanceof BatchPacket){
			$dataPacket = clone $event->getPacket();
			$dataPacket->decode();
			foreach($dataPacket->getPackets() as $buf){
				$pk = PacketPool::getPacketById(ord($buf{0}));
				if(!$pk->canBeBatched()){
					continue;
				}
				$pk->setBuffer($buf, 1);
				//Now handle $pk like a normal Packet
				if(!$pk instanceof BatchPacket&&!$pk instanceof TextPacket&&!$pk instanceof MovePlayerPacket&&!$pk instanceof MoveActorAbsolutePacket&&!$pk instanceof SetTimePacket){
					$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($pk));
					$this->getLogger()->info("? ".$name);
					if(isset($this->isJoined[$event->getPlayer()->getName()])){
						$event->getPlayer()->sendMessage("? ".$name);
					}
				}
			}
		}
	}

	public function PacketSend(DataPacketSendEvent $event){
		if(!$this->enablePacketLogger){
			return;
		}
		if(!$event->getPacket() instanceof BatchPacket&&!$event->getPacket() instanceof TextPacket&&!$event->getPacket() instanceof MovePlayerPacket&&!$event->getPacket() instanceof MoveActorAbsolutePacket&&!$event->getPacket() instanceof SetTimePacket){
			$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($event->getPacket()));
			$this->getLogger()->info("!!!! ".$name);
			if(isset($this->isJoined[$event->getPlayer()->getName()])){
				$event->getPlayer()->sendMessage("!!!! ".$name);
			}
		}

		if(!$this->enableBatchPacketLogger){
			return;
		}

		if($event->getPacket() instanceof BatchPacket){
			$dataPacket = clone $event->getPacket();
			$dataPacket->decode();
			foreach($dataPacket->getPackets() as $buf){
				$pk = PacketPool::getPacketById(ord($buf{0}));
				if(!$pk->canBeBatched()){
					continue;
				}
				$pk->setBuffer($buf, 1);
				$pk->decode();
				//Now handle $pk like a normal Packet

				if(!$pk instanceof BatchPacket&&!$pk instanceof TextPacket&&!$pk instanceof MovePlayerPacket&&!$pk instanceof MoveActorAbsolutePacket&&!$pk instanceof SetTimePacket){
					$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($pk));
					$this->getLogger()->info("?? ".$name);
					if(isset($this->isJoined[$event->getPlayer()->getName()])){
						$event->getPlayer()->sendMessage("?? ".$name);//"
					}

				}

			}
		}
	}
}
