@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">出库管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.outbounds.index') }}" id="search" class="form-inline" style="gap:8px; display:flex; flex-wrap:wrap; align-items:center;">
                <div class="form-group">
                    <input type="text" name="outbound_code" placeholder="出库单号" class="form-control input-sm" value="{{ request('outbound_code') }}">
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

                <!-- 日期时间段 -->
                <div class="input-group" style="width: auto;">
                    <input id="start_date" type="text" name="start_date" autocomplete="off" placeholder="出库起始日期" value="{{ request('start_date') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div class="input-group" style="width: auto;">
                    <input id="end_date" type="text" name="end_date" placeholder="出库截止日期" value="{{ request('end_date') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-sm">搜索</button>
                    <button type="reset" id="R" class="btn btn-warning btn-sm">重置</button>
                </div>
            </form>

            <table class="table table-bordered table-hover table-striped" id="outbound_table">
                <thead>
                <tr>
                    <th>选择</th>
                    <th>序号</th>
                    <th>客户</th>
                    <th>图片</th>
                    <th>货号</th>
                    <th>商品</th>
                    <th>SKU</th>
                    <th>出库数量</th>
                    <th>单价</th>
                    <th>金额</th>
                    <th>总数量</th>
                    <th>总金额</th>
                    <th>出库单号</th>
                    <th>出库日期</th>
                    <th>录入人</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="outbound_list">
                @foreach($mainOrders as $key => $outbound)
                    @php
                        $itemCount = $outbound->items->count();
                        $totalQuantity = $outbound->items->sum('quantity');
                        $totalAmount = $outbound->items->sum('amount');
                    @endphp

                    @foreach($outbound->items as $itemKey => $item)

                        <tr class="text-center">
                            {{-- 合并：第一行开始 --}}
                            @if($itemKey === 0)
                                <td rowspan="{{ $itemCount }}">
                                    <input type="checkbox" name="one[]" value="{{ $outbound->id }}">
                                </td>
                                <td rowspan="{{ $itemCount }}"> {{ ($mainOrders->currentPage() - 1) * $mainOrders->perPage() + $key + 1 }}</td>
                                <td rowspan="{{ $itemCount }}">{{ $outbound->customer->name ?? '-' }}</td>
                            @endif

                            {{-- 产品独立行 --}}
                            <td>
                                @if($item->goods->thumb_image)
                                    <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; line-height:40px;">
                                        <img src="{{ asset($item->goods->thumb_image) }}"
                                             class="thumb-img click-preview"
                                             data-src="{{ asset($item->goods->main_image) }}"
                                             style="height:40px; max-height:40px; width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">
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
                            <td>{!! \App\Enums\CommonStyleEnum::getClass('prompt', optional($item->sku)->color ?? '-',40) !!}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->price }}</td>
                            <td>{{ $item->amount }}</td>

                            {{-- 合并字段 --}}
                            @if($itemKey === 0)
                                <td rowspan="{{ $itemCount }}">{{ $totalQuantity }}</td>
                                <td rowspan="{{ $itemCount }}">{{ $totalAmount }}</td>
                                <td rowspan="{{ $itemCount }}">
                                    {!! \App\Enums\CommonStyleEnum::getClass('prompt',$outbound->outbound_code,80) !!}
                                    <br>
                                    <a href="{{ route('admin.outbounds.show', $outbound) }}" class="text-primary m-r-5">
                                        <span class="glyphicon glyphicon-eye-open"></span> 查看
                                    </a>
                                </td>
                                <td rowspan="{{ $itemCount }}">{{ $outbound->outbound_at_date }}</td>
                                <td rowspan="{{ $itemCount }}">{{ $outbound->creator->name ?? '系统' }}</td>
                                <td rowspan="{{ $itemCount }}">
                                    @can('admin.outbounds.update')
                                        @can('update',$outbound)
                                            <a href="{{ route('admin.outbounds.edit', $outbound) }}" class="text-info m-r-5">
                                                <span class="glyphicon glyphicon-edit"></span> 编辑
                                            </a>
                                        @endcan
                                    @endcan

                                    @can('admin.outbounds.destroy')
                                        @can('delete',$outbound)
                                            <a href="javascript:;" class="del_order text-danger" data-id="{{ $outbound->id }}">
                                                <span class="glyphicon glyphicon-remove"></span> 删除
                                            </a>
                                        @endcan
                                    @endcan
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="18" class="text-center">
                        {{ $mainOrders->appends(request()->all())->links('pagination::bootstrap-4') }}
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
                maxDate: '+0y +0m +0d',
                onSelect: function (dateText, inst) {
                    $("#end_date").datepicker("option", "minDate", dateText);
                }
            });
            $("#end_date").datepicker({
                maxDate: '+0y +0m +0d',
                onSelect: function (dateText, inst) {
                    $("#start_date").datepicker("option", "maxDate", dateText);
                }
            });

            $('#R').click(function (e) {
                e.preventDefault();
                $('#search input[type="text"]').val('');
                $('.selectpicker').selectpicker('deselectAll').selectpicker('refresh');
                window.location.href = "{{ route('admin.outbounds.index') }}";
            });

            $("#outbound_list").on('click', ".del_order", function () {
                if (!confirm("确定删除该订单？")) return false;
                let id = $(this).data('id');
                $.post("{{ route('admin.outbounds.destroy','') }}/" + id, {
                    _token: "{{ csrf_token() }}",
                    _method: "DELETE"
                }, res => {
                    alert(res.msg);
                    if (res.code === 200) location.reload();
                }).fail(err => alert(err.responseJSON?.msg || '删除失败'));
            });
        });
    </script>
@endsection
