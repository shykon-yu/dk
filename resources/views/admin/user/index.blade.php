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
                <!-- 部门 -->
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
                <button type="button" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>
                @can('admin.user.store')
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm pull-right">
                        <span class="glyphicon glyphicon-plus"></span> 新增用户
                    </a>
                @endcan
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
                    <th>部门</th>
                    <th>状态</th>
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
                        <td>
                            <div data-toggle="tooltip"
                                 data-placement="top"
                                 data-container="body"
                                 title="{{ $vo->departments_name ?? '-' }}"
                                 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: inline-block;">
                                {{ Str::limit($vo->departments_name ?? '-', 20) }}
                            </div>
                        </td>
                        <td class="change-status" style="cursor:pointer" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @can('admin.user.update')
                                @if($vo->status)
                                    <span class="label label-success">启用</span>
                                @else
                                    <span class="label label-default">禁用</span>
                                @endif
                            @endcan

                        </td>
                        <td>{{ $vo->created_at->format('Y-m-d') }}</td>
                        <td>
                            @can('admin.users.update')
                                @can('update',$vo)
                                    <a href="{{ route('admin.users.edit', $vo) }}" class="text-info m-r-1">
                                        <span class="glyphicon glyphicon-edit">编辑</span>
                                    </a>
                                @endcan

                            @endcan
                            @if($vo->id != 1)
                            @can('admin.users.destroy')
                                    @can('delete', $vo)
                                    <a href="javascript:;" class="delete del_user" data-id="{{ $vo->id }}">
                                        <span class="glyphicon glyphicon-remove">删除</span>&nbsp;
                                    </a>
                                    @endcan
                            @endcan
                            @endif
                            <a href="{{ route('admin.users.show', $vo) }}" class="details m-r-1">
                                <span class="glyphicon glyphicon-list">详情</span>
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
                        @can('admin.users.destroy')
                            <button type="button" class="btn btn-danger btn-sm" onclick="Alldel()">批量删除</button>
                        @endcan
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

            // 状态切换
            $("#user_list").on('click', ".change-status", function () {
                let id = $(this).data('id');
                let now = $(this).data('status');
                let to = now == 1 ? 0 : 1;
                if (!confirm("确定" + (to ? "启用" : "禁用") + "？")) return false;

                $.post("{{ route('admin.users.status','') }}/" + id, {
                    _token: "{{ csrf_token() }}",
                    status: to
                }, res => {
                    if (res.code === 200) {
                        let html = res.status == 1
                            ? '<span class="label label-success">启用</span>'
                            : '<span class="label label-default">禁用</span>';
                        $(this).html(html).data('status', res.status);
                    }
                    alert(res.msg);
                }).fail(err => alert('操作失败'));
            });

            // 搜索
            $('#T').click(function () {
                $('#search').submit();
            });

            $('#R').click(function () {
                $('input[name="name"]').val('');
                $('.selectpicker').val('').selectpicker('refresh');
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
