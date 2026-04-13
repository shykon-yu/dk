<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta name="author" content="后台管理系统">
    <title>@yield('title','后台管理')</title>
    <!-- 样式文件 -->
    @include('admin.layouts._css_app')
    <!-- 脚本文件 -->
    @include('admin.layouts._js_app')
</head>
<body>
@yield('content')
</body>
</html>
@yield('script')

