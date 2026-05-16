@extends('admin.layouts.app')

@section('extends_css')
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/ajaxupload/css/style.css"/>
    <style>
        #log td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-row {
            display: flex;
            gap: 4px;
            justify-content: center;
            align-items: center;
        }
        .orderCodeFixed {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            padding: 8px 0;
        }
        .modal-body .table {
            margin-top: 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">添加出库</span>
        </div>
        <div class="panel-body navbar-form">
            <form id="order" onkeydown="if(event.keyCode==13)return false;">

                <!-- 部门 -->
                <select class="form-control input-sm" name="department_id" id="department_id">
                    <option value="">-请选择部门-</option>
                    @foreach($_departments_auth as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <!-- 客户 -->
                <select class="form-control input-sm" name="customer_id" id="customer_id">
                    <option value="">-请选择客户-</option>
                </select>

                <!-- 清关方式 -->
                <select class="form-control input-sm" name="clearance_id" id="clearance_id">
                    <option value="">-清关方式-</option>
                    @foreach($_clearances as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <!-- 支付方式 -->
                <select class="form-control input-sm" name="payment_id" id="payment_id">
                    <option value="">-支付方式-</option>
                    @foreach($_payments as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <!-- 胶带 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="tape" placeholder="胶带">
                </div>

                <!-- 封箱号 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="seal_container_no" placeholder="封箱号">
                </div>

                <!-- 出库日期 -->
                <div class="input-group">
                    <input class="form-control input-sm" name="outbound_at" id="outbound_at" autocomplete="off"
                           placeholder="出库日期" type="text">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <!-- 出库单号 -->
                <span class="order">出库单号：<span id="outbound_code"></span></span>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped"
                       style="margin-top:2em;">
                    <thead>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>唛头</th>
                        <th>起始</th>
                        <th>截止</th>
                        <th>仓库</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>品牌</th>
                        <th>箱数</th>
                        <th>单箱数</th>
                        <th>数量</th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额</th>
                        <th>长</th>
                        <th>宽</th>
                        <th>高</th>
                        <th>CBM</th>
                        <th>工序</th>
                        <th>毛重</th>
                        <th>净重</th>
                        <th>备注</th>
                    </tr>
                    </thead>

                    <tbody class="text-center p_body padding_0" id="log">
                    <tr>
                        <td>
                            <div class="btn-row">
                                <button type="button" class="btn btn-sm btn-danger goods_minus">-</button>
                                <button type="button" class="btn btn-sm btn-success goods_plus">+</button>
                            </div>
                        </td>
                        <td class="serial_number">1</td>

                        <!-- 唛头 -->
                        <td data-key="shipping_mark" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm shipping_mark" name="shipping_mark">
                        </td>

                        <!-- 起始 -->
                        <td data-key="carton_no_start" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_no_start" name="carton_no_start">
                        </td>

                        <!-- 截止 -->
                        <td data-key="carton_no_end" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_no_end" name="carton_no_end">
                        </td>
                        <!-- 仓库 -->
                        <td data-key="warehouse_id" class="padding_0">
                            <select class="form-control input_no_border input-sm warehouse_id" name="warehouse_id">
                                <option value="">-仓库-</option>
                            </select>
                        </td>

                        <!-- 产品 -->
                        <td data-key="goods_id" class="padding_0 select_goods">
                            <select name="goods_id"
                                    class="input_no_border form-control selectpicker goods_id"
                                    data-live-search="true"
                                    data-live-search-placeholder="输入货号/名称搜索"
                                    title="请选择产品">
                            </select>
                        </td>

                        <!-- 图片 -->
                        <td class="preview_image padding_0" image_url="">
                            <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle;">
                                <img height="20px" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                     class="thumb-img click-preview" data-src="">
                                <img src=""
                                     class="hover-preview"
                                     style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                            </div>
                        </td>

                        <!-- SKU 颜色 -->
                        <td data-key="sku_id" class="padding_0">
                            <select name="sku_id" class="form-control input_no_border input-sm sku_id">
                                <option value="">--颜色--</option>
                            </select>
                        </td>

                        <!-- 品牌 -->
                        <td data-key="brand_logo" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm brand_logo" name="brand_logo" placeholder="品牌">
                        </td>

                        <!-- 箱数 -->
                        <td data-key="carton_qty" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_qty" name="carton_qty" value="0">
                        </td>

                        <!-- 单箱数量 -->
                        <td data-key="unit_carton_qty" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm unit_carton_qty" name="unit_carton_qty" value="0">
                        </td>

                        <!-- 数量 -->
                        <td data-key="quantity" class="padding_0">
                            <input type="text" size="5" class="form-control input_no_border input-sm quantity" name="quantity" value="0" readonly>
                        </td>

                        <!-- 货币 -->
                        <td data-key="currency_id" class="padding_0">
                            <select class="form-control input_no_border input-sm currency_id" name="currency_id">
                                @foreach($_currencies as $vo)
                                    <option value="{{ $vo->id }}" currency_icon="{{ $vo->currency_symbol }}" {{ $vo->id == 1 ? 'selected' : '' }}>
                                        {{ $vo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <!-- 单价 -->
                        <td data-key="price" class="padding_0">
                            <input type="text" size="5" class="form-control input_no_border input-sm price" name="price">
                        </td>

                        <!-- 金额 -->
                        <td data-key="amount" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm amount" name="amount" readonly>
                        </td>

                        <!-- 长 -->
                        <td data-key="carton_length" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_length" name="carton_length">
                        </td>

                        <!-- 宽 -->
                        <td data-key="carton_width" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_width" name="carton_width">
                        </td>

                        <!-- 高 -->
                        <td data-key="carton_height" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm carton_height" name="carton_height">
                        </td>

                        <!-- CBM -->
                        <td data-key="cbm" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm cbm" name="cbm" readonly>
                        </td>

                        <!-- 工序 -->
                        <td data-key="craft_method_id" class="padding_0">
                            <select class="form-control input_no_border input-sm craft_method_id" name="craft_method_id">
                                @foreach($_craft_methods as $vo)
                                    <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                                @endforeach
                            </select>
                        </td>

                        <!-- 毛重 -->
                        <td data-key="gross_weight" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm gross_weight" name="gross_weight">
                        </td>

                        <!-- 净重 -->
                        <td data-key="net_weight" class="padding_0">
                            <input type="text" size="2" class="form-control input_no_border input-sm net_weight" name="net_weight">
                        </td>

                        <!-- 备注 -->
                        <td data-key="remark" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm remark" name="remark">
                        </td>

                    </tr>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>唛头</th>
                        <th>起始</th>
                        <th>截止</th>
                        <th>仓库</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>品牌</th>
                        <th>箱数<br><span id="total_carton_qty"></span></th>
                        <th>单箱数</th>
                        <th>总数<br><span id="total_quantity"></span></th>
                        <th>货币</th>
                        <th>单价</th>
                        <th>金额<br><span id="total_amount"></span></th>
                        <th>长</th>
                        <th>宽</th>
                        <th>高</th>
                        <th>CBM<br><span id="total_cbm"></span></th>
                        <th>工序</th>
                        <th>毛重</th>
                        <th>净重</th>
                        <th>备注</th>
                    </tr>
                    <tr class="hidden" id="timg">
                        <th colspan="20"><img src="/images/timg.gif"/>正在提交中...</th>
                    </tr>
                    </tfoot>
                </table>

                <button class="btn btn-success pull-right" type="submit" id="p_confirm">提交</button>
            </form>
        </div>
    </div>

    <div class="form-group container" style="margin-top: 20px;">
        <label>其他备注:</label>
        <textarea class="form-control" rows="4" name="comment"></textarea>
    </div>

    <div id="success" class="text-center text-info" style="display:none; margin-top:20px;">
        <div><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;添加成功</div>
    </div>
@endsection

@section('script_js')
    <script>
        var CURRENT_CUSTOMER_GOODS = [];
        var GOODS_KEYWORD = '';

        $(function () {
            // 日期
            $("#outbound_at").datepicker({maxDate: 0});

            // 出库单号
            let u = new Date().getTime();
            let rn = Math.floor(Math.random() * 900 + 100);
            $("#outbound_code").text("out" + u + rn);

            // 加行
            $(document).on("click", ".goods_plus", function () {
                let _this_tr = $(this).parents("tr");
                let main_image = _this_tr.find('.thumb-img').data('src');
                let skulist = _this_tr.data('skuList');
                let tr = _this_tr.clone();
                _this_tr.after(tr);
                _this_tr.next().find('.thumb-img').data('src',main_image);
                _this_tr.next().data('skuList',skulist);
                _this_tr.next().find('.bootstrap-select').find("button:first").remove();
                _this_tr.next().find('.selectpicker').selectpicker("val");
                _this_tr.next().find("input,select").not(".brand_logo,.warehouse_id,.goods_id," +
                    ".shipping_mark,.carton_no_start,.carton_no_end,.carton_qty,.unit_carton_qty,.currency_id,.quantity," +
                    ".carton_length,.carton_width,.carton_height,.cbm,.gross_weight,.net_weight,.craft_method_id").val("");
                _this_tr.next().find('.selectpicker').selectpicker('refresh');
                _this_tr.next().find('.selectpicker').selectpicker('render');
                // setTimeout(() => {
                //     _this_tr.next().find('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD);
                // }, 100);
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
                $(".serial_number").each((i, el) => $(el).text(i + 1));
            }

            // 自动计算：数量、金额、CBM、净重
            $(document).on("input propertychange", ".p_body input", function () {
                calcRow($(this).parents("tr"));
                totalAll();
            });

            function calcRow(tr) {
                // 1. 计算总数量 = 箱数 × 单箱数量
                let carton_qty = Number(tr.find(".carton_qty").val() || 0);
                let unit_qty = Number(tr.find(".unit_carton_qty").val() || 0);
                let qty = carton_qty * unit_qty;
                tr.find(".quantity").val(qty);

                // 2. 计算金额 = 数量 × 单价
                let price = Number(tr.find(".price").val() || 0);
                tr.find(".amount").val((qty * price).toFixed(2));

                // 3. CBM：长×宽×高×箱数
                let l = Number(tr.find(".carton_length").val() || 0);
                let w = Number(tr.find(".carton_width").val() || 0);
                let h = Number(tr.find(".carton_height").val() || 0);
                let cbm = (l * w * h * carton_qty)
                tr.find(".cbm").val(cbm.toFixed(2));

                // 4. 净重 = 毛重 - 2
                let gw = Number(tr.find(".gross_weight").val() || 0);
                tr.find(".net_weight").val((gw > 0 ? gw - 2 : 0).toFixed(2));
            }

            // 合计
            function totalAll() {
                sum("carton_qty", 0);
                sum("quantity", 0);
                sum("amount", 2);
                sum("cbm", 2);
            }

            function sum(field, dec) {
                let total = 0;
                $("." + field).each(function () {
                    total += Number($(this).val() || 0);
                });
                $("#total_" + field).text(total.toFixed(dec));
            }


            $(document).on('input', '.carton_no_start', function () {
                let row = $(this).closest('tr');
                let start = parseInt($(this).val()) || 0;
                let endInput = row.find('.carton_no_end');
                let end = parseInt(endInput.val()) || 0;

                // 只有 截止为空 或 截止 < 起始 时才赋值
                if (endInput.val() === '' || end < start) {
                    endInput.val(start);
                }
            });

            // 部门 → 客户 + 仓库
            $(document).on('change', '#department_id', function () {
                let deptId = $(this).val();
                $("#customer_id").html('<option value="">-请选择客户-</option>');
                $(".warehouse_id").html('<option value="">-仓库-</option>');
                if (!deptId) return;

                $.post("{{ route('admin.common.customer-by-dept') }}", {
                    department_id: deptId, _token: "{{ csrf_token() }}"
                }, res => {
                    let str = '<option value="">-请选择客户-</option>';
                    $.each(res.data, (i, item) => str += `<option value="${item.id}">${item.name}</option>`);
                    $("#customer_id").html(str);
                });

                $.post("{{ route('admin.common.warehouse-by-dept') }}", {
                    department_id: deptId, _token: "{{ csrf_token() }}"
                }, res => {
                    let str = '<option value="">-请选择仓库-</option>';
                    $.each(res.data, (i, item) => str += `<option value="${item.id}">${item.name}</option>`);
                    $(".warehouse_id").html(str);
                });
            });

            // 客户 → 商品
            $(document).on('change', '#customer_id', function () {
                let $select = $("select[name='goods_id']");
                $select.html('').selectpicker('refresh');
                CURRENT_CUSTOMER_DEFAULT_GOODS = [];
                $("select[name='warehouse_id']").val('');
                return;
            });

            // 客户 → 商品
            $(document).on('change', '.warehouse_id', function () {
                let customer_id = $('#customer_id').val();
                let warehouse_id = $(this).val();
                let $select = $("select[name='goods_id']");
                if (!customer_id) {
                    alert('请选择客户');
                    $(this).val("");
                    $select.html('').selectpicker('refresh');
                    CURRENT_CUSTOMER_DEFAULT_GOODS = [];
                    return;
                }
                GOODS_SEARCH_KEYWORD = '';
                $.ajax({
                    url: "{{ route('admin.common.customer-stock-goods') }}",
                    type: 'POST',
                    data: {customer_id,warehouse_id, _token: "{{ csrf_token() }}"},
                    success: res => {
                        if (res.code === 200) {
                            CURRENT_CUSTOMER_DEFAULT_GOODS = res.data;
                            let str = "";
                            $.each(res.data, (i, item) => {
                                str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                            });
                            $select.html(str).selectpicker('refresh').selectpicker('render');
                        }
                    }
                });
            });

            // 商品搜索
            {{--$(document).on('shown.bs.select', '.goods_id', function () {--}}
            {{--    let $select = $(this).find("select[name='goods_id']");--}}
            {{--    let customer_id = $("#customer_id").val();--}}
            {{--    setTimeout(() => $('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD), 100);--}}
            {{--    $('.bs-searchbox input').off('keyup').on('keyup', function () {--}}
            {{--        GOODS_SEARCH_KEYWORD = $(this).val().trim();--}}
            {{--        let keyword = GOODS_SEARCH_KEYWORD;--}}
            {{--        if (keyword === '') {--}}
            {{--            let str = "";--}}
            {{--            $.each(CURRENT_CUSTOMER_DEFAULT_GOODS, (i, item) => {--}}
            {{--                str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;--}}
            {{--            });--}}
            {{--            $select.html(str).selectpicker('refresh').selectpicker('render');--}}
            {{--            return;--}}
            {{--        }--}}
            {{--        if (keyword.length < 2) return;--}}
            {{--        $.ajax({--}}
            {{--            url: "{{ route('admin.common.goods-search') }}",--}}
            {{--            type: 'POST',--}}
            {{--            data: {customer_id, keyword, _token: "{{ csrf_token() }}"},--}}
            {{--            success: res => {--}}
            {{--                let html = '';--}}
            {{--                $.each(res.data, (i, item) => {--}}
            {{--                    html += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;--}}
            {{--                });--}}
            {{--                $select.html(html).selectpicker('refresh').selectpicker('render');--}}
            {{--            }--}}
            {{--        });--}}
            {{--    });--}}
            {{--});--}}

            // 商品 → 颜色
            $(document).on('change', '.goods_id', function () {
                let goods_id = $(this).val();
                let _this = $(this).parents('tr');
                _this.find('.sku_id').html("<option>请选择</option>");
                _this.find('.price').val('');
                if (!goods_id) return false;

                $.ajax({
                    url: "{{ route('admin.common.sku-by-goods') }}",
                    type: 'POST',
                    data: {goods_id, _token: "{{ csrf_token() }}"},
                    success: res => {
                        if (res.code === 200) {
                            let thumb = "{{ asset(':url') }}".replace(':url', res.data.goods.thumb_image);
                            let main = "{{ asset(':url') }}".replace(':url', res.data.goods.main_image);
                            _this.find('img.thumb-img,img.hover-preview').attr('src', thumb).data('src', main);
                            let str = "<option>-请选择颜色-</option>";
                            $.each(res.data.skus, (i, item) => {
                                str += `<option value="${item.id}">${item.color}</option>`;
                            });
                            _this.find('.sku_id').html(str);
                            _this.data('skuList', res.data.skus);
                            _this.find('.brand_logo').val(res.data.goods.brand_logo);
                        }
                    }
                });
            });

            // 颜色 → 单价
            $(document).on('change', '.sku_id', function () {
                let $sku = $(this);
                let skuId = $sku.val();
                let $tr = $sku.parents('tr');
                let skuList = $tr.data('skuList');
                if (!skuId || !skuList) return;
                let sku = skuList.find(item => item.id == skuId);
                $tr.find('.price').val(sku.sell_price);
                $tr.find('.currency_id').val(sku.sell_currency_id);
                calcRow($(this).parents("tr"));
                totalAll();
            });

            // 货币符号
            $(document).on('change', '.currency_id', function () {
                let icon = $(this).find('option:selected').attr('currency_icon');
                $(this).parents('tr').find('.currency_icon_span').html(icon);
            });

            // ====================== 提交 ======================
            $("#order").validate({
                rules: {
                    department_id: "required",
                    customer_id: "required",
                    clearance_id: "required",
                    payment_id: "required",
                    tape: "required",
                    outbound_at: "required",
                },
                messages: {
                    department_id: "请选择部门",
                    customer_id: "请选择客户",
                    clearance_id: "请选择清关方式",
                    payment_id: "请选择支付方式",
                    tape: "请输入胶带",
                    outbound_at: "请选择出库日期",
                },
                submitHandler: function (form) {
                    $("#p_confirm").prop('disabled', true).text('提交中...');
                    $("#timg").removeClass("hidden");

                    let fd = new FormData();
                    fd.append('department_id', $("#department_id").val());
                    fd.append('customer_id', $("#customer_id").val());
                    fd.append('clearance_id', $("#clearance_id").val());
                    fd.append('payment_id', $("#payment_id").val());
                    fd.append('tape', $("input[name=tape]").val());
                    fd.append('seal_container_no', $("input[name=seal_container_no]").val());
                    fd.append('outbound_at', $("#outbound_at").val());
                    fd.append('outbound_code', $("#outbound_code").text());
                    fd.append('comment', $("textarea[name=comment]").val());

                    $(".p_body tr").each(function (i) {
                        let t = $(this);
                        fd.append(`goods[${i}][brand_logo]`, t.find(".brand_logo").val());
                        fd.append(`goods[${i}][warehouse_id]`, t.find(".warehouse_id").val());
                        fd.append(`goods[${i}][goods_id]`, t.find(".goods_id").val());
                        fd.append(`goods[${i}][sku_id]`, t.find(".sku_id").val());
                        fd.append(`goods[${i}][shipping_mark]`, t.find(".shipping_mark").val());
                        fd.append(`goods[${i}][carton_no_start]`, t.find(".carton_no_start").val());
                        fd.append(`goods[${i}][carton_no_end]`, t.find(".carton_no_end").val());
                        fd.append(`goods[${i}][carton_qty]`, t.find(".carton_qty").val());
                        fd.append(`goods[${i}][unit_carton_qty]`, t.find(".unit_carton_qty").val());
                        fd.append(`goods[${i}][quantity]`, t.find(".quantity").val());
                        fd.append(`goods[${i}][currency_id]`, t.find(".currency_id").val());
                        fd.append(`goods[${i}][price]`, t.find(".price").val());
                        //fd.append(`goods[${i}][amount]`, t.find(".amount").val());
                        fd.append(`goods[${i}][carton_length]`, t.find(".carton_length").val());
                        fd.append(`goods[${i}][carton_width]`, t.find(".carton_width").val());
                        fd.append(`goods[${i}][carton_height]`, t.find(".carton_height").val());
                        //fd.append(`goods[${i}][cbm]`, t.find(".cbm").val());
                        fd.append(`goods[${i}][craft_method_id]`, t.find(".craft_method_id").val());
                        fd.append(`goods[${i}][gross_weight]`, t.find(".gross_weight").val());
                        fd.append(`goods[${i}][net_weight]`, t.find(".net_weight").val());
                        fd.append(`goods[${i}][remark]`, t.find(".remark").val());
                    });

                    $.ajax({
                        url: "{{ route('admin.outbounds.store') }}",
                        type: "POST",
                        data: fd,
                        contentType: false,
                        processData: false,
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        dataType: "json",
                        success: res => {
                            if (res.code === 200) {
                                $("#success").show();
                                setTimeout(() => location.reload(), 1600);
                            } else {
                                alert(res.msg);
                                $("#p_confirm").prop('disabled', false).text('提交');
                                $("#timg").addClass("hidden");
                            }
                        },
                        error: res => {
                            alert(res.responseJSON?.msg || '提交失败');
                            $("#p_confirm").prop('disabled', false).text('提交');
                            $("#timg").addClass("hidden");
                        }
                    });
                }
            });
        });
    </script>
@endsection
