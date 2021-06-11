@extends('layouts.design')
@section('content')

<div class="container">
    <div class="row no-gutters">
        <div class="col-lg-5">
            <img src="{{asset('img/img2.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-lg-7 px-5 pt-5">
            <h2 class="font-weight-bold py-3" style="color:#ffa500;"> Management Partners Myanmar </h2>
            <h4> Sign Into Your Account </h4>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-row">
                    <div class="col-lg-7">
                        <input  id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror my-3 p-4" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-7">
                        <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror my-3 p-4" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-7">
                        <button type="submit" class="btn1 mt-3 mb-5"> Login </button>
                    </div>
                </div>
                <!-- <a href="#"> Forgot Password </a> -->
                <!-- <p> Don't have an account ? <a href="#"> Register Here </a> </p> -->
            </form>
        </div>
    </div>
</div>

@endsection