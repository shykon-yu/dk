@extends('admin.layouts.app')
@section('title','首页')
@section('content')
    <div style="padding:20px; background:#f5f7fa; min-height:100vh;">

        {{-- 标题 --}}
        <div style="margin-bottom:20px;">
            <h3 style="margin:0; font-size:18px; font-weight:bold;">基本信息</h3>
        </div>

        {{-- 统计卡片 --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:25px;">

            {{-- 卡片1 --}}
            <div style="background:#fff; border-radius:8px; padding:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size:14px; color:#666; margin-bottom:8px;">近七日入库数量</div>
                <div style="font-size:24px; font-weight:bold; color:#2d8cf0;" id="log_out">0</div>
            </div>

            {{-- 卡片2 --}}
            <div style="background:#fff; border-radius:8px; padding:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size:14px; color:#666; margin-bottom:8px;">昨日生产成材</div>
                <div style="font-size:24px; font-weight:bold; color:#f56c6c;">0 m³</div>
            </div>

            {{-- 卡片3 --}}
            <div style="background:#fff; border-radius:8px; padding:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size:14px; color:#666; margin-bottom:8px;">近七日帽子出库数量</div>
                <div style="font-size:24px; font-weight:bold; color:#e6a23c;">0</div>
            </div>

            {{-- 卡片4 --}}
            <div style="background:#fff; border-radius:8px; padding:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                <div style="font-size:14px; color:#666; margin-bottom:8px;">昨日综合出材率</div>
                <div style="font-size:24px; font-weight:bold; color:#67c23a;">0%</div>
            </div>

        </div>

        {{-- 产品出库报表 + 搜索 --}}
        <div style="background:#fff; border-radius:8px; padding:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
            <form method="get" action="{{ route('admin.info') }}" style="margin-bottom:15px; display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="start_date" id="start_date" placeholder="开始日期" value="{{ request('start_date') }}" style="padding:6px 12px; border:1px solid #ddd; border-radius:4px;">
                <input type="text" name="end_date" id="end_date" placeholder="结束日期" value="{{ request('end_date') }}" style="padding:6px 12px; border:1px solid #ddd; border-radius:4px;">
                <button type="submit" style="background:#2d8cf0; color:#fff; border:none; padding:6px 15px; border-radius:4px; cursor:pointer;">搜索</button>
            </form>

            <table style="width:100%; border-collapse:collapse; text-align:left;">
                <thead>
                <tr style="background:#f8f9fa;">
                    <th style="padding:10px; border:1px solid #eee;">序号</th>
                    <th style="padding:10px; border:1px solid #eee;">客户</th>
                    <th style="padding:10px; border:1px solid #eee;">类目</th>
                    <th style="padding:10px; border:1px solid #eee;">数量</th>
                    <th style="padding:10px; border:1px solid #eee;">金额</th>
                </tr>
                </thead>
                <tbody>
{{--                @foreach($outbound_list as $k => $vo)--}}
{{--                    <tr>--}}
{{--                        <td style="padding:10px; border:1px solid #eee;">{{ $k+1 }}</td>--}}
{{--                        <td style="padding:10px; border:1px solid #eee;">{{ $vo['custom_name'] ?? '' }}</td>--}}
{{--                        <td style="padding:10px; border:1px solid #eee;">{{ $vo['products_category_name'] ?? '' }}</td>--}}
{{--                        <td style="padding:10px; border:1px solid #eee;">{{ $vo['sum_outbound_number'] ?? 0 }}</td>--}}
{{--                        <td style="padding:10px; border:1px solid #eee;">{{ $vo['currency_icon'] ?? '' }} {{ $vo['sum_money'] ?? 0 }}</td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                </tbody>
            </table>
        </div>

    </div>
@endsection
