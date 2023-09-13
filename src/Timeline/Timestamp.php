<?php

namespace App\Timeline;

use DateTime;

class Timestamp {
   private string $title;
   private DateTime $date;
   private string $content;
   public function __construct(DateTime $date, string $title, ?string $content){
        $this->title = $title;
        $this->date = $date;
        $this->content = $content;
   } 

   public function getTitle(): string
   {
      return $this->title;
   }

   public function getDate(): DateTime
   {
      return $this->date;
   }

   public function getFileName(): string
   {
      return $this->date->format('Y-m-d') . '_' . $this->title . '.md';
   }

   public function getContent(): string
   {
      return $this->content;
   }
}