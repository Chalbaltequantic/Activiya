@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@if(session('errorRows'))
    <div class="alert alert-warning" style="max-height:220px;overflow:auto">
        <b>Skipped Rows</b>
        <ul>
            @foreach(session('errorRows') as $err)
                <li>Row {{ $err['row'] }}: {{ $err['reason'] }}</li>
            @endforeach
        </ul>
    </div>
@endif
