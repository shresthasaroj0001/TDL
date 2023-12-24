@if(count($errors)>0)
<div class="alert alert-danger alert-dismissable fade show" role="alert">
  <ul>
    @foreach ($errors->all() as $error)
    <li>
      {{ $error }}
    </li>
    @endforeach
  </ul>
</div>
@endif

@if(null !== (session()->get('success')))
<div class="card bg-success text-white mb-4">
  <div class="card-body">{{ session()->get('success') }}</div>
</div>
@endif

@if(null !== (session()->get('error')))
<div class="card bg-danger text-white mb-4">
  <div class="card-body">{{ session()->get('error')}}</div>
</div>
@endif