<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    //
    protected $guarded = []; // To make every attributes mass assignable
    // If you put like this means these 2 attributes can't be mass assigned
    // mass assigned is kind of a security measure, I'll teach you what it is in the future now not important
    // We want it to be empty now 
}
