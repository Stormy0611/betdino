<?php namespace App\Sport\Provider\Phoenix;

use App\Sport\Provider\SportCompetitor;
use App\Sport\Provider\SportGame;
use App\Sport\Provider\SportLeague;
use App\Sport\Provider\SportLiveStatus;
use App\Sport\Provider\SportMarket;
use App\Sport\Provider\SportMarketRunner;
use App\Sport\Sport;
use Illuminate\Support\Facades\Cache;
use \Dejurin\GoogleTranslateForFree;
use Illuminate\Support\Facades\Crypt;

class PhoenixGame extends SportGame {

    private $json;
    private $live;

    public function __construct(array $json, bool $live) {
        $this->json = $json;
        $this->live = $live;
    }

    public function id(): string {
        return $this->json['id'];
    }

    public function isLive(): bool {
        return $this->live;
    }

    public function name(): string {
        return $this->json['nameDefault'];
    }

    public function isOpen(): bool {
        return $this->json['open'] && $this->json['status'] === 'OPEN';
    }

    public function sportRadarId(): int {
        return isset($this->json['widgetId']) ? $this->json['widgetId'] : -1;
    }

    public function league(): ?SportLeague {
        if(!isset($this->json['league']) || !isset($this->json['league']['nameDefault'])) return null;

        return new class($this->json['league']) extends SportLeague {
            private $league;

            public function __construct($league) {
                $this->league = $league;
            }

            public function id(): ?int {
                return null;
            }

            public function name(): string {
                return $this->league['nameDefault'];
            }
        };
    }

    public function competitors(): array {
        $competitors = [];
        foreach($this->json['competitors'] as $competitor)
            array_push($competitors, new class($competitor) extends SportCompetitor {
                private $competitor;

                public function __construct($competitor) {
                    $this->competitor = $competitor;
                }

                function name(): string {
                    $name = $this->competitor['name'];

                    if(preg_match('/[А-Яа-яЁё]/u', $name)) {
                        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
                        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
                        $name = str_replace($rus, $lat, $name);
                    }

                    return $name;
                }

                function logo(): ?string {
                    if(isset($this->competitor['logo'])) return "/api/sport/image/" . Crypt::encryptString($this->competitor['logo']);
                    return null;
                }
            });

        return $competitors;
    }

    public function liveStatus(): ?SportLiveStatus {
        return new class($this->json) extends SportLiveStatus {
            private $liveStatus;

            public function __construct($liveStatus) {
                $this->liveStatus = $liveStatus;
            }

            public function stage(): string {
                if(!isset($this->liveStatus['liveStatus'])) return '-';

                $stage = $this->liveStatus['liveStatus']['stage'];

                if(Cache::has("sport:translation:".$stage)) $stage = Cache::get("sport:translation:".$stage);
                else {
                    $result = (new GoogleTranslateForFree())->translate('ru', 'en', $stage, 5);
                    Cache::put("sport:translation:".$stage, $result, now()->addYear());

                    $stage = $result;
                }

                return $stage;
            }

            public function score(): string {
                return !isset($this->liveStatus['liveStatus']) ? '0:0' : $this->liveStatus['liveStatus']['score'];
            }

            public function setScores(): string {
                return !isset($this->liveStatus['liveStatus']) ? '-' : $this->liveStatus['liveStatus']['setScores'];
            }

            public function createdAt(): int {
                return $this->liveStatus['kickoff'];
            }
        };
    }

    public function markets(): array {
        $markets = [];

        foreach ($this->json['markets'] as $market) {
            $runner = new class($market) extends SportMarket {
                private $market;

                public function __construct($market) {
                    $this->market = $market;
                }

                function name() {
                    return $this->market['name'];
                }

                function isOpen(): bool {
                    return $this->market['open'];
                }

                function getRunners(): array {
                    $runners = [];

                    foreach($this->market['runners'] as $runner) {
                        $marketRunner = new class($this, $runner) extends SportMarketRunner {
                            private SportMarket $market;
                            private array $runner;

                            public function __construct($market, $runner) {
                                $this->market = $market;
                                $this->runner = $runner;
                            }

                            public function marketName() {
                                return $this->market->name();
                            }

                            function name(): string {
                                return $this->runner['name'];
                            }

                            function price(): float {
                                return $this->runner['price'];
                            }

                            function isOpen(): bool {
                                return $this->runner['open'];
                            }

                            public function supported(): bool{
                                return Sport::getLine()->findMarket($this->market->name(), $this->name()) != null;
                            }
                        };

                            /** @noinspection PhpArrayPushWithOneElementInspection */
                        if($marketRunner->supported()) array_push($runners, $marketRunner);
                    }

                    return $runners;
                }
            };

            if(count($runner->getRunners()) > 0) array_push($markets, $runner);
        }

        return $markets;
    }

}
