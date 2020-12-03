<?php

namespace App\Http\Controllers;

use App\Log;
use Storage;
use Validator;
use App\LogDetail;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $log = Log::getLogs();
        return response()->json($log);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|max:102400|mimes:txt,gz']); // 102400 = 100MB
        if ($validator->fails())
            return response()->json($validator->errors()->toArray(), 422);

        return response()->json($this->upload($request->name));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($name = null)
    {
        if (Storage::disk('log')->exists($name))
            return Storage::disk('log')->download($name);
        return response()->json(['success' => false, 'error_msg' => 'Log with specific name doesn’t exist'], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function destroy($name = null)
    {
        $code = 200;
        $log = Log::where('name', $name)->first();
        if (!empty($log) && $log->destroyer())
            $response = ['success' => true, 'msg' => 'Successfully deleted log!'];
        else {
            $code = 404;
            $response = ['success' => false, 'error_msg' => 'Log with specific name doesn’t exist'];
        }

        return response()->json($response, $code);
    }

    private function upload($file)
    {
        $content     = $this->getContent($file);
        $log         = $this->storeLogFile($file);
        $log_details = explode("\n", $content);

        foreach ($log_details as $l)
            $this->getLogParts($l, $log->id);

        $response = [];
        $response['success']     = true;
        $response['name']        = $log->name;
        $response['upload_time'] = $log->created_at;
        return $response;
    }

    /**
     * This method divide log on the parts and store to the database
     * @param $log
     * @param $id
     * @return bool
     */
    private function getLogParts($log, $id)
    {
        preg_match(config('regex.log_regex'), $log, $data);

        if (!empty($data))
        {
            $log_date   = str_replace('/', '.', $data[3]) . ' ' . $data[4];
            $method_url = explode(' ', $data[5]);

            $log_details = new LogDetail();
            $log_details->log_id      = $id;
            $log_details->method      = $method_url[0];
            $log_details->ip          = $data[1];
            $log_details->url         = strtok($method_url[1], '?');
            $log_details->domain      = $data[10];
            $log_details->status_code = $data[6];
            $log_details->agent       = $data[9];
            $log_details->log_date    = date('Y-m-d H:i:s', strtotime($log_date));

            return $log_details->save();
        }
    }

    /**
     * This method get content of text file or gzip
     * @param $file
     * @return false|string
     */
    private function getContent($file)
    {
        $content = file_get_contents($file->getPathname());
        if ($file->getClientMimeType() == 'application/gzip') {
            $gzip = gzopen($file->getPathname(), 'rb');
            $content = '';
            while(!gzeof($gzip)) {
                $buffer_size = 51200;
                $content .= gzread($gzip, $buffer_size);

            }
        }

        return $content;
    }

    /**
     * This method store log file in storage and save log info in database
     * @param $file
     * @param $content
     * @return object
     */
    private function storeLogFile($file)
    {
        $i = 1;
        $content = file_get_contents($file->getPathname());
        $newFileName  = $file->getClientOriginalName();
        preg_match('/(.*)\..*/', $newFileName, $name);
        $name = $name[1] ?? '';

        while (Storage::disk('log')->exists($newFileName))
            $newFileName = $name . ' (' . ($i++) . ').' . $file->getClientOriginalExtension();

        Storage::disk('log')->put($newFileName, $content);
        $log = new Log();
        $log->name = $newFileName;
        $log->size = $file->getClientSize();
        $log->save();

        return (object) ['id' => $log->id, 'name' => $newFileName, 'created_at' => date('Y-m-d H:i:s', strtotime($log->created_at))];
    }
}
