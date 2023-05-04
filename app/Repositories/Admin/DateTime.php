<?php

namespace App\Repositories\Admin;

class DateTime
{
    public static function getMonth($value){
        if($value == 1){
            $month = 'January';
        }
        elseif($value == 2){
            $month = 'February';
        }
        elseif($value == 3){
            $month = 'March';
        }
        elseif($value == 4){
            $month = 'April';
        }
        elseif($value == 5){
            $month = 'May';
        }elseif($value == 6){
            $month = 'June';
        }elseif($value == 7){
            $month = 'July';
        }elseif($value == 8){
            $month = 'August';
        }elseif($value == 9){
            $month = 'September';
        }elseif($value == 10){
            $month = 'October';
        }elseif($value == 11){
            $month = 'November';
        }
        else{
            $month = 'December';
        }

        return $month;
    }

    public static function allMonths(){
        $month = [];
        for ($m=1; $m<=12; $m++) {
            $month[$m] = date('F', mktime(0,0,0,$m, 1, date('Y')));
        }
        return $month;
    }

    public static function getYear(){
        return range(2010, date("Y", time()));
    }

    public static function  allWeeks(){
        $day_of_week = array
            (
                1 => 'Saturday',
                2 => 'Sunday',
                3 => 'Monday',
                4 => 'Tuesday',
                5 => 'Wednesday',
                6 => 'Thursday',
                7 => 'Friday'
            );
        return $day_of_week;
    }

    public static function getDay($value){
        if($value == 1){
            $week = 'Saturday';
        }
        elseif($value == 2){
            $week = 'Sunday';
        }
        elseif($value == 3){
            $week = 'Monday';
        }
        elseif($value == 4){
            $week = 'Tuesday';
        }
        elseif($value == 5){
            $week = 'Wednesday';
        }elseif($value == 6){
            $week = 'Thursday';
        }
        else{
            $week = 'Friday';
        }
        return $week;
    }
}
