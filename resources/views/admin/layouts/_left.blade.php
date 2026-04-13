<!-- 左侧导航 -->
<div class="leftnav">
    <div class="leftnav-title">
        <span class="logo-tit">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;后台管理系统</span>
    </div>

    @foreach($menu as $item)
        <h2>
            <span class="icon-caret-down"></span>{{ $item['title'] }}
        </h2>
        <ul>
            @foreach($item['children'] ?? [] as $sub)
                <li>
                    <a href="{{ $sub['route'] ? route($sub['route']) : 'javascript:;' }}" target="right">
                        <span class="icon-caret-right"></span>{{ $sub['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
