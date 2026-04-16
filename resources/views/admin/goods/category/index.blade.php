@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-th-list"></span>
            <span class="panel-tit">商品分类管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.goods.categories.index') }}" id="search">
                <input type="text" name="name" placeholder="分类名称" class="form-control input-sm" value="{{ request('name') }}">

                <button type="button" class="btn btn-info btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>

                <!-- 新增按钮 -->
                <a href="{{ route('admin.goods.categories.create') }}" class="btn btn-primary btn-sm pull-right">
                    <span class="glyphicon glyphicon-plus"></span> 新增分类
                </a>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>ID</th>
                    <th>分类名称</th>
                    <th>父级名称</th>
                    <th>级别</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="category_list">
                @foreach($list as $vo)
                    {{-- 一级分类 --}}
                    <tr class="text-center info">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $vo->id }}</td>
                        <td>{{ $vo->name }}</td>
                        <td>顶级分类</td>
                        <td>一级</td>
                        <td>{{ $vo->sort }}</td>
                        <td class="change-status" style="cursor: pointer;" data-id="{{ $vo->id }}" data-status="{{ $vo->status }}">
                            @if($vo->status == 1)
                                <span class="label label-success">启用</span>
                            @else
                                <span class="label label-default">禁用</span>
                            @endif
                        </td>
                        <td>{{ $vo->created_at_date }}</td>
                        <td>
                            <a href="{{ route('admin.goods.categories.edit', $vo) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a href="javascript:;" class="delete del_category" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>

                    {{-- 二级分类 --}}
                    @foreach($vo->children as $child)
                        <tr class="text-center">
                            <td><input type="checkbox" name="one[]" value="{{ $child->id }}"></td>
                            <td>{{ $child->id }}</td>
                            <td>　└─ {{ $child->name }}</td>
                            <td>{{ $vo->name }}</td>
                            <td>二级</td>
                            <td>{{ $child->sort }}</td>
                            <td class="change-status" style="cursor: pointer;" data-id="{{ $child->id }}" data-status="{{ $child->status }}">
                                @if($child->status == 1)
                                    <span class="label label-success">启用</span>
                                @else
                                    <span class="label label-default">禁用</span>
                                @endif
                            </td>
                            <td>{{ $child->created_at_date }}</td>
                            <td>
                                <a href="{{ route('admin.goods.categories.edit', $child) }}" class="text-info m-r-1">
                                    <span class="glyphicon glyphicon-edit"></span> 编辑
                                </a>
                                <a href="javascript:;" class="delete del_category" data-id="{{ $child->id }}">
                                    <span class="glyphicon glyphicon-remove"></span> 删除
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="9" class="text-center pagelist">
                        {{ $list->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
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
            $("#category_list").on('click', ".del_category", function(){
                if(!confirm("确定要删除吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    url: "{{ route('admin.goods.categories.destroy', '') }}/" + id,
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
                $('input[name="name"]').val('');
                $('#T').click();
            });

            //更换状态
            $("#category_list").on('click', ".change-status", function(){
                let id = $(this).data("id");
                let status = $(this).data("status");
                let obj = $(this);

                if(!confirm("确定要"+(status == 1 ? "禁用" : "启用")+"吗？")) return false;

                $.ajax({
                    url: "{{ route('admin.goods.categories.status','') }}/"+id,
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

        // 批量删除 ———— 🔥🔥🔥 这里已经完全修复
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val()); // 👈 修复了 $($this) → $(this)
            });

            if(ids.length === 0){
                alert("请选择要删除的分类！");
                return false;
            }

            if(!confirm("确定要删除选中分类吗？")) return false;

            $.ajax({
                url: "{{ route('admin.goods.categories.batch.destroy') }}",
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
