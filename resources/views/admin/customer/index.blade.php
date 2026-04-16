@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">客户管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.customers.index') }}" id="search">
                <input type="text" name="name" placeholder="客户名称" class="form-control input-sm" value="{{ request('name') }}">
                <select name="department_ids[]"
                        id="department_ids"
                        class="selectpicker"
                        data-live-search="true"
                        multiple
                        data-live-search-placeholder="Search"
                        data-actions-box="true"
                        title="请选择部门">

                    @foreach($_departments as $dept)
                        <option value="{{ $dept->id }}"
                                @if(in_array($dept->id, (array)request('department_ids', [])))
                                    selected
                            @endif>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <!-- 关键：type="submit" -->
                <button type="submit" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <!-- 新增按钮 -->
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增客户
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>所属部门</th>
                    <th>客户名称</th>
                    <th>韩文名称</th>
                    <th>品牌LOGO</th>
                    <th>货号前缀</th>
                    <th>清关方式</th>
                    <th>支付方式</th>
                    <th>联系人</th>
                    <th>电话</th>
                    <th>邮箱</th>
                    <th>地址</th>
                    <th>状态</th>
                    <th>备注</th>
                    <th>创建人</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="customer_list">
                @foreach($list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $vo->department->name ?? '-' }}</td>
                        <td>{{ $vo->name }}</td>
                        <td>{{ $vo->name_kr ?? '-' }}</td>
                        <td>{{ $vo->brand_logo ?? '-' }}</td>
                        <td>{{ $vo->sku_prefix ?? '-' }}</td>
                        <td>{{ $vo->clearance->name ?? '-' }}</td>
                        <td>{{ $vo->payment->name ?? '-' }}</td>
                        <td>{{ $vo->contact ?? '-' }}</td>
                        <td>{{ $vo->phone ?? '-' }}</td>
                        <td>{{ $vo->email ?? '-' }}</td>
                        <td>
                            <div data-toggle="tooltip"
                                 data-placement="top"
                                 data-container="body"
                                 title="{{ $vo->address ?? '-' }}"
                                 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: inline-block;">
                                {{ Str::limit($vo->address ?? '-', 25) }}
                            </div>
                        </td>
                        <td class="change-status" style="cursor: pointer;" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @if($vo->status == 1)
                                <span class="label label-success">启用</span>
                            @else
                                <span class="label label-default">禁用</span>
                            @endif
                        </td>
                        <td>{{ $vo->remark ?? '-' }}</td>
                        <td>{{ $vo->creator->name ?? '系统' }}</td>
                        <td>{{ $vo->created_at_date }}</td>
                        <td>
                            <a href="{{ route('admin.customers.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_customer" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="18" class="text-center pagelist">
                        {{ $list->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="18">
                        <button type="button" class="btn btn-danger btn-sm" onclick="Alldel()">批量删除</button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // 全选/取消全选
        function checkAll() {
            let a = document.getElementById('all');
            let b = document.getElementsByName('one[]');
            for (let i = 0; i < b.length; i++) {
                b[i].checked = a.checked;
            }
        }

        $(function(){
            // 初始化悬浮提示
            $('[data-toggle="tooltip"]').tooltip();

            // 初始化 selectpicker
            $('.selectpicker').selectpicker();

            // 单个删除
            $("#customer_list").on('click', ".del_customer", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.customers.destroy', '') }}/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"
                    },
                    dataType: "json",
                    success: function(data){
                        alert(data.msg);
                        if(data.code === 200){
                            obj.closest("tr").remove();
                        }
                    },
                    error: function(xhr){
                        alert(xhr.responseJSON?.msg || "删除失败");
                    }
                });
            });

            $('#R').click(function () {
                $('input[name="name"]').val('');
                $('.selectpicker').val('').selectpicker('refresh');
                $('#T').click();
            });

            //更换状态
            $("#customer_list").on('click', ".change-status", function(){
                let id = $(this).data("id");
                let status = $(this).data("status");
                let obj = $(this);

                if(!confirm("确定要"+(status == 1 ? "禁用" : "启用")+"吗？")) return false;

                $.ajax({
                    url: "{{ route('admin.customers.status','') }}/"+id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status == 1 ? 0 : 1
                    },
                    dataType: "json",
                    success: function(data){
                        if(data.code === 200){
                            if(data.status == 1){
                                obj.html('<span class="label label-success">启用</span>');
                                obj.data('status', 1);
                            }else{
                                obj.html('<span class="label label-default">禁用</span>');
                                obj.data('status', 0);
                            }
                            alert(data.msg);
                        }else{
                            alert(data.msg || '操作失败');
                        }
                    },
                    error: function(xhr){
                        alert(xhr.responseJSON?.msg || "操作失败");
                    }
                });
            });
        });

        // 批量删除
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要删除的客户！");
                return false;
            }

            if(!confirm("确定要删除选中客户吗？")) return false;

            $.ajax({
                url: "{{ route('admin.customers.batch.destroy') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids,
                    _method: "DELETE"
                },
                dataType: "json",
                success: function(data){
                    alert(data.msg);
                    if(data.code === 200){
                        window.location.reload();
                    }
                },
                error: function(xhr){
                    alert(xhr.responseJSON?.msg || "批量删除失败");
                }
            });
        }
    </script>
@endsection
