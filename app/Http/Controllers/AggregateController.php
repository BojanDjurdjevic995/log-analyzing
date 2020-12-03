<?php

namespace App\Http\Controllers;

use App\LogDetail;
use Illuminate\Http\Request;

class AggregateController extends Controller
{
    /**
     * Aggregated by IP
     * @param null $name
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAggregateByIp(Request $request)
    {
        $log = $this->aggregateResultByKey('ip', $request);
        return response()->json($log);
    }

    /**
     * Aggregated by HTTP method
     * @param null $name
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAggregateByMethod(Request $request)
    {
        $log = $this->aggregateResultByKey('method', $request);
        return response()->json($log);
    }

    /**
     * Aggregate by URL (without GET arguments)
     * @param null $name
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAggregateByUrl(Request $request)
    {
        $log = $this->aggregateResultByKey('url', $request);
        return response()->json($log);
    }

    /**
     * This method filtere data from database by key
     * @param $key
     * @param $name
     * @param $request
     * @return mixed
     */
    private function aggregateResultByKey($key, $request)
    {
        $log = LogDetail::aggregateByKey($request->request->get('dt_start'), $request->request->get('dt_end'), $request->request->get('name'), $key);

        return $log;
    }
}
