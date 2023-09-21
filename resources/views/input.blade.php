@extends('voyager::master')


@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="icon voyager-data"></i>
       資料匯入
    </h1>
@stop

@section('content')
    <div class="container">
        <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csv_file">選擇 CSV 檔案</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">匯入資料</button>
        </form>
    </div>



    <div class="container">
        @if (!empty($average))
            <h2>匯入的資料： {{ $filename }}</h2>
        
            <table border="1">
                <thead>
                    <tr>
                        <th>系統重(N)</th>
                        <th>系統重標準差(StdN)</th>
                        <th>5倍系統重標準差(N)</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td>{{ $average }}</td>
                            <td>{{ $stdDeviation }}</td>
                            <td>{{ $stdDeviation5 }}</td>
                        </tr>
                </tbody>
            </table>

            <br>

            <table border="1">
                <thead>
                    <tr>
                        <th>關鍵點位</th>
                        <th>時間(s)</th>
                        <th>座標位置</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td>跳躍起始點</td>
                            <td>{{ $jumps[0]['time']}}</td>
                            <td>{{ $jumps[0]['n']}}</td>
                        </tr>
                        <tr>
                            <td>制動起始點</td>
                            <td>{{ $brake[0]['time']}}</td>
                            <td>{{ $brake[0]['n']}}</td>
                        </tr>
                </tbody>
            </table>

        @else
            <br>
            <p>没有匯入的資料。</p>
        @endif
    </div>

@stop

@section('javascript')
    <script>
      
    </script>
@stop
