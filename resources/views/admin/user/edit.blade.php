@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
        /* 完全和用户新增页保持一致 */
        .form-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .form-item .label {
            width: 100px;
            text-align: right;
            padding-right: 10px;
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }
        .form-item .field {
            flex: 1;
            max-width: 800px;
        }
        .form-control.input-sm {
            padding: 6px 10px;
            font-size: 14px;
            color: #333;
        }
        .panel-tit {
            font-size: 16px !important;
            font-weight: bold !important;
            color: #333 !important;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span>
            <span class="panel-tit">编辑用户</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="editUserForm">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">

                <!-- 用户名 -->
                <div class="form-item">
                    <label class="label">用户名：</label>
                    <div class="field">
                        <input type="text" name="username" value="{{ $user->username }}" placeholder="请输入用户名"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 真实姓名 -->
                <div class="form-item">
                    <label class="label">姓名：</label>
                    <div class="field">
                        <input type="text" name="name" value="{{ $user->name }}" placeholder="请输入姓名"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 密码：不填则不修改 -->
                <div class="form-item">
                    <label class="label">密码：</label>
                    <div class="field">
                        <input type="password" name="password" value="" placeholder="不填则保持原密码"
                               class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 邮箱 -->
                <div class="form-item">
                    <label class="label">邮箱：</label>
                    <div class="field">
                        <input type="email" name="email" value="{{ $user->email ?? '' }}" placeholder="请输入邮箱"
                               class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 手机号 -->
                <div class="form-item">
                    <label class="label">手机号：</label>
                    <div class="field">
                        <input type="text" name="phone_number" value="{{ $user->phone_number ?? '' }}" placeholder="请输入手机号"
                               class="form-control input-sm w50">
                    </div>
                </div>
                @can('admin.user.update')
                <!-- OpenID -->
                <div class="form-item">
                    <label class="label">OpenID：</label>
                    <div class="field">
                        <input type="text" name="open_id" value="{{ $user->open_id ?? '' }}" placeholder="第三方OpenID（选填）"
                               class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 角色选择 -->
                <div class="form-item">
                    <label class="label">分配角色：</label>
                    <div class="field">
                        <select name="role_id" class="form-control input-sm w50" required>
                            <option value="">请选择角色</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->roles->first()?->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 部门选择 -->
                <div class="form-item">
                    <label class="label">部门选择：</label>
                    <div class="field">
                        <select name="department_id[]"  class="selectpicker"
                                data-live-search="true"
                                multiple
                                data-live-search-placeholder="Search"
                                data-actions-box="true"
                                title="请选择部门">

                            @foreach($_departments as $dpat)
                                {{-- 关键：判断当前用户是否已拥有该部门，有就加 selected --}}
                                <option
                                    value="{{$dpat->id}}"
                                    {{ $user->departments->contains('id', $dpat->id) ? 'selected' : '' }}
                                >
                                    {{$dpat->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endcan
                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认编辑
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
                            <span class="glyphicon glyphicon-remove"></span> 取消返回
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function(){
            $("#submitBtn").click(function(){
                var formData = $("#editUserForm").serialize();
                var btn = $(this);

                // 关键：PUT 请求
                formData += "&_method=PATCH";
                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.users.update', $user->id) }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.reload();
                        //window.location.href = "{{ route('admin.users.index') }}";
                    }
                }, 'json')
                    .fail(function(xhr){
                        let msg = xhr.responseJSON?.msg || "编辑失败，请检查输入内容！";
                        alert(msg);
                    })
                    .always(function(){
                        btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 确认编辑");
                    });
            });
        });
    </script>
@endsection
