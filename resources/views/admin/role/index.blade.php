@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">角色管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.roles.index') }}" id="search">
                <input type="text" name="name" placeholder="角色名称" class="form-control input-sm" value="{{ request('name') }}">
                <input type="submit" class="btn btn-info btn-sm" value="搜索" id="T">
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增角色
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    @foreach($headers as $h)
                        <th>
                            @if($h['field'] == 'check')
                                <input id="all" name="all" type="checkbox" onclick="checkAll()" />
                            @else
                                {{ $h['name'] }}
                            @endif
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody id="role_list">
                @foreach($roles as $item)
                    <tr class="text-center">
                        <td><input type='checkbox' name='one[]' value="{{ $item['id'] }}"></td>
                        <td>{{ $item['id'] }}</td>
                        <td style="text-align:left; padding-left:10px;">{{ $item['name'] }}</td>
                        <td>{{ $item->permissions->count() }} 个权限</td>
                        <td>{{ $item['created_at_date'] }}</td>
                        <td>
                            <a class="text-info m-r-1 edit_order" href="{{ route('admin.roles.edit', $item) }}">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>

                            @if($item->name !== 'super-admin')
                                <a href="javascript:;" class="delete del_role" data-id="{{ $item->id }}">
                                    <span class="glyphicon glyphicon-remove"></span> 删除
                                </a>
                            @else
                                <span class="text-muted" style="color:#999;">系统管理员</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- 批量删除 -->
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
            // 单个删除
            $("#role_list").on('click', ".del_role", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.roles.destroy', '') }}/" + id,
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

            // 重置按钮
            $(document).on('click', '#R', function () {
                $("input[name='name']").val("");
                $("#T").click();
            });
        });

        // 批量删除
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要删除的角色！");
                return false;
            }

            if(!confirm("确定要删除选中角色吗？")) return false;

            $.ajax({
                url: "{{ route('admin.roles.batch.destroy') }}",
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
