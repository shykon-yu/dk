<!-- 左侧导航 -->
<div class="leftnav">
    <div class="leftnav-title">
        <span class="logo-tit">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;后台管理系统</span>
    </div>
    @foreach( $admin_menus as $item)
        <h2>
            <span class="icon-caret-down"></span>{{ $item['title'] }}
        </h2>
        <ul>
            @foreach($item['children'] ?? [] as $sub)
                <li>
                    <a href="{{ $sub['route'] && $sub['route']!=' ' ? menu_route($sub['route']) : 'javascript:;' }}" target="right">
                        <span class="icon-caret-right"></span>{{ $sub['title'] }}
                    </a>

                    {{-- 三级菜单 --}}
                    @if(!empty($sub['children']))
                        <ul>
                            @foreach($sub['children'] as $third)
                                <li>
                                    <a href="{{ $third['route'] && $third['route']!=' ' ? menu_route($third['route']) : 'javascript:;' }}" target="right">{{ $third['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
