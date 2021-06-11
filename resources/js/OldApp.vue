<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="border-right" id="sidebar-wrapper">         
                    <div id="navs" class="list-group list-group-flush">
                    <!-- <router-link to="/attendList"  class="nav-item nav-link list-group-item bg-light" ><a  href="/attendList" >出退勤一覧</a></router-link>                  -->
                    <a href="/attendList" class="nav-item nav-link list-group-item bg-secondary">Attendance list</a>  <!--出退勤一覧  -->             
                    <a href="/salaryList" class="nav-item nav-link list-group-item bg-secondary">Salary allowance list</a> <!-- 給与手当一覧 -->
                   
                    <!-- <router-link to="/holidays"  class="nav-item nav-link list-group-item bg-light" ><a  href="/holidays" >休日一覧</a></router-link> -->
                    <a href="/holidays" class="nav-item nav-link list-group-item bg-secondary">Holiday list</a> <!--休日一覧  -->

                    <a href="/salaryHistory" class="nav-item nav-link list-group-item bg-secondary">Salary related personal history</a><!--給与関連個人履歴  -->
                    
                    <a href="/attendManage" class="nav-item nav-link list-group-item bg-secondary">Attendance management output</a><!--勤怠管理表  -->
                    <!-- <router-link to="/attendManage"  class="nav-item nav-link list-group-item bg-light" ><a  href="/attendManage" >勤怠管理表</a></router-link> -->

                    <a href="/setting" class="nav-item nav-link list-group-item bg-secondary">Setting</a><!--設定  -->
                    <!-- <router-link to="/setting"  class="nav-item nav-link list-group-item bg-light" ><a  href="/setting" >設定</a></router-link> -->

                    <router-link to="/logout" class="nav-item nav-link list-group-item bg-secondary" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                    ><a   >Log out</a><!--ログアウト  -->
                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                            <input type="hidden" name="_token" :value="csrf">
                        </form>
                    </router-link>             
                    </div>
                </div>           
            </div>    
            <router-view></router-view>         
        </div>
    </div>
    
</template>



<style>

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
                        if(path=="/attendList" || path=="/salaryList" ||  path=="/holidays" || path=="/salaryHistory" || path=="/attendManage" || path=="/setting" )
                          jQuery('a[href="'+path+'"]').attr('data-target',1)
                    });
                }

        },
    }
</script>
