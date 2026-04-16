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
            <span class="panel-tit">编辑商品分类</span>
        </div>

        <div class="panel-body navbar-form" style="padding-top: 20px;">
            <form id="editCategoryForm">
                @csrf
                @method('PUT')

                <!-- 分类名称 -->
                <div class="form-item">
                    <label class="label">分类名称：</label>
                    <div class="field">
                        <input type="text" name="name" value="{{ $category->name }}" placeholder="请输入分类名称"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 父级分类（只显示一级，不能选自己） -->
                <div class="form-item">
                    <label class="label">父级分类：</label>
                    <div class="field">
                        <select name="parent_id" class="form-control input-sm w50" required>
                            <option value="0">顶级分类（一级）</option>
                            @foreach($parentCategories as $item)
                                @if($item->id != $category->id)
                                    <option value="{{ $item->id }}" {{ $category->parent_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 排序 -->
                <div class="form-item">
                    <label class="label">排序：</label>
                    <div class="field">
                        <input type="number" name="sort" value="{{ $category->sort }}" placeholder="数字越小越靠前"
                               class="form-control input-sm w50" required>
                    </div>
                </div>

                <!-- 状态 -->
                <div class="form-item">
                    <label class="label">启用状态：</label>
                    <div class="field">
                        <label style="margin-right: 15px;">
                            <input type="radio" name="status" value="1" {{ $category->status == 1 ? 'checked' : '' }}> 启用
                        </label>
                        <label>
                            <input type="radio" name="status" value="0" {{ $category->status == 0 ? 'checked' : '' }}> 禁用
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
                        <a href="{{ route('admin.goods.categories.index') }}" class="btn btn-warning btn-sm" style="margin-left:10px;">
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
                var formData = $("#editCategoryForm").serialize();
                var btn = $(this);

                btn.prop("disabled", true).html("<span class='glyphicon glyphicon-refresh glyphicon-spin'></span> 提交中...");

                $.post("{{ route('admin.goods.categories.update', $category) }}", formData, function(data){
                    alert(data.msg);
                    if (data.code === 200) {
                        window.location.href = "{{ route('admin.goods.categories.index') }}";
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
