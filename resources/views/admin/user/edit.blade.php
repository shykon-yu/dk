@extends('admin.layouts.app')

@section('content')
    <div class="panel admin-panel">
        <div class="panel-head">
            <strong><span class="icon-key"></span> 账号修改</strong>
        </div>

        <div class="body-content">
            <form method="post" class="form-x" id="editForm">
                @csrf

                <!-- 选择账号 -->
                <div class="form-group">
                    <div class="label">
                        <label>帐号：</label>
                    </div>
                    <div class="field">
                        <select class="input w50" name="user_id" id="user_id">
                            <option value="">-请选择账号-</option>
                            @foreach($user_list as $vo)
                                <option value="{{ $vo->id }}" {{ $user_data->id == $vo->id ? 'selected' : '' }}>
                                    {{ $vo->user_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 密码 -->
                <div class="form-group">
                    <div class="label">
                        <label>密码：</label>
                    </div>
                    <div class="field">
                        <input type="password" class="input w50" id="password" name="password"
                               placeholder="请输入密码（密码为空不修改密码）">
                    </div>
                </div>

                <!-- 默认版块 -->
                <div class="form-group">
                    <div class="label">
                        <label>默认版块：</label>
                    </div>
                    <div class="field">
                        <select class="input w50" name="section_id" id="section_id">
                            <option value="">-请选择默认版块-</option>
                            <option value="0" {{ $user_data->section_id == 0 ? 'selected' : '' }}>-首页-</option>
                            @foreach($section_list as $vo)
                                <option value="{{ $vo->id }}" {{ $user_data->section_id == $vo->id ? 'selected' : '' }}>
                                    {{ $vo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 部门 -->
                <div class="form-group">
                    <div class="label">
                        <label>选择部门：</label>
                    </div>
                    <div class="field">
                        <select class="w50 selectpicker show-tick" name="department_id[]" id="department_id"
                                multiple data-live-search="true" data-actions-box="true" title="请选择部门">
                            <option value="0" style="color:red;">最高管理员</option>
                            @foreach($department_list as $vo)
                                <option value="{{ $vo->id }}" {{ in_array($vo->id, $user_data->department_arr ?? []) ? 'selected' : '' }}>
                                    {{ $vo->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 浏览类型 -->
                <div class="form-group">
                    <div class="label">
                        <label>选择浏览类型：</label>
                    </div>
                    <div class="field">
                        <select class="input w50" name="group_id" id="group_id">
                            <option value="">-请选择浏览类型-</option>
                            @foreach($group_list as $vo)
                                <option value="{{ $vo->id }}" {{ $user_data->group_id == $vo->id ? 'selected' : '' }}>
                                    {{ $vo->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 管理员姓名 -->
                <div class="form-group">
                    <div class="label">
                        <label>管理员姓名：</label>
                    </div>
                    <div class="field">
                        <input type="text" class="input w50" name="name" id="name"
                               value="{{ $user_data->name }}" placeholder="请输入管理员姓名">
                    </div>
                </div>

                <!-- 提交 -->
                <div class="form-group">
                    <div class="label">
                        <label></label>
                    </div>
                    <div class="field">
                        <button class="button bg-main icon-check-square-o" type="button" id="submitBtn">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            // 选择用户自动加载数据
            $('#user_id').change(function () {
                let user_id = $(this).val();
                if (!user_id) return;

                $.post("{{ route('admin.user.get.user.data') }}", {
                    user_id: user_id,
                    _token: "{{ csrf_token() }}"
                }, function (data) {
                    $('#section_id').val(data.section_id);
                    $('#name').val(data.name);
                    $('#group_id').val(data.group_id);
                    $('.selectpicker').selectpicker('val', data.departments);
                }, 'json');
            });

            // 提交保存
            $('#submitBtn').click(function () {
                let user_id     = $('#user_id').val();
                let password    = $('#password').val();
                let section_id  = $('#section_id').val();
                let department  = $('#department_id').val();
                let group_id    = $('#group_id').val();
                let name        = $('#name').val();

                if (!user_id) {
                    alert('请选择账号！');
                    return false;
                }
                if (!section_id) {
                    alert('请选择默认版块！');
                    return false;
                }
                if (!department || department.length === 0) {
                    alert('请选择部门！');
                    return false;
                }
                if (!group_id) {
                    alert('请选择用户组！');
                    return false;
                }
                if (!name) {
                    alert('请输入管理员姓名！');
                    return false;
                }

                $.post("{{ route('admin.user.update') }}", {
                    user_id, password, section_id,
                    department_id: department, group_id, name,
                    _token: "{{ csrf_token() }}"
                }, function (data) {
                    alert(data.msg);
                    if (data.code === 200) {
                        location.reload();
                    }
                }, 'json');
            });
        });
    </script>
@endsection
