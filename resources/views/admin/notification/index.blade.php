@extends('admin.layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-bell"></span>
            <span class="panel-tit">消息通知管理</span>
        </div>

        <div class="panel-body navbar-form">
            <!-- 搜索栏 -->
            <form method="get" action="{{ route('admin.notifications.index') }}" id="search">
{{--                <input type="text" name="title" placeholder="通知标题" class="form-control input-sm" value="{{ request('title') }}">--}}
{{--                <select name="type" class="form-control input-sm" style="width: auto; display: inline-block;">--}}
{{--                    <option value="">全部类型</option>--}}
{{--                    <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>入库通知</option>--}}
{{--                    <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>订单通知</option>--}}
{{--                    <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>系统通知</option>--}}
{{--                </select>--}}
{{--                <select name="read_status" class="form-control input-sm" style="width: auto; display: inline-block;">--}}
{{--                    <option value="">全部状态</option>--}}
{{--                    <option value="0" {{ request('read_status') == '0' ? 'selected' : '' }}>未读</option>--}}
{{--                    <option value="1" {{ request('read_status') == '1' ? 'selected' : '' }}>已读</option>--}}
{{--                </select>--}}

{{--                <button type="button" class="btn btn-info btn-sm" id="T">搜索</button>--}}
{{--                <button type="reset" class="btn btn-info btn-sm btn-warning" id="R">重置</button>--}}

                <!-- 一键已读 -->
                <button type="button" class="btn btn-success btn-sm pull-right m-l-5" id="readAll">
                    <span class="glyphicon glyphicon-ok"></span> 全部已读
                </button>
            </form>

            <!-- 表格 -->
            <table class="table table-bordered table-hover table-striped" id="log_form">
                <thead>
                <tr>
                    <th><input id="all" name="all" type="checkbox" onclick="checkAll()"></th>
                    <th>序号</th>
                    <th>通知标题</th>
                    <th>通知内容</th>
                    <th>通知类型</th>
                    <th>接收人</th>
                    <th>阅读状态</th>
                    <th>通知时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="notification_list">
                @foreach($notifications as $key => $vo)
                    @php
                        $data = is_string($vo->data) ? json_decode($vo->data, true) : $vo->data;
                        $isRead = !is_null($vo->read_at);
                    @endphp
                    <tr class="text-center {{ $isRead ? '' : 'bg-info-light' }}" style="{{ $isRead ? '' : 'background:#fcfdef;' }}">
                        <td><input type="checkbox" name="one[]" value="{{ $vo->id }}"></td>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $data['title'] ?? '系统通知' }}</td>
                        <td style="max-width:280px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
                            {{ $data['content'] ?? '-' }}
                        </td>
                        <td>
                            {{ \App\Enums\NotificationEnum::getText($data['type']) }}
                        </td>
                        <td>{{ $vo->notifiable->name ?? '管理员' }}</td>
                        <td class="change-read" style="cursor: pointer;" data-id="{{ $vo->id }}" data-read="{{ $isRead ? 1 : 0 }}">
                            @if($isRead)
                                <span class="label label-default">已读</span>
                            @else
                                <span class="label label-success">未读</span>
                            @endif
                        </td>
                        <td>{{ $vo->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.notifications.show', ['id' => $data['id'], 'type' => $data['type']]) }}" class="text-info m-r-1">
                                <span class="glyphicon glyphicon-eye-open"></span> 查看
                            </a>
                            <a href="javascript:;" class="delete del_notification" data-id="{{ $vo->id }}">
                                <span class="glyphicon glyphicon-remove"></span> 删除
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="9" class="text-center pagelist">
{{--                        {{ $notifications->appends(request()->all())->links('pagination::bootstrap-4') }}--}}
                    </td>
                </tr>
                <tr>
                    <td colspan="9">
                        <button type="button" class="btn btn-danger btn-sm" onclick="Alldel()">批量删除</button>
                        <button type="button" class="btn btn-success btn-sm m-l-5" onclick="AllRead()">批量已读</button>
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
            $("#notification_list").on('click', ".del_notification", function(){
                if(!confirm("确定要删除这条通知吗？")) return false;

                let id = $(this).data("id");
                let obj = $(this);

                $.ajax({
                    {{--url: "{{ route('admin.notifications.destroy', '') }}/" + id,--}}
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
                $('input[name="title"]').val('');
                $('select[name="type"], select[name="read_status"]').val('');
                $('#T').click();
            });

            // 切换已读/未读
            $("#notification_list").on('click', ".change-read", function(){
                let id = $(this).data("id");
                let isRead = $(this).data("read");
                let obj = $(this);

                $.ajax({
                    {{--url: "{{ route('admin.notifications.read','') }}/"+id,--}}
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: "json",
                    success: function(data){
                        if(data.code === 200){
                            obj.html('<span class="label label-default">已读</span>');
                            obj.data('read', 1);
                            obj.closest('tr').css('background','');
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

            // 一键已读
            $('#readAll').click(function(){
                if(!confirm("确定要将所有通知标为已读吗？")) return false;
                $.ajax({
                    {{--url: "{{ route('admin.notifications.read.all') }}",--}}
                    type: "POST",
                    data: { _token:"{{ csrf_token() }}" },
                    dataType: "json",
                    success: function(res){
                        alert(res.msg);
                        if(res.code === 200) window.location.reload();
                    }
                })
            });
        });

        // 批量删除
        function Alldel() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要删除的通知！");
                return false;
            }

            if(!confirm("确定要删除选中通知吗？")) return false;

            $.ajax({
                {{--url: "{{ route('admin.notifications.batch.destroy') }}",--}}
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

        // 批量已读
        function AllRead() {
            let ids = [];
            $("input[name='one[]']:checked").each(function(){
                ids.push($(this).val());
            });

            if(ids.length === 0){
                alert("请选择要标为已读的通知！");
                return false;
            }

            $.ajax({
                {{--url: "{{ route('admin.notifications.batch.read') }}",--}}
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                dataType: "json",
                success: function(data){
                    alert(data.msg);
                    if(data.code === 200){
                        window.location.reload();
                    }
                },
                error: function(xhr){
                    alert(xhr.responseJSON?.msg || "操作失败");
                }
            });
        }
    </script>
@endsection
