@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
        /* 适配表单间距，和订单页搜索栏一致 */
        .form-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .form-item .label {
            width: 100px;
            text-align: right;
            padding-right: 10px;
            font-weight: 500; /* 加粗一点 */
            font-size: 14px;   /* 字体正常 */
            color: #333;       /* 颜色加深 */
        }
        .form-item .field {
            flex: 1;
            max-width: 500px;
        }
        .form-control.input-sm {
            padding: 6px 10px; /* 输入框变大一点 */
            font-size: 14px;   /* 输入文字清晰 */
            color: #333;       /* 文字黑色 */
        }

        /* 面板标题样式强化 */
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
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">编辑菜单</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <!-- 编辑表单 -->
            <form id="editMenuForm">
                @csrf
                <input type="hidden" name="id" value="{{ $menu['id'] }}">

                <!-- 上级菜单 -->
                <div class="form-item">
                    <label class="label">上级菜单：</label>
                    <div class="field">
                        <select name="parent_id" class="form-control input-sm w50">
                            <option value="0">└─ 顶级菜单</option>
                            @foreach($allMenus as $item)
                                <!-- 一级菜单 -->
                                <option value="{{ $item['id'] }}" {{ $item['id'] == $menu['parent_id'] ? 'selected' : '' }}>
                                    {{ $item['title'] }}
                                </option>
                                <!-- 二级菜单（缩进展示，不可选自身/子级为上级） -->
                                @foreach($item['children'] ?? [] as $sub)
                                    @if($sub['id'] != $menu['id'])
                                        <option value="{{ $sub['id'] }}" {{ $sub['id'] == $menu['parent_id'] ? 'selected' : '' }}>
                                            &nbsp;&nbsp;├─ {{ $sub['title'] }}
                                        </option>
                                    @endif
                                    <!-- 三级菜单不做上级选项，避免层级过深 -->
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 菜单名称 -->
                <div class="form-item">
                    <label class="label">菜单名称：</label>
                    <div class="field">
                        <input type="text" name="title" value="{{ $menu['title'] }}" placeholder="请输入菜单名称"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 路由名称 -->
                <div class="form-item">
                    <label class="label">路由名称：</label>
                    <div class="field">
                        <input type="text" name="route" value="{{ $menu['route'] ?? '' }}" placeholder="例：admin.user.index（无路由留空）"
                               class="form-control input-sm w50">
                        <span style="color:#999; font-size:12px; margin-left:10px;">注：与路由定义一致，空则为纯菜单组</span>
                    </div>
                </div>
                <!-- 权限名称 -->
                <div class="form-item">
                    <label class="label">权限名称：</label>
                    <div class="field">
                        <input type="text" name="permission" value="{{ $menu['permission'] ?? '' }}" placeholder="例：admin.user.index"
                               class="form-control input-sm w50">
                        <span style="color:#999; font-size:12px; margin-left:10px;">注：与权限名称需完全一致</span>
                    </div>
                </div>

                <div class="form-item">
                    <label class="label">生成权限：</label>
                    <div class="field">
                        <label style="font-weight: normal; display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="auto_create_permission" value="1" checked>
                            自动生成该模块【index、store、update、destroy、audit、export】全套权限，否则只生成index
                        </label>
                        <span style="color:#ff6666; font-size:12px; margin-left:10px;">建议首次创建时勾选，已生成可取消</span>
                    </div>
                </div>
                <!-- 排序号 -->
                <div class="form-item">
                    <label class="label">排序号：</label>
                    <div class="field">
                        <input type="number" name="sort" value="{{ $menu['sort'] ?? 0 }}" placeholder="数字越小越靠前"
                               class="form-control input-sm" style="width: 100px;">
                    </div>
                </div>

                <!-- 操作按钮（和订单页/列表页按钮样式完全一致） -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 保存修改
                        </button>
                        <a href="{{ route('admin.menu.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
            var formData = $("#editMenuForm").serialize();
            var btn = $(this);

            // 禁用按钮 + 加载状态
            btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 保存中...");

            $.post("{{ route('admin.menu.update') }}", formData, function(data){
                // 成功提示
                alert(data.msg);

                // 成功后跳转
                if (data.code === 200) {
                    window.location.reload();
                    //window.location.href = "{{ route('admin.menu.index') }}";
                }
            }, 'json')
                .fail(function(xhr){
                    // 后台验证错误（你写的 MenuRequest 会返回这里）
                    let msg = xhr.responseJSON?.msg || "保存失败，请检查输入内容！";
                    alert(msg);
                })
                .always(function(){
                    // 无论成功失败，都恢复按钮（最关键）
                    btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 保存修改");
                });
        });
    });
</script>
@endsection
