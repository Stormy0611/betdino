<?php namespace App\Games\Kernel\Module\General;

use App\Games\Crash;
use App\Games\Kernel\Data;
use App\Games\Kernel\Game;
use App\Games\Kernel\Module\Module;
use App\Games\Kernel\Module\ModuleConfigurationOption;
use App\Modules;

class CrashModule extends Module {

    function id(): string {
        return "crash";
    }

    function name(): string {
        return "Crash";
    }

    function description(): string {
        return "Modules for Crash game";
    }

    function settings(): array {
        return [
            new class extends ModuleConfigurationOption {
                function id(): string {
                    return "chance";
                }

                function name(): string {
                    return "Low Payout Chance";
                }

                function description(): string {
                    return "Will crash at x1.01 - x1.03 with probability:";
                }

                function defaultValue(): ?string {
                    return '10';
                }

                function type(): string {
                    return 'input';
                }
            }
        ];
    }

    function supports(): bool {
        return $this->game instanceof Crash;
    }

    function lose(?Data $data): bool {
        return $this->chance(floatval(Modules::get($this->game, false)->get($this, 'chance')));
    }

}
