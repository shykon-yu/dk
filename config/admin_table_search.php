<?php
return [
    // 菜单管理搜索配置
    'menu' => [
        'action' => 'admin.menu.index',
        'method' => 'get',
        'items'  => [
            [
                'type'        => 'input',
                'name'        => 'title',
                'placeholder' => '菜单名称',
                'class'       => 'form-control input-sm',
            ],
            [
                'type'        => 'select',
                'name'        => 'status',
                'placeholder' => '状态',
                'class'       => 'form-control input-sm',
                'options'     => [1 => '启用', 0 => '禁用'],
            ],
            [
                'type'        => 'date',
                'name'        => 'start_date',
                'placeholder' => '创建开始日期',
                'class'       => 'form-control input-sm',
            ],
            [
                'type'        => 'date',
                'name'        => 'end_date',
                'placeholder' => '创建结束日期',
                'class'       => 'form-control input-sm',
            ],
        ],
    ],

    // 订单管理搜索配置（完全复刻你原订单页）
    'order' => [
        'action' => 'admin.order.index',
        'method' => 'get',
        // 搜索项：输入框/多选下拉/隐藏域/日期/快捷按钮
        'items'  => [
            // 1. 多选下拉（宝贝选择，带搜索/全选）
            [
                'type'          => 'select-multiple',
                'name'          => 'products_id',
                'id'            => 'products_id',
                'class'         => 'selectpicker form-control input-sm',
                'placeholder'   => '请选择宝贝',
                'onchange'      => 'change_products()',
                'attrs'         => [ // 自定义标签属性
                    'data-live-search' => 'true',
                    'data-live-search-placeholder' => 'Search',
                    'data-actions-box' => 'true',
                    'title' => '请选择宝贝'
                ],
                'options'       => [0 => '-请选择宝贝-'], // 动态数据控制器补充
                'empty_option'  => false, // 不重复显示空选项
            ],
            // 2. 隐藏域
            [
                'type'        => 'hidden',
                'name'        => 'products_ids',
                'id'          => 'products_ids',
            ],
            // 3. 输入框（宝贝货号）
            [
                'type'        => 'input',
                'name'        => 'item_no',
                'id'          => 'item_no',
                'placeholder' => '按宝贝货号查询',
                'class'       => 'form-control input-sm',
            ],
            // 4. 输入框（旺旺ID）
            [
                'type'        => 'input',
                'name'        => 'wangwang_id',
                'id'          => 'wangwang_id',
                'placeholder' => '按旺旺ID查询',
                'class'       => 'form-control input-sm',
            ],
            // 5. 单选下拉（是否补单）
            [
                'type'        => 'select',
                'name'        => 'supplement_status',
                'id'          => 'examined',
                'class'       => 'form-control input-sm ax supplement_status',
                'placeholder' => '是否补单',
                'options'     => [2 => '是否补单', 1 => '是补单', 0 => '非补单'],
            ],
            // 6. 输入框（快递单号）
            [
                'type'        => 'input',
                'name'        => 'express_number',
                'id'          => 'express_number',
                'placeholder' => '按快递单号查询',
                'class'       => 'form-control input-sm',
            ],
            // 7. 输入框（订单号）
            [
                'type'        => 'input',
                'name'        => 'order_number',
                'id'          => 'order_number',
                'placeholder' => '按订单号查询',
                'class'       => 'form-control input-sm',
            ],
            // 8. 快捷日期按钮组（30天/7天/昨天/今天）
            [
                'type'        => 'button-group',
                'class'       => 'btn-group',
                'buttons'     => [
                    ['text' => '30天', 'class' => 'btn btn-info btn-sm recent_days', 'attrs' => ['recent_days' => '30']],
                    ['text' => '7天', 'class' => 'btn btn-info btn-sm recent_days', 'attrs' => ['recent_days' => '7']],
                    ['text' => '昨天', 'class' => 'btn btn-info btn-sm recent_days', 'attrs' => ['recent_days' => '1']],
                    ['text' => '今天', 'class' => 'btn btn-info btn-sm recent_days', 'attrs' => ['recent_days' => '0']],
                ],
            ],
            // 9. 日期框（购买起始日期）
            [
                'type'        => 'date',
                'name'        => 'start_date',
                'id'          => 'start_date',
                'placeholder' => '购买起始日期',
                'class'       => 'form-control input-sm',
                'attrs'       => ['autocomplete' => 'off'],
            ],
            // 10. 日期框（购买结束日期）
            [
                'type'        => 'date',
                'name'        => 'end_date',
                'id'          => 'end_date',
                'placeholder' => '购买结束时间',
                'class'       => 'form-control input-sm',
            ],
        ],
    ],
];
