@extends('layouts.app')

@section('content')

    <div class="container" style="margin-top: 3%">
        <div class="container">
            @if(count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger text-center">
                        {{ $error }}
                    </div>
                @endforeach
            @endif
        </div>
        <form method="post" action="/conversation">
            {{ csrf_field() }}
          {{--  <div class="form-group">
                <label for="formGroupExampleInput">Para:</label>
                <input type="text" class="form-control" id="formGroupExampleInput1" placeholder="Usuario" name="user" required>
            </div>--}}
            <div class="form-group">
                <label for="formGroupExampleInput">Asunto:</label>
                <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Asunto" name="subject" required>
            </div>
{{--            <div class="form-group">
                <input type="password" class="form-control" id="formGroupExampleInput3"  placeholder="Secret" name="secret" required>
            </div>--}}
            <div class="form-group">
                <textarea class="form-control" placeholder="Escriba su texto aquí" rows="5" id="message" name="message" required></textarea>
            </div>
            <label for="formGroupExampleInput">Tu mensaje será encriptado:</label>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="Send">
            </div>
        </form>
    </div>

@endsection
