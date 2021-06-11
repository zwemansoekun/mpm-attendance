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
            <form>
                <div class="form-row">
                    <div class="col-lg-7">
                        <input type="email" placeholder="Email" class="form-control my-3 p-4">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-7">
                        <input type="password" placeholder="Password" class="form-control my-3 p-4">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-lg-7">
                        <button type="submit" class="btn1 mt-3 mb-5"> Login </button>
                    </div>
                </div>
                <a href="#"> Forgot Password </a>
                <p> Don't have an account ? <a href="#"> Register Here </a> </p>
            </form>
        </div>
    </div>
</div>

@endsection