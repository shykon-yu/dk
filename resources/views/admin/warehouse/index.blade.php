@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">仓库管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.warehouses.index') }}" id="search">
                <input type="text" name="name" placeholder="仓库名称" class="form-control input-sm" value="{{ request('name') }}">
                <select name="department_ids[]" id="department_ids" class="selectpicker" data-live-search="true"
                        multiple data-live-search-placeholder="Search" data-actions-box="true" title="请选择部门">
                    @foreach($_departments as $dept)
                        <option value="{{ $dept->id }}"
                                @if(in_array($dept->id, (array)request('department_ids', [])))
                                    selected
                            @endif>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增仓库
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>所属部门</th>
                    <th>仓库名称</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="warehouse_list">
                @foreach($list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $vo->department->name ?? '-' }}</td>
                        <td>{{ $vo->name }}</td>
                        <td>{{ $vo->sort ?? 0 }}</td>
                        <td class="change-status" style="cursor: pointer;" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @if($vo->status == 1)
                                <span class="label label-success">启用</span>
                            @else
                                <span class="label label-default">禁用</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.warehouses.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_warehouse" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="7" class="text-center pagelist">
                        {{ $list->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
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
            $("#warehouse_list").on('click', ".del_warehouse", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.warehouses.destroy', '') }}/" + id,
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

            // 重置搜索
            $('#R').click(function () {
                $('input[name="name"]').val('');
                $('.selectpicker').val('').selectpicker('refresh');
                $('#T').click();
            });

            //更换状态
            $("#warehouse_list").on('click', ".change-status", function(){
                let id = $(this).data("id");
                let status = $(this).data("status");
                let obj = $(this);

                if(!confirm("确定要"+(status == 1 ? "禁用" : "启用")+"吗？")) return false;

                $.ajax({
                    url: "{{ route('admin.warehouses.status','') }}/"+id,
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
                alert("请选择要删除的仓库！");
                return false;
            }

            if(!confirm("确定要删除选中仓库吗？")) return false;

            $.ajax({
                url: "{{ route('admin.warehouses.batch.destroy') }}",
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
