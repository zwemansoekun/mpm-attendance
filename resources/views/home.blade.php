@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="bg-light border-right" id="sidebar-wrapper">         
                <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action bg-light">出退勤一覧</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">給与手当一覧</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">休日一覧</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">給与関連個人履歴</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">勤怠管理表</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">設定</a>
                <a href="#" class="list-group-item list-group-item-action bg-light">ログアウト</a>
                </div>
            </div>
        </div>    
    
         <!-- Page Content -->
         <div class="col-md-9"> 
            <div class="container-fluid">
              <h1 class="mt-4">Simple Sidebar</h1>
              <p>The starting state of the menu will appear collapsed on smaller screens, and will appear non-collapsed on larger screens. When toggled using the button below, the menu will change.</p>
              <p>Make sure to keep all page content within the <code>#page-content-wrapper</code>. The top navbar is optional, and just for demonstration. Just create an element with the <code>#menu-toggle</code> ID which will toggle the menu when clicked.</p>
            </div>
          </div>
          <!-- /#page-content-wrapper -->
    </div>
@endsection
