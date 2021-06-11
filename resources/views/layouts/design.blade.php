<!DOCTYPE html>
<html>
<head>
    <title> Login </title>

    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/designforlogin.css')}}" rel="stylesheet" />

    <style type="text/css">
        *{
            padding: 0;
            margin:0;
            box-sizing: border-box;
        }
        body {
            background-color: #d8d9de;
        }

        .row {
            background-color: white;
            border-radius: 30px;
            box-shadow: 8px 8px 8px #777c8c;
            margin:100px 0px;
        }

        img {
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
            margin:90px 0px;
        }

        .btn1 {
            border:none;
            outline: none;
            height: 50px;
            width: 100%;
            background-color: #50576b;
            color: white;
            font-weight: bold;
            border-radius: 4px;
        }

        .btn1:hover {
            background-color: white;
            color: #50576b;
            border: 1px solid;
        }

    </style>
</head>

<body>
    <section class="Form my-4 mx-5">
    @yield('content')
        <!-- <div class="container">
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
        </div> -->
    </section>
</body>
<script src="{{asset('js/core/jquery.min.js')}}"></script>
<script src="{{asset('js/core/popper.min.js')}}"></script>
<script src="{{asset('js/core/bootstrap.min.js')}}"></script>
</html>