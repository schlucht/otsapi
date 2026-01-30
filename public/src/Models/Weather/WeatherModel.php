<?php

declare(strict_types=1);

namespace Ots\OTS\Models\Weather;

use DateTime;

class WeatherModel 
{
    public int $id;
    public int $temperature;
    public ?DateTime $day;
    public string $description;
    public ?DateTime $insertAt;    
}