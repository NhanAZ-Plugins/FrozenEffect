<?php

declare(strict_types=1);

namespace FrozenEffectConsumer;

use pocketmine\plugin\DisablePluginException;
use pocketmine\plugin\PluginBase;
use skymin\FrozenEffect\FrozenEffect;

final class Main extends PluginBase {
	protected function onEnable() : void {
		if (!class_exists(FrozenEffect::class)) {
			throw new DisablePluginException('FrozenEffect virion was not bundled');
		}
	}
}
