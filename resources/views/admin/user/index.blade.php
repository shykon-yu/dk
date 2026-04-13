@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">用户管理</span>
        </div>

        <div class="panel-body navbar-form">
            <form method="get" action="{{ route('admin.user.index') }}" id="search">
                <div class="input-group">
                    <input id="user_name" type="text" name="user_name" autocomplete="off"
                           placeholder="按用户名" class="form-control input-sm"
                           value="{{ request('user_name') }}">
                </div>
                <div class="input-group">
                    <input id="name" type="text" name="name" autocomplete="off"
                           placeholder="姓名" class="form-control input-sm"
                           value="{{ request('name') }}">
                </div>

                <button type="button" class="btn btn-success btn-sm" id="T">搜索</button>
                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>
            </form>

            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>姓名</th>
                    <th>用户名</th>
                    <th>注册时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="order_list">
                @foreach($user_list as $key => $vo)
                    <tr class="text-center">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $vo->name }}</td>
                        <td>{{ $vo->username }}</td>
                        <td>{{ $vo->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.user.edit', $vo->id) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-edit"></span> 编辑
                            </a>
                            <a class="delete del_order" data-id="{{ $vo->id }}" style="cursor:pointer">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                            <a href="{{ route('admin.user.show', $vo->id) }}" class="details m-r-1">
                                <span class="glyphicon glyphicon-list"></span> 详情
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6" class="text-center pagelist">
                        {{ $user_list->appends(request()->all())->links('pagination::bootstrap-4') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <button type="button" class="btn btn-danger btn-sm" id="delete_more">批量删除</button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        //全选
        function checkAll() {
            let all = document.getElementById('all');
            let one = document.getElementsByName('one[]');
            one.forEach(item => item.checked = all.checked);
        }

        $(function () {
            //搜索
            $('#T').click(function () {
                $('#search').submit();
            });

            //重置
            $('#R').click(function () {
                $('#user_name, #name').val('');
                $('#T').click();
            });

            //单条删除
            $(document).on('click', '.del_order', function () {
                if (!confirm('确定删除？')) return;
                let id = $(this).data('id');
                let that = $(this);

                $.post("{{ route('admin.user.delete') }}", {
                    user_ids: id,
                    _token: "{{ csrf_token() }}"
                }, function (res) {
                    alert(res.msg);
                    if (res.code === 200) {
                        that.parents('tr').remove();
                    }
                }, 'json');
            });

            //批量删除
            $('#delete_more').click(function () {
                if (!confirm('确定批量删除？')) return;
                let ids = [];
                $('input[name="one[]"]:checked').each(function () {
                    ids.push($(this).val());
                });
                if (ids.length === 0) {
                    alert('请选择用户');
                    return;
                }

                $.post("{{ route('admin.user.delete') }}", {
                    user_ids: ids.join(','),
                    _token: "{{ csrf_token() }}"
                }, function (res) {
                    alert(res.msg);
                    if (res.code === 200) {
                        location.reload();
                    }
                }, 'json');
            });
        });
    </script>
@endsection
