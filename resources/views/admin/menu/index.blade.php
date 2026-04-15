@extends('admin.layouts.app')
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">菜单管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.menus.index') }}" id="search">
                <input type="text" name="title" placeholder="菜单名称" class="form-control input-sm" value="{{ request('title') }}">
                <input type="submit" class="btn btn-info btn-sm" value="搜索" id="T">
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增菜单
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
                <tbody id="menu_list">
                @foreach($menu as $item)
                    <!-- 一级菜单 -->
                    <tr class="text-center">
                        <td><input type='checkbox' name='one[]' value="{{ $item['id'] }}"></td>
                        <td>{{ $item['id'] }}</td>
                        <td style="text-align:left; padding-left:10px;">{{ $item['title'] }}</td>
                        <td>{{ $item['route'] }}</td>
                        <td>{{ $item['permission'] }}</td>
                        <td>{{ $item['sort'] }}</td>
                        <td>{{ $item['created_at_date'] }}</td>
                        <td>
                            <a class="text-info m-r-1 edit_order" href="{{ route('admin.menus.edit', $item['id']) }}">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_menu" data-id="{{ $item['id'] }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>

                    <!-- 二级菜单 -->
                    @foreach($item['children'] ?? [] as $sub)
                        <tr class="text-center">
                            <td><input type='checkbox' name='one[]' value="{{ $sub['id'] }}"></td>
                            <td>{{ $sub['id'] }}</td>
                            <td style="text-align:left; padding-left:30px;">├─ {{ $sub['title'] }}</td>
                            <td>{{ $sub['route'] }}</td>
                            <td>{{ $sub ['permission'] }}</td>
                            <td>{{ $sub['sort'] }}</td>
                            <td>{{ $sub['created_at_date'] }}</td>
                            <td>
                                <a class="text-info m-r-1 edit_order" href="{{ route('admin.menus.edit', $sub['id']) }}">
                                    <span class="glyphicon glyphicon-edit"></span> 编辑
                                </a>
                                <a href="javascript:;" class="delete del_menu" data-id="{{ $sub['id'] }}">
                                    <span class="glyphicon glyphicon-remove"></span> 删除
                                </a>
                            </td>
                        </tr>

                        <!-- 三级菜单 -->
                        @foreach($sub['children'] ?? [] as $third)
                            <tr class="text-center">
                                <td><input type='checkbox' name='one[]' value="{{ $third['id'] }}"></td>
                                <td>{{ $third['id'] }}</td>
                                <td style="text-align:left; padding-left:50px;">├─ └─ {{ $third['title'] }}</td>
                                <td>{{ $third['route'] }}</td>
                                <td>{{ $third['permission'] }}</td>
                                <td>{{ $third['sort'] }}</td>
                                <td>{{ $third['created_at_date'] }}</td>
                                <td>
                                    <a class="text-info m-r-1 edit_order" href="{{ route('admin.menus.edit', $third['id']) }}">
                                        <span class="glyphicon glyphicon-edit"></span> 编辑
                                    </a>
                                    <a href="javascript:;" class="delete del_menu" data-id="{{ $third['id'] }}">
                                        <span class="glyphicon glyphicon-remove"></span> 删除
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
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

        $(function(){
            // 单个删除（标准 DELETE）
            $("#menu_list").on('click',".del_menu",function(){
                if (!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.menus.destroy', '') }}/" + id,
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
                $("input[name='title']").val("");
                $("#T").click();
            });
        });

        // 批量删除（标准 DELETE）
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要删除的菜单！");
                return false;
            }

            if(!confirm("确定要删除选中菜单吗？")) return false;

            $.ajax({
                url: "{{ route('admin.menus.batch.destroy') }}",
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
