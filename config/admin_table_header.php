<?php
return [
    'menu' => [
        ['field' => 'check', 'name' => '全选'],
        ['field' => 'id', 'name' => 'ID'],
        ['field' => 'title', 'name' => '菜单名称'],
        ['field' => 'route', 'name' => '路由'],
        ['field' => 'permission', 'name' => '关联权限'],
        ['field' => 'sort', 'name' => '排序'],
        ['field' => 'created_at', 'name' => '创建时间'],
        ['field' => 'action', 'name' => '操作'],
    ],

    // 👇 角色表头（和菜单风格 1:1 对齐）
    'role' => [
        ['field' => 'check', 'name' => '全选'],
        ['field' => 'id', 'name' => 'ID'],
        ['field' => 'name', 'name' => '角色名称'],
        ['field' => 'permissions_count', 'name' => '权限数'],
        ['field' => 'created_at', 'name' => '创建时间'],
        ['field' => 'action', 'name' => '操作'],
    ],
];
