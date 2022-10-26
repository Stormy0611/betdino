<?php namespace App\Sport\Provider\Phoenix;

use App\Sport\Provider\SportCategory;
use App\Sport\Sport;
use App\Sport\Provider\Phoenix\PhoenixLineProvider;

class PhoenixCategory extends SportCategory {

    private $family;

    public function __construct($family) {
        $this->family = $family;
    }

    function id(): string {
        return $this->family;
    }

    function icon(): string {
        switch ($this->id()) {
            case "Soccer": return "fad fa-futbol";
            case "Ice Hockey": return "fad fa-hockey-puck";
            case "Tennis": return "fad fa-tennis-ball";
            case "Basketball": return "fad fa-basketball-ball";
            case "Table Tennis": return "fad fa-table-tennis";
            case "Australian Rules": return "fal fa-futbol";
            case "Motorsport": return "fad fa-motorcycle";
            case "Badminton": return "fad fa-shuttlecock";
            case "Baseball": return "fad fa-baseball-ball";
            case "Boxing": return "fad fa-boxing-glove";
            case "Cycling": return "fad fa-biking";
            case "Water Polo": return "fad fa-swimmer";
            case "Volleyball": return "fad fa-volleyball-ball";
            case "Handball": return "far fa-futbol";
            case "Golf": return "fad fa-golf-ball";
            case "Darts": return "darts";
            case "Curling": return "fad fa-curling";
            default: return "sport";
        }
    }

    private function formattedId() {
        return strtolower(str_replace(" ", "", $this->id()));
    }

    public function getGames(): array {
        $live = []; $incoming = [];

        $json = json_decode(Sport::cachedRequest("https://phoenix-gambling.com/proxy/games/" . $this->formattedId() . "/" . PhoenixLineProvider::$apiKey, 2), true);

        foreach ($json['data'] as $value)
            if(isset($value['markets']) && isset($value['widgetId'])) array_push($live, new PhoenixGame($value, true));

        return array_merge($live, $incoming);
    }

}
