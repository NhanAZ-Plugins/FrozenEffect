<?php

declare(strict_types=1);

use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\player\Player;
use skymin\FrozenEffect\FrozenEffect;

$source = $argv[1] ?? null;
if ($source === null || !is_file($source . '/vendor/autoload.php')) {
	fwrite(STDERR, "Usage: php tests/frozen-packet.php /path/to/Axolotl-PM-source\n");
	exit(2);
}
require $source . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/skymin/FrozenEffect/FrozenEffect.php';

final class FrozenEffectTestSession extends NetworkSession {
	public ?ClientboundPacket $captured = null;

	public function sendDataPacket(ClientboundPacket $packet, bool $immediate = false) : bool {
		$this->captured = $packet;
		return true;
	}
}

final class FrozenEffectTestPlayer extends Player {
	public FrozenEffectTestSession $testSession;

	public function __destruct() {}

	public function getNetworkSession() : NetworkSession {
		return $this->testSession;
	}

	public function getId() : int {
		return 42;
	}
}

$session = (new ReflectionClass(FrozenEffectTestSession::class))->newInstanceWithoutConstructor();
$player = (new ReflectionClass(FrozenEffectTestPlayer::class))->newInstanceWithoutConstructor();
$player->testSession = $session;

foreach ([0.0, 0.375, 1.0] as $strength) {
	FrozenEffect::sendFrozenEffectScreen($player, $strength);
	$packet = $session->captured;
	$property = $packet instanceof SetActorDataPacket
		? ($packet->metadata[EntityMetadataProperties::FREEZING_EFFECT_STRENGTH] ?? null)
		: null;
	if (!$packet instanceof SetActorDataPacket || $packet->actorRuntimeId !== 42
		|| !$property instanceof FloatMetadataProperty || $property->getValue() !== $strength
		|| $packet->syncedProperties->getIntProperties() !== []
		|| $packet->syncedProperties->getFloatProperties() !== []
		|| $packet->tick !== 0) {
		throw new RuntimeException('FrozenEffect generated the wrong packet');
	}
}

FrozenEffect::sendFrozenEffectScreen($player);
$packet = $session->captured;
$property = $packet instanceof SetActorDataPacket ? ($packet->metadata[FrozenEffect::FROZEN_EFFECT] ?? null) : null;
if (!$property instanceof FloatMetadataProperty || $property->getValue() !== 1.0) {
	throw new RuntimeException('FrozenEffect default strength is wrong');
}

echo "PASS: FrozenEffect packet fields and default strength\n";
