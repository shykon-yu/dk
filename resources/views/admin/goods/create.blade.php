@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
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
            <span class="glyphicon glyphicon-plus"></span>
            <span class="panel-tit">新增客户</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="createForm">
                @csrf

                <!-- 上级客户 -->
                <div class="form-item">
                    <label class="label">上级客户：</label>
                    <div class="field">
                        <select name="parent_id" class="form-control input-sm selectpicker" data-live-search="true" title="请选择上级客户">
                            <option value="0">无上级</option>
                            @foreach($_customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 所属部门 -->
                <div class="form-item">
                    <label class="label">所属部门：</label>
                    <div class="field">
                        <select name="department_id" class="form-control input-sm selectpicker" data-live-search="true" title="请选择部门" required>
                            @foreach($_departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 客户名称 -->
                <div class="form-item">
                    <label class="label">客户名称：</label>
                    <div class="field">
                        <input type="text" name="name" value="" placeholder="请输入客户名称" class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 韩文名称 -->
                <div class="form-item">
                    <label class="label">韩文名称：</label>
                    <div class="field">
                        <input type="text" name="name_kr" value="" placeholder="请输入韩文名称" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 品牌LOGO -->
                <div class="form-item">
                    <label class="label">品牌LOGO：</label>
                    <div class="field">
                        <input type="text" name="brand_logo" value="" placeholder="请输入品牌LOGO" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 货号前缀 -->
                <div class="form-item">
                    <label class="label">货号前缀：</label>
                    <div class="field">
                        <input type="text" name="sku_prefix" value="" placeholder="请输入货号前缀" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 清关方式 -->
                <div class="form-item">
                    <label class="label">清关方式：</label>
                    <div class="field">
                        <select name="clearance_id" class="form-control input-sm selectpicker" data-live-search="true" title="请选择清关方式" required>
                            @foreach($_clearances as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 支付方式 -->
                <div class="form-item">
                    <label class="label">支付方式：</label>
                    <div class="field">
                        <select name="payment_id" class="form-control input-sm selectpicker" data-live-search="true" title="请选择支付方式" required>
                            @foreach($_payments as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 联系人 -->
                <div class="form-item">
                    <label class="label">联系人：</label>
                    <div class="field">
                        <input type="text" name="contact" value="" placeholder="请输入联系人" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 电话 -->
                <div class="form-item">
                    <label class="label">电话：</label>
                    <div class="field">
                        <input type="text" name="phone" value="" placeholder="请输入电话" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 邮箱 -->
                <div class="form-item">
                    <label class="label">邮箱：</label>
                    <div class="field">
                        <input type="text" name="email" value="" placeholder="请输入邮箱" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 地址 -->
                <div class="form-item">
                    <label class="label">地址：</label>
                    <div class="field">
                        <input type="text" name="address" value="" placeholder="请输入地址" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 排序 -->
                <div class="form-item">
                    <label class="label">排序：</label>
                    <div class="field">
                        <input type="number" name="sort" value="0" placeholder="请输入排序值" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 状态 -->
                <div class="form-item">
                    <label class="label">状态：</label>
                    <div class="field">
                        <label style="margin-right: 20px;">
                            <input type="radio" name="status" value="1" checked> 启用
                        </label>
                        <label>
                            <input type="radio" name="status" value="0"> 禁用
                        </label>
                    </div>
                </div>

                <!-- 备注 -->
                <div class="form-item">
                    <label class="label">备注：</label>
                    <div class="field">
                        <textarea name="remark" rows="3" placeholder="请输入备注" class="form-control input-sm"></textarea>
                    </div>
                </div>

                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认新增
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
            $('.selectpicker').selectpicker();

            $("#submitBtn").click(function(){
                var formData = $("#createForm").serialize();
                var btn = $(this);

                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.customers.store') }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.href = "{{ route('admin.customers.index') }}";
                    }
                }, 'json')
                    .fail(function(xhr){
                        let msg = xhr.responseJSON?.msg || "保存失败，请检查输入内容！";
                        alert(msg);
                    })
                    .always(function(){
                        btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 确认新增");
                    });
            });
        });
    </script>
@endsection
