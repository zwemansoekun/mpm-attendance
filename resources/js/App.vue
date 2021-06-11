<template>
    
    <div class="container-fluid">
        
        <div class="sidebar" data-color="white" data-active-color="danger" >
      
            <div class="logo" style="height: 64px;">
                <a href="/" class="simple-text logo-mini">
                <div class="logo-image-small">
                    <img :src="'img/logo.jpg'" style="position: absolute;  left: 90px; width: 48px; height: 48;">
                     
                </div>
                </a>
            </div>
            <div class="col-md-2" style="margin-left:-15px;">   
                <div class="sidebar-wrapper" >
                    <ul class="nav">
                    <li id="bar1">
                        <a href="/attendList">
                        <p> Attendance List </p>
                        </a>
                    </li>
                    <li id="bar2">
                        <a href="/salaryList">
                        <p> Salary Allowance List </p>
                        </a>
                    </li>
                    <li id="bar3">
                        <a href="/holidays">
                        <p> Holiday List </p>
                        </a>
                    </li>
                    <li id="bar4">
                        <a href="/salaryHistory">
                        <p>Salary Related Personal History </p>
                        </a>
                    </li>
                    <li id="bar5">
                        <a href="/attendManage">
                        <p> Attendance Management Output </p>
                        </a>
                    </li>
                    <li id="bar6">
                        <a href="/setting">
                        <p> Setting </p>
                        </a>
                    </li>
                    <li>
                        <router-link to="/logout" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"
                        >
                        <a>
                        <p>Log Out</p>
                        </a>
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" :value="csrf">
                        </form>
                        </router-link> 
                    </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel mr-3">

            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-absolute fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <div class="navbar-toggle ">
                        <button type="button" class="navbar-toggler">
                            <span class="navbar-toggler-bar bar1"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </button>
                        </div>
                        <!-- style="color:#ffc55a;" -->
                        <a class="navbar-brand text-white" href="/" style="font-weight:bold;">
                        <b>Management Partners Myanmar</b>
                        </a>
                    </div>

                </div>
            </nav>

            <div class="content">
      
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                        <router-view>
                            
                        </router-view> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
      
</template>



<style>
    /* .nav > li {
        background-color:silver;
    } */
</style>
<script>
    export default { 
        data() {
            return {             
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
         },    
        created() {             
                let path=window.location.pathname;
                if(path!=''){                  
                    jQuery(document).ready(function(){
                        
                        // $('.nav').on('click', 'li', function(e){
                        //     $(this).addClass('active').siblings().removeClass('active');
                        // })

                        if(path=="/attendList" || path=="/salaryList" ||  path=="/holidays" || path=="/salaryHistory" || path=="/attendManage" || path=="/setting" )
                          jQuery('a[href="'+path+'"]').attr('data-target',1)

                        

                    });
                }

        },
    }

      $(document).ready(function(){
        let path=window.location.pathname;
        if(path=="/attendList"){
            $('#bar1').addClass('active');
        }
        else if(path=="/salaryList"){
            $('#bar2').addClass('active');
        }

        else if(path=="/holidays"){
            $('#bar3').addClass('active');
        }

        else if(path=="/salaryHistory"){
            $('#bar4').addClass('active');
        }

        else if(path=="/attendManage"){
            $('#bar5').addClass('active');
        }

        else if(path=="/setting"){
            $('#bar6').addClass('active');
        }

        // $('.nav').on('click', 'li', function(e){
        //     $(this).addClass('active').siblings().removeClass('active');
        // })

        // $('.nav').on('click', '#bar1', function(e){

        //     $('#bar1').addClass('active');
        //     $('#bar1').prop('style','background-color:silver');


        // })

        // $('.nav').on('click', '#bar2', function(e){


        //     $('#bar2').addClass('active');
        //     $('#bar2').prop('style','background-color:silver');


        // })
        // $('.nav').click(function() {
        //     $('.bar1').addClass('active');

        // });

        // $('.nav').on('click', '.bar1', function(){
             
        //     $('.bar1').prop('style','background-color:silver');
        //     $('.bar2').prop('style','background-color:white');

        // })

        // $('.nav').on('click', '.bar2', function(){
        //     $('.bar1').prop('style','background-color:white');
        //     $('.bar2').prop('style','background-color:silver');

        // })


      })


</script>