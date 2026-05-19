@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">物流订单列表</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏：只保留 部门、客户、日期 -->
            <form method="get" action="{{ route('admin.outbounds.logistics.index') }}" id="search" class="form-inline" style="gap:8px; display:flex; flex-wrap:wrap; align-items:center;">
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
                    <input id="start_date" type="text" name="start_date" autocomplete="off" placeholder="入库起始日期" value="{{ request('start_date')??today()->subMonth(8)->format('Y-m-d') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div class="input-group" style="width: auto;">
                    <input id="end_date" type="text" name="end_date" placeholder="入库结束日期" value="{{ request('end_date') }}" class="form-control input-sm">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-sm">搜索</button>
                    <button type="reset" id="R" class="btn btn-warning btn-sm">重置</button>
                </div>
            </form>

            <table class="table table-bordered table-hover table-striped text-center">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>部门</th>
                    <th>客户</th>
                    <th>清关方式</th>
                    <th>支付方式</th>
                    <th>总数量</th>
                    <th>总金额</th>
                    <th>出库日期</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($mainOrders as $key => $item)
                    <tr>
                        <td>{{ ($mainOrders->currentPage() - 1) * $mainOrders->perPage() + $key + 1 }}</td>
                        <td>{{ $item->department->name ?? '-' }}</td>
                        <td>{{ $item->customer->name ?? '-' }}</td>
                        <td>{{ $item->clearance->name ?? '-' }}</td>
                        <td>{{ $item->payment->name ?? '-' }}</td>
                        <td>{{ $item->logistics_quantity ?? '-' }}</td>
                        <td>{{ $item->logistics_amount ?? '-' }}</td>
                        <td>{{ $item->outbound_at }}</td>
                        <td>
                            <a href="{{ route('admin.outbounds.logistics.show', [
                                    'outbound_at' => $item->outbound_at,
                                    'customer_id' => $item->customer_id,
                                    'clearance_id' => $item->clearance_id,
                                    'payment_id' => $item->payment_id
                                ]) }}" class="text-primary">
                                <span class="glyphicon glyphicon-eye-open"></span> 查看
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="12" class="text-center">
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
                window.location.href = "{{ route('admin.outbounds.logistics.index') }}";
            });
        });
    </script>
@endsection
