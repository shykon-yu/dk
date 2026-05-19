@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">订单子单管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏（和主订单完全一样） -->
            <form method="get" action="{{ route('admin.orders.items') }}" id="search" class="form-inline" style="gap:8px; display:flex; flex-wrap:wrap; align-items:center;">
                <div class="form-group">
                    <input type="text" name="order_code" placeholder="订单号" class="form-control input-sm" value="{{ request('order_code') }}">
                </div>

                <div class="form-group">
                    <input type="text" name="goods_name" placeholder="商品名称" class="form-control input-sm" value="{{ request('goods_name') }}">
                </div>

                <div class="form-group">
                    <input type="text" name="customer_sku" placeholder="货号/SKU" class="form-control input-sm" value="{{ request('customer_sku') }}">
                </div>

                <div class="form-group">
                    <select name="department_ids[]" id="department_ids" class="selectpicker" data-live-search="true"
                            multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择部门">
                        @foreach($_departments_auth as $dept)
                            <option value="{{ $dept->id }}"
                                    @if(in_array($dept->id, (array)request('department_ids', []))) selected @endif>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select name="customer_ids[]" id="customer_ids" class="selectpicker" data-live-search="true"
                            multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择客户">
                        @foreach($_customers as $val)
                            <option value="{{ $val->id }}"
                                    @if(in_array($val->id, (array)request('customer_ids', []))) selected @endif>
                                {{ $val->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select name="supplier_ids[]" id="supplier_ids" class="selectpicker" data-live-search="true"
                            multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择供应商">
                        @foreach($_suppliers as $val)
                            <option value="{{ $val->id }}"
                                    @if(in_array($val->id, (array)request('supplier_ids', []))) selected @endif>
                                {{ $val->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <select name="status[]" id="status" class="selectpicker" data-live-search="true"
                            multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择状态">
                        @foreach(\App\Enums\OrderStatusEnum::options() as $key=>$val)
                            <option value="{{ $key }}" @if(in_array($key, (array)request('status', [0,1,2]))) selected @endif>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
                <!-- 日期时间段 -->
                <div class="input-group" style="width: auto;">
                    <input id="start_date" type="text" name="start_date" autocomplete="off" placeholder="入库起始日期" value="{{ request('start_date')??today()->subMonth(8)->format('Y-m-d') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div class="input-group" style="width: auto;">
                    <input id="end_date" type="text" name="end_date" placeholder="入库结束日期" value="{{ request('end_date') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-sm">搜索</button>
                    <button type="reset" class="btn btn-warning btn-sm" id="R">重置</button>
                </div>
            </form>

            <table class="table table-bordered table-hover table-striped" id="items_table">
                <thead>
                <tr>
                    <th>选择</th>
                    <th>序号</th>
                    <th>客户</th>
                    <th>供应商</th>
                    <th>图片</th>
                    <th>货号</th>
                    <th>商品</th>
                    <th>SKU</th>
                    <th>订货数</th>
                    <th>已入库</th>
                    <th>单价</th>
                    <th>金额</th>
                    <th>订单号</th>
                    <th>状态</th>
                    <th>备注</th>
                    <th>创建人</th>
                    <th>下单日期</th>
                    <th>交货日期</th>
                </tr>
                </thead>
                <tbody id="items_list">
                @foreach($items as $key => $item)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $item->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td style="max-width:20px">{{ $item->order->customers->name ?? '-' }}</td>
                        <td style="max-width:20px">{{ $item->order->suppliers->name ?? '-' }}</td>
                        <td>
                            @if($item->goods->thumb_image)
                            <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; line-height:40px;">
                                {{-- 缩略图 --}}
                                <img src="{{ asset($item->goods->thumb_image) }}"
                                     class="thumb-img click-preview"
                                     data-src="{{ asset($item->goods->main_image) }}"
                                     style="height:40px; max-height:40px; width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">

                                {{-- 右侧悬浮预览图 --}}
                                <img src="{{ asset($item->goods->thumb_image) }}"
                                     class="hover-preview"
                                     style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                            </div>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->goods->customer_sku ?? '-' }}</td>
                        <td>{{ $item->goods->name ?? '-' }}</td>
                        <td>{!! \App\Enums\CommonStyleEnum::getClass('prompt',$item->goodsSkus->color,40) !!}</td>
                        <td>{{ $item->number }}</td>
                        <td>{{ $item->received_quantity }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->money }}</td>
                        <td>{{ $item->order->order_code ?? '-' }}</td>
                        <td>
                            <span class="{{ \App\Enums\OrderStatusEnum::getClass($item->status) }}">
                                {{ \App\Enums\OrderStatusEnum::getText($item->status) }}
                            </span>
                        </td>
                        <td>{!! \App\Enums\CommonStyleEnum::getClass('prompt',$item->remark,80) !!}</td>
                        <td>{{ $item->order->creator->name ?? '-' }}</td>
                        <td>{{ $item->order->ordered_at_date ?? '-' }}</td>
                        <td>{{ $item->order->delivery_at_date ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="18" class="text-center pagelist">
                        {{ $items->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('.selectpicker').selectpicker();
            $("#start_date").datepicker({
                maxDate: '+0y +0m +0d',//最大日期
                onSelect: function (dateText, inst) {
                    $("#end_date").datepicker("option", "minDate", dateText);
                }

            });
            $("#end_date").datepicker({
                maxDate: '+0y +0m +0d',//最大日期
                onSelect: function (dateText, inst) {
                    $("#start_date").datepicker("option", "maxDate", dateText);
                }
            });
            $('#R').click(function (e) {
                e.preventDefault();
                $('#search input[type="text"]').val('');
                $('.selectpicker').selectpicker('deselectAll').selectpicker('refresh');
                window.location.href = "{{ route('admin.orders.items') }}";
            });
        });
    </script>
@endsection
