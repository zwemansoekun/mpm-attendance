<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="bg-light border-right" id="sidebar-wrapper">         
                    <div id="navs" class="list-group list-group-flush">
                    <!-- <router-link to="/attendList"  class="nav-item nav-link list-group-item bg-light" ><a  href="/attendList" @click="nameColor($event)">出退勤一覧</a></router-link>                  -->
                    <a href="/attendList"  @click="nameColor($event)"  class="nav-item nav-link list-group-item  bg-light">出退勤一覧</a>               
                    <a href="/salaryList"  @click="nameColor($event)"  class="nav-item nav-link list-group-item  bg-light" >給与手当一覧</a>
                   
                    <!-- <router-link to="/holidays"  class="nav-item nav-link list-group-item bg-light" ><a  href="/holidays" @click="nameColor($event)">休日一覧</a></router-link> -->
                    <a href="/holidays" @click="nameColor($event)" class="nav-item nav-link list-group-item  bg-light" >休日一覧</a>

                    <a href="/salaryHistory" @click="nameColor($event)" class="nav-item nav-link list-group-item  bg-light" >給与関連個人履歴</a>
                    
                    <a href="/attendManage" @click="nameColor($event)" class="nav-item nav-link list-group-item  bg-light" >勤怠管理表</a>
                    <!-- <router-link to="/attendManage"  class="nav-item nav-link list-group-item bg-light" ><a  href="/attendManage" @click="nameColor($event)">勤怠管理表</a></router-link> -->

                    <a href="/setting" @click="nameColor($event)" class="nav-item nav-link list-group-item  bg-light" >設定</a>
                    <!-- <router-link to="/setting"  class="nav-item nav-link list-group-item bg-light" ><a  href="/setting" @click="nameColor($event)">設定</a></router-link> -->

                    <router-link to="/logout"   class="nav-item nav-link list-group-item  bg-light" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                    ><a  @click="nameColor($event)">ログアウト</a>
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
                let target = localStorage.getItem('target');
            
                if(target!=''){                  
                    jQuery(document).ready(function(){
                         jQuery('a[href="'+target+'"]').attr('data-target',1);
                    });
                   
                }

        },
        methods: {  
            nameColor:function(event){
                let target = localStorage.getItem('target');
                jQuery('a[href="'+target+'"]').removeAttr('data-target');
                localStorage.removeItem('target');
               
                let hrefName=jQuery(event.target).attr('href');
             
                localStorage.setItem('target', hrefName);
                // if(hrefName=="/attendList" || hrefName=="/holidays" || hrefName=="/attendManage" || hrefName=="/setting"  ){
                //     jQuery('a[href="'+hrefName+'"]').attr('data-target',1);
                // }
            },
        }
    }
</script>
