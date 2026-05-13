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
        /* 给表格上边留间距，不被固定栏压住表头 */
        .modal-body .table {
            margin-top: 10px !important;
        }
    </style>
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-pencil"></span>
            <span class="panel-tit">添加入库</span>
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

                <select class="form-control input-sm" name="warehouse_id" id="warehouse_id">
                    <option value="">-请选择仓库-</option>
                </select>

                <select name="supplier_id" id="supplier_id" class="selectpicker"
                        data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true" title="请选择供应商">
                    @foreach($_suppliers as $vo)
                        <option value="{{ $vo->id }}">{{ $vo->name }}</option>
                    @endforeach
                </select>

                <div class="input-group">
                    <input class="form-control input-sm" name="inbound_at" id="inbound_at" autocomplete="off"
                           placeholder="入库日期" type="text">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>

                <span class="order">入库单号：<span id="inbound_code"></span></span>

                <table id="dialog_radius" class="table table-bordered table-condensed table-hover table-striped"
                       style="margin-top:2em;">
                    <thead>
                    <tr>
                        <th colspan="2">排头信息</th>
                        <th colspan="4">产品信息</th>
                        <th colspan="7">订单信息</th>
                    </tr>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>订单</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>剩余数量</th>
                        <th>入库数量</th>
                        <th>货币</th>
                        <th>符号</th>
                        <th>单价</th>
                        <th>金额</th>
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

                        <!-- 关联订单 -->
                        <td data-key="order_code" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm order_code" name="order_code" value="关联订单" readonly>
                        </td>
                        <td data-key="order_item_id" class="padding_0" hidden>
                            <input type="text" class="form-control input_no_border input-sm order_item_id" name="order_item_id" value="0" readonly>
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
                            <div class="img-hover-box" style="position:relative; display:inline-block; vertical-align:middle; ">
                                <img height="20px" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                                     class="thumb-img click-preview"
                                     data-src=""
                                     style="width:auto; object-fit:contain; border-radius:3px; cursor:pointer;">
                                <img src=""
                                     class="hover-preview"
                                     style="position:absolute; left:calc(100% + 10px); top:0; opacity:0; transition:all 0.2s; max-width:280px; max-height:280px; object-fit:contain; z-index:9999; border-radius:4px; box-shadow:0 2px 12px rgba(0,0,0,0.2); pointer-events:none;">
                            </div>
                        </td>

                        <!-- 颜色 -->
                        <td data-key="sku_id" class="padding_0">
                            <select name="sku_id"
                                    class="form-control input_no_border input-sm sku_id">
                                <option value="">--请选择颜色--</option>
                            </select>
                        </td>

                        <td class="padding_0"><span class="number_difference"></span></td>

                        <!-- 入库数量 -->
                        <td data-key="quantity" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm quantity"
                                   name="quantity" value="0" placeholder="入库数量">
                        </td>

                        <!-- 货币 -->
                        <td data-key="currency_id" class="padding_0">
                            <select class="form-control input_no_border input-sm currency_id" name="currency_id">
                                @foreach($_currencies as $vo)
                                    <option value="{{ $vo->id }}" currency_icon="{{ $vo->currency_symbol }}" {{ $vo->id==1 ? 'selected' : '' }}>
                                        {{ $vo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><span class="currency_icon_span">￥</span></td>

                        <!-- 单价 -->
                        <td data-key="price" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm price"
                                   name="price" placeholder="单价">
                        </td>

                        <!-- 金额 -->
                        <td data-key="money" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm money"
                                   name="money" readonly placeholder="金额">
                        </td>

                        <!-- 备注 -->
                        <td data-key="remark" class="padding_0">
                            <input type="text" class="form-control input_no_border input-sm remark"
                                   name="remark" placeholder="备注">
                        </td>

                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>操作</th>
                        <th>序号</th>
                        <th>订单</th>
                        <th>产品</th>
                        <th>图片</th>
                        <th>颜色</th>
                        <th>订单数量</th>
                        <th>入库数量<br><span id="total_quantity"></span></th>
                        <th>货币</th>
                        <th>符号</th>
                        <th>单价</th>
                        <th>金额<br><span id="total_money"></span></th>
                        <th>备注</th>
                    </tr>
                    <tr class="hidden" id="timg">
                        <th colspan="19"><img src="/images/timg.gif"/>正在提交中...</th>
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

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="orderCodeModal" tabindex="1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">订单</h4>
                </div>
                <div class="modal-body" style="padding-top:15px;">
                    <div class="orderCodeFixed">
                        <hr/>
                        <div class="form-inline" style="margin-bottom: 0.5em;">
                            检索：
                            <input type="text" class="form-control input-sm search_products_name" name="search_products_name" placeholder="按产品搜索">
                            <input type="text" class="form-control input-sm search_item_no" name="search_item_no" placeholder="按货号搜索">
                            <input type="text" class="form-control input-sm search_order_number" name="search_order_number" placeholder="按订单号搜索">
                        </div>
                    </div>
                    <table class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr>
                            <th>操作</th>
                            <th>图片</th>
                            <th>产品</th>
                            <th>货号</th>
                            <th>颜色</th>
                            <th>数量</th>
                            <th>已入库</th>
                            <th>剩余数量</th>
                            <th>单价</th>
                            <th>订单号</th>
                            <th>订货时间</th>
                            <th>交货时间</th>
                        </tr>
                        </thead>
                        <tbody class="text-center" id="orderCodeList">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="orderCodeConfirm">确认</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('script_js')
    <script>
        var CURRENT_CUSTOMER_DEFAULT_GOODS = [];
        var GOODS_SEARCH_KEYWORD = '';

        $(function () {
            // 日期
            $("#inbound_at").datepicker({maxDate: 0});

            // 入库单号
            let u = new Date().getTime();
            let mi = Math.floor(Math.random() * 900 + 100);
            $("#inbound_code").text("in" + u + mi);

            // 回车
            $(document).on("keyup", "input", function (e) {
                if (e.keyCode == 13) {
                    let name = $(this).attr("name");
                    $(this).parents("tr").next().find(`input[name=${name}]`).select();
                }
            });

            // 加行【和订单页面完全一致】
            $(document).on("click", ".goods_plus", function () {
                let _this_tr = $(this).parents("tr");
                let main_image = _this_tr.find('.thumb-img').data('src');
                let skulist = _this_tr.data('skuList');
                let order_item_id = _this_tr.find('.order_item_id').val();
                let tr = _this_tr.clone();
                _this_tr.after(tr);
                _this_tr.next().find('.thumb-img').data('src',main_image);
                _this_tr.next().data('skuList',skulist);
                _this_tr.next().find('.bootstrap-select').find("button:first").remove();
                _this_tr.next().find('.selectpicker').selectpicker("val");
                _this_tr.next().find(".goods_id,.currency_id,.sku_id").attr('disabled',false);
                _this_tr.next().find("input,select").not(".goods_id,.sku_id,.currency_id").val("");
                if( order_item_id != 0 ){
                    _this_tr.next().find(".goods_id,.sku_id").val(" ");
                    _this_tr.next().find(".order_item_id").val(0).attr('value',0);
                }
                _this_tr.next().find(".order_code").val('关联订单');
                _this_tr.next().find(".quantity,.money,.order_item_id").val(0);
                _this_tr.next().find('.selectpicker').selectpicker('refresh');
                _this_tr.next().find('.selectpicker').selectpicker('render');

                setTimeout(() => {
                    _this_tr.next().find('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD);
                }, 100);
                refreshSerial();
            });

            // 减行【和订单页面完全一致】
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
                let number = parseFloat(self.find(".quantity").val() || 0);
                self.find(".money").val((price * number).toFixed(2));
            }

            // 合计
            function totalAll() {
                total("quantity", 0);
                total("money", 2);
            }

            function total(t, n) {
                let sum = 0;
                $("." + t).each(function () {
                    sum += parseFloat($(this).val() || 0);
                });
                $("#total_" + t).text(sum.toFixed(n));
            }

            // 部门 → 客户 + 仓库
            $(document).on('change', '#department_id', function () {
                let department_id = $(this).val();
                $('#customer_id').html('<option value="">-请选择客户-</option>');
                $('#warehouse_id').html('<option value="">-请选择仓库-</option>');
                if (!department_id) return;

                // 客户
                $.ajax({
                    url: "{{ route('admin.common.customer-by-dept') }}",
                    type: "POST",
                    data: {_token: "{{ csrf_token() }}", department_id},
                    dataType: "json",
                    success: res => {
                        if (res.code === 200) {
                            let str = '<option value="">-请选择客户-</option>';
                            $.each(res.data, (i, item) => {
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
                    data: {_token: "{{ csrf_token() }}", department_id},
                    dataType: "json",
                    success: res => {
                        if (res.code === 200) {
                            let str = '';
                            $.each(res.data, (i, item) => {
                                str += `<option value="${item.id}">${item.name}</option>`;
                            });
                            $('#warehouse_id').html(str);
                        }
                    }
                });
            });

            // 客户 → 商品
            $(document).on('change', '#customer_id', function () {
                let customer_id = $(this).val();
                let $select = $("select[name='goods_id']");
                if (!customer_id) {
                    $select.html('').selectpicker('refresh');
                    CURRENT_CUSTOMER_DEFAULT_GOODS = [];
                    return;
                }
                GOODS_SEARCH_KEYWORD = '';
                $.ajax({
                    url: "{{ route('admin.common.customer-default-goods') }}",
                    type: 'POST',
                    data: {customer_id, _token: "{{ csrf_token() }}"},
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
            $(document).on('shown.bs.select', '.goods_id', function () {
                let $select = $(this).find("select[name='goods_id']");
                let customer_id = $("#customer_id").val();
                setTimeout(() => $('.bs-searchbox input').val(GOODS_SEARCH_KEYWORD), 100);
                $('.bs-searchbox input').off('keyup').on('keyup', function () {
                    GOODS_SEARCH_KEYWORD = $(this).val().trim();
                    let keyword = GOODS_SEARCH_KEYWORD;
                    if (keyword === '') {
                        let str = "";
                        $.each(CURRENT_CUSTOMER_DEFAULT_GOODS, (i, item) => {
                            str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                        });
                        $select.html(str).selectpicker('refresh').selectpicker('render');
                        return;
                    }
                    if (keyword.length < 2) return;
                    $.ajax({
                        url: "{{ route('admin.common.goods-search') }}",
                        type: 'POST',
                        data: {customer_id, keyword, _token: "{{ csrf_token() }}"},
                        success: res => {
                            let html = '';
                            $.each(res.data, (i, item) => {
                                html += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
                            });
                            $select.html(html).selectpicker('refresh').selectpicker('render');
                        }
                    });
                });
            });

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
                            _this.find('img.thumb-img').attr('src', thumb).data('src', main);
                            let str = "<option>-请选择颜色-</option>";
                            $.each(res.data.skus, (i, item) => {
                                str += `<option value="${item.id}">${item.color}</option>`;
                            });
                            _this.find('.sku_id').html(str);
                            _this.data('skuList', res.data.skus);
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
                $tr.find('.price').val(sku.cost_price);
            });

            // 货币符号
            $(document).on('change', '.currency_id', function () {
                let icon = $(this).find('option:selected').attr('currency_icon');
                $(this).parents('tr').find('.currency_icon_span').html(icon);
            });

            // ====================== 关联订单弹窗 ======================
            $(document).on("focus", ".order_code", function () {
                let dept = $("#department_id").val();
                let custom = $("#customer_id").val();
                let supplier = $("#supplier_id").val();
                let date = $("#inbound_at").val();
                if (!dept || !custom || !supplier || !date) {
                    return false;
                }
                $("#orderCodeList").html('<tr><td colspan="11" align="center">加载中...</td></tr>');
                $(this).parents("tr").removeClass("active").addClass("active");

                $.ajax({
                    url: "{{ route('admin.common.get-order-items-list') }}",
                    type: 'post',
                    data: {
                        department_id: dept,
                        customer_id: custom,
                        supplier_id: supplier,
                        inbound_at: date,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: res => {
                        let str = '';
                        if (res.code === 200) {
                            $.each(res.list, (i, n) => {
                                // 拼接图片路径（带 asset()）
                                let thumbUrl = n.goods?.thumb_image ? "{{ asset(':url') }}".replace(':url', n.goods.thumb_image) : '';
                                // 计算剩余数量
                                let number = Number(n.number || 0);
                                let quantity = Number(n.quantity || 0);
                                let remainNumber = number - quantity;
                                str += `<tr>
                <td><input type="checkbox" name="orderCodeRadio" class="hidden">
                    <i class="fa fa-square-o"></i></td>
                <td class="orderImageUrl" image_url="${thumbUrl}">
                    <img height="30" src="${thumbUrl}">
                </td>
                <td class="orderGoodsName">${n.goods?.name || ''}</td>
                <td class="orderCustomerSku">${n.goods?.customer_sku || ''}</td>
                <td class="orderColorName">${n.goods_skus?.color || ''}</td>
                <td class="orderNumber">${number}</td>
                <td class="orderQuantity">${quantity}</td>
                <td class="orderRemainNumber">${remainNumber}</td>
                <td class="orderPrice">${n.price || 0}</td>
                <td class="orderOrderCode">${n.order?.order_code || ''}</td>
                <td class="orderOrderedAt">${n.order?.ordered_at_date || ''}</td>
                <td class="orderDeliveryAt">${n.order?.delivery_at_date || ''}</td>
                <td class="orderOrderItemId" style='display:none'>${n.id || ''}</td>
                <td class="orderGoodsId" style='display:none'>${n.goods_id || ''}</td>
                <td class="orderSkuId" style='display:none'>${n.sku_id || ''}</td>
                <td class="orderCurrencyId" style='display:none'>${n.currency_id || ''}</td>
            </tr>`;
                            });
                        }
                        console.log(str);
                        $("#orderCodeList").html(str);
                    }
                });
                $('#orderCodeModal').modal('show');
            });

            // 选中行
            $(document).on("click", "#orderCodeList tr", function () {
                let c = $(this).find("input[name='orderCodeRadio']").prop("checked");
                $(this).find("input[name='orderCodeRadio']").prop("checked", !c);
                $(this).find("i").toggleClass("fa-square-o fa-check-square-o text-info");
            });

// 确认选择订单（重构版：逻辑清晰、结构分层、无冗余）
            $("#orderCodeConfirm").click(function () {
                const $checked = $("input[name='orderCodeRadio']:checked");

                // 1. 校验勾选
                if ($checked.length === 0) {
                    alert("未勾选下单编号");
                    return false;
                }

                // 2. 循环处理选中项
                $checked.each(function () {
                    const $pTr     = $(this).closest("tr");       // 模态框当前行
                    const $active  = $(".active:last");           // 页面待赋值的行
                    const rowClone = $active.clone();             // 克隆空白行

                    // ====================== 从模态框取值 ======================
                    const orderItemId    = $pTr.find(".orderOrderItemId").text().trim();
                    const goodsId        = $pTr.find(".orderGoodsId").text().trim();
                    const skuId          = $pTr.find(".orderSkuId").text().trim();
                    const currencyId     = $pTr.find(".orderCurrencyId").text().trim();
                    const orderCode      = $pTr.find(".orderOrderCode").text().trim();
                    const goodsName      = $pTr.find(".orderGoodsName").text().trim();
                    const custmoerSku    = $pTr.find(".orderCustomerSku").text().trim();
                    const colorName      = $pTr.find(".orderColorName").text().trim();
                    const imageUrl       = $pTr.find(".orderImageUrl").attr("image_url");
                    const price          = $pTr.find(".orderPrice").text().trim();
                    const totalNumber    = Number($pTr.find(".orderNumber").text()) || 0;
                    const inQuantity     = Number($pTr.find(".orderQuantity").text()) || 0;
                    const remainNumber   = totalNumber - inQuantity;  // 剩余可入库

                    // ====================== 给当前行赋值 ======================
                    $active.addClass(orderItemId);

                    $active.find(".order_code").val(orderCode);
                    $active.find(".order_item_id").val(orderItemId).attr('value', orderItemId);

                    // ==============================================
                    // 商品下拉框，不在默认的200个内情况
                    // ==============================================
                    const $goodsSelect = $active.find(".goods_id");
                    let goodsExists = CURRENT_CUSTOMER_DEFAULT_GOODS.some(item => item.id == goodsId);

                    // 如果商品不在默认列表里，手动追加进去
                    if (!goodsExists) {
                        $goodsSelect.append(`<option value="${goodsId}" selected>${custmoerSku} ${goodsName}</option>`);
                    }

                    // 赋值 + 选中 + 禁用
                    $goodsSelect.val(goodsId).prop("disabled", true);
                    $goodsSelect.selectpicker('refresh').selectpicker('render');
                    // ==============================================


                    $active.find(".preview_image").attr("image_url", imageUrl);
                    $active.find(".thumb-img").attr("src", imageUrl).data("src", imageUrl);
                    $active.find(".sku_id").html(`<option value="${skuId}">${colorName}</option>`).prop("disabled", true);
                    $active.find(".number_difference").html(remainNumber);
                    $active.find(".currency_id").val(currencyId).prop("disabled", true);
                    $active.find(".price").val(price);
                    $active.find(".selectpicker").selectpicker('refresh').selectpicker('render');

                    // ====================== 插入新空白行 ======================
                    $active.after(rowClone);

                    // 清空新行
                    const $newRow = $active.next();
                    $newRow.find('.bootstrap-select button:first').remove();
                    $newRow.find('.selectpicker').selectpicker('val', '');
                    $newRow.find("input, select").not(".quantity").val("");
                    $newRow.find(".order_code").val("关联订单");
                    $newRow.find(".order_item_id").val(0);
                    $newRow.find(".quantity, .price, .money").val(0);
                    $newRow.removeClass(orderItemId);
                    $newRow.find(".selectpicker").selectpicker('refresh').selectpicker('render');
                });

                // 3. 清理模板行
                $(".active:last").remove();

                // 4. 刷新序号
                $(".p_body tr").each(function (i) {
                    $(this).find(".serial_number").text(i + 1);
                });

                // 5. 关闭弹窗 + 重新计算
                $('#orderCodeModal').modal('hide');
                totalAll();
            });
            // 搜索绑定（合并写法）
            $(document).on('keyup', '.search_products_name, .search_item_no, .search_order_number', search);

            // 搜索函数
            function search() {
                // 获取搜索值
                const keyword1 = $('.search_products_name').val().trim();
                const keyword2 = $('.search_item_no').val().trim();
                const keyword3 = $('.search_order_number').val().trim();

                // 遍历行
                $('#orderCodeList tr').each(function () {
                    const name = $(this).find('.orderGoodsName').text().trim();
                    const sku  = $(this).find('.orderCustomerSku').text().trim();
                    const code = $(this).find('.orderOrderCode').text().trim();

                    // 包含判断
                    const match1 = name.includes(keyword1);
                    const match2 = sku.includes(keyword2);
                    const match3 = code.includes(keyword3);

                    // 显示/隐藏
                    $(this).toggleClass('hidden', !(match1 && match2 && match3));
                });
            }

            // ====================== 提交 ======================
            $("#order").validate({
                rules: {
                    department_id: {required: true},
                    customer_id: {required: true},
                    supplier_id: {required: true},
                    warehouse_id: {required: true},
                    inbound_at: {required: true},
                },
                messages: {
                    department_id: "请选择部门",
                    customer_id: "请选择客户",
                    supplier_id: "请选择供应商",
                    warehouse_id: "请选择仓库",
                    inbound_at: "请选择入库日期",
                },
                submitHandler: function (form) {
                    $("#p_confirm").prop('disabled', true).text('提交中...');
                    $("#timg").removeClass("hidden");
                    let formData = new FormData();

                    formData.append('department_id', $("#department_id").val());
                    formData.append('customer_id', $("#customer_id").val());
                    formData.append('supplier_id', $("#supplier_id").val());
                    formData.append('warehouse_id', $("#warehouse_id").val());
                    formData.append('inbound_at', $("#inbound_at").val());
                    formData.append('inbound_code', $("#inbound_code").text());
                    formData.append('comment', $("#comment").val());

                    $(".p_body tr").each(function (index) {
                        let $tr = $(this);
                        formData.append(`goods[${index}][order_item_id]`, $tr.find("[name='order_item_id']").val());
                        //formData.append(`goods[${index}][order_code]`, $tr.find("[name='order_code']").val());
                        formData.append(`goods[${index}][goods_id]`, $tr.find("[name='goods_id']").val());
                        formData.append(`goods[${index}][sku_id]`, $tr.find("[name='sku_id']").val());
                        formData.append(`goods[${index}][quantity]`, $tr.find("[name='quantity']").val());
                        formData.append(`goods[${index}][currency_id]`, $tr.find("[name='currency_id']").val());
                        formData.append(`goods[${index}][price]`, $tr.find("[name='price']").val());
                        //formData.append(`goods[${index}][money]`, $tr.find("[name='money']").val());
                        formData.append(`goods[${index}][remark]`, $tr.find("[name='remark']").val());
                    });

                    $.ajax({
                        url: "{{ route('admin.inbounds.store') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        dataType: "json",
                        success: res => {
                            if (res.code === 200) {
                                $("#success").show();
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                alert(res.msg);
                                $("#p_confirm").prop('disabled', false).text('提交');
                                $("#timg").addClass("hidden");
                            }
                        },
                        error: res => {
                            let msg = res.responseJSON?.msg || '系统异常';
                            alert(msg);
                            $("#p_confirm").prop('disabled', false).text('提交');
                            $("#timg").addClass("hidden");
                        }
                    });
                }
            });

        });
    </script>
@endsection
