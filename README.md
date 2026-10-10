# FrozenEffect

FrozenEffect is a virion for Axolotl-PM plugins. It sends the Bedrock freezing overlay strength to one player through a `SetActorDataPacket`. It does not run as a standalone plugin.

## Requirements and compatibility

- Axolotl-PM plugin API `5.0.0`, as declared in [`virion.yml`](virion.yml).
- PHP 8.1 or newer with the Axolotl-PM 5 runtime.
- A consumer plugin that bundles the virion or loads it through DevTools during development.

Packet checks and PHPStan at maximum level pass against Axolotl-PM 5.0.0 and 5.49.1 source revisions. Intermediate 5.x releases and the visual result on a Bedrock client have not been tested.

## Add FrozenEffect to a plugin

Place this repository's `virion.yml`, `LICENSE` and `src/` together under `virions/FrozenEffect/` in the consumer project. Declare the dependency in the consumer's `devtools.yml`.

```yaml
virions:
  - name: FrozenEffect
    version: ^2.0.0
```

Use [DevTools](https://github.com/NhanAZ/DevTools) to build the consumer PHAR. The build includes a private copy of this virion and its MIT license. During source development, DevTools can load the same local virion into a folder plugin. Keep the consumer's virion revision fixed for reproducible builds.

## API

```php
use skymin\FrozenEffect\FrozenEffect;

FrozenEffect::sendFrozenEffectScreen($player, 0.5);
```

The second argument is the freezing strength from `0.0` to `1.0`. Omit it for the default `1.0`. The public parameter name remains `$persent` for compatibility with callers using PHP named arguments. The packet is sent only to the supplied player's network session.

## Upgrade from 1.x

Version 2.0.0 targets Axolotl-PM API 5.0.0. Update a consumer's virion version constraint to `^2.0.0` and rebuild its PHAR. The public `FROZEN_EFFECT` constant now uses Axolotl-PM's `FREEZING_EFFECT_STRENGTH` metadata ID, and the packet includes the required property sync data. Test the visual overlay with a Bedrock client before deploying a consumer that depends on it.

## Development and verification

Run `php tests/frozen-packet.php /path/to/Axolotl-PM-source` to inspect the packet fields against a chosen source revision. The [validation workflow](.github/workflows/validate.yml) runs packet checks, PHPStan at maximum level and a DevTools consumer build using pinned dependencies. The consumer fixture is for integration testing and is not a plugin release.

## License and support

The code retains the [MIT license and copyright notice of skymin](LICENSE). Report issues through [GitHub Issues](https://github.com/NhanAZ-Plugins/FrozenEffect/issues) or contact [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
