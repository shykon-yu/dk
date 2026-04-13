@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">角色管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.role.index') }}" id="search">
                <input type="text" name="name" placeholder="角色名称" class="form-control input-sm" value="{{ request('name') }}">
                <input type="submit" class="btn btn-info btn-sm" value="搜索" id="T">
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <a href="{{ route('admin.role.create') }}" class="btn btn-primary btn-sm pull-right">
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
                            <a class="text-info m-r-1 edit_order" href="{{ route('admin.role.edit', $item['id']) }}">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            @if($item->name !== 'super-admin')
                                <a class="delete del_role" order_id="{{ $item['id'] }}">
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
            var a = document.getElementById('all');
            var b = document.getElementsByName('one[]');
            for (i = 0; i < b.length; i++) {
                b[i].checked = a.checked;
            }
        }

        // 单个删除
        $(function(){
            $("#role_list").on('click',".del_role",function(){
                if (confirm("确定要删除吗？")) {
                    var id = $(this).attr("order_id");
                    var obj = $(this);
                    $.post("{{ route('admin.role.delete') }}", {
                        _token: "{{ csrf_token() }}",
                        ids : id,
                    }, function(data){
                        alert(data.msg);
                        if( data.code == 200 ){
                            obj.parent().parent().remove();
                        }
                    }, 'json');
                }else{
                    return false;
                }
            });

            // 重置按钮
            $(document).on('click', '#R', function () {
                $("input[name='name']").val("");
                $("#T").click();
            });
        })

        // 批量删除
        function Alldel() {
            var arr = new Array();
            $("input[type='checkbox']:checked").each(function () {
                arr.push($(this).val());
            });

            if (confirm("确定要删除选中角色吗？")) {
                $.post("{{ route('admin.role.delete') }}", {
                    _token: "{{ csrf_token() }}",
                    ids: arr
                }, function (data) {
                    alert(data.msg);
                    if (data.code == 200) {
                        window.location.reload();
                    }
                }, 'json');
            }
        }
    </script>
@endsection
