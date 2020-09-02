<template>
    <div class="col-md-10"> 
            <div class="container">
                <div class="row">
                     <div class="col-md-4"> 
                         <!-- <a href="/export" class="btn btn-primary">Export to .xls</a>
                        <router-link to="/export">export example</router-link> -->

                         
                        <select class="form-control" id="selectEmployee" name="employ_selected" required focus v-model="select_employee">

                            <option value="" disabled selected>Please select employee</option>        
                        
                            <option v-bind:key="emp.id" v-for="emp in emps"  >{{ emp.id }} {{emp.name }}</option>
                        
                        </select>                      
                     </div>  
                    <div class="col-md-4 offset-md-2"> 
                        <select class="form-control" id="selectDate"  @change="dateChange($event)" name="date_selected" required focus v-model="select_date">
                            <option value="" disabled selected>Please select Year/Month</option>        
                        
                            <option v-bind:key="date.id" v-for="date in dates"  >{{ date.recordedDateTime }}</option>
                            
                        </select>                       
                    </div> 
                </div> 
            </div>  

             <div v-if="errors"  style="text-align: center">
                 <div v-for="(v,k) in errorsFun" :key="k"> 
                     <div v-if="error_check_messg">
                        <p class="alert alert-danger container mt-1 text-sm" role="alert">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <strong > {{ v }}</strong> 
                        </p>
                    </div>
                 </div>
            </div>

            <div class="alert alert-danger" role="alert" v-if="data_check_messg"  id="check-alert"   style="text-align: center">
                  <button type="button" class="close" data-dismiss="alert">x</button>
                 <strong >データはありませんでした。</strong> 
            </div>
            <div class="container mt-5" v-if="form_open">
                <!-- //form -->                      
                    <div class="row">
                        <div class="col-md-4">
                              <button type="button" class="btn btn-secondary" onclick="this.blur();">出勤簿生成</button>
                        </div>
                         <div class="col-md-4 offset-md-2">
                              <button type="button" class="btn btn-secondary" style="width: 220px;" onclick="this.blur();" @click="allButtonClick()">全て自動計算</button>
                         </div> 
                    </div>                 
                    <form id="form" class="" @submit.prevent="attendSave">
                    <div class="row mt-3">
                        <div class="col-md-4">
                              <input name="emp_no" ref="myButton" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${emp_no}`">
                              <button type="submit" class="btn btn-primary" onclick="this.blur();" >登録</button>
                        </div>
                        <div class="col-md-4 offset-md-2">
                            <button type="button" class="btn btn-secondary" onclick="this.blur();" @click="filterInput()" style="width: 220px;color: red;">空のところだけ自動計算</button>
                        </div>
                    </div>
                    <div>

                    </div>
                    <!-- v-on:submit.prevent="attendSave" -->
                     <!-- {{get_attend_data}} -->

                    <div class="row mt-5">
                        <div class="col-md-4"> {{this.select_date}}</div>                          
                    </div>  
                    <div class="row">
                        <div class="col-md-4"> Employee No.{{emp_no}}</div>                          
                    </div>    
                    <div class="row">
                        <div class="col-md-4"> Name: {{emp_name}}</div>                          
                    </div>  
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td style="text-align: center;width: 8.3%;"></td>                          
                                <td style="text-align: center;width: 30.14%;" >AM</td>   
                                <td style="text-align: center;width: 30%;">PM</td>
                                <td style="text-align: center;width: 15%;">Total Hours</td>
                                <td style="text-align: center;width: 30.1%;"></td>
                            </tr>
                        </thead>
                        <tbody> 
                                <tr v-bind:key="dayindex" v-for="(day,dayindex) in ampm_by_day_arr" v-bind:class="day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])">                                 
                                   
                                   <!-- {{day}} -->
                                    <th style="width: 100px;" scope="row">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</th>
                                    <td colspan="4"    style="text-align: center;padding:0px">
                                     
                                        <div v-if="day!==null">
                                            <tr :class="`mainIndex_${dayindex}`" >              
                                                <!-- {{day.am1}} -->
                                                <td style="width: 16.4%;" v-bind:key="key"  v-for="(date,index,key) in day" v-bind:class="`bg-${checkColor(date,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date==null)};`">   
                                                    <div v-if="date!== null" :class="ampm_index(key)">                                        
                                                    
                                                        {{date}}                                                
                                                        
                                                    </div>
                                                </td>

                                                <!-- <td style="width: 16.4%;" v-bind:key="index"  v-for="(date,index) in day" v-bind:class="`bg-${checkColor(date,index)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date==null)};`">   
                                                     <!-- //"`main_${dayindex}`"     -->
                                                     <!-- _${index} -->
                                                     <!-- {{date}}
                                                    <div v-if="date!== null" :class="ampm_index(index)">                                        
                                                    
                                                        {{date.am_pm}}                                                        
                                                        
                                                    </div>

                                                </td>     -->
                                                <td style="width: 16.4%;" >  
                                                </td>  
                                                <td style="width: 20.4%;" >  
                                                    
                                                </td>                                                                  
                                            </tr>                                        

                                            <tr :class="`index_${dayindex}`">   
                                                <!-- <div> -->
                                                <!-- <div v-if="check_attend_data"> -->
                                                    <td  style="width: 200px;padding:0px" v-bind:key="key"  v-for="(date,index,key) in day" :class="date==null?([0,1].includes(key)==true?'paid-leave1':'paid-leave2'):''">   
                                                        <div v-if="date!== null">                                        
                                                            <template v-if="key<2" >
                                                                <input :name="`am${key+1}[]`"  @change="updateInput" :class="`form-control input-sm am${key+1}`"  style="text-align: center;" type="text">                                                   
                                                            </template>
                                                            <template v-else>
                                                                <input :name="`pm${key-1}[]`"  @change="updateInput"  :class="`form-control input-sm pm${key-1}`"  style="text-align: center;" type="text">   
                                                            </template>
                                                        </div>
                                                        <div v-else>
                                                            <template v-if="index<2" >
                                                                <!-- :name="`am${index+1}[]`" -->
                                                                <input    class="form-control input-sm"  style="text-align: center;" type="text" readonly>                                                   
                                                            </template>
                                                            <template v-else>
                                                                <!-- :name="`pm${index-1}[]`"  -->
                                                                <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly>   
                                                            </template>
                                                        </div>
                                                    </td>

                                            <tr  :class="`index_${dayindex}`">
                                                    <!-- <td  style="width: 200px;padding:0px" ><input name="am1" class="form-control input-sm"  style="text-align: center;" type="text"></td>
                                                    <td  style="width: 200px;padding:0px" ><input name="am2" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                    <td  style="width: 200px;padding:0px" ><input name="pm1" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                    <td  style="width: 200px;padding:0px" ><input name="pm2" class="form-control input-sm"  style="text-align: center;" type="text"></td>   -->
                                                    
                                                    <td  style="width: 200px;padding:0px" >
                                                        <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text">
                                                        <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${(dayindex+1).toString().length==1?'0'+(dayindex+1):(dayindex+1)}`">
                                                    </td>           
                                                    <td  style="width: 200px;padding:0px;" ><button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">自動計算</button></td>     
                                                <!-- </div>  -->
                                                <!-- <div v-else> -->
                                                    <!-- <td  style="width: 200px;padding:0px" v-bind:key="index"  v-for="(date,index) in get_attend_data" :class="date==null?([0,1].includes(index)==true?'paid-leave1':'paid-leave2'):''">   
                                                       <pre> {{date}}</pre>
                                                       <!-- <pre>{{}}</pre> -->

                                                <!-- <td  style="width: 200px;padding:0px" ><input name="am1" class="form-control input-sm"  style="text-align: center;" type="text"></td>
                                                <td  style="width: 200px;padding:0px" ><input name="am2" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                <td  style="width: 200px;padding:0px" ><input name="pm1" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                <td  style="width: 200px;padding:0px" ><input name="pm2" class="form-control input-sm"  style="text-align: center;" type="text"></td>   -->
                                                
                                                <td  style="width: 200px;padding:0px" >
                                                    <input name="thour[]" class="form-control input-sm thour" style="text-align: center;" type="text">
                                                    <!-- <input name="date" class="form-control input-sm" style="text-align: center;" type="hidden" :value="`${year}-${month}-${dayindex+1}`"> -->
                                                </td>           
                                                <td  style="width: 200px;padding:0px;" ><button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">自動計算</button></td>                                                     

                                            </tr>

                                        </div>
                                        <div v-else>                                           
                                            <tr :class="`mainIndex_${dayindex}`"> 
                                                <!-- {{name_of_day}} -->
                                                   <!-- // style="width: 371.05px;height:50px;background-color:#FFDAB9"                  -->
                                                    <!-- style="width: 371.05px;height:50px;" -->
                                              <!-- :style="`{background-color:${checkBgColor(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],null)}`" -->
                                                <td :style="`width: 371.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`"  > 
                                                </td>   
                                                <!-- <td style="width: 200px" > 
                                                </td>  -->
                                                <td :style="`width: 368.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`" > 
                                                </td> 
                                                <!-- <td style="width: 200px" >  -->
                                                <!-- </td>    -->
                                                <td style="width: 182px;height:50px;">  
                                                </td>  
                                                <td style="width: 200px;height:50px;" >  
                                                    
                                                </td>                                                                      
                                            </tr>                                        
                                             <tr :class="`index_${dayindex}`"> 
                                                <!-- {{name_of_day}} -->
                                                   <!-- // style="width: 371.05px;height:50px;background-color:#FFDAB9"                  -->
                                                    <!-- style="width: 371.05px;height:50px;" -->
                                              <!-- :style="`{background-color:${checkBgColor(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],null)}`" -->
                                                <td style="width: 371.05px;height:50px;" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave1':''" > 
                                                    <!-- <span class="context-menu-one btn btn-neutral">right click me</span> -->
                                                </td>   
                                                <!-- <td style="width: 200px" > 
                                                </td>  -->
                                                <td style="width: 368.05px;height:50px;" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave2':''" > 
                                                </td> 
                                                <!-- <td style="width: 200px" >  -->
                                                <!-- </td>    -->
                                                <td style="width: 182px;height:50px;">  
                                                </td>  
                                                <td style="width: 200px;height:50px;" >  
                                                    <button v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" onclick="this.blur();" type="button" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">自動計算</button>  

                                                </td>                                                                      
                                            </tr>                  

                                        </div>   
                                    </td>
                                    <!-- <td></td>
                                    <td>
                                        <button type="button" class="btn btn-secondary">自動計算</button>
                                    </td> -->
                                </tr>
                         </tbody>
                    </table>
                     </form>
            </div>
    </div>    
