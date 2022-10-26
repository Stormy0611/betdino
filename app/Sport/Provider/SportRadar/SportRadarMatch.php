<?php namespace App\Sport\Provider\SportRadar;

class SportRadarMatch {

    private array $array;

    public function __construct($array) {
        $this->array = $array;
    }

    public function isFinished() {
        return $this->array['result']['winner'] != null;
    }

    public function homeScore() {
        return $this->array['result']['home'];
    }

    public function awayScore() {
        return $this->array['result']['away'];
    }

    public function winner(): string {
        return $this->homeScore() === $this->awayScore() ? 'draw' : ($this->array['result']['winner'] === 'home' ? 'home'
            : ($this->array['result']['winner'] === 'away' ? 'away' : ($this->homeScore() > $this->awayScore() ? 'home' : 'away')));
    }

    public function period(int $period): ?SportRadarPeriod {
        if(!isset($this->array['periods']['p'.$period])) return null;
        return new SportRadarPeriod($this->array['periods']['p'.$period], $this->array['periods']['p'.$period]);
    }

    public function periods(): int {
        return count($this->array['periods']);
    }

}
