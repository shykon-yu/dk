@extends('admin.layouts.app')
@section('extends_css')
    <style type="text/css">
        /* 完全统一 */
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
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">编辑商品季节</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="editSeasonForm">
                @csrf
                <input type="hidden" name="id" value="{{ $season->id }}">

                <!-- 季节名称 -->
                <div class="form-item">
                    <label class="label">季节名称：</label>
                    <div class="field">
                        <input type="text" name="name" value="{{ $season->name }}" placeholder="请输入季节名称"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 年份 -->
                <div class="form-item">
                    <label class="label">年份：</label>
                    <div class="field">
                        <input type="number" name="year" value="{{ $season->year }}" placeholder="请输入数字年份"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 季节选项 -->
                <div class="form-item">
                    <label class="label">季节：</label>
                    <div class="field">
                        <select name="season" class="form-control input-sm w50" required>
                            <option value="">请选择季节</option>
                            <option value="1" {{ $season->season == 1 ? 'selected' : '' }}>春季</option>
                            <option value="2" {{ $season->season == 2 ? 'selected' : '' }}>夏季</option>
                            <option value="3" {{ $season->season == 3 ? 'selected' : '' }}>秋季</option>
                            <option value="4" {{ $season->season == 4 ? 'selected' : '' }}>冬季</option>
                        </select>
                    </div>
                </div>

                <!-- 状态 -->
                <div class="form-item">
                    <label class="label">启用状态：</label>
                    <div class="field">
                        <label style="margin-right: 15px;">
                            <input type="radio" name="status" value="1" {{ $season->status == 1 ? 'checked' : '' }}> 启用
                        </label>
                        <label>
                            <input type="radio" name="status" value="0" {{ $season->status == 0 ? 'checked' : '' }}> 禁用
                        </label>
                    </div>
                </div>

                <!-- 按钮 -->
                <div class="form-item">
                    <label class="label"></label>
                    <div class="field">
                        <button type="button" class="btn btn-info btn-sm" id="submitBtn">
                            <span class="glyphicon glyphicon-ok"></span> 确认修改
                        </button>
                        <a href="{{ route('admin.goods.seasons.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
                var formData = $("#editSeasonForm").serialize();
                var btn = $(this);
                formData += "&_method=PATCH";
                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.goods.seasons.update',$season->id) }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.href = "{{ route('admin.goods.seasons.index') }}";
                    }
                }, 'json')
                    .fail(function(xhr){
                        let msg = xhr.responseJSON?.msg || "保存失败，请检查输入内容！";
                        alert(msg);
                    })
                    .always(function(){
                        btn.prop("disabled", false).html("<span class='glyphicon glyphicon-ok'></span> 确认修改");
                    });
            });
        });
    </script>
@endsection
