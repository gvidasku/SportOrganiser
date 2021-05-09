@extends('layouts.account')

@section('content')
  <div class="account-layout  border">
    <div class="account-hdr bg-primary text-white border">
      Dalyviai
    </div>
    <div class="account-bdy p-3">
      <div class="row">
        <div class="col-sm-12 col-md-12">
          <div class="table-responsive pt-3">
            <table class="table table-hover table-striped small">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Vardas</th>
                  <th>El. paštas</th>
                  <th>Pavadinimas</th>
                  <th>Užsiregistravimo data</th>
                  <th>Veiksmai</th>
                </tr>
              </thead>
              <tbody>
                @if($applications && $applications->count())
                  @foreach($applications as $application)
                  <tr>
                    <td>1</td>
                    <td>{{$application->user->name}}</td>
                    <td><a href="mailto:{{$application->user->email}}">{{$application->user->email}}</a></td>
                    <td><a href="{{route('post.show',['sportevent'=>$application->post->id])}}">{{substr($application->post->sportevent_title,0,14)}}...</a></td>
                    <td>{{$application->created_at}}</td>
                    <td><a href="{{route('sporteventApplication.show',['id'=>$application])}}" class="btn primary-outline-btn">Peržiūrėti</a>
                      <form action="{{route('sporteventApplication.destroy')}}" method="POST" class="d-inline-block">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="application_id" value="{{$application->id}}">
                        <button type="submit" class="btn danger-btn">Pašalinti</button>
                      </form>
                    </td>
                  </tr>
                  @endforeach
                @else
                  <tr>
                    <td>Nėra dalyvių.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-center mt-4 custom-pagination">
            {{ $applications && $applications->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endSection
