<script>
    /**
     * 【可复用】客户商品选择器
     * 支持：
     *  1. 选择客户加载200默认商品
     *  2. 搜索实时查询
     *  3. 清空恢复默认（不发请求）
     *  4. 多实例、多行tr独立运行
     *  5. 外部可获取默认商品（plus 行用）
     */
    function CustomerGoodsSelector(options) {
        let _this = this;

        // 配置
        _this.options = $.extend({
            customerEl: '#customer_id',           // 客户选择器
            goodsEl: 'select[name="goods_id"]',   // 商品选择器
            urlDefault: '',                       // 默认200条接口
            urlSearch: '',                        // 搜索接口
        }, options);

        // 🔥 内部缓存（不是全局！）
        _this.defaultGoods = [];

        // 元素
        _this.$customer = $(_this.options.customerEl);
        _this.$goods     = $(_this.options.goodsEl);

        // 初始化
        _this.init();
    }

    // 初始化事件
    CustomerGoodsSelector.prototype.init = function () {
        let _this = this;
        _this.bindCustomerChange();
        _this.bindSearch();
    };

    // 选择客户 → 加载默认200
    CustomerGoodsSelector.prototype.bindCustomerChange = function () {
        let _this = this;

        $(document).off('change', _this.options.customerEl).on('change', _this.options.customerEl, function () {
            let customer_id = $(this).val();

            if (!customer_id) {
                _this.defaultGoods = [];
                _this.renderOptions(_this.$goods, []);
                return;
            }

            $.ajax({
                url: _this.options.urlDefault,
                type: 'POST',
                data: {
                    customer_id: customer_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.code === 200) {
                        _this.defaultGoods = res.data; // 🔥 存在实例里
                        _this.renderOptions(_this.$goods, res.data);
                    }
                }
            });
        });
    };

    // 搜索
    CustomerGoodsSelector.prototype.bindSearch = function () {
        let _this = this;

        $(document).off('keyup', '.bs-searchbox input').on('keyup', '.bs-searchbox input', function (e) {
            let keyword = $(this).val().trim();
            let $input = $(this);

            // 找到当前打开的是哪一个 select
            let $openSelect = $input.closest('.bootstrap-select').siblings('select');

            if (keyword === '') {
                _this.renderOptions($openSelect, _this.defaultGoods);
                return;
            }

            if (keyword.length < 2) return;

            $.ajax({
                url: _this.options.urlSearch,
                type: 'POST',
                data: {
                    customer_id: $(_this.options.customer_id).val(),
                    keyword: keyword,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.code === 200) {
                        _this.renderOptions($openSelect, res.data);
                    }
                }
            });
        });
    };

    // 渲染下拉
    CustomerGoodsSelector.prototype.renderOptions = function ($select, list) {
        let str = '';
        $.each(list, function (i, item) {
            str += `<option value="${item.id}">${item.customer_sku} ${item.name}</option>`;
        });
        $select.html(str).selectpicker('refresh').selectpicker('render');
    };

    // 🔥 外部获取默认商品（plus 新增行时调用）
    CustomerGoodsSelector.prototype.getDefaultGoods = function () {
        return this.defaultGoods || [];
    };
</script>
