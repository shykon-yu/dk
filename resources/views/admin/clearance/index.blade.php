@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">清关管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.clearances.index') }}" id="search">
                <input type="text" name="name" placeholder="清关名称" class="form-control input-sm" value="{{ request('name') }}">

                <button type="submit" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <a href="{{ route('admin.clearances.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增清关
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>清关名称</th>
                    <th>韩文名称</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="clearance_list">
                @foreach($list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $vo->name ?? '-' }}</td>
                        <td>{{ $vo->name_kr ?? '-' }}</td>
                        <td>{{ $vo->sort ?? 0 }}</td>
                        <td class="change-status" style="cursor: pointer;" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @if($vo->status == 1)
                                <span class="label label-success">启用</span>
                            @else
                                <span class="label label-default">禁用</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.clearances.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_clearance" data-id="{{ $vo->id }}">
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

            // 单个删除
            $("#clearance_list").on('click', ".del_clearance", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.clearances.destroy', '') }}/" + id,
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
                $('#T').click();
            });

            //更换状态
            $("#clearance_list").on('click', ".change-status", function(){
                let id = $(this).data("id");
                let status = $(this).data("status");
                let obj = $(this);

                if(!confirm("确定要"+(status == 1 ? "禁用" : "启用")+"吗？")) return false;

                $.ajax({
                    url: "{{ route('admin.clearances.status','') }}/"+id,
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
                alert("请选择要删除的清关！");
                return false;
            }

            if(!confirm("确定要删除选中清关吗？")) return false;

            $.ajax({
                url: "{{ route('admin.clearances.batch.destroy') }}",
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
