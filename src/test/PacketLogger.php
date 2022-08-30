<?php

namespace test;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\TNT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\item\StringToItemParser;


class PacketLogger extends PluginBase implements Listener{
	public $isJoined = [];

	public $enablePacketLogger = true;

	public $send = true;
	public $receive = true;

	/*
	public $enablePacketLogger = false;
	public $enableBatchPacketLogger = false;
	 */

	public function onEnable(): void{
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
		if(!$this->receive){
			return;
		}
		if(!$event->getPacket() instanceof TextPacket&&!$event->getPacket() instanceof MovePlayerPacket&&!$event->getPacket() instanceof MoveActorAbsolutePacket&&!$event->getPacket() instanceof SetTimePacket&&!$event->getPacket() instanceof PlayerAuthInputPacket){
			$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($event->getPacket()));
			$this->getLogger()->info("!! ".$name);
			if($event->getOrigin()->isConnected()&&isset($this->isJoined[$event->getOrigin()->getPlayer()?->getName()])){
				$event->getOrigin()->getPlayer()->sendMessage("!! ".$name);
			}
		}
	}

	public function PacketSend(DataPacketSendEvent $event){
		if(!$this->enablePacketLogger){
			return;
		}
		if(!$this->send){
			return;
		}
		foreach($event->getPackets() as $packet){
			if(!$packet instanceof TextPacket&&!$packet instanceof MovePlayerPacket&&!$packet instanceof MoveActorAbsolutePacket&&!$packet instanceof SetTimePacket&&!$packet instanceof PlayerAuthInputPacket){
				$name = str_replace("pocketmine\\network\\mcpe\\protocol\\", "", get_class($packet));
				$this->getLogger()->info("!!!! ".$name);
				foreach($event->getTargets() as $origin){
					if($origin->isConnected()&&$origin->isConnected()&&isset($this->isJoined[$origin->getPlayer()?->getName()])){
						$origin->getPlayer()->sendMessage("!!!! ".$name);
					}
				}
			}
		}
	}
}
