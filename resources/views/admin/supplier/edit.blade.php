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
            <span class="glyphicon glyphicon-edit"></span>
            <span class="panel-tit">编辑供应商</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $supplier->id }}">

                <!-- 供应商名称 -->
                <div class="form-item">
                    <label class="label">供应商名称：</label>
                    <div class="field">
                        <input type="text" name="name" value="{{ $supplier->name }}" placeholder="请输入供应商名称" class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 联系人 -->
                <div class="form-item">
                    <label class="label">联系人：</label>
                    <div class="field">
                        <input type="text" name="contact" value="{{ $supplier->contact ?? '' }}" placeholder="请输入联系人" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 电话 -->
                <div class="form-item">
                    <label class="label">电话：</label>
                    <div class="field">
                        <input type="text" name="phone" value="{{ $supplier->phone ?? '' }}" placeholder="请输入电话" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 邮箱 -->
                <div class="form-item">
                    <label class="label">邮箱：</label>
                    <div class="field">
                        <input type="text" name="email" value="{{ $supplier->email ?? '' }}" placeholder="请输入邮箱" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 地址 -->
                <div class="form-item">
                    <label class="label">地址：</label>
                    <div class="field">
                        <input type="text" name="address" value="{{ $supplier->address ?? '' }}" placeholder="请输入地址" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 排序 -->
                <div class="form-item">
                    <label class="label">排序：</label>
                    <div class="field">
                        <input type="number" name="sort" value="{{ $supplier->sort ?? 0 }}" placeholder="请输入排序值" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 状态 -->
                <div class="form-item">
                    <label class="label">状态：</label>
                    <div class="field">
                        <label style="margin-right: 20px;">
                            <input type="radio" name="status" value="1" {{ $supplier->status == 1 ? 'checked' : '' }}> 启用
                        </label>
                        <label>
                            <input type="radio" name="status" value="0" {{ $supplier->status == 0 ? 'checked' : '' }}> 禁用
                        </label>
                    </div>
                </div>

                <!-- 备注 -->
                <div class="form-item">
                    <label class="label">备注：</label>
                    <div class="field">
                        <textarea name="remark" rows="3" placeholder="请输入备注" class="form-control input-sm">{{ $supplier->remark ?? '' }}</textarea>
                    </div>
                </div>

                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认编辑
                        </button>
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
                var formData = $("#editForm").serialize();
                var btn = $(this);

                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.suppliers.update', $supplier->id) }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.href = "{{ route('admin.suppliers.index') }}";
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
        });
    </script>
@endsection
