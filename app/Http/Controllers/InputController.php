<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;


class InputController extends Controller
{
    public function index()
    {
        return view('input');
    }


    public function import(Request $request)
    {
        
        $request->validate([
            'csv_file' => 'required|file|mimes:csv',
        ]);

        $file = $request->file('csv_file');
        $filename = $file->getClientOriginalName();


        // 存储文件
        Storage::disk('public')->putFileAs('csv', $file, $filename);
        

        // 解析 CSV 文件并处理数据
        $csv = Reader::createFromPath(storage_path('app/public/csv/' . $filename), 'r');
        $csv->setHeaderOffset(0);


        // 取得列名
        $headers = $csv->getHeader();

        $sum = 0; 
        $count = 0; 
        $varianceSum = 0; // 用于累积方差的總和
        $average = 0; // 系統重(N)
        $stdDeviation = 0;  // 系統重標準差(StdN)

    
        foreach ($csv->getRecords() as $index => $record) {

            // 只处理第 2 到第 1001 行的数据
            if ($index > 1 && $index <= 1001) {
                $columnValue = $record[$headers[2]];
                $sum += (float)$columnValue; // 将列的值累积到总和中
                $count++; // 增加行数
            }
        }

        if ($count > 0) {
            $average = $sum / $count; // 计算平均值

            // 重新迭代以计算方差
            foreach ($csv->getRecords() as $index => $record) {
                if ($index > 1 && $index <= 1001) {
                    $columnValue = (float)$record[$headers[2]];
                    $varianceSum += ($columnValue - $average) ** 2;
                }
            }

            // 计算方差
            $variance = $varianceSum / ($count - 1);

            // 计算标准差
            $stdDeviation = sqrt($variance);
        }


        // 1. 跳躍起始點：(成立/判斷條件) 在1秒之後並Fz(N)最接近且小於(N - 5*StdN)的第一個點位
        $jumps = [];
        $jump = $average - (5 * $stdDeviation);
        foreach ($csv->getRecords() as $index => $record) {

            // 在1秒之不處理
            if ($index < 1001) {
                continue;
            }

            if($record[$headers[2]] < $jump){
                $jumps[] = [
                    'time' => $record[$headers[1]],
                    'n' => $record[$headers[2]],
                    'index' => $index
                ];
                
                break;
            }

        }


        // 2. 制動起始點：(成立/判斷條件) 最系統重(N) ±1個點位裡最接近的
        $brake = [];
        foreach ($csv->getRecords() as $index => $record) {

            // 在跳躍起始點秒之不處理
            if ($index < $jumps[0]['index'] + 1) {
                continue;
            }

            if($record[$headers[2]] > $average){

                // 上一點位
                $previou = $average - $previouRecord[$headers[2]];

                // 下一點位
                $next = $record[$headers[2]] - $average;


             
                if($previou < $next){
                    $brake[] = [
                        'time' => $previouRecord[$headers[1]],
                        'n' => $previouRecord[$headers[2]],
                        'index' => $index  - 1
                    ];

                }else{
                    $brake[] = [
                        'time' => $record[$headers[1]],
                        'n' => $record[$headers[2]],
                        'index' => $index
                    ];
                }

                break;
            }

            $previouRecord = $record;
        }



        // 处理完成后，可以返回成功消息或重定向回原始页面
        return view('input', [
            'average' => $average,
            'stdDeviation' => $stdDeviation,
            'stdDeviation5' => 5 * $stdDeviation,
            'filename' => $filename,
            'jumps' => $jumps,
            'brake' => $brake
        ]);
    }

}
