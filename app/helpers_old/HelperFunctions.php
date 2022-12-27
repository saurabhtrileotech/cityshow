<?php
namespace App\helpers;


class HelperFunctions{

  public static function convertLcalToUtc($date,$timezone){
      if(!empty($timezone)){
        date_default_timezone_set($timezone);
        $utc_date = \Carbon\Carbon::parse($date)->tz('UTC')->format('Y-m-d H:i:s');
        return $utc_date;
      }else{
        $utc_date = \Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
        return $utc_date;
      }
  }

  public static function convertUtcToLocal($date,$timezone){
      if(!empty($timezone)){
        //date_default_timezone_set('UTC');
        if(auth()->user()->hasRole('teacher') || auth()->user()->hasRole('parent')){
          $utc_date = \Carbon\Carbon::parse($date)->tz($timezone)->format('g:i A');
        }else{
          $utc_date = \Carbon\Carbon::parse($date)->tz($timezone)->format('Y-m-d H:i:s');
        }
        return $utc_date;
      }else{
        if(auth()->user()->hasRole('teacher') || auth()->user()->hasRole('parent')){
           $utc_date = \Carbon\Carbon::parse($date)->format('g:i A'); 
        }
        else{
          $utc_date = $date;
        }
        return $utc_date;
      }
  }

}