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
            <span class="panel-tit">新增支付方式</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="createForm">
                @csrf

                <!-- 支付方式名称 -->
                <div class="form-item">
                    <label class="label">支付方式名称：</label>
                    <div class="field">
                        <input type="text" name="name" value=""
                               placeholder="请输入支付方式名称" class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 韩文名称 -->
                <div class="form-item">
                    <label class="label">韩文名称：</label>
                    <div class="field">
                        <input type="text" name="name_kr" value=""
                               placeholder="请输入韩文名称" class="form-control input-sm w50">
                    </div>
                </div>

                <!-- 排序 -->
                <div class="form-item">
                    <label class="label">排序：</label>
                    <div class="field">
                        <input type="number" name="sort" value="0"
                               placeholder="请输入排序值" class="form-control input-sm w50">
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

                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认新增
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
                var formData = $("#createForm").serialize();
                var btn = $(this);

                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.ajax({
                    url: "{{ route('admin.payments.store') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(data){
                        alert(data.msg);
                        if (data.code === 200) {
                            window.location.href = "{{ route('admin.payments.index') }}";
                        }
                    },
                    error: function(xhr){
                        let msg = xhr.responseJSON?.msg || "保存失败，请检查输入内容！";
                        alert(msg);
                    },
                    complete: function(){
                        btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 确认新增");
                    }
                });
            });
        });
    </script>
@endsection
