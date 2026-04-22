@extends('admin.layouts.app')

@section('extends_js')
    <script src="/js/jquery-ui-1.9.2.custom.js"></script>
    <script src="/js/jquery.form.js"></script>
    <script src="/js/jquery.mousewheel.min.js"></script>
    <script src="/js/tableFix.js"></script>
@endsection

@section('extends_css')
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/ajaxupload/css/style.css" />
    <style>
        /* 整体宽度 50% */
        #dialog_radius {
            width: 50% !important;
            min-width: 500px;
        }
        .upload_label .upload_image {
            height: 35px;
        }
        #dialog_radius td {
            vertical-align: middle;
            text-align: center;
        }

        /* 加减号按钮美化（横向并排） */
        .btn-row {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
        }
        .btn-row .btn {
            padding: 2px 8px !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">产品编辑</span>
        </div>
        <div class="panel-body navbar-form">
            {{-- 隐藏ID字段，提交时传递产品ID --}}
            <input type="hidden" name="id" id="goods_id" value="{{ $good->id }}">

            <form id="order" onkeydown="if(event.keyCode==13)return false;">

                {{-- 顶部下拉 --}}
                <select class="form-control input-sm" name="department_id" id="department_id" style="width:18%;display:inline-block;">
                    <option value="">-请选择部门-</option>
                    @foreach($_departments as $vo)
                        <option value="{{ $vo->id }}" {{ $good->department_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                    @endforeach
                </select>

                <select class="form-control input-sm" name="customer_id" id="customer_id" style="width:18%;display:inline-block;">
                    <option value="">-请选择客户-</option>
                    @foreach($customers as $vo)
                    <option value="{{ $vo->id }}" {{ $good->customer_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                    @endforeach
                </select>

                <select name="supplier_id" id="supplier_id" class="selectpicker" style="width:28%;display:inline-block;"
                        data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true" title="请选择默认供应商">
                    @foreach($_suppliers as $vo)
                        <option value="{{ $vo->id }}" {{ $good->supplier_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                    @endforeach
                </select>

                <select class="form-control input-sm" name="season_id" id="season_id" style="width:28%;display:inline-block;">
                    @foreach($_goods_seasons as $vo)
                        <option value="{{ $vo->id }}" {{ $good->season_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                    @endforeach
                </select>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped" style="margin-top:2em;">
                    <tbody class="text-center p_body" id="major_log">

                    <tr>
                        <td>产品名称：</td>
                        <td data-key="name" class="padding_0 number">
                            <input type="text" class="form-control input-sm input_no_border" id="name" name="name" placeholder="请填写产品名称" value="{{ $good->name }}">
                        </td>
                    </tr>
                    <tr><td></td></tr>

                    <tr>
                        <td>产品货号：</td>
                        <td data-key="customer_sku" class="padding_0 number">
                            <input type="text" class="form-control input-sm input_no_border" id="customer_sku" name="customer_sku" placeholder="请填写产品货号" value="{{ $good->customer_sku }}">
                        </td>
                    </tr>
                    <tr><td></td></tr>

                    <tr>
                        <td>品牌LOGO：</td>
                        <td data-key="brand_logo" class="padding_0 number">
                            <input type="text" class="form-control input-sm input_no_border" name="brand_logo" placeholder="请填写品牌LOGO" value="{{ $good->brand_logo }}">
                        </td>
                    </tr>
                    <tr><td></td></tr>

                    {{-- ===================== 一级二级分类 同行 ===================== --}}
                    <tr>
                        <td>产品类目：</td>
                        <td class="padding_0 number" style="display:flex;gap:5px;">
                            <select name="category_pid" id="category_pid" class="form-control input-sm selectpicker"
                                    data-live-search="true" title="一级类目" style="width:48%;">
                                @foreach($_goods_categories as $vo)
                                    <option value="{{ $vo->id }}" {{ $good->category->parent_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                @endforeach
                            </select>
                            <select name="category_id" id="category_id" class="form-control input-sm selectpicker"
                                    data-live-search="true" title="二级分类" style="width:48%;">
                                @foreach($categoryChildren as $vo)
                                <option value="{{ $vo->id }}" {{ $good->category_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>仓库：</td>
                        <td data-key="warehouse_id" class="padding_0 number">
                            <select name="warehouse_id" id="warehouse_id"
                                    class="form-control selectpicker" data-live-search="true"
                                    data-live-search-placeholder="搜索" data-actions-box="true" title="请选择仓库">
                                @foreach($warehouses as $vo)
                                <option value="{{ $vo->id }}" {{ $good->warehouse_id == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>产品主图：</td>
                        <td data-key="color_image_id" class="padding_0 number">
                            <input type="hidden" name="main_image" id="main_image" value="{{ $good->main_image ?? '' }}">
                            <input type="hidden" name="thumb_image" id="thumb_image" value="{{ $good->thumb_image ?? '' }}">
                            {{-- 编辑页回显已上传的图片 --}}
                            <img id="preview_img" style="float:left;display:inline-block" width="40px" src="{{ $good->main_image ? asset($good->main_image) : '' }}">
                            <label class="upload_label">
                                <input type="button" class="btn" value="重新上传">
                                <span class="text"></span>
                                <input type="file" id="upload_img" class="upload_image" accept="image/*">
                            </label>
                        </td>
                    </tr>
                    <tr><td></td></tr>

                    </tbody>
                </table>

                {{-- ===================== 产品成分（独立模块 + 百分比） ===================== --}}
                <div style="width:50%;margin-top:30px;">
                    <label style="font-weight:bold;">产品成分</label>
                    <table class="table table-bordered table-condensed table-hover table-striped" style="margin:0;">
                        <thead>
                        <tr>
                            <th>操作</th>
                            <th>成分</th>
                            <th>百分比(%)</th>
                        </tr>
                        </thead>
                        <tbody id="component_tbody">
                        {{-- 编辑页回显成分数据 --}}
                        @if(!empty($good->components) && count($good->components) > 0)
                            @foreach($good->components as $component)
                                <tr class="component_tr">
                                    <td>
                                        <div class="btn-row">
                                            <button type="button" class="btn btn-sm btn-danger component_minus">-</button>
                                            <button type="button" class="btn btn-sm btn-success component_plus">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="component_id[]"  class="form-control input-sm selectpicker"
                                                data-live-search="true" title="选择成分">
                                            @foreach($_goods_components as $vo)
                                                <option value="{{ $vo->id }}" {{ $component['id'] == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="component_percent[]" class="form-control input-sm" placeholder="%" value="{{ format_decimal($component->pivot->percent) }}"></td>
                                </tr>
                            @endforeach
                        @else
                            {{-- 无数据时显示默认行 --}}
                            <tr class="component_tr">
                                <td>
                                    <div class="btn-row">
                                        <button type="button" class="btn btn-sm btn-danger component_minus">-</button>
                                        <button type="button" class="btn btn-sm btn-success component_plus">+</button>
                                    </div>
                                </td>
                                <td>
                                    <select name="component_id[]"  class="form-control input-sm selectpicker"
                                            data-live-search="true" title="选择成分">
                                        @foreach($_goods_components as $vo)
                                            <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="component_percent[]" class="form-control input-sm" placeholder="%" value="100"></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                {{-- ===================== 颜色分类 ===================== --}}
                <div style="width:100%;margin-top:30px;">
                    <label style="font-weight:bold;">颜色分类 <span style="color:red">***颜色库存生成后不能编辑，请去库存页面编辑，新增的颜色可以设置库存***</span></label>
                    <table class="table table-bordered table-condensed table-hover table-striped" style="margin:0;">
                        <thead>
                        <tr>
                            <th colspan="3">批量输入</th>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="color_card" placeholder="批量色号"></td>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="stock" placeholder="批量库存"></td>

                            <td>
                                <select class="form-control input-sm batch_currency_sell">
                                    <option value="">-售价货币-</option>
                                    @foreach($_currencies as $vo)
                                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="sell_price" placeholder="批量售价1"></td>

                            <td><input type="text" class="form-control input-sm batch_all" data-target="sell_price2" placeholder="批量售价2"></td>

                            <td>
                                <select class="form-control input-sm batch_currency_cost">
                                    <option value="">-成本货币-</option>
                                    @foreach($_currencies as $vo)
                                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="cost_price" placeholder="批量进价1"></td>

                            <td><input type="text" class="form-control input-sm batch_all" data-target="cost_price2" placeholder="批量进价2"></td>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="process_price" placeholder="批量辅材1"></td>
                            <td><input type="text" class="form-control input-sm batch_all" data-target="process_step2_price" placeholder="批量辅材2"></td>
                        </tr>
                        <tr>
                            <th>操作</th>
                            <th>启用状态</th>
                            <th>颜色名称</th>
                            <th>色号</th>
                            <th>库存</th>
                            <th>售价货币</th>
                            <th>售价1</th>
                            <th>售价2</th>
                            <th>成本货币</th>
                            <th>进价1</th>
                            <th>进价2</th>
                            <th>辅材1</th>
                            <th>辅材2</th>
                        </tr>
                        </thead>
                        <tbody id="log_color">
                        {{-- 编辑页回显颜色数据 --}}
                        @if(!empty($good->skus) && count($good->skus) > 0)
                            @foreach($good->skus as $sku)
                                <tr class="tr_spec" id="{{$sku->id}}">
                                    <td>
                                        <div class="btn-row">
                                            <button type="button" class="btn btn-sm btn-danger color_minus">-</button>
                                            <button type="button" class="btn btn-sm btn-success color_plus">+</button>
                                        </div>
                                    </td>
                                    <td data-key="id" class="hidden">
                                        <input type="hidden" name="id[]" value="{{$sku->id}}">
                                    </td>
                                    <td data-key="status">
                                        <select class="form-control input-sm status" name="status[]">
                                            <option value="1" {{ $sku['status'] == 1 ? 'selected' : '' }}>启用</option>
                                            <option value="0" {{ $sku['status'] == 0 ? 'selected' : '' }}>不启用</option>
                                        </select>
                                    </td>
                                    <td data-key="color"><input type="text" class="form-control input-sm color" name="color[]" placeholder="颜色" value="{{ $sku['color'] }}"></td>
                                    <td data-key="color_card"><input type="text" class="form-control input-sm color_card" name="color_card[]" value="{{ $sku['color_card'] }}"></td>
                                    <td data-key="stock"><input type="text" class="form-control input-sm stock" name="stock[]" value="{{ $sku['stock'] }}"></td>

                                    <td data-key="sell_currency_id">
                                        <select class="form-control input-sm sell_currency_id" name="sell_currency_id[]">
                                            <option value="">-售价货币-</option>
                                            @foreach($_currencies as $vo)
                                                <option value="{{ $vo->id }}" {{ $sku['sell_currency_id'] == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td data-key="sell_price"><input type="text" class="form-control input-sm sell_price" name="sell_price[]" value="{{ $sku['sell_price'] }}"></td>
                                    <td data-key="sell_price2"><input type="text" class="form-control input-sm sell_price2" name="sell_price2[]" value="{{ $sku['sell_price2'] }}"></td>

                                    <td data-key="cost_currency_id">
                                        <select class="form-control input-sm cost_currency_id" name="cost_currency_id[]">
                                            <option value="">-成本货币-</option>
                                            @foreach($_currencies as $vo)
                                                <option value="{{ $vo->id }}" {{ $sku['cost_currency_id'] == $vo->id ? 'selected' : '' }}>{{ $vo->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td data-key="cost_price"><input type="text" class="form-control input-sm cost_price" name="cost_price[]" value="{{ $sku['cost_price'] }}"></td>

                                    <td data-key="cost_price2"><input type="text" class="form-control input-sm cost_price2" name="cost_price2[]" value="{{ $sku['cost_price2'] }}"></td>
                                    <td data-key="process_price"><input type="text" class="form-control input-sm process_price" name="process_price[]" value="{{ $sku['process_price'] }}"></td>
                                    <td data-key="process_step2_price"><input type="text" class="form-control input-sm process_step2_price" name="process_step2_price[]" value="{{ $sku['process_step2_price'] }}"></td>
                                </tr>
                            @endforeach
                        @else
                            {{-- 无数据时显示默认行 --}}
                            <tr class="tr_spec">
                                <td>
                                    <div class="btn-row">
                                        <button type="button" class="btn btn-sm btn-danger color_minus">-</button>
                                        <button type="button" class="btn btn-sm btn-success color_plus">+</button>
                                    </div>
                                </td>
                                <td data-key="status">
                                    <select class="form-control input-sm status" name="status[]">
                                        <option value="1">启用</option>
                                        <option value="0">不启用</option>
                                    </select>
                                </td>
                                <td data-key="color"><input type="text" class="form-control input-sm color" name="color[]" placeholder="颜色"></td>
                                <td data-key="color_card"><input type="text" class="form-control input-sm color_card" name="color_card[]"></td>
                                <td data-key="stock"><input type="text" class="form-control input-sm stock" name="stock[]" value="0"></td>

                                <td data-key="sell_currency_id">
                                    <select class="form-control input-sm sell_currency_id" name="sell_currency_id[]">
                                        <option value="">-售价货币-</option>
                                        @foreach($_currencies as $vo)
                                            <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td data-key="sell_price"><input type="text" class="form-control input-sm sell_price" name="sell_price[]" value="0"></td>
                                <td data-key="sell_price2"><input type="text" class="form-control input-sm sell_price2" name="sell_price2[]" value="0"></td>

                                <td data-key="cost_currency_id">
                                    <select class="form-control input-sm cost_currency_id" name="cost_currency_id[]">
                                        <option value="">-成本货币-</option>
                                        @foreach($_currencies as $vo)
                                            <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td data-key="cost_price"><input type="text" class="form-control input-sm cost_price" name="cost_price[]" value="0"></td>

                                <td data-key="cost_price2"><input type="text" class="form-control input-sm cost_price2" name="cost_price2[]" value="0"></td>
                                <td data-key="process_price"><input type="text" class="form-control input-sm process_price" name="process_price[]" value="0"></td>
                                <td data-key="process_step2_price"><input type="text" class="form-control input-sm process_step2_price" name="process_step2_price[]" value="0"></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                {{-- 备注 --}}
                <div style="width:50%;margin-top:20px;">
                    <label style="font-weight:bold;">备注</label>
                    <textarea class="form-control input-sm" id="remark" name="remark" placeholder="请填写备注"cols="80" rows="3">{{ $good->remark ?? '' }}</textarea>
                </div>

                {{-- 提交 --}}
                <div style="width:50%;margin-top:20px;text-align:right;">
                    <button class="btn btn-success" type="submit" id="p_confirm">提交修改</button>
                </div>

            </form>
        </div>

    </div>
@endsection

@section('script_js')
    <script>
        $(function(){
            // ===================== 图片上传（编辑页兼容：重新上传覆盖原有路径） =====================
            $(document).on('change', '#upload_img', function(){
                let file = $(this)[0].files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('admin.goods.upload.image') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        if (res.code === 200) {
                            // 1. 覆盖原有主图/缩略图路径
                            $('#main_image').val(res.data.url);
                            $('#thumb_image').val(res.data.thumb_url);

                            // 2. 更新预览图
                            let previewUrl = "{{ asset(':url') }}".replace(':url', res.data.url);
                            $('#preview_img').attr('src', previewUrl);

                            alert('重新上传成功');
                        } else {
                            alert(res.msg ?? '上传失败');
                        }
                    },
                    error: function () {
                        alert('上传请求失败');
                    }
                });
            });

            // ===================== 一级分类 → 二级分类 AJAX（编辑页兼容） =====================
            $(document).on('change', '#category_pid', function(){
                let pid = $(this).val();
                $('#category_id').html('<option value="">-选择二级分类-</option>').selectpicker('refresh');
                if(!pid) return;

                $.post("{{ route('admin.common.category-by-parent') }}", {
                    pid: pid,
                    _token: "{{ csrf_token() }}"
                }, function(res){
                    if (res.code !== 200) {
                        alert(res.msg || '获取失败');
                        return;
                    }
                    let html = '<option value="">-选择二级分类-</option>';
                    if(res.data) {
                        res.data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                    }
                    $('#category_id').html(html).selectpicker('refresh');
                }, 'json').fail(function(){
                    alert('服务器请求失败');
                });
            });

            // ===================== 成分 加减（编辑页兼容） =====================
            $(document).on('click', '.component_plus', function(){
                let tr = $(this).closest('.component_tr').clone();
                tr.find('.bootstrap-select').find('button:first').remove();
                tr.find('.selectpicker').selectpicker('val', '');
                tr.find('input[name="component_percent[]"]').val(0);
                $('#component_tbody').append(tr);
                tr.find('.selectpicker').selectpicker('refresh');
                tr.find('.selectpicker').selectpicker('render');
            });

            $(document).on('click', '.component_minus', function(){
                if($('#component_tbody .component_tr').length <=1){
                    alert('至少保留一行成分');
                    return false;
                }
                $(this).closest('.component_tr').remove();
            });

            // ===================== 颜色 加减（编辑页兼容） =====================
            $(document).on('click', '.color_plus', function(){
                let tr = $(this).closest('.tr_spec').clone();
                tr.attr('id','').find('input').not('.sell_price,.sell_price2,.cost_price,.cost_price2,.process_price,.process_step2_price').val('0');
                tr.find('.color').val('');
                $('#log_color').append(tr);
            });
            $(document).on('click', '.color_minus', function(){
                if($('#log_color .tr_spec').length <=1){
                    alert('至少保留一行颜色'); return;
                }
                $(this).closest('.tr_spec').remove();
            });

            // ===================== 批量输入（编辑页兼容） =====================
            $(document).on('input', '.batch_all', function(){
                let cls = $(this).data('target');
                let val = $(this).val();
                $('#log_color .'+cls).val(val);
            });

            // ===================== 批量货币（编辑页兼容） =====================
            $(document).on('change', '.batch_currency_sell', function(){
                let val = $(this).val();
                $('.sell_currency_id').val(val);
            });
            $(document).on('change', '.batch_currency_cost', function(){
                let val = $(this).val();
                $('.cost_currency_id').val(val);
            });

            // ===================== 部门切换 → 加载客户 + 仓库（编辑页兼容） =====================
            $(document).on('change', '#department_id', function () {
                let department_id = $(this).val();
                $('#customer_id').html('<option value="">-请选择客户-</option>');
                $('#warehouse_id').html('').selectpicker('refresh');
                if (!department_id) return;

                // 客户
                $.ajax({
                    url: "{{ route('admin.common.customer-by-dept') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", department_id: department_id },
                    dataType: "json",
                    success: function (res) {
                        if (res.code === 200) {
                            let str = '<option value="">-请选择客户-</option>';
                            $.each(res.data, function (i, item) {
                                str += `<option value="${item.id}">${item.name}</option>`;
                            });
                            $('#customer_id').html(str);
                        }
                    }
                });

                // 仓库
                $.ajax({
                    url: "{{ route('admin.common.warehouse-by-dept') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", department_id: department_id },
                    dataType: "json",
                    success: function (res) {
                        if (res.code === 200) {
                            let str = '<option value="">-请选择仓库-</option>';
                            $.each(res.data, function (i, item) {
                                str += `<option value="${item.id}">${item.name}</option>`;
                            });
                            $('#warehouse_id').html(str).selectpicker('refresh');
                        }
                    }
                });
            });

            // ===================== 【最终提交】整合所有数据（编辑页：提交到update路由） =====================
            $("#order").validate({
                onsubmit: true,
                rules: {
                    department_id: {required:true},
                    customer_id: {required:true},
                    supplier_id: {required:true},
                    season_id: {required:true},
                    name: {required:true},
                    customer_sku: {required:true},
                    brand_logo: {required:true},
                    category_pid: {required:true},
                    category_id: {required:true},
                    warehouse_id: {required:true},
                },
                messages: {
                    department_id: "请选择部门",
                    customer_id: "请选择客户",
                    supplier_id: "请选择供应商",
                    season_id: "请选择年份",
                    name: "请填写产品名称",
                    customer_sku: "请填写货号",
                    brand_logo: "请填写LOGO",
                    category_pid: "请选择一级类目",
                    category_id: "请选择二级分类",
                    warehouse_id: "请选择仓库",
                },
                submitHandler: function(form){
                    $("#p_confirm").prop('disabled',true).text('提交中...');

                    // 1. 收集基础表单数据（包含隐藏的产品ID）
                    let formData = $(form).serializeArray();
                    let data = {};
                    $.each(formData, function(){
                        data[this.name] = this.value;
                    });

                    // 2. 整合【成分数组】
                    let components = [];
                    $('#component_tbody .component_tr').each(function(){
                        let component_id = $(this).find('select[name="component_id[]"]').val();
                        let percent = $(this).find('input[name="component_percent[]"]').val();
                        components.push({
                            component_id: component_id,
                            percent: percent
                        });
                    });
                    data.components = components;

                    // 3. 整合【颜色数组】
                    let colors = [];
                    $('#log_color .tr_spec').each(function(){
                        let id = $(this).attr('id');
                        let status = $(this).find('.status').val();
                        let color = $(this).find('.color').val();
                        let color_card = $(this).find('.color_card').val();
                        let stock = $(this).find('.stock').val();
                        let sell_currency_id = $(this).find('.sell_currency_id').val();
                        let sell_price = $(this).find('.sell_price').val();
                        let sell_price2 = $(this).find('.sell_price2').val();
                        let cost_currency_id = $(this).find('.cost_currency_id').val();
                        let cost_price = $(this).find('.cost_price').val();
                        let cost_price2 = $(this).find('.cost_price2').val();
                        let process_price = $(this).find('.process_price').val();
                        let process_step2_price = $(this).find('.process_step2_price').val();

                        colors.push({
                            id: id,
                            status: status,
                            color: color,
                            color_card: color_card,
                            stock: stock,
                            sell_currency_id: sell_currency_id,
                            sell_price: sell_price,
                            sell_price2: sell_price2,
                            cost_currency_id: cost_currency_id,
                            cost_price: cost_price,
                            cost_price2: cost_price2,
                            process_price: process_price,
                            process_step2_price: process_step2_price
                        });
                    });
                    data.colors = colors;

                    // 4. 提交（编辑页：路由改为admin.goods.update，传递产品ID）
                    $.ajax({
                        url: "{{ route('admin.goods.update', $good->id) }}",
                        type: "POST", // Laravel PUT/PATCH需配合_method字段，这里用POST+_method
                        data: {
                            ...data,
                            _method: 'PUT' // 模拟PUT请求
                        },
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res){
                            alert(res.msg);
                            if(res.code === 200){
                                // 编辑成功后可返回列表页，或刷新当前页
                                // location.href = "{{ route('admin.goods.index') }}";
                                location.reload();
                            }
                            $("#p_confirm").prop('disabled',false).text('提交修改');
                        },
                        error: function(){
                            alert('提交失败');
                            $("#p_confirm").prop('disabled',false).text('提交修改');
                        }
                    });
                }
            });

            // ===================== 编辑页初始化：刷新所有selectpicker =====================
            $('.selectpicker').selectpicker('refresh');
        });
    </script>
@endsection
