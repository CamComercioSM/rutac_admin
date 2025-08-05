<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo me-1">
        @include('_partials.macros',["height"=>20])
      </span>
      <span class="app-brand-text demo menu-text fw-semibold ms-2">{{config('variables.templateName')}}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    <li class="menu-item">
        <a class="menu-link" href="/dashboard">
          <i class="ri-dashboard-fill me-1"></i> Dashboard
        </a>
    </li>

    @foreach(session('user_menu', []) as $menu)    

        <li class="menu-item">
          <a href="{{ url($menu->url) }}" class="menu-link" >
            @isset($menu->icon)
              <i class="{{ $menu->icon }} me-1"></i>
            @endisset
            <div>{{ $menu->label }}</div>
          </a>
        </li>
    @endforeach

  </ul>

</aside>
