@extends('layouts.account')

@section('content')
  <div class="account-layout  border">
    <div class="account-hdr bg-primary text-white border" >
    Bendra informacija
    </div>
    <div class="account-bdy p-3">
        <div class="row mb-3">
          <div class="col-xl-4 col-sm-6 py-2">
              <div class="card dashboard-card text-white h-100 shadow">
                  <div class="card-body primary-bg">
                      <div class="rotate">
                          <i class="fas fa-users fa-4x"></i>
                      </div>
                      <h6 class="text-uppercase">Dalyviai</h6>
                      <h1 class="">{{$dashCount['user']}}</h1>
                  </div>
              </div>
          </div>
          <div class="col-xl-4 col-sm-6 py-2">
              <div class="card dashboard-card text-white  h-100 shadow">
                  <div class="card-body bg-secondary">
                      <div class="rotate">
                          <i class="fas fa-building fa-4x"></i>
                      </div>
                      <h6 class="text-uppercase">Visi užsiėmimai</h6>
                      <h1 class="">{{$dashCount['post']}}</h1>
                  </div>
              </div>
          </div>
          <div class="col-xl-4 col-sm-6 py-2">
              <div class="card dashboard-card text-white h-100 shadow">
                  <div class="card-body bg-info">
                      <div class="rotate">
                          <i class="fas fa-user-tie fa-4x"></i>
                      </div>
                      <h6 class="text-uppercase">Organizatoriai</h6>
                      <h1 class="">{{$dashCount['author']}}</h1>
                  </div>
              </div>
          </div>
          <div class="col-xl-6 col-sm-6 py-2">
            <div class="card dashboard-card text-white h-100 shadow">
                <div class="card-body bg-danger">
                    <div class="rotate">
                        <i class="fas fa-star-of-life fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Aktyvūs užsiėmimai</h6>
                    <h1 class="">{{$dashCount['livePost']}}</h1>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-sm-6 py-2">
            <div class="card dashboard-card text-white h-100 shadow">
                <div class="card-body bg-warning">
                    <div class="rotate">
                        <i class="fas fa-industry fa-4x"></i>
                    </div>
                    <h6 class="text-uppercase">Skirtingų miestų skaičius</h6>
                    <h1 class="">{{$organisatorcity->count()}}</h1>
                </div>
            </div>
        </div>
      </div>

      <section class="dashboard-authors my-5">
        <div class="row my-4">
          <div class="col-lg-12 col-md-8 col-sm-12">
            <h4 class="card-title text-secondary">Tvarkyti Organizatorius </h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-inverse">
                        <tr>
                            <th>#</th>
                            <th>Vardas</th>
                            <th>El.paštas</th>
                            <th>Organizacijos pavadinimas/Vardas Pavardė</th>
                            <th>Veiksmai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAuthors as $author)
                        @if ($author->organisator)
                        <tr>
                            <td>{{$author->id}}</td>
                            <td>{{$author->name}}</td>
                            <td>{{$author->email}}</td>
                            <td>{{$author->organisator->title}}</td>
                            <td>
                            <a href="{{route('account.Organisator',['Organisator'=>$author->organisator])}}" class="btn primary-btn">Peržiūrėti organizatorius</a>
                            </td> 
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button class="btn primary-outline-btn disabled">Užsiregistravusių organizatorių skaičius ({{ $recentAuthors->total()}}) </button>

            <div class="d-flex justify-content-center mt-4 custom-pagination">
                {{ $recentAuthors->links() }}
              </div>
          </div>
        </div>
      <!--/row-->
      </section>
      <hr>
    
      <section class="dashboard-organisator">
          <h4 class="card-title text-secondary">Miestų informacija</h4>
          <div class="row my-4">
            <div class="col-sm-12 col-md-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#city-tab" role="tab" data-toggle="tab">Miestai</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <br>
                    <div role="tabpanel" class="tab-pane active" id="city-tab">
                        <div class="mb-3">
                            <form action="{{route('city.store')}}" method="POST">
                                @csrf
                                <label for="">Pridėti naują miestą</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" placeholder="Miestai" name="city_name">
                                    <button class="btn secondary-btn">Pridėti</button>
                                </div>
                                @error('city_name')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                            </form>
                        </div>
                      
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th>#</th>
                                        <th>Miestas</th>
                                        <th>Veiksmai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($organisatorcity as $city)
                                    <tr>
                                        <td>{{$city->id}}</td>
                                        <td>{{$city->city_name}}</td>
                                        <td><a class="btn secondary-btn" href="{{route('city.edit',['city'=>$city])}}">Redaguoti</a> 
                                            <form action="{{route('city.destroy',['id'=>$city->id])}}" id="cityDestroyForm" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button id="cityDestroyBtn" class="btn danger-btn">Ištrinti</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="roles-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th>#</th>
                                        <th>Rolės</th>
                                        <th>Veiksmai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $index=>$role)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$role}}</td>
                                            <td><a class="btn secondary-btn" href="">Redaguoti</a> <form action="" class="d-inline"><button type="submit" class="btn danger-btn">Ištrinti</button></form></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="permissions-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th>#</th>
                                        <th>Leidimai</th>
                                        <th>Veiksmai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $index=>$permission)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$permission}}</td>
                                            <td><a class="btn secondary-btn" href="">Redaguoti</a> 
                                            <form action="" class="d-inline"><button type="submit" class="btn danger-btn">Ištrinti</button></form></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="role-have-permission-tab">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th>#</th>
                                        <th>Rolė</th>
                                        <th>Leidimai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rolesHavePermissions as $index=>$role)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>
                                                {{$role->name}}
                                            </td>
                                            <td>
                                                @if($role->permissions->count() == 0)
                                                    <span class="badge badge-primary">Paprastas leidimas</span>
                                                @else
                                                    @foreach ($role->permissions as $permission)
                                                        <span class="badge badge-primary">{{$permission->name}}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
          </div>
      </section>
    </div>
  </div>
@endSection

@push('js')
<script>
     $(document).ready(function(){
        //delete city 
        $('#cityDestroyBtn').click(function(e){
            e.preventDefault();
            if(window.confirm('Ar jūs tikrai norite ištrinti sporto užsiėmimo kategoriją?')){
                $('#cityDestroyForm').submit();
            }
        })
    })
</script>
@endpush
