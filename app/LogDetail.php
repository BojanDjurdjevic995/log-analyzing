<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogDetail extends Model
{
    const PAGINATE_NUMBER = 10;

    public function scopeAggregateByKey($query, $start, $end, $name, $key)
    {
        $query = $query->selectRaw($key . ', COUNT(*) AS cnt')->groupBy($key)->join('logs', 'log_details.log_id', 'logs.id');
        $query = (isset($start) && isset($end)) ? $query->whereBetween('log_date', [$start, $end]) : $query;
        $query = $name ? $query->where('name', 'LIKE', '%' . $name . '%') : $query;
        return $query->paginate(LogDetail::PAGINATE_NUMBER);
    }
}
