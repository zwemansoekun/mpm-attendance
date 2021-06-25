<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="border-right" id="sidebar-wrapper">         
                    <div id="navs" class="list-group list-group-flush">
                    <router-link to="/attendList" exact class="nav-item nav-link list-group-item  bg-secondary"  @click.native="colorChange()">Attendance list</router-link>                 
                    <!-- <a href="/attendList" class="nav-item nav-link list-group-item bg-secondary">Attendance list</a>  出退勤一覧               -->

                     <router-link to="/salaryList" exact class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()">Salary allowance list</router-link>    
                    <!-- <a href="/salaryList" class="nav-item nav-link list-group-item bg-secondary">Salary allowance list</a> 給与手当一覧 -->
                   
                    <router-link to="/holiday" exact  class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()" >Holiday list</router-link>
                    <!-- <a href="/holidays" class="nav-item nav-link list-group-item bg-secondary">Holiday list</a> 休日一覧  -->

                    <router-link to="/salaryHistory" exact  class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()">Salary related personal history</router-link>
                    <!-- <a href="/salaryHistory" class="nav-item nav-link list-group-item bg-secondary">Salary related personal history</a>給与関連個人履歴  -->
                    
                    <router-link to="/attendManage" exact class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()">Attendance management output</router-link>
                    <!-- <a href="/attendManage" class="nav-item nav-link list-group-item bg-secondary">Attendance management output</a>勤怠管理表                    -->

                    <router-link to="/setting"  exact class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()">Setting</router-link>
                    <!-- <a href="/setting" class="nav-item nav-link list-group-item bg-secondary">Setting</a>設定  -->
                  
                    <router-link to="/logout" exact class="nav-item nav-link list-group-item bg-secondary" @click.native="colorChange()" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                    >Log out<!--ログアウト  -->
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
                this.colorChange();
        },
        methods :{
            colorChange: function(){              
                let path=window.location.pathname;
                if(path!=''){         
                    jQuery('a[href="/attendList"]').removeAttr('data-target');jQuery('a[href="/salaryList"]').removeAttr('data-target');
                    jQuery('a[href="/holidays"]').removeAttr('data-target');jQuery('a[href="/salaryHistory"]').removeAttr('data-target');
                    jQuery('a[href="/attendManage"]').removeAttr('data-target');jQuery('a[href="/setting"]').removeAttr('data-target');       
                    jQuery(document).ready(function(){
                        if(path=="/attendList" || path=="/salaryList" ||  path=="/holidays" || path=="/salaryHistory" || path=="/attendManage" || path=="/setting" )
                          jQuery('a[href="'+path+'"]').attr('data-target',1)
                    });
                }
            }
        }
    }
</script>