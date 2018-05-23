# Business Hours Diff

A PHP library to calculate business hours between two dates

## Installation
`composer require raupp/business-hours-diff`

## Usage
```
$businessOpensAt = 9;
$businessClosesAt = 19;

$date1 = Carbon::parse('2018-04-01 08:02');
$date2 = Carbon::parse('2018-04-01 22:39');

$minutes = (new BusinessHoursDiff($businessOpensAt, $businessClosesAt))
            ->diff($date1, $date2);
            
echo $minutes;      // 600
```
