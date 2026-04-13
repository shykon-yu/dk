<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="renderer" content="webkit">
    <title>@yield('title','后台管理系统')</title>
    @include('admin.layouts._header')
</head>
<body>
@include('admin.layouts._left')
<!-- 顶部导航 -->
<ul class="nav navbar-nav bread">
    <li>{{ auth()->user()->name }}，你好！欢迎登录</li>
    <li><a href="{{ route('admin.dashboard') }}">首页</a></li>
    <li><a id="logout-btn" style="cursor:pointer;">退出</a></li>
</ul>
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- 主体内容 -->
<div class="admin">
    <div class="default-body">
        @yield('content')
    </div>
    <iframe src="" name="right" width="100%" height="100%" frameborder="0"></iframe>
</div>

@include('admin.layouts._footer')
</body>
</html>
