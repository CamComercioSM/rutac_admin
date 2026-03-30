<aside id="layout-menu" class="layout-menu menu-vertical menu">

    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo me-1">
                @include('_partials.macros', ['height' => 40])
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2"></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="menu-toggle-icon d-xl-block align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $currentRoute = request()->getPathInfo();
    @endphp

    <ul class="menu-inner py-1 mb-5">

        <li class="menu-item mt-7 {{ $currentRoute === '/dashboard' ? 'active' : '' }}">
            <a class="menu-link" href="{{ url('/dashboard') }}">
                <i class="menu-icon icon-base ri ri-dashboard-fill me-1"></i>
                <div>Panel de Inicio</div>
            </a>
        </li>

        @foreach (session('user_menu', []) as $menu)
            @php
                $menuUrl = $menu->url ?? '';
                $isParentActive = false;

                // Determinar si algún hijo está activo para abrir el menú desplegable
                if (isset($menu->submenus) && $menu->submenus->isNotEmpty()) {
                    foreach ($menu->submenus as $submenu) {
                        if ($currentRoute === ($submenu->url ?? '')) {
                            $isParentActive = true;
                            break;
                        }
                    }
                }
            @endphp

            {{-- CASO 1: ES UN ENCABEZADO / SEPARADOR --}}
            @if ($menu->type === 'HEADER')
                <li class="menu-header mt-5">
                    @isset($menu->icon)
                        <i class="menu-icon icon-base ri {{ $menu->icon }} me-1"></i>
                    @endisset
                    <span class="menu-header-text">{{ $menu->label }}</span>
                </li>

            {{-- CASO 2: ES UN ENLACE (CON O SIN SUBMENÚS) --}}
            @else
                <li class="menu-item {{ $currentRoute === $menuUrl ? 'active' : '' }} {{ $isParentActive ? 'open' : '' }}">
                    
                    @if (isset($menu->submenus) && $menu->submenus->isNotEmpty())
                        {{-- Menu con Submenús (Dropdown) --}}
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            @isset($menu->icon)
                                <i class="menu-icon icon-base ri {{ $menu->icon }} me-1"></i>
                            @endisset
                            <div>{{ $menu->label }}</div>
                        </a>

                        <ul class="menu-sub">
                            @foreach ($menu->submenus as $submenu)
                                @php $subUrl = $submenu->url ?? ''; @endphp
                                <li class="menu-item {{ $currentRoute === $subUrl ? 'active' : '' }}">
                                    <a href="{{ !empty($subUrl) ? url($subUrl) : 'javascript:void(0);' }}" class="menu-link">
                                        @isset($submenu->icon)
                                            <i class="menu-icon icon-base ri {{ $submenu->icon }} me-1"></i>
                                        @endisset
                                        <div>{{ $submenu->label }}</div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        {{-- Enlace simple sin submenús --}}
                        <a href="{{ !empty($menuUrl) ? url($menuUrl) : 'javascript:void(0);' }}" class="menu-link">
                            @isset($menu->icon)
                                <i class="menu-icon icon-base ri {{ $menu->icon }} me-1"></i>
                            @endisset
                            <div>{{ $menu->label }}</div>
                        </a>
                    @endif
                </li>
            @endif

        @endforeach
    </ul>
</aside>