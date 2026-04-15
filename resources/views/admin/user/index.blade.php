@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-user"></span>
            <span class="panel-tit">用户管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.users.index') }}" id="search">
                <input type="text" name="user_name" placeholder="用户名" class="form-control input-sm" value="{{ request('user_name') }}">
                <input type="text" name="name" placeholder="姓名" class="form-control input-sm" value="{{ request('name') }}">

                <button type="button" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <!-- 新增用户按钮 -->
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增用户
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>姓名</th>
                    <th>用户名</th>
                    <th>角色</th>
                    <th>注册时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="user_list">
                @foreach($user_list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $vo->name }}</td>
                        <td>{{ $vo->username }}</td>
                        <td>{{ $vo->roles->first()?->name }}</td>
                        <td>{{ $vo->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_user" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                            <a href="{{ route('admin.users.show', $vo) }}" class="details m-r-1">
                                <span class="glyphicon glyphicon-list"></span> 详情
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="7" class="text-center pagelist">
                        {{ $user_list->appends(request()->all())->links('pagination::bootstrap-4') }}
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
            // 单个删除
            $("#user_list").on('click', ".del_user", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.users.destroy', '') }}/" + id,
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

            // 搜索
            $('#T').click(function () {
                $('#search').submit();
            });

            // 重置
            $('#R').click(function () {
                $('input[name="user_name"], input[name="name"]').val('');
                $('#T').click();
            });
        });

        // 批量删除
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要删除的用户！");
                return false;
            }

            if(!confirm("确定要删除选中用户吗？")) return false;

            $.ajax({
                url: "{{ route('admin.users.batch.destroy') }}",
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
