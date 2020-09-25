<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="bg-light border-right" id="sidebar-wrapper">         
                    <div class="list-group list-group-flush">
                    <a href="/attendList" class="nav-item nav-link list-group-item list-group-item-action bg-light">出退勤一覧</a>
                    <a href="/salaryList" class="nav-item nav-link list-group-item list-group-item-action bg-light">給与手当一覧</a>
                    <a href="/holidays" class="nav-item nav-link list-group-item list-group-item-action bg-light">休日一覧</a>
                    <a href="/salaryHistory" class="nav-item nav-link list-group-item list-group-item-action bg-light">給与関連個人履歴</a>
                    <a href="/attendManage" class="nav-item nav-link list-group-item list-group-item-action bg-light">勤怠管理表</a>
                    <a href="/setting" class="nav-item nav-link list-group-item list-group-item-action bg-light">設定</a>
                    <router-link to="/logout" class="nav-item nav-link list-group-item list-group-item-action bg-light" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                    >ログアウト
                        <!-- <form id="logout-form"  method="POST" style="display: none;">
                            <input type="hidden" name="_token" :value="csrf">
                        </form> -->
                    </router-link>             
                    </div>
                </div>           
            </div>    
            <router-view></router-view>         
        </div>
    </div>
</template>

<script>
    export default { 
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
         },    
    }
</script>