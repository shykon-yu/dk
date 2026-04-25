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
                    @php
                        $hasRoute = !empty($sub['route']) && trim($sub['route']) !== '';
                        $routeExists = $hasRoute ? \Illuminate\Support\Facades\Route::has(trim($sub['route'])) : false;
                    @endphp

                    <a href="{{ $routeExists ? menu_route(trim($sub['route'])) : 'javascript:void(0);' }}"
                       target="{{ $routeExists ? 'right' : '_self' }}"
                        {{ !$routeExists ? 'onclick="return false;"' : '' }}>
                        <span class="icon-caret-right"></span>{{ $sub['title'] }}
                    </a>

                    @if(!empty($sub['children']))
                        <ul>
                            @foreach($sub['children'] as $third)
                                <li>
                                    @php
                                        $thirdHasRoute = !empty($third['route']) && trim($third['route']) !== '';
                                        $thirdRouteExists = $thirdHasRoute ? \Illuminate\Support\Facades\Route::has(trim($third['route'])) : false;
                                    @endphp
                                    <a href="{{ $thirdRouteExists ? menu_route(trim($third['route'])) : 'javascript:void(0);' }}"
                                       target="{{ $thirdRouteExists ? 'right' : '_self' }}"
                                        {{ !$thirdRouteExists ? 'onclick="return false;"' : '' }}>
                                        {{ $third['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
