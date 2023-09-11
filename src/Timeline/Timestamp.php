<?php

namespace App\Timeline;

use DateTime;

class Timestamp {
   private string $title;
   private DateTime $date;

   public function __construct(DateTime $date, string $title){
        $this->title = $title;
        $this->date = $date;
   } 

   public function getTitle(): string
   {
      return $this->title;
   }

   public function getDate(): DateTime
   {
      return $this->date;
   }
}