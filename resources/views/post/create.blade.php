@extends('layouts.account')

@section('content')
  <div class="account-layout border">
    <div class="account-hdr bg-primary text-white border">
      Sporto užsiėmimo kūrimas
    </div>
    <div class="account-bdy p-3">
      <p class="text-primary mb-4">Užpildykite visus laukelius</p>
      <div class="row mb-3">
        <div class="col-sm-12 col-md-12">
          <form action="{{route('post.store')}}" id="postForm" method="POST">
            @csrf
            <div class="form-group">
              <label for="">Užsiėmimo pavadinimas</label>
              <input type="text" placeholder="Pavadinimas" class="form-control @error('sportevent_title') is-invalid @enderror" name="sportevent_title" value="{{ old('sportevent_title') }}" required autofocus >
              @error('sportevent_title')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="">Sporto Rūšis</label>
                  <select name="sport_category" class="form-control" value="{{old('sport_category')}}" required>
                    <option value="Lengvoji atletika">Lengvoji atletika</option>
                    <option value="Krepšinis">Krepšinis</option>
                    <option value="Futbolas">Futbolas</option>
                    <option value="Asmenin. treniruotė">Asmenin. treniruotė</option>
                    <option value="Grupinės treniruotės">Grupinės treniruotės</option>
                    <option value="Bėgimas">Bėgimas</option>
                    <option value="Tenisas">Tenisas</option>
                    <option value="Stalo tenisas">Stalo tenisas</option>
                    <option value="Plaukimas">Plaukimas</option>
                    <option value="Sporto renginys">Sporto renginys</option>
                    
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="">Dalyvių skaičius</label>
                  <input type="number" class="form-control @error('attendance') is-invalid @enderror" name="attendance" value="{{ old('attendance') }}" required >
                  @error('attendance')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>
            </div>
       

            <div class="form-group">
              <label for="">Užsiėmimo tipas</label>
              <select name="event_type" class="form-control" name="event_type" value="{{old('event_type')}}"required>
                <option value="Mokamas">Mokamas</option>
                <option value="Nemokamai">Nemokamai</option>
              </select>
            </div>

            <div class="form-group">
              <label for="">Adresas</label>
              <input type="text" placeholder="Adresas" class="form-control @error('sportevent_location') is-invalid @enderror" name="sportevent_location" value="{{ old('sportevent_location') }}" required >
              @error('sportevent_location')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="">Dalyvio mokestis (Jeigu nemokama įrašyti 0)</label>
                  <input type="text" placeholder="eurai" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" >
                  @error('price')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="">Data</label>
                  <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" required >
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="">Lygis</label>
                  <select name="level" class="form-control" value="{{old('level')}}">
                    <option value="Profesionalus">Profesionalus</option>
                    <option value="Mėgėjiškas">Mėgėjiškas</option>
                  </select>
                </div>

                
                <div class="col-md-6">
                  <label for="">Amžius</label>
                  <select name="age" class="form-control" value="{{old('age')}}">
                    <option value="Nesvarbu">Nesvarbu</option>
                    <option value="iki 7m.">iki 7m.</option>
                    <option value="7-14m.">7-14m.</option>
                    <option value="14-18m.">14-18m.</option>
                    <option value="18-30m.">18-30m.</option>
                   
                  </select>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="">Laikas <span class="text-info"></span></label>
              <input type="text" placeholder="hh:mm" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time') }}" required >
            </div>



            <div class="form-group">
              <label for="">Užsiėmimo aprašymas <small>(Pasirinktinai)</small></label>
              <input type="hidden" id="description" name="description" value="{{old('description')}}">
              <div id="quillEditor" style="height:200px"></div>
            </div>

            <button type="button" id="postBtn" class="btn primary-btn">Sukurti</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endSection

@push('css')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('js')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  $(document).ready(function(){
    var quill = new Quill('#quillEditor', {
    modules: {
      toolbar: [
          [{ 'font': [] }, { 'size': [] }],
          ['bold', 'italic'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link', 'blockquote', 'code-block', 'image'],
        ]
      },
    
    theme: 'snow'
    });
    

    const postBtn = document.querySelector('#postBtn');
    const postForm = document.querySelector('#postForm');
    const description = document.querySelector('#description');
    
    if(description.value){
      quill.root.innerHTML = description.value;
    }

    postBtn.addEventListener('click',function(e){
      e.preventDefault();
      description.value = quill.root.innerHTML
      
      postForm.submit();
    })
  })
</script>
@endpush