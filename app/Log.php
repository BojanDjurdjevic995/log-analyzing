<?php

namespace App;

use Storage;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function scopeGetLogs($query)
    {
        return $query->selectRaw('name, created_at AS upload_time, size')->get();
    }

    public function destroyer()
    {
        Storage::disk('log')->delete($this->name);
        LogDetail::where('log_id', $this->id)->delete();
        return $this->delete();
    }
}
