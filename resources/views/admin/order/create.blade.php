@extends('admin.layouts.app')

@section('extends_css')
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/ajaxupload/css/style.css"/>
    <style>
        #log td {
            vertical-align: middle;
            text-align: center;
        }

        .product_minus, .product_plus {
            cursor: pointer;
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">添加订货</span>
        </div>
        <div class="panel-body navbar-form">
            <form id="order" onkeydown="if(event.keyCode==13)return false;">

                <select class="form-control input-sm" name="department_id" id="department_id">
                    <option value="">-请选择部门-</option>
                    @foreach($_departments_auth as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <select class="form-control input-sm" name="customer_id" id="customer_id">
                    <option value="">-请选择客户-</option>
                </select>

                <select name="supplier_company_id" id="supplier_company_id" class="selectpicker"
                        data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true" title="请选择供应商">
                    @foreach($_suppliers as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <div class="input-group">
                    <input class="form-control input-sm" name="order_date" id="order_date" autocomplete="off"
                           placeholder="订货日期" type="text">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
                <div class="input-group">
                    <input class="form-control input-sm" name="delivery_date" id="delivery_date" autocomplete="off"
                           placeholder="交货日期" type="text">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <span class="order">订单号：<span id="order_number"></span></span>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped"
                       style="margin-top:2em;">
                    <thead>
                    <tr>
                        <th colspan="2">排头信息</th>
                        <th colspan="4">产品信息</th>
                        <th colspan="6">订单信息</th>
{{--                        <th colspan="6">辅材信息</th>--}}
                    </tr>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>色号</th>
                        <th>数量</th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额</th>
                        <th>备注</th>
{{--                        <th style='color:blueviolet'>供应商1</th>--}}
{{--                        <th style='color:blueviolet'>单价1</th>--}}
{{--                        <th style='color:blueviolet'>金额1</th>--}}
{{--                        <th style='color:blueviolet'>供应商2</th>--}}
{{--                        <th style='color:blueviolet'>单价2</th>--}}
{{--                        <th style='color:blueviolet'>金额2</th>--}}
                    </tr>
                    </thead>
                    <tbody class="text-center p_body" id="log">
                    <tr>
                        <td>
{{--                            <span class="glyphicon glyphicon-minus product_minus"></span>&nbsp;&nbsp;--}}
{{--                            <span class="glyphicon glyphicon-plus product_plus"></span>--}}
                            <div class="btn-row">
                                <button type="button" class="btn btn-sm btn-danger goods_minus ">-</button>
                                <button type="button" class="btn btn-sm btn-success goods_plus ">+</button>
                            </div>
                        </td>
                        <td class="serial_number">1</td>
                        <td data-key="goods_id" class="padding_0 select_products">
                            <select name="goods_id"
                                    class="input_no_border form-control selectpicker goods_id"
                                    data-live-search="true"
                                    data-live-search-placeholder="输入货号/名称搜索"
                                    title="请选择产品">
                                @foreach($goods as $good)
                                    <option value="{{$good->id}}">{{$good->customer_sku}}</option>
                                @endforeach

                            </select>
                        </td>
                        <td>
                            <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; ">
                                {{-- 缩略图 --}}
                                <img height="20px" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                     class="thumb-img click-preview"
                                     data-src=""
                                     style="width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">

                                {{-- 右侧悬浮预览图 --}}
                                <img src=""
                                     class="hover-preview"
                                     style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                            </div>
                        </td>
                        <td data-key="sku_id" class="padding_0">
                            <select name="sku_id"
                                    class="form-control input_no_border input-sm sku_id">
                                <option value="">--请选择颜色--</option>
                            </select>
                        </td>
                        <td data-key="color_card" class="padding_0">
                            <input type="text" class="form-control input-sm input_no_border color_card"
                                   name="color_card" placeholder="色号">
                        </td>
                        <td data-key="number" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm number" name="number"
                                   value="0" placeholder="数量">
                        </td>
                        <td data-key="currency_id" class="padding_0">
                            <select class="form-control input_no_border input-sm currency_id" name="currency_id">
                                @foreach($_currencies as $vo)
                                    <option value="{{ $vo->id }}"
                                            currency_icon="{{ $vo->currency_symbol }}" {{ $vo->id==1 ? 'selected' : '' }}>
                                        {{ $vo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td data-key="price" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm price" name="price"
                                   placeholder="单价">
                        </td>
                        <td data-key="money" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm money" name="money"
                                   placeholder="金额">
                        </td>
                        <td data-key="remark" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm remark" name="remark"
                                   placeholder="备注">
                        </td>
{{--                        <td data-key="process_company_id" class="padding_0">--}}
{{--                            <select name="process_company_id"--}}
{{--                                    class="input_no_border form-control selectpicker process_company_id"--}}
{{--                                    data-live-search="true" title="供应商">--}}
{{--                                <option value="0">供应商</option>--}}
{{--                                @foreach($_suppliers as $vo)--}}
{{--                                    <option value="{{ $vo->id }}">{{ $vo->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </td>--}}
{{--                        <td data-key="process_price" class="padding_0">--}}
{{--                            <input type="text" value="0" class="form-control input_no_border input-sm process_price"--}}
{{--                                   name="process_price">--}}
{{--                        </td>--}}
{{--                        <td data-key="process_money" class="padding_0">--}}
{{--                            <input type="text" class="form-control input_no_border input-sm process_money" value="0"--}}
{{--                                   name="process_money" readonly>--}}
{{--                        </td>--}}
{{--                        <td data-key="process_company_id2" class="padding_0">--}}
{{--                            <select name="process_company_id2"--}}
{{--                                    class="input_no_border form-control selectpicker process_company_id2"--}}
{{--                                    data-live-search="true" title="供应商">--}}
{{--                                <option value="0">供应商</option>--}}
{{--                                @foreach($_suppliers as $vo)--}}
{{--                                    <option value="{{ $vo->id }}">{{ $vo->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </td>--}}
{{--                        <td data-key="process_price2" class="padding_0">--}}
{{--                            <input type="text" value="0" class="form-control input_no_border input-sm process_price2"--}}
{{--                                   name="process_price2">--}}
{{--                        </td>--}}
{{--                        <td data-key="process_money2" class="padding_0">--}}
{{--                            <input type="text" class="form-control input_no_border input-sm process_money2" value="0"--}}
{{--                                   name="process_money2" readonly>--}}
{{--                        </td>--}}
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>色号</th>
                        <th>数量<br><span id="total_number"></span></th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额<br><span id="total_money"></span></th>
                        <th>备注</th>
{{--                        <th>供应商1</th>--}}
{{--                        <th>单价1</th>--}}
{{--                        <th>金额1<br><span id="total_process_money"></span></th>--}}
{{--                        <th>供应商2</th>--}}
{{--                        <th>单价2</th>--}}
{{--                        <th>金额2<br><span id="total_process_money2"></span></th>--}}
                    </tr>
                    <tr class="hidden" id="timg">
                        <th colspan="18"><img src="/images/timg.gif"/>正在提交中...</th>
                    </tr>
                    </tfoot>
                </table>

                <button class="btn btn-success pull-right" type="submit" id="p_confirm">提交</button>
            </form>
        </div>
    </div>

    <div class="form-group container">
        <label for="comment">其他注意事项:</label>
        <textarea class="form-control" rows="5" id="comment"></textarea>
    </div>

    <div id="success" class="text-center text-info" style="display:none;">
        <div style="margin-top:1.5em"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;添加成功</div>
    </div>

    <!-- 上传Excel -->
    <div class="upload_main container" id="upload_file">
        <h4 class="title">上传生产通知书</h4>
        <input type="hidden" name="excel_id" id="excel_id" value="0">
        <label class="upload_label">
            <span class="text"></span>
            <input type="file" id="fileinp" class="upload_excel" accept="*">
        </label>
    </div>
@endsection

@section('script_js')
    <script>
        // 全局缓存：当前客户的【默认200个商品】
        var CURRENT_CUSTOMER_DEFAULT_GOODS = [];
        // 存商品搜索框的关键词
        var GOODS_SEARCH_KEYWORD = '';
        $(function () {
            // 日期初始化
            $("#order_date").datepicker({maxDate: 0});
            $("#delivery_date").datepicker();

            // 订单号
            var u = new Date().getTime();
            var mi = Math.floor(Math.random() * 900 + 100);
            $("#order_number").text("o" + u + mi);

            // 回车切换
            $(document).on("keyup", "input", function (e) {
                if (e.keyCode == 13) {
                    $(this).parents("tr").next().find("input").select();
                }
            });

            // 加行
            $(document).on("click", ".goods_plus", function () {
                let _this_tr = $(this).parents("tr");
                let main_image = _this_tr.find('.thumb-img').data('src');
                let tr = _this_tr.clone();
                _this_tr.after(tr);
                _this_tr.next().find('.thumb-img').data('src',main_image);
                _this_tr.next().find('.bootstrap-select').find("button:first").remove();
                _this_tr.next().find('.selectpicker').selectpicker("val");
                _this_tr.next().find("input,select").not(".goods_id,.process_company_id,.process_company_id2,.currency_id").val("");
                _this_tr.next().find(".process_price,.process_price2,.number").val(0);
                _this_tr.next().find(".money,.process_money,.process_money2").val(0);
                _this_tr.next().find('.selectpicker').selectpicker('refresh');
                _this_tr.next().find('.selectpicker').selectpicker('render');

                setTimeout(() => {
                    _this_tr.next().find('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD);
                }, 100);
                // $(this).remove();
                refreshSerial();
            });

            // 减行
            $(document).on("click", ".goods_minus", function () {
                if ($("#log tr").length <= 1) {
                    alert("至少保留一行");
                    return;
                }
                $(this).parents("tr").remove();
                refreshSerial();
                totalAll();
            });

            function refreshSerial() {
                $(".serial_number").each(function (i) {
                    $(this).text(i + 1);
                });
            }

            // 计算
            $(document).on("input propertychange", ".p_body input", function () {
                stereNum(this);
                totalAll();
            });

            function stereNum(obj) {
                let self = $(obj).parents("tr");
                let price = parseFloat(self.find(".price").val() || 0);
                let number = parseFloat(self.find(".number").val() || 0);
                // let p1 = parseFloat(self.find(".process_price").val() || 0);
                // let p2 = parseFloat(self.find(".process_price2").val() || 0);
                self.find(".money").val((price * number).toFixed(2));
                // self.find(".process_money").val((p1 * number).toFixed(2));
                // self.find(".process_money2").val((p2 * number).toFixed(2));
            }

            // 合计
            function totalAll() {
                total("number", 0);
                total("money", 2);
                // total("process_money", 2);
                // total("process_money2", 2);
            }

            function total(t, n) {
                let sum = 0;
                $("." + t).each(function () {
                    sum += parseFloat($(this).val() || 0);
                });
                $("#total_" + t).text(sum.toFixed(n));
            }

            // 部门 → 客户
            $(document).on('change', '#department_id', function () {
                let department_id = $(this).val();
                $('#customer_id').html('<option value="">-请选择客户-</option>');
                if (!department_id) return;

                // 客户
                $.ajax({
                    url: "{{ route('admin.common.customer-by-dept') }}",
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}", department_id: department_id},
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
            });

            // 选择客户 → 加载该客户的默认200个商品
            $(document).on('change', '#customer_id', function () {
                let customer_id = $(this).val();
                let $select = $("select[name='goods_id']");

                if (!customer_id) {
                    $select.html('').selectpicker('refresh');
                    CURRENT_CUSTOMER_DEFAULT_GOODS = []; // 清空缓存
                    return;
                }
                GOODS_SEARCH_KEYWORD = ''; // 切换客户清空
                // AJAX 获取 200 个默认商品
                $.ajax({
                    url: "{{ route('admin.common.customer-default-goods') }}",
                    type: 'POST',
                    data: { customer_id: customer_id, _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (res.code === 200) {
                            // 👇 关键：缓存到全局变量
                            CURRENT_CUSTOMER_DEFAULT_GOODS = res.data;

                            // 渲染到下拉框
                            let str = "";
                            $.each(res.data, function (i, item) {
                                str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                            });
                            $select.html(str).selectpicker('refresh').selectpicker('render');
                        }
                    }
                });
            });


            // 商品搜索（输入时触发）
            $(document).on('shown.bs.select', '.goods_id', function () {
                let $select = $(this).find("select[name='goods_id']");
                let customer_id = $("#customer_id").val();

                setTimeout(() => {
                    $('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD);
                }, 100);

                // 监听搜索框输入事件
                $('.bs-searchbox input').off('keyup').on('keyup', function () {
                    GOODS_SEARCH_KEYWORD = $(this).val().trim();
                    let keyword = $(this).val().trim();
                    if (keyword === '') {
                        let str = "";
                        // 直接用全局缓存的200个
                        $.each(CURRENT_CUSTOMER_DEFAULT_GOODS, function (i, item) {
                            str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                        });
                        $select.html(str).selectpicker('refresh').selectpicker('render');
                        return;
                    }

                    // 关键词太短不搜索（避免频繁请求）
                    if (keyword.length < 2) {
                        return;
                    }

                    // AJAX 后端搜索商品
                    $.ajax({
                        url: "{{ route('admin.common.goods-search') }}",
                        type: 'POST',
                        data: {
                            customer_id: customer_id,
                            keyword: keyword,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            let html = '';
                            $.each(res.data, function (i, item) {
                                html += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                            });

                            // 替换下拉内容
                            $select.html(html).selectpicker('refresh');
                            $select.selectpicker('render');
                        }
                    });
                });
            });

            // 选择商品 → 加载颜色
            $(document).on('change', '.goods_id', function () {
                let goods_id = $(this).val();
                let _this = $(this).parents('tr');
                _this.find('.sku_id').html("<option>请选择</option>");
                _this.find('.price').val('');
                if (!goods_id) {
                    return false;
                }

                // AJAX 获取 200 个默认商品
                $.ajax({
                    url: "{{ route('admin.common.sku-by-goods') }}",
                    type: 'POST',
                    data: { goods_id: goods_id, _token: "{{ csrf_token() }}" },
                    success: function (res) {
                        if (res.code === 200) {
                            let thumb_image =  "{{ asset(':url') }}".replace(':url', res.data.goods.thumb_image);
                            let main_image = "{{ asset(':url') }}".replace(':url', res.data.goods.main_image);
                            _this.find('img').attr('src',thumb_image);
                            _this.find('.thumb-img').data('src',main_image);
                            let str = "<option>-请选择颜色-</option>";
                            $.each(res.data.skus, function (i, item) {
                                str += `<option value="${item.id}">${item.color}</option>`;
                            });
                            _this.find('.sku_id').html(str);
                            _this.data('skuList', res.data.skus);
                        }
                    }
                });
            });

            $(document).on('change', '.sku_id', function () {
                let $sku = $(this);
                let skuId = $sku.val();
                let $tr = $sku.parents('tr');     // 当前行
                let skuList = $tr.data('skuList'); // 取出之前存的SKUs

                if (!skuId || !skuList) return;

                // 找到对应SKU
                let sku = skuList.find(item => item.id == skuId);

                $tr.find('.price').val(sku.cost_price);         // 单价
                //$tr.find('.process_price').val(sku.process_price); // 加工费
                //$tr.find('.money').val(sku.price);         // 金额
            });

            // 表单验证
            $("#order").validate({
                onsubmit: true,
                rules: {
                    department_id: {required: true},
                    customer_id: {required: true},
                    supplier_company_id: {required: true},
                    order_date: {required: true},
                    delivery_date: {required: true},
                },
                messages: {
                    department_id: "请选择部门",
                    customer_id: "请选择客户",
                    supplier_company_id: "请选择供应商",
                    order_date: "请选订货日期",
                    delivery_date: "请选交货日期",
                },
                submitHandler: function (form) {
                    $("#p_confirm").prop('disabled', true).text('提交中...');
                    $("#timg").removeClass("hidden");

                    let formData = new FormData();

                    // 1. 追加表头信息
                    formData.append('department_id', $("#department_id").val());
                    formData.append('customer_id', $("#customer_id").val());
                    formData.append('supplier_company_id', $("#supplier_company_id").val());
                    formData.append('order_date', $("#order_date").val());
                    formData.append('delivery_date', $("#delivery_date").val());
                    formData.append('order_number', $("#order_number").text());
                    formData.append('excel_id', $("#excel_id").val());
                    formData.append('comment', $("#comment").val());

                    $(".p_body tr").each(function (index) {
                        let $tr = $(this);

                        formData.append(`goods[${index}][goods_id]`,      $tr.find("[name='goods_id']").val());
                        formData.append(`goods[${index}][sku_id]`,        $tr.find("[name='sku_id']").val());
                        formData.append(`goods[${index}][color_card]`,    $tr.find("[name='color_card']").val());
                        formData.append(`goods[${index}][number]`,        $tr.find("[name='number']").val());
                        formData.append(`goods[${index}][currency_id]`,   $tr.find("[name='currency_id']").val());
                        formData.append(`goods[${index}][price]`,         $tr.find("[name='price']").val());
                        formData.append(`goods[${index}][money]`,         $tr.find("[name='money']").val());
                        formData.append(`goods[${index}][remark]`,        $tr.find("[name='remark']").val());
                        // formData.append(`goods[${index}][process_company_id]`,  $tr.find("[name='process_company_id']").val());
                        // formData.append(`goods[${index}][process_price]`,       $tr.find("[name='process_price']").val());
                        // formData.append(`goods[${index}][process_money]`,       $tr.find("[name='process_money']").val());
                        // formData.append(`goods[${index}][process_company_id2]`, $tr.find("[name='process_company_id2']").val());
                        // formData.append(`goods[${index}][process_price2]`,      $tr.find("[name='process_price2']").val());
                        // formData.append(`goods[${index}][process_money2]`,      $tr.find("[name='process_money2']").val());
                    });

                    // 3. AJAX 提交（固定写法）
                    $.ajax({
                        url: "{{ route('admin.orders.store') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function (res) {
                            if (res.code === 200) {
                                $("#success").show();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                alert(res.msg);
                                $("#p_confirm").prop('disabled', false).text('提交');
                                $("#timg").addClass("hidden");
                            }
                        },
                        error: function () {
                            alert('提交失败，请重试');
                            $("#p_confirm").prop('disabled', false).text('提交');
                            $("#timg").addClass("hidden");
                        }
                    });
                }
            });
        });
    </script>
@endsection
@include('admin.order._——share')
