

<aside id="layout-menu" class="layout-menu menu-vertical menu" >

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo me-1">
        @include('_partials.macros',["height"=>40 ])
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

    <li class="menu-item mt-7">
        <a class="menu-link" href="/dashboard">
          <i class="menu-icon icon-base ri ri-dashboard-fill me-1"></i> 
          <div>Dashboard</div>
        </a>
    </li>

    @foreach(session('user_menu', []) as $menu)    

        @php
          $isParentActive = false;

          if ($menu->submenus && $menu->submenus->isNotEmpty()) {
              foreach ($menu->submenus as $submenu) {
                  if ($currentRoute === $submenu->url) {
                      $isParentActive = true;
                      break;
                  }
              }
          }
        @endphp

        @if ($menu->icon == null && $menu->url == null)

          <li class="menu-header mt-5">
            <span class="menu-header-text">{{ $menu->label }}</span>
          </li>

        @else        

          <li class="menu-item {{$currentRoute === $menu->url ? 'active' : ''}} {{$isParentActive ? 'open' : ''}}">
                        
              @if($menu->submenus->isNotEmpty())
                <a href="javascript:void(0);" class="menu-link menu-toggle" >
                  @isset($menu->icon)
                    <i class="menu-icon icon-base ri {{ $menu->icon }} me-1"></i>
                  @endisset
                  <div>{{ $menu->label }}</div>
                </a>

                <ul class="menu-sub">
                  @foreach($menu->submenus as $submenu)    
                    <li class="menu-item {{$currentRoute === $submenu->url ? 'active' : ''}}" >

                      <a href="{{ url($submenu->url) }}" class="menu-link" >
                        @isset($submenu->icon)
                          <i class="menu-icon icon-base ri {{ $submenu->icon }} me-1"></i>
                        @endisset
                        <div>{{ $submenu->label }}</div>
                      </a>

                    </li>
                  @endforeach
                </ul>
              
              @else
                <a href="{{ url($menu->url) }}" class="menu-link" >
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

