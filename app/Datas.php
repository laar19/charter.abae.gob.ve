<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Datas extends Model
{
    //protected $table = 'datas';
    protected $fillable = ['folder_name', 'xml_original', 'xml_charter'];
}
