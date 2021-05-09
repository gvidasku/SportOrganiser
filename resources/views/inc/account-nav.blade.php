<div class="account-nav">
  <ul class="list-group">
    @role('admin')
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'dashboard' ? 'active': ''}}">
      <a href="{{route('account.dashboard')}}" class="account-nav-link">
        <i class="fas fa-info"></i> Bendra informacija
      </a>
    </li>
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'view-all-users' ? 'active': ''}}">
      <a href="{{route('account.viewAllUsers')}}" class="account-nav-link">
        <i class="fas fa-users"></i> Vartotojų sąrašas
      </a>
    </li>
    @endrole
    @role('author')
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'author-section' ? 'active': ''}}">
      <a href="{{route('account.authorSection')}}" class="account-nav-link">
        <i class="fas fa-info"></i> Organizavimas
    </li>
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'post' && request()->segment(3) == 'create' ? 'active': ''}}">
      <a href="{{route('post.create')}}" class="account-nav-link">
        <i class="fas fa-plus-square"></i> Sporto užsiėmimo kūrimas
    </li>
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'sportevent-application' ? 'active': ''}}">
      <a href="{{route('sporteventApplication.index')}}" class="account-nav-link">
        <i class="fas fa-users"></i> Dalyvių sąrašas
    </li>
    @endrole
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'overview' ? 'active': ''}}">
      <a href="{{route('account.index')}}" class="account-nav-link">
        <i class="fas fa-user-shield"></i> Vartotojo paskyra
      </a>
    </li>
    @role('user')
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'become-Organisator' ? 'active': ''}}">
      <a href="{{route('account.becomeOrganisator')}}" class="account-nav-link">
        <i class="fas fa-user-shield"></i> Tapti organizatoriumi
      </a>
    </li>
    @endrole
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'change-password' ? 'active': ''}}">
      <a href="{{route('account.changePassword')}}" class="account-nav-link">
        <i class="fas fa-fingerprint"></i> Keisti slaptažodį
      </a>
    </li>    
    <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'my-saved-sportevents' ? 'active': ''}}">
      <a href="{{route('savedsportevent.index')}}" class="account-nav-link">
        <i class="fas fa-stream"></i> Išsaugoti užsiėmimai
      </a>
    </li>   
     <li class="list-group-item list-group-item-action {{ request()->segment(2) == 'deactivate' ? 'active': ''}}">
      <a href="{{route('account.deactivate')}}" class="account-nav-link">
        <i class="fas fa-trash-alt"></i> Ištrinti paskyrą
      </a>
    </li>    
  </ul>
</div>