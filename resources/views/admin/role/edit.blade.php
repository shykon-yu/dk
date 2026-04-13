@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
        /* 完全和菜单新增页保持一致 */
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

        /* 权限树样式 */
        .permission-box {
            border: 1px solid #e5e5e5;
            padding: 15px;
            max-height: 420px;
            overflow-y: auto;
            border-radius: 4px;
        }
        .menu-group {
            margin-bottom: 12px;
        }
        .menu-title {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .perm-item {
            display: inline-block;
            margin-right: 18px;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .child-perms {
            padding-left: 22px;
            margin-bottom: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-edit"></span>
            <span class="panel-tit">编辑角色</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="editRoleForm">
                @csrf

                <!-- 角色ID（隐藏） -->
                <input type="hidden" name="id" value="{{ $role->id }}">

                <!-- 角色名称 -->
                <div class="form-item">
                    <label class="label">角色名称：</label>
                    <div class="field">
                        <input type="text" name="name" value="{{ $role->name }}" placeholder="请输入角色名称"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 权限分配（树形结构 + 自动勾选） -->
                <div class="form-item">
                    <label class="label">分配权限：</label>
                    <div class="field">
                        <div class="permission-box">
                            @foreach($menuTree as $menu)
                                <div class="menu-group">
                                    <label class="menu-title">
                                        <input type="checkbox" class="module-check" data-menu-id="{{ $menu['id'] }}">
                                        {{ $menu['title'] }}（全选）
                                    </label>

                                    @if(isset($permissionMap[$menu['id']]) && count($permissionMap[$menu['id']]))
                                        <div class="child-perms">
                                            @foreach($permissionMap[$menu['id']] as $perm)
                                                <label class="perm-item">
                                                    <input type="checkbox" name="permissions[]"
                                                           class="perm-check"
                                                           data-menu-id="{{ $menu['id'] }}"
                                                           value="{{ $perm->id }}"
                                                        {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}>
                                                    {{ $perm->title }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(isset($menu['children']) && count($menu['children']))
                                        @foreach($menu['children'] as $sub)
                                            <div class="menu-group" style="margin-left:22px;">
                                                <label class="menu-title">
                                                    <input type="checkbox" class="module-check" data-menu-id="{{ $sub['id'] }}">
                                                    {{ $sub['title'] }}（全选）
                                                </label>

                                                @if(isset($permissionMap[$sub['id']]) && count($permissionMap[$sub['id']]))
                                                    <div class="child-perms">
                                                        @foreach($permissionMap[$sub['id']] as $perm)
                                                            <label class="perm-item">
                                                                <input type="checkbox" name="permissions[]"
                                                                       class="perm-check"
                                                                       data-menu-id="{{ $sub['id'] }}"
                                                                       value="{{ $perm->id }}"
                                                                    {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}>
                                                                {{ $perm->title }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认编辑
                        </button>
                        <a href="{{ route('admin.role.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
            // 提交编辑
            $("#submitBtn").click(function(){
                var formData = $("#editRoleForm").serialize();
                var btn = $(this);

                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.role.update') }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.reload();
                       // window.location.href = "{{ route('admin.role.index') }}";
                    }
                }, 'json')
                    .fail(function(xhr){
                        let msg = xhr.responseJSON?.msg || "保存失败，请检查输入内容！";
                        alert(msg);
                    })
                    .always(function(){
                        btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 确认编辑");
                    });
            });

            // 全选功能（按菜单）
            $(document).on("click", ".module-check", function(){
                let menuId = $(this).data("menu-id");
                let checked = $(this).is(":checked");
                $(".perm-check[data-menu-id='" + menuId + "']").prop("checked", checked);
            });
        });
    </script>
@endsection