</template>

<script>
    export default {
        data() {
            return {
                select_employee:'',
                select_date:'',          
                emps:[],
                dates:[],
                emp_no:'',
                emp_name:'',
                year:'',
                month:'',
                // day_count:'',
                days:['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                // memory_index:1,  
                memory:'',
                // countDay:[],
                ampm_inner_arr:[],
                ampm_arr:[],
                ampm_by_day:[],
                ampm_by_day_arr:[],
                dayCount:'',
                form_open:false,
                data_check_messg:false,
                error_check_messg:false,
                default_ampm:{"am":'',"pm":''},
                name_of_day:true,
                model_check:[
                        // {
                            [
                                 {"am1":22},
                                 {"am2":33},
                                 {"pm1":44},
                                 {"pm2":55},
                           ]    
                        // }
                  
                ],
                errors:null,
                get_attend_data:{},
                check_attend_data:true,               
            }
        },
        created() {
            this.axios
                .get('http://localhost:5000/employees')
                .then(response => {
                
                    this.emps=response.data;
                  
                });

            this.axios
            .get('http://localhost:5000/attendances/all/date')
            .then(response => {
             
                this.dates=response.data;
            
            });    

        },

        computed: {            

        },
        watch: {
            deep: true,            
            error_check_messg:function(){
                if(this.error_check_messg==true){
                    setTimeout(() => {
                        this.error_check_messg = false
                    },3000)
                    return false;
                }
            }  
        },
         mounted(){
                  
              this.day_name();
              this.ampm_index();       
              this.checkColor();
              this.checkBgColor();
              this.mouse_rclick();
            //   this.check_input();
        },      
        methods: {  
            
            empChange:function(event){
                let val='';
        
                if(event.target.value!=''){

                  let  val=event.target.value;
                    let split_name=val.split(" ");
                    this.emp_no=split_name[0];

                    val=val.replace(this.emp_no,'');
                    this.emp_name=val;

                }  
                
                if(val!='' && this.select_date!=''){   
                         this.update_call();                         
                    this.ampm_calling();             
                }
            },
            dateChange:function(event){
                let val='';
                if(event.target.value!=''){
                    val=event.target.value;
                    let split_date=val.split("/");                  
                    this.year=split_date[0];
                  
                    val=val.replace(this.year+"/",'');
                    this.month=val;
                   
                }  
                 if(val!='' && this.select_employee!=''){
                          this.update_call();
                     this.ampm_calling();
                }
            },
            attendSave:function(){
                 let those=this;
                // jQuery.noConflict(); 
                // jQuery(document).ready(function($){
                        // console.log('hello');
                var validator = jQuery('#form').validate({
                  
                    rules: {
                            "am1[]": "required",
                            "am2[]": "required",
                            "pm1[]": "required",
                            "pm2[]": "required",
                            // "thour[]":"required",
                            "am_leave[]":"required",
                            "pm_leave[]":"required",
                    },  
                     errorPlacement: function(error,element) {
                        return true;
                    },
                });
               
                if(jQuery('#form').valid()){
                         alert($('#form').serialize());
                      console.log($('#form').serialize());
                }else{
                    alert("Invalid");
                    return false;
                }
// });
                
                    

                //  console.log($('#form').serialize());
                // console.log($('#form').serializeArray());//serialize   //serializeArray
            },
            updateInput:function(event){ 

                   let am1,am2,pm1,pm2='';let total_am,total_pm=0;
                   am1=$(event.target).parent().parent().parent().find('.am1').val()!=undefined?$(event.target).parent().parent().parent().find('.am1').val().split(":"):'';
                   am2=$(event.target).parent().parent().parent().find('.am2').val()!=undefined?$(event.target).parent().parent().parent().find('.am2').val().split(":"):'';
                   pm1=$(event.target).parent().parent().parent().find('.pm1').val()!=undefined?$(event.target).parent().parent().parent().find('.pm1').val().split(":"):'';
                   pm2=$(event.target).parent().parent().parent().find('.pm2').val()!=undefined?$(event.target).parent().parent().parent().find('.pm2').val().split(":"):'';
                
                  const {t_hr, t_min}=this.totalHourCal(am1,am2,pm1,pm2,total_am,total_pm,'',''); 
                  let res=isNaN((parseFloat(t_hr)+parseFloat(t_min)).toFixed(2))?'':(parseFloat(t_hr)+parseFloat(t_min)).toFixed(2);
                  jQuery(event.target).parent().parent().parent().find(".thour").val(res);
            }, 
            allButtonClick:function(){ 
                $('[id^=autobut]').click();
            },  
            filterInput:function(){
                // let count=1;
                $('table tbody').find('td input').filter(function () {
                     return this.value === ""
                }).addClass('checkColumn').parent().parent().find('[type=button]').click().parent().parent().find('td input').removeClass('checkColumn');
                //  console.log(  );
                // if( $('table tbody').find('td input').length==0) {
                //     console.log(jQuery(this));
                // }
            },
            mouse_rclick:function(){ 
        
                let class_name='';let parent_class_name='';
                let that=this; 
                $(function() {

                    jQuery(document).on("contextmenu","td.paid-leave1,td.paid-leave2", function(e){
                            // console.log(jQuery(this).parent().parent().find(">:first-child").attr('class'));
                            // console.log(jQuery(this).parent().parent().find(">:first-child").attr('class'));
                            parent_class_name=jQuery(this).parent().parent().find(">:first-child").attr('class');
                            class_name=jQuery(this).attr('class');
                            console.log(parent_class_name);
                            console.log(class_name);
                    });
// "td.paid-leave1,td.paid-leave2",
                    $.contextMenu({
                        selector:".paid-leave1,.paid-leave2",
                        callback: function(key, options) {
                            console.log('p',jQuery(this).attr('class'));
                            if(key=='o'){
                                that.showTimer(parent_class_name,class_name,'circle');

                            }else{
                                that.showTimer(parent_class_name,class_name,'dash');
                            }

                            // var m = "clicked: " + key;
                            // window.console && console.log(m) || alert(m); 
                        },
                        items: {
                            "o": {name: "paid", icon: "fa-circle"},
                            "-": {name: "leave", icon: "fa-window-minimize"},
                        //    copy: {name: "Copy", icon: "copy"},
                        //     "paste": {name: "Paste", icon: "paste"},
                        //     "delete": {name: "Delete", icon: "delete"},
                        //     "sep1": "---------",
                        //     "quit": {name: "Quit", icon: function(){
                        //         return 'context-menu-icon context-menu-icon-quit';
                        //     }}
                        }
                    });

        // jQuery('.paid-leave').on('click', function(e){
        //     console.log('clicked11', this);
        // })    
                });

                //    $.noConflict();
                //    $(".paid-leave").contextMenu(menu,{
                //                 'triggerOn':'click',
                //                 'displayAround': 'cursor',
                //                 'mouseClick': 'right',
                          
                //     });

                // jQuery(document).ready(function(){
                //     jQuery(document).on("contextmenu", ".paid-leave", function(e){
                //             console.log(jQuery(this).parent().parent().find(">:first-child").attr('class'));
                //              parent_class_name=jQuery(this).parent().parent().find(">:first-child").attr('class');
                //              class_name=jQuery(this).parent().attr('class');
                //             jQuery(this).contextMenu(menu,{
                //                 'triggerOn':'click',
                //                 'displayAround': 'cursor',
                //                 'mouseClick': 'right',
                          
                //             });
                //     });
                // }) 
            },
            checkBgColor:function(year,month,dayindex,index){
                          
             
                let custom_date=year+"/"+("0" + month).slice(-2)+("0" +parseInt(dayindex)).slice(-2);                
                let val=this.days[new Date(year+'/'+month+'/'+parseInt(dayindex)).getDay()];
                let cur_date=new Date().getFullYear()+"/"+("0" + parseInt(new Date().getMonth()+1)).slice(-2)+"/"+("0" +new Date().getDate()).slice(-2); 
               
                if(index==1){                
                    return (val!=="Sat" && val!=="Sun" && cur_date>custom_date)? '#FFDAB9' : '';
                }else if(index==true && cur_date>custom_date){
                    return '#FFDAB9';
                }

            },
            checkColor:function(val,index){                
           
                if( (index==0 || index==2) && val!=null){
                    //  let val_split=val!==''?val.am_pm.split(":"):'';
                        let val_split=val!==''?val.split(":"):'';
                      if(val_split!='' && ( (val_split[0]==8 && val_split[1]>0 && val_split[1]<6) || (val_split[0]==13 && val_split[1]>0 && val_split[1]<16) ) ){
                          return 'warning';
                      }else if(val_split!='' && ((val_split[0]==8 &&  val_split[1]>5) || (val_split[0]==13 &&  val_split[1]>15) )){
                          return 'danger';
                      }else  
                           return '';
                }else{
                      return '';
                } 
            },            
           day_name(res){
                {     
                  
                     return (res==="Sat" || res==="Sun")? 'table-secondary' : '';
                }  
           },
           ampm_index(index){
                {             
                    switch(index) {
                        case 0:
                            return `am1_${index}`;
                        break;
                        case 1:
                             return `am2_${index}`;
                        break;
                        case 2:
                            return `pm1_${index}`;
                        break;
                        case 3:
                            return `pm2_${index}`;
                        break;
                        default:
                            return '';
                    }
                }  
           },   
           update_call(){
               
                    let up_data={
                        "emp_no":this.emp_no,
                        "date":this.select_date,
                        "year":this.year,
                        "month":this.month,
                    };
                    let ok1='';let ok12=[];let ok3={};
                    this.axios({
                      url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList/getmonth",
                      method: 'post',
                      data:up_data,
                    })                  
                    .then(response=>{ 

                        that.check_attend_data=false;                        
                        that.get_attend_data={
    "karrrrr": {
        "Nissan": [
            {"model":"Sentra", "doors":4},
            {"model":"Maxima", "doors":4},
            {"model":"Skyline", "doors":2}
        ],
        "Ford": [
            {"model":"Taurus", "doors":4},
            {"model":"Escort", "doors":4}
        ]
    }
};//response.data;
                        console.log('res',that.get_attend_data);                     
                    })
                    .catch(function (error) {
                        console.log('geterror',error.response);
                    }); 

           },   
            ampm_calling(){                
                    let that = this;
                    let those=this;
                    this.ampm_by_day_arr=[];
                    this.ampm_by_day=[];
                    this.ampm_inner_arr=[];
                    this.ampm_arr=[];
                    let ex2=[];let ex1={};
               
                if(this.year!='' && this.month!='' && this.emp_no!=''){                    
                   
                    this.dayCount=new Date(this.year,this.month, 0).getDate();
                    // this.update_call();
                    console.log('okkkkkkkkkk',that.get_attend_data);
                    // let up_data={
                    //     "emp_no":this.emp_no,
                    //     "date":this.select_date,
                    //     "year":this.year,
                    //     "month":this.month,
                    // };
                    // let ok1='';let ok12=[];let ok3={};
                    // this.axios({
                    //   url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList/getmonth",
                    //   method: 'post',
                    //   data:up_data,
                    // })                  
                    // .then(response=>{ 

                    //     that.check_attend_data=false;                        
                    //     that.get_attend_data=response.data;
                    //     console.log('res',that.get_attend_data);                     
                    // })
                    // .catch(function (error) {
                    //     console.log('geterror',error.response.data.errors);
                    // }); 


                   
                    // that.get_attend_data
                //        console.log('const1000',sailNames);
                // console.log('const1000',that.get_attend_data);
                   
                //     console.log(sailNames.length);
                //     console.log(Object.keys(sailNames));

                //     console.log(sailNames[Object.keys(sailNames)[0]]);
                 
//                         let count = 0;
//   for (var k in sailNames) {
//     if (sailNames.hasOwnProperty(k)) {
//       count++;
//     }
//   }
//   console.log(count);



                    //     $.each(sailNames, function(key, value) {
                    //     // sailNames.forEach(function(res, index) {
                    //         console.log('res',key);
                    //          console.log('index',value);
                    //     });    

                    //    console.log('const100022',sailNames);

                    // let ex2=[];let ex1={};
                    this.axios
                    .get("http://localhost:5000/attendances/ampm/"+this.emp_no+"/"+this.year+this.month)                 
                    .then(response => { 

                    
                        if(response.data.length===0){
                            this.form_open=false;
                                
                            this.data_check_messg = true
                            setTimeout(() => {
                                this.data_check_messg = false
                            },2000)
                            return false;
                        }else{
                            this.form_open=true;
                        }
                               
                      
                        // let that = this;                        
                        let eg1={};let eg2=[];
                        response.data.forEach(function(res, index) {
                                if (res.recordedDateTime === that.memory && that.memory !== "") {
                                    that.memory = res.recordedDateTime;
                                     eg1[res.recordedDateTime]= res    
                                    that.ampm_inner_arr.push(res);
                                } else if (res.recordedDateTime !== that.memory && that.memory !== "") {
                                    eg2.push(eg1);
                                    that.ampm_arr.push(that.ampm_inner_arr);
                                    eg1={};
                                    that.ampm_inner_arr = [];
                                    that.memory = res.recordedDateTime;
                                    eg1[ res.recordedDateTime]= res 
                                    // eg2.push(eg1);    
                                    that.ampm_inner_arr.push(res);
                                } else {
                                    that.memory = res.recordedDateTime;     
                                    eg1[ res.recordedDateTime]= res                          
                                    that.ampm_inner_arr.push(res);
                                }
                                if(response.data.length - 1 === index) {
                                    eg2.push(eg1);   
                                    that.ampm_arr.push(that.ampm_inner_arr);
                                }
                        });
                        that.memory='';       
                        // console.log('e1',that.ampm_arr);
                        // console.log('e2',eg2);
                        for(let i=1;i<=this.dayCount;i++){
                            for(let j = 0; j < that.ampm_arr.length; j++) {
                                    let connect_to_dayampm = that.ampm_arr[j];                                         
                                for(let k = 0; k < connect_to_dayampm.length; k++) {
                                           
                                    if(new Date(that.year+"/"+that.month+"/"+i).getDate()===new Date(connect_to_dayampm[k].recordedDateTime).getDate()){
                                        
                                        that.ampm_by_day.push(connect_to_dayampm[k]); 
                                        // console.log('t1',that.ampm_by_day);
                                        if (connect_to_dayampm.length - 1 === k) {        
                                      
                                            if(that.ampm_by_day.length<5 && that.ampm_by_day.length!==4){                                            
                                                let add_arr=4-that.ampm_by_day.length;
                                                for(let a=0;a<add_arr;a++){                                             
                                                    that.ampm_by_day.push(null); //{"am_pm":"00:00","hour":"00","minute":"00"}
                                                }
                                            } 
                                            //    console.log('t2',that.ampm_by_day);                                      
                                            let store_arr=[];//let ex1={};//let ex2=[];
                                            // var parsedobj = JSON.parse(JSON.stringify(this.get_attend_data))
                                            // console.log("hay checking!!",parsedobj);
                                            //  console.log("hay checking!!",this.get_attend_data);
                                            //   console.log("hay checking2",that.get_attend_data);
                                            //    console.log("hay checking33",that.$root);
                                            //    let parsedObj =JSON.stringify(that.get_attend_data)
                                            //     console.log(parsedObj)
                                            for(let i=0;i<that.ampm_by_day.length;i++){
                                        
                                                if(that.ampm_by_day[i]!=null){

                                                    // console.log("hay checking!!",that.get_attend_data[i]);

                                                    if(that.ampm_by_day[i].hour<12){        
                                                                                            
                                                    //    store_arr[0]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;   
                                                        ex1['am1']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }else if(that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0  && that.ampm_by_day[i].hour<13 && ex1['am2']==undefined && ex1['am1']!=undefined ){  //&& store_arr[0]!==undefined
                                                    
                                                        // store_arr[1]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;  
                                                         ex1['am2']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }else if( ( (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0 ) || (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0   ) )&& that.ampm_by_day[i].hour<17 && ex1['pm1']==null){ //&& store_arr[1]!==undefined   //&& store_arr[1]===undefined                                                    
                                                      
                                                    //    store_arr[2]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;   
                                                      ex1['pm1']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                     
                                                    }else if(that.ampm_by_day[i].hour>=17 && that.ampm_by_day[i].minute>=0){
                                                
                                                        // store_arr[3]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;  
                                                        ex1['pm2']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }
                                                    if(ex1['am1']==undefined){
                                                       ex1['am1']=null;
                                                    }
                                                    if(ex1['am2']==undefined){
                                                        ex1['am2']=null;
                                                    }
                                                    if(ex1['pm1']==undefined){
                                                        ex1['pm1']=null;
                                                    }
                                                    if(ex1['pm2']==undefined){
                                                        ex1['pm2']=null;
                                                    }
                                        
                                                }
                                            } 
                                            let tar=[];
                                            // console.log('ex1',ex1);
                                            ex2.push(ex1);
                                            tar.push(ex1);
                                            //   console.log('ex2',ex2);
                                                //  console.log('tar',tar);
                                            ex1={};tar=[];
                                            // that.ampm_by_day_arr.push(store_arr);
                                            that.status=1; 
                                            that.ampm_by_day=[]; 
                                        }
                                    }

                                }
                            }
                            if(that.status===1){                        
                               that.status=0;                                
                            }else{
                                  ex2.push(null);
                            //    that.ampm_by_day_arr.push(null);
                            }
                        }                              
                    });
                    that.ampm_by_day_arr=ex2;
                //    console.log('checking',that.ampm_by_day_arr);
                    this.axios
                    .get('http://127.0.0.1:8000/api/setting/delayTime/'+this.year+"/"+this.month)//+this.year+"/"+this.month
                    .then(response => { 
                          that.default_ampm.am=response.data.am;
                          that.default_ampm.pm=response.data.pm;
                    }); 

             

                















                } 

            },
            showTimer(index,sec_index,day_leave=''){  //mainIndex_0
            //   console.log("sec",sec_index);         
            
              let am1=jQuery("."+index).find('.am1_0').text().trim();
              let am2=jQuery("."+index).find('.am2_1').text().trim();

              let pm1=jQuery("."+index).find('.pm1_2').text().trim();
              let pm2=jQuery("."+index).find('.pm2_3').text().trim();


            if(day_leave=='circle' || day_leave=='dash'){           
                // .attr('class')
                    let leave_sign=day_leave=='circle'?"〇":"-";
                    if(am1=='' && am2=='' && sec_index=='paid-leave1'){    
                       
                    //   console.log(); 
                        if(jQuery("."+index).next().children('td').length==6){
                            jQuery("."+index).next().find("td:first").attr('colspan','2').find("td:first").remove();
                            jQuery("."+index).next().find("td:nth-child(2)").remove();
                            jQuery("."+index).next().find('td:first').css({'text-align':'center','width':'220px'});                       
                        }   
                         jQuery("."+index).next().find(".paid-leave1").text(leave_sign);
                         
                    }else if(pm1== '' && pm2=='' && sec_index=='paid-leave2'){

                        if(jQuery("."+index).next().children('td').length==6){
                            jQuery("."+index).next().find("td:nth-child(3)").attr('colspan','2').find("td:nth-child(3)").remove();
                            jQuery("."+index).next().find("td:nth-child(4)").remove();
                            jQuery("."+index).next().find('td:nth-child(3)').css({'text-align':'center','width':'220px'});                       
                        }  

                         jQuery("."+index).next().find(".paid-leave2").text(leave_sign);
                    }
            }
            // else if(day_leave=='dash'){
            //     //   console.log('2',ok1);
            //         if(am1=='' && am2=='' && sec_index=='paid-leave1' ){                   
            //         // jQuery("."+sec_index).find("[name=am1]").parent().attr('colspan','2').find("[name=am1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
            //         // jQuery("."+sec_index).find("[name=am2]").parent().remove();
            //              jQuery("."+index).next().find(".paid-leave1").text("-");
            //         }else if(pm1== '' && pm2=='' && sec_index=='paid-leave2'){
            //         // jQuery("."+sec_index).find("[name=pm1]").parent().attr('colspan','2').find("[name=pm1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
            //         // jQuery("."+sec_index).find("[name=pm2]").parent().remove();
            //             jQuery("."+index).next().find(".paid-leave2").text("-");
            //         }
            // }
            else{
              let ap_split1=am1!==''?am1.split(":"):'';
              let ap_split2=am2!==''?am2.split(":"):'';
            
              let ap_split3=pm1!==''?pm1.split(":"):'';
              let ap_split4=pm2!==''?pm2.split(":"):'';
              
            //   console.log('a',jQuery("."+sec_index).find("[name='am1']").hasClass('checkColumn'));
            //   if(jQuery("."+sec_index).find("[name='am1']").hasClass('checkColumn')){

            //   }
          
            //   if(am1=='' && am2==''){                   
            //     jQuery("."+sec_index).find("[name=am1]").parent().attr('colspan','2').find("[name=am1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
            //     jQuery("."+sec_index).find("[name=am2]").parent().remove();
            //     jQuery("."+sec_index).find('td:first').css({'text-align':'center','width':'220px'}).text("-");
            //   }else if(pm1== '' && pm2==''){
            //     jQuery("."+sec_index).find("[name=pm1]").parent().attr('colspan','2').find("[name=pm1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
            //     jQuery("."+sec_index).find("[name=pm2]").parent().remove();
            //     jQuery("."+sec_index).find('td:nth-child(3)').css({'text-align':'center','width':'220px'}).text("-");
            //   }
                // let total_hour=0;let total_minute=0;   

                // if( jQuery("."+sec_index).find("[name='am1']").hasClass('checkColumn') ||
                //         jQuery("."+sec_index).find("[name='am2']").hasClass('checkColumn') ||
                //         jQuery("."+sec_index).find("[name='pm1']").hasClass('checkColumn') ||
                //         jQuery("."+sec_index).find("[name='pm2']").hasClass('checkColumn')  ){
                //         jQuery("."+sec_index).find("[name="+name+"]").val(ap_split[0]+":"+ap_split[1]); 
                // }
                let auto_am1,auto_am2,auto_pm1,auto_pm2='';

                 if(!jQuery("."+sec_index).find(".am1").hasClass('checkColumn') && !jQuery("."+sec_index).find(".am2").hasClass('checkColumn')
                && !jQuery("."+sec_index).find(".pm1").hasClass('checkColumn') && !jQuery("."+sec_index).find(".pm2").hasClass('checkColumn')){   
                         auto_am1=this.ampm_time_check(ap_split1,".am1",sec_index);     
                         auto_am2=this.ampm_time_check(ap_split2,".am2",sec_index);   
                         auto_pm1=this.ampm_time_check(ap_split3,".pm1",sec_index);   
                         auto_pm2=this.ampm_time_check(ap_split4,".pm2",sec_index); 
                }

                if(jQuery("."+sec_index).find(".am1").hasClass('checkColumn')){
                   auto_am1=this.ampm_time_check(ap_split1,".am1",sec_index);   
                    //  console.log('a',auto_am1);  
                }
                 if(jQuery("."+sec_index).find(".am2").hasClass('checkColumn')){
                    auto_am2=this.ampm_time_check(ap_split2,".am2",sec_index);   
                    //   console.log('b',auto_am2);
                }
                if(jQuery("."+sec_index).find(".pm1").hasClass('checkColumn')){
                     auto_pm1=this.ampm_time_check(ap_split3,".pm1",sec_index);   
                    //    console.log('c',auto_pm1);
                }
                if(jQuery("."+sec_index).find(".pm2").hasClass('checkColumn') ){
                     auto_pm2=this.ampm_time_check(ap_split4,".pm2",sec_index); 
                    //    console.log('d',auto_pm2);
                }
                // karanotokoro
                if( auto_am1==undefined || auto_am2==undefined || auto_pm1==undefined || auto_pm2==undefined ){
                          
                             if(auto_am1==undefined ){
                                name=".am1";
                           
                             }else if(auto_am2==undefined ){
                                name=".am2";
                           
                             }else if(auto_pm1==undefined ){
                                name=".pm1";
                            
                             }else if(auto_pm2==undefined ){
                                name=".pm2";
                             
                             }
                            //  console.log('av',jQuery("."+sec_index).find("[name="+name+"]").val()); 
                            let split_ap=jQuery("."+sec_index).find(name).val()!=undefined?jQuery("."+sec_index).find(name).val().split(":"):'';
                            if(split_ap!=''){
                                let h1,m1='';
                                // let [h1,m1] =split_ap.split(":");
                                // let split_data=split_ap.split(":");
                                h1=split_ap[0]<10?"0"+parseInt(split_ap[0]):split_ap[0];
                                m1=split_ap[1]<10?"0"+parseInt(split_ap[1]):split_ap[1];
                          
                                // console.log('m',m1);
                                if( ((h1+":"+m1).search(/^\d{2}:\d{2}$/) != -1) &&
                                    ((h1+":"+m1).substr(0,2) >= 0 && (h1+":"+m1).substr(0,2) <= 24) &&
                                    ((h1+":"+m1).substr(3,2) >= 0 && (h1+":"+m1).substr(3,2) <= 59) ){
                                        // auto_am1=[];auto_am2=[];auto_pm1=[];auto_pm2=[];
                                     if(name==".am1"){
                                          auto_am1=[];
                                         console.log('h',h1);
                                           auto_am1[0]=h1;      //this.ampm_time_check(split_ap,"am1",sec_index);   
                                           auto_am1[1]=m1;
                                     }else if(name==".am2"){
                                           auto_am2=[];
                                           console.log('h1',h1);
                                           auto_am2[0]=h1;           //this.ampm_time_check(split_ap,"am2",sec_index);   
                                           auto_am2[1]=m1;  
                                     }else if(name==".pm1"){
                                            auto_pm1=[];
                                           console.log('h2',h1);
                                            auto_pm1[0]=h1;            //this.ampm_time_check(split_ap,"pm1",sec_index);  
                                            auto_pm1[1]=m1;  
                                     }else if(name==".pm2"){
                                           auto_pm2=[];
                                           console.log('h3',h1);
                                            auto_pm2[0]=h1;            //this.ampm_time_check(split_ap,"pm2",sec_index);  
                                            auto_pm2[1]=m1;  
                                     }       
                                }
                            }                 
                }
                // console.log('b',auto_am1);
                //    console.log('a',auto_am1);  
                //       console.log('ab',auto_am2);  
                //          console.log('ac',auto_pm1);  
                //             console.log('ad',auto_pm2);  
               
                let total_am=0;let total_pm=0; 
                const {t_hr, t_min}=this.totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm);               
                let res=isNaN((parseFloat(t_hr)+parseFloat(t_min)).toFixed(2))?'':(parseFloat(t_hr)+parseFloat(t_min)).toFixed(2);                
                jQuery("."+sec_index).find(".thour").val(res==0?"0.00":res);
            } 
            },
                
            totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm){
                if(auto_am1!='' && auto_am2!='' && auto_am1!=undefined && auto_am2!=undefined){
                    total_am= (+auto_am2[0] + (+auto_am2[1] / 60))-(+auto_am1[0] + (+auto_am1[1] / 60));                  
                }
                if(auto_pm1!='' && auto_pm2!=''  && auto_pm1!=undefined && auto_pm2!=undefined){
                    total_pm=(+auto_pm2[0] + (+auto_pm2[1] / 60))-(+auto_pm1[0] + (+auto_pm1[1] / 60));                 
                }
                //  console.log('y');
                // console.log(total_am);
                // console.log(total_pm); 
                // console.log('x');
                return {
                    t_hr: total_am==''?0:total_am,
                    t_min: total_pm==''?0:total_pm,
                };            
            }, 
            ampm_time_check(ap_split,name,sec_index){
                
                if(ap_split===''){ 
                      return false;   
                }

                if(name==".am2"){
                    ap_split[0]=12;ap_split[1]="00";     
                }else if(name==".pm2"){
                    ap_split[0]=17;ap_split[1]="00";  
                }else{
                    if( (ap_split[0]<8 && ap_split[1]<60) || (ap_split[0]==8 && ap_split[1]<6) ){
                        ap_split[0]=8;ap_split[1]="00";     
                    }else if(ap_split[0]==13 && ap_split[1]<16){
                        ap_split[0]=13;ap_split[1]="00"; 
                    }else if( ( ap_split[0]==8  && ( ap_split[1]>=6 && ap_split[1]<=30) ) || (ap_split[0]>8 && (ap_split[1]>=0 && ap_split[1]<=30) )    ) {
                        ap_split[0]=ap_split[0];ap_split[1]="30"; 
                    }else if(ap_split[0]>=8 && (ap_split[1]>=30 && ap_split[1]<=60 ) ){
                        ap_split[0]=parseInt(ap_split[0])+1;ap_split[1]="00"; 
                    }else if(ap_split[0]==13 && (ap_split[1]>=16 && ap_split[1]<=30 ) ){
                        ap_split[0]=13;ap_split[1]="30"; 
                    }
                }       
                

                    // if( jQuery("."+sec_index).find("[name='am1']").hasClass('checkColumn') ||
                    //     jQuery("."+sec_index).find("[name='am2']").hasClass('checkColumn') ||
                    //     jQuery("."+sec_index).find("[name='pm1']").hasClass('checkColumn') ||
                    //     jQuery("."+sec_index).find("[name='pm2']").hasClass('checkColumn')  ){
                    //     jQuery("."+sec_index).find("[name="+name+"]").val(ap_split[0]+":"+ap_split[1]); 
                    // }
               
                    
                    // else{
                    //     jQuery("."+sec_index).find("[name="+name+"]").val(ap_split[0]+":"+ap_split[1]);
                    // }

                    // let total_am=0;let total_pm=0; 
                    // if(ap_split1!='' && ap_split2!=''){
                    //     total_am=(+ap_split1[0] + (+ap_split1[1] / 60)) - (+ap_split2[0] + (+ap_split2[1] / 60));
                    // }
                    // if(ap_split3!='' && ap_split4!=''){
                    //     total_pm=(+ap_split3[0] + (+ap_split3[1] / 60)) - (+ap_split4[0] + (+ap_split4[1] / 60))
                    // }
        //  console.log('z',name);
                    // jQuery("."+sec_index).find("[name='thour']").val(parseInt(total_am)+parseInt(total_pm));
                    jQuery("."+sec_index).find(name).val(ap_split[0]+":"+ap_split[1]);
                    return ap_split!=''?ap_split:'';
                    // total_hour+=ap_split[0];
                    // total_minute+=ap_split[1];
            },           
        }, 
    }
</script>