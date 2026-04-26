@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">订单管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.orders.index') }}" id="search" class="form-inline" style="gap:8px; display:flex; flex-wrap:wrap; align-items:center;">
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

                <div class="form-group">
                    <select name="is_star[]" id="is_star" class="selectpicker" data-live-search="true"
                            multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择星标">
                        <option value="1" @if(in_array(1, (array)request('is_star', []))) selected @endif>星标</option>
                        <option value="0" @if(in_array(0, (array)request('is_star', []))) selected @endif>非星标</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-sm">搜索</button>
                    <button type="reset" class="btn btn-warning btn-sm" id="R">重置</button>
                </div>

            </form>

            <table class="table table-bordered table-hover table-striped" id="orders_table">
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
                    <th>总数量{{$mainOrders->sum('total_number')}}</th>
                    <th>总收货</th>
                    <th>总金额{{
                        $mainOrders->reduce(function ($carry, $item) {
                            return bcadd(
                                (string)($carry ?? 0),
                                (string)($item->total_amount ?? 0),
                                2
                            );
                        })
                    }}</th>
                    <th>订单号</th>
                    <th>状态</th>
                    <th>星标</th>
                    <th>备注</th>
                    <th>创建人</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="orders_list">
                @foreach($mainOrders as $key => $order)
                    @php
                        $itemCount = count($order->items);
                    @endphp
                    @foreach($order->items as $itemKey => $item)
                        <tr class="text-center">
                            {{-- 合并列：只第一行显示 + 合并行数 --}}
                            @if($itemKey === 0)
                                <td rowspan="{{ $itemCount }}">
                                    <input type="checkbox" name="one[]" value="{{ $order->id }}">
                                </td>
                                <td rowspan="{{ $itemCount }}">{{ $key + 1 }}</td>
                                <td style="max-width:20px" rowspan="{{ $itemCount }}">{{ $order->customers->name ?? '-' }}</td>
                                <td style="max-width:20px" rowspan="{{ $itemCount }}">{{ $order->suppliers->name ?? '-' }}</td>
                            @endif

                            {{-- 商品明细行 独立字段 --}}
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
                            <td class="text-left">{{ $item->goods->customer_sku ?? '-' }}</td>
                            <td class="text-left">{{ $item->goods->name ?? '-' }}</td>
                            <td>
                                {!! \App\Enums\CommonStyleEnum::getClass('prompt',$item->goodsSkus->color,40) !!}
                            </td>
                            <td>{{ $item->number }}</td>
                            <td>{{ $item->received_quantity }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ bcadd($item->number * $item->price, 0, 2) }}</td>

                            {{-- 合并列 --}}
                            @if($itemKey === 0)
                                <td rowspan="{{ $itemCount }}">{{ $order->total_number }}</td>
                                <td rowspan="{{ $itemCount }}">{{ $order->total_received_quantity }}</td>
                                <td rowspan="{{ $itemCount }}">{{ $order->total_amount }}</td>
                                <td rowspan="{{ $itemCount }}">
                                    {!! \App\Enums\CommonStyleEnum::getClass('prompt',$order->order_code,80) !!}
                                </td>
                                <td rowspan="{{ $itemCount }}">
                                    <span class="{{ \App\Enums\OrderStatusEnum::getClass($item->status)  }}">
                                        {{ \App\Enums\OrderStatusEnum::getText($item->status)  }}
                                    </span>
                                </td>
                                <td rowspan="{{ $itemCount }}" class="change-star" style="cursor:pointer" data-id="{{ $order->id }}" data-star="{{ $order->is_star }}">
                                    @if($order->is_star)
                                        <span class="glyphicon glyphicon-star text-warning" style="font-size:16px;"></span>
                                    @else
                                        <span class="glyphicon glyphicon-star-empty text-muted" style="font-size:16px;"></span>
                                    @endif
                                </td>
                                <td rowspan="{{ $itemCount }}">
                                    {!! \App\Enums\CommonStyleEnum::getClass('prompt',$order->remark,80) !!}
                                </td>
                                <td rowspan="{{ $itemCount }}">{!! \App\Enums\CommonStyleEnum::getClass('prompt',$order->creator->name,50) !!}</td>
                                <td rowspan="{{ $itemCount }}">{{ $order->created_at_date }}</td>
                                <td rowspan="{{ $itemCount }}">
                                    <a href="{{ route('admin.orders.edit', $order) }}" class="text-info m-r-5">
                                        <span class="glyphicon glyphicon-edit"></span> 编辑
                                    </a>
                                    <a href="javascript:;" class="del_order text-danger" data-id="{{ $order->id }}">
                                        <span class="glyphicon glyphicon-remove"></span> 删除
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="18" class="text-center pagelist">
                        {{ $mainOrders->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                </tfoot>
            </table>

            <div style="margin-top:15px; text-align:left;">
                <input type="button" class="btn btn-danger btn-sm" value="批量删除" onclick="Alldel()">
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function checkAll() {
            let all = document.getElementById('all');
            let items = document.getElementsByName('one[]');
            for (let i = 0; i < items.length; i++) {
                items[i].checked = all.checked;
            }
        }

        $(function () {
            $('.selectpicker').selectpicker();

            $('#R').click(function (e) {
                e.preventDefault();
                $('#search input[type="text"]').val('');
                $('.selectpicker').selectpicker('deselectAll').selectpicker('refresh');
                window.location.href = "{{ route('admin.orders.index') }}";
            });

            // 单条删除
            $("#orders_list").on('click', ".del_order", function () {
                if (!confirm("确定删除该订单？")) return false;
                let id = $(this).data('id');
                $.post("{{ route('admin.orders.destroy','') }}/" + id, {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                }, res => {
                    alert(res.msg);
                    if (res.code === 200) location.reload();
                }).fail(err => alert(err.responseJSON?.msg || '删除失败'));
            });
        });

        // 批量删除
        {{--function Alldel() {--}}
        {{--    let ids = [];--}}
        {{--    $("input[name='one[]']:checked").each(function () {--}}
        {{--        ids.push($(this).val());--}}
        {{--    });--}}
        {{--    if (ids.length === 0) return alert("请选择订单");--}}
        {{--    if (!confirm("确定批量删除选中订单？")) return false;--}}
        {{--    $.post("{{ route('admin.orders.batch.destroy') }}", {--}}
        {{--        _token: "{{ csrf_token() }}",--}}
        {{--        ids: ids,--}}
        {{--        _method: "DELETE"--}}
        {{--    }, res => {--}}
        {{--        alert(res.msg);--}}
        {{--        if (res.code === 200) location.reload();--}}
        {{--    });--}}
        {{--}--}}
    </script>
@endsection
