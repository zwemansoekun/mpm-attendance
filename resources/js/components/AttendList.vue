<template>
    <div class="col-md-10"> 
            <div class="container">
                <div class="row">
                     <div class="col-md-4"> 
                         <!-- <a href="/export" class="btn btn-primary">Export to .xls</a>
                        <router-link to="/export">export example</router-link> -->
                    
                        <select class="form-control" id="selectEmployee" @change="empChange($event)"  name="employ_selected" required focus v-model="select_employee">
                            <option value="" disabled selected>Please select employee</option>        
                        
                            <option v-bind:key="emp.id" v-for="emp in emps"> {{emp.id }} {{emp.name }}</option>
                        
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
                    <form id="form" class="" @submit.prevent="attendSave"  autocomplete="on">
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
                     <!-- {{pp1}} -->

                    <div class="row mt-5">
                        <div class="col-md-4"> {{this.select_date}}</div>                          
                    </div>  
                    <div class="row">
                        <div class="col-md-4"> Employee No.{{emp_no}}</div>                          
                    </div>    
                    <div class="row">
                        <div class="col-md-4"> Name: {{emp_name}}</div>                          
                    </div>  
                    <table id="attendTable" class="table table-bordered">
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
                                    <tr v-bind:key="dayindex" v-for="(day,dayindex) in pp1" v-bind:class="day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])">                                
                                    <th style="width: 100px;" scope="row">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</th>
                                     <td colspan="4"    style="text-align: center;padding:0px">
                                         <!-- {{day}} -->
                                           <div v-if="day!==null">
                                                <tr :class="key==0?`mainIndex_${dayindex}`:`index_${dayindex}`" v-bind:key="key"  v-for="(date,key) in day" >                                                    
                                                     <template v-if="key==0">                                                         
                                                        <td style="width: 16.4%;" v-bind:class="`bg-${checkColor(date.am1,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am1==null)};`">
                                                        <div v-if="date.am1!== null"  :class="ampm_index(0)"> 
                                                            {{date.am1}}  
                                                        </div>
                                                    </td>
                                                    <td style="width: 16.4%;"   v-bind:class="`bg-${checkColor(date.am2,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am2==null)};`">
                                                        <div v-if="date.am2!== null"  :class="ampm_index(1)"> 
                                                            {{date.am2}}  
                                                        </div>
                                                    </td>
                                                    <td style="width: 16.4%;"  v-bind:class="`bg-${checkColor(date.pm1,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.pm1==null)};`">
                                                        <div v-if="date.pm1!== null"  :class="ampm_index(2)"> 
                                                            {{date.pm1}}  
                                                        </div>
                                                    </td>
                                                    <td style="width: 16.4%;"  v-bind:class="`bg-${checkColor(date.pm2,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.pm2==null)};`">
                                                        <div v-if="date.pm2!== null"  :class="ampm_index(3)"> 
                                                            {{date.pm2}}   
                                                        </div>
                                                    </td>
                                                    <td style="width: 16.4%;" >  
                                                    </td>  
                                                    <td style="width: 20.4%;" >
                                                    </td>
                                                    </template>    
                                                    <template v-else>
                                                         <!-- {{old("am1[]")}}   :value="`${date.am1}`!=undefined?date.am1:''"-->
                                                        <td style="width: 16.4%;padding:0px" v-bind:class="day[0].am1==null?([0,1].includes(0)==true?'paid-leave1':'paid-leave2'):''">
                                                        <!-- {{date.am1!=undefined?date.am1:23}} -->
                                                                 <div v-if="day[0].am1!== null">   
                                                                    <!-- <template v-if="form=='edit'">                                      -->
                                                                        <input :name="`am1[]`"  @change="updateInput"  :class="`form-control input-sm am1`"  style="text-align: center;" type="text" :value="`${date.am1}`!=undefined?date.am1:`${old(am1)}`">
                                                                    <!-- </template>    -->
                                                                    <!-- <template>
                                                                           <input :name="`am1[]`"  @change="updateInput"  :class="`form-control input-sm am1`"  style="text-align: center;" type="text" @value="`${date.am1}`!=undefined?date.am1:`${old(am1)}`">      
                                                                    </template>  -->
                                                                 </div>
                                                                 <div v-else>
                                                                      <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am1}`!=undefined?date.am1:null">   
                                                                 </div>
                                                        </td>   
                                                        <td style="width: 16.4%;padding:0px"   v-bind:class="day[0].am2==null?([0,1].includes(1)==true?'paid-leave1':'paid-leave2'):''">
                                                     
                                                                <div v-if="day[0].am2!== null">  
                                                                    <input :name="`am2[]`"  @change="updateInput" :class="`form-control input-sm am2`"  style="text-align: center;" type="text" :value="`${date.am2}`!=undefined?date.am2:`${old(am2)}`">  
                                                                </div>   
                                                                <div v-else>
                                                                   <input    class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am2}`!=undefined?date.am2:null">  
                                                                </div>
                                                        </td>
                                                        <td style="width: 16.4%;padding:0px"  v-bind:class="day[0].pm1==null?([0,1].includes(2)==true?'paid-leave1':'paid-leave2'):''">
                                                                <div v-if="day[0].pm1!== null">  
                                                                        <input :name="`pm1[]`"  @change="updateInput"  :class="`form-control input-sm pm1`"  style="text-align: center;" type="text" :value="`${date.pm1}`!=undefined?date.pm1:`${old(pm1)}`"> 
                                                                </div>
                                                                <div v-else>
                                                                       <input    class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.pm1}`!=undefined?date.pm1:null"> 
                                                                </div>
                                                        </td>
                                                        <td style="width: 16.4%;padding:0px"  v-bind:class="day[0].pm2==null?([0,1].includes(3)==true?'paid-leave1':'paid-leave2'):''">
                                                                <div v-if="day[0].pm2!== null">
                                                                      <input :name="`pm2[]`"  @change="updateInput"  :class="`form-control input-sm pm2`"  style="text-align: center;" type="text" :value="`${date.pm2}`!=undefined?date.pm2:`${old(pm2)}`"> 
                                                                </div>
                                                                <div v-else>
                                                                       <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly  :value="`${date.pm2}`!=undefined?date.pm2:null">   
                                                                </div>
                                                        </td>
                                
                                                            <td  style="width: 200px;padding:0px" >
                                                               <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" :value="`${date.total_hours}`!=undefined?date.total_hours:`${old(total_hours)}`">
                                                               <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${(dayindex+1).toString().length==1?'0'+(dayindex+1):(dayindex+1)}`">
                                                            </td>           
                                                            <td  style="width: 200px;padding:0px;" ><button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">自動計算</button></td>     
                                              
                                                    </template>
                                                   
                                                </tr>
                                           </div>  
                                            <div v-else>    
                                                <tr :class="`mainIndex_${dayindex}`">                                           
                                                    <td :style="`width: 371.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`"  > 
                                                    </td>   
                                            
                                                    <td :style="`width: 368.05px;height:50px;background-color:${checkBgColor(year,month,dayindex+1,1)};`" > 
                                                    </td>
                                                
                                                    <td style="width: 182px;height:50px;">  
                                                        
                                                    </td>  
                                                    <td style="width: 200px;height:50px;" >  
                                                        
                                                    </td>                                                                      
                                                </tr>  
                                                <tr :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?`index_${dayindex}`:'' "> 
                                                        <td style="width: 371.05px;padding:0px" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave1':''" > 
                                               
                                                        </td> 
                                                        <td style="width: 368.05px;padding:0px" :class="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false?'paid-leave2':''" > 
                                                        </td> 
                                                        <td style="width: 182px;padding:0px">  
                                                            <input v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text">
                                                            <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${(dayindex+1).toString().length==1?'0'+(dayindex+1):(dayindex+1)}`">
                                                        </td> 
                                                        <td style="width: 200px;padding:0px" >  
                                                            <button :id="`autobut${dayindex}`" v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" onclick="this.blur();" type="button" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">自動計算</button>  
                                                       </td>                                                                      
                                                </tr>                  
      
                                            </div> 
                                     </td> 
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
                errors:null,
                get_attend_data:[],
                check_attend_data:true,  
                pp1:[], 
                form:'',
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
                empv: function(val)
            {                  
              this.ems=val; 
              console.log('hi');
            }, 
            errorsFun:function(){              
                // console.log('sdfsdf',this.errors);
                // let result = {};
                // for ( let k in this.errors) {
                    
                //     // if (!['0.total_hours','0.pm2'].includes(k))
                //     //     continue;
                //     result[k] = this.errors[k];
                // }
                this.error_check_messg = true 
                return this.errors;
            },           
        },
        watch: {
            deep: true,            
            error_check_messg:function(){
                if(this.error_check_messg==true){
                    setTimeout(() => {
                        this.error_check_messg = false
                    },5000)
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
                               
                    // this.ampm_calling();             
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
                   
                    //  this.ampm_calling();
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
                            "total_hours[]":"required",
                            // "am_leave[]":"required",
                            // "pm_leave[]":"required",
                    },  
                     errorPlacement: function(error,element) {
                        return true;
                    },
                });
                let temp_arr=[];let am1,am2,pm1,pm2,thour,date;let am_leave=null,pm_leave=null;   let data_arr={};let eg_arr=[];
                if(jQuery('#form').valid()){
                        //  alert($('#form').serialize());
                    //   console.log($('#form').serializeArray());
                    // var tb = $('#attendTable:eq(0) tbody tr');
                    // var size = tb.find("tr").length;
                    // console.log(tb.length);
                     $('#attendTable tr[class^=index_]').each(function (key,value) {
                        //  if(key==0){
                        //      return;
                        //  }
                        //  console.log('key',key);
                        //  console.log('value',value);
                         let that=this;
                         $(that).find('td').each(function (index,v2) {
                             let isLastElement = index == $(that).find('td').length-2;
                          
                            //  console.log(isLastElement);
                            //  console.log(index);
                            //  console.log($(that).find('td').length-1);
                            //  console.log('k2',index);
                            //  console.log('v2',jQuery(this).find('.am1').val());
                            if(jQuery(this).find('.am1').val()){
                                //  console.log(index);
                                   am1=jQuery(this).find('.am1').val();
                                    // data_arr['am1']=am1;
                                       data_arr['am1']=am1;
                                //    console.log(am1);
                            }
                            if(jQuery(this).find('.am2').val()){
                                //  console.log(index);
                                  am2=jQuery(this).find('.am2').val();
                                    //  data_arr['am2']=am2;
                                       data_arr['am2']=am2;
                                    //    console.log(am2);
                            }
                            if(jQuery(this).find('.pm1').val()){
                                //  console.log(index);
                                  pm1=jQuery(this).find('.pm1').val();
                                //   data_arr['pm1']=pm1;
                                        data_arr['pm1']=pm1;
                                    //    console.log(pm1);
                            }
                            if(jQuery(this).find('.pm2').val()){
                                //  console.log(index);
                                   pm2=jQuery(this).find('.pm2').val();
                                    //   data_arr['pm2']=pm2;
                                          data_arr['pm2']=pm2;
                                        // console.log(pm2);
                            }
                            if(jQuery(this).find('.thour').val()){
                                //  console.log(index);
                                    thour=jQuery(this).find('.thour').val();
                                    //    data_arr['thour']=thour;
                                         data_arr['total_hours']=thour;
                                        //  console.log(thour);
                            }
                            if(jQuery(this).find('.date').val()){
                                
                                  date=jQuery(this).find('.date').val();
                                    //  data_arr['date']=date;
                                       data_arr['date']=date;
                                //    console.log(date);
                            }
                            if(jQuery(this).find('.amleave').val()){
                                
                                  am_leave=jQuery(this).find('.amleave').val();
                                    //  data_arr['date']=date;
                                    //    data_arr['date']=date;
                                //    console.log(date);
                            }
                              if(jQuery(this).find('.pmleave').val()){
                                
                                  pm_leave=jQuery(this).find('.pmleave').val();
                                    //  data_arr['date']=date;
                                    //    data_arr['date']=date;
                                //    console.log(date);
                            }
                           
                            if (isLastElement) {
                                        
                               if(am1=='' && am2=='' && am_leave==null){                               
                                   am_leave=0;
                               }
                               if(pm1=='' && pm2=='' && pm_leave==null){                                  
                                   pm_leave=0;
                               }
                                temp_arr.push({"date": date,"emp_no":those.emp_no,"total_hours":thour,"am1": am1 ,"am2":am2,"pm1":pm1,"pm2":pm2,"am_leave":am_leave,"pm_leave":pm_leave}); 
                                // console.log('temp',temp_arr);
                                date='';am1='';am2='';pm1='';pm2='';thour='',am_leave=null,pm_leave=null;
                                return false;
                            }
                         });
                        //  console.log('temp',temp_arr);
                        // $(value+'tr[class^=index_]').each(function (key1,value1) {
                        //         console.log('key1',key1);
                        //         console.log('value1',value1);
                        // });

                        // var name = $('.attrName', b).text();
                        // var value = $('.attrValue', b).text();
                        // ary.push({ Name: name, Value: value });               
                   });

                    // console.log(temp_arr);
                    this.axios({
                      url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList",
                      method: 'post',
                      data: temp_arr
                    })
                    .then(function (response) {
                        console.log('sdfsdaf',response);
                        // your action after success                      
                        if(response.data){
                            if(response.data.message==true){
                                those.$fire({
                                    title: "成功しました",
                                    text: "データの登録は成功しました。",
                                    type: "success",
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    // position:'center',
                                }).then(r => {
                                    //    console.log(r.value);
                                });
                            }else{
                                those.$fire({
                                title: "失敗！！",
                                text: "データの登録は出来ません。",
                                type: "error",
                                timer: 1500,
                                showCancelButton: false,
                                showConfirmButton: false,
                                // position:'center',
                                }).then(r => {
                                //    console.log(r.value);
                                }); 
                                those.errors=response.data.errors;                             
                            }                       
                        }
                    })
                    .catch(function (error) {
                    });
                }else{
                     those.$fire({
                        title: "失敗！！",
                        text: "データを記入してください。",
                        type: "warning",
                        timer: 3000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        // position:'center',
                        }).then(r => {
                        //    console.log(r.value);
                        }); 
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
                  jQuery(event.target).parent().parent().parent().find(".thour").val(Math.abs(res).toFixed(2));
            }, 
            allButtonClick:function(){ 
                $('[id^=autobut]').click();
            },  
            filterInput:function(){
                // let count=1;
                $('#attendTable tbody').find('td input').filter(function () {
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
                            // console.log(parent_class_name);
                            // console.log(class_name);
                    });
// "td.paid-leave1,td.paid-leave2",
                    $.contextMenu({
                        selector:".paid-leave1,.paid-leave2",
                        callback: function(key, options) {
                            // let class_name=jQuery(this).attr('class');
                            // console.log('p',jQuery(this).attr('class'));
                            if(key=='o'){
                                that.showTimer(parent_class_name,class_name,'circle');
                                if(class_name=='paid-leave1'){
                                       $('<input/>').attr({ type: 'hidden',name: 'am_leave[]',value:1,class:'amleave'}).appendTo(jQuery(this));
                                }else{
                                       $('<input/>').attr({ type: 'hidden',name: 'pm_leave[]',value:1,class:'pmleave'}).appendTo(jQuery(this));
                                }
                                   jQuery(this).parent().find('[id^=autobut]').click();   
                             
                            }else{
                                that.showTimer(parent_class_name,class_name,'dash');
                                if(class_name=='paid-leave1'){
                                      $('<input/>').attr({ type: 'hidden',name: 'am_leave[]',value:2,class:'amleave'}).appendTo(jQuery(this));
                                }else{
                                      $('<input/>').attr({ type: 'hidden',name: 'pm_leave[]',value:2,class:'pmleave'}).appendTo(jQuery(this));
                                }
                                jQuery(this).parent().find('[id^=autobut]').click();    
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
                // console.log('date',val);
                //  console.log('key',index);
                let val_split='';              
                    // console.log('te4',val);
                    // return '';
                if( (index==0 || index==2) && val!=null){
                    //  let val_split=val!==''?val.am_pm.split(":"):'';
                       val_split=val!==''?val.split(":"):'';
                    // if(val.am1!=null){
                    //     val_split=val!==''?val.am1.split(":"):'';
                    // }
                    // if(val.am2!=null){
                    //     val_split=val!==''?val.am2.split(":"):'';
                    // }
                    // if(val.pm1!=null){
                    //     val_split=val!==''?val.pm1.split(":"):'';
                    // }
                    // if(val.pm2!=null){
                    //     val_split=val!==''?val.pm2.split(":"):'';
                    // } 

                        // console.log('vvd',val_split);

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
                    let that=this;
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
                        that.get_attend_data=response.data;
                        console.log('res',that.get_attend_data);   
                        that.ampm_calling(that.get_attend_data);                  
                    })
                    .catch(function (error) {
                        console.log('geterror',error.response);
                    }); 

           },   
            ampm_calling(attend_data=''){        
                    console.log('ampmin',attend_data);           
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
                    // console.log('okkkkkkkkkk',that.get_attend_data);
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

                     
                       

                        for(let i=1;i<=this.dayCount;i++){
                            // console.log('count',i);
                                //    console.log('testiung123',attend_data[i-1]);
                            for(let j = 0; j < that.ampm_arr.length; j++) {
                                    let connect_to_dayampm = that.ampm_arr[j];                                         
                                for(let k = 0; k < connect_to_dayampm.length; k++) {
                                    //   console.log('suzin',new Date(that.year+"/"+that.month+"/"+i).getDate());   
                                    //   console.log('zweman',attend_data[i-1].date);    
                                    //   console.log('sdate',attend_data[i-1].date);   
                                   
                                    //   console.log('date',moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD'));
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
                                            
                                            // let tar=[];
                                            // console.log('ex1',ex1);
                                        
                                            //  if(attend_data[i-1].date==moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')){                                                   
                                            //       ex2.push({"0":ex1,"1":attend_data[i-1]});
                                            //  }else{

                                                    // console.log("i",i);
                                                    //   console.log("j",j);
                                                        // console.log("k",k);
                                                            // console.log('zzz');
                                                   ex2.push({"0":ex1,"1":[]});
                                            //  }  

                                            // ex2.push({"1":attend_data});
                                            // tar.push(ex1);
                                            //   console.log('ex2',ex2);
                                                //  console.log('tar',tar);
                                            ex1={};//tar=[];
                                            // that.ampm_by_day_arr.push(store_arr);
                                            that.status=1; 
                                            that.ampm_by_day=[]; 
                                        }
                                    }
                                    
                                    // console.log('sdate',attend_data[i-1].date);
                                    // console.log('date',moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD'));
                                    // if(attend_data[i-1].date==moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')){
                                    //         ex2.push({"1":attend_data[i-1].date});
                                    // }    

                                }
                            }
                                //  console.log('ddddd1',that.dayCount);
                                //      console.log('ddddd2',i);
                              
                                        if(that.status===1){                        
                                           that.status=0;                                
                                        }else{
                                            //  console.log('else',attend_data[i-1].date);
                                            //   console.log('elseif',moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD'));
                                            // if(attend_data[i-1].date==moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')){                                                   
                                            //       ex2.push({"1":attend_data[i-1]});
                                            //  }else{
                                                  ex2.push(null);
                                            //  }

                                        //    that.ampm_by_day_arr.push(null);
                                        }
                        }   
                              console.log('msuper',ex2);   
                              console.log('getdata',attend_data);              
                       
                           if(attend_data.length!=0){
                                               let pp2=[];let k=0;
                        for(let i=1;i<=this.dayCount;i++){                                
                            if(k<attend_data.length){
                          
                            for(let j=(k!=0?k:0+(i-1));j<attend_data.length;j++){                                   
                                if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')==attend_data[j].date){                                
                                
                                    // console.log('wah',ex2[j]);
                                            if(ex2[i-1]!=null){
                                                that.pp1.push({"0":ex2[i-1][0],"1":attend_data[j]});
                                            }else{
                                                that.pp1.push({"0":[],"1":attend_data[j]});
                                            }
                                            // pp1.push(attend_data[j]);
                                            ++k;                                          
                                            break;
                                }else if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')<attend_data[j].date){
                                       k=j;
                                       that.pp1.push(null);
                                            break;
                                }else{
                                         that.pp1.push(null);
                                    continue;
                                }
                            }
                              }//else  //dd
                                //   that.pp1.push(null);
                        }
                            that.form="edit";
                           }else{
                               that.pp1=ex2;
                            //    that.form="register";
                           }
                       

                        // pp2.push(pp1)
                          console.log('pp1',that.pp1);
                        // that.ampm_by_day_arr.push(pp1);
                        // console.log(that.ampm_by_day_arr);

                    });
                    
                    // that.ampm_by_day_arr=ex2;
                   
                        // console.log('m',ex2.length);
                        // console.log('n',attend_data.length);
//                         setTimeout(function(){
//     console.log('m2',ex2.length);
// }, 1000)
                      
                        
       // []
                //    console.log('checking',Object.getOwnPropertyDescriptors(that.ampm_by_day_arr));
                    // that.log(that.ampm_by_day_arr);


//                     var objects = [{ 'a': 1 }, { 'b': 2 }];
// var deep =$_.cloneDeep(objects);
// console.log('ce',deep[0] === objects[0]);

                //     let deep=$_.cloneDeep(that.ampm_by_day_arr);
                //   console.log('hah22',deep);
                //   console.log(deep[0] === that.ampm_by_day_arr[0]);
                        //   let pp1=[];let pp2=[];let k=0;
                        // for(let i=1;i<=this.dayCount;i++){                                
                        //     if(k<attend_data.length){
                          
                        //     for(let j=(k!=0?k:0+(i-1));j<attend_data.length;j++){                                   
                        //         if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')==attend_data[j].date){

                        //             //    for(let v in ex2){
                        //             //         console.log('vvm',attend_data[v]);
                        //             //         if(ex2[v].date==attend_data[j].date){

                        //             //         }
                        //             //     }
                        //             // console.log('hah',ex2[j]);
                        //             // console.log('wah',ex2[j]);
                        //                     // if(){
                        //                     //     pp1.push({"0":'',"1":attend_data[j]});
                        //                     // }else{
                        //                     //     pp1.push({"1":attend_data[j]});
                        //                     // }
                        //                     pp1.push(attend_data[j]);
                        //                     ++k;                                          
                        //                     break;
                        //         }else if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')<attend_data[j].date){
                        //                k=j;
                        //                pp1.push(null);
                        //                     break;
                        //         }else{
                        //                  pp1.push(null);
                        //             continue;
                        //         }
                        //     }
                        //       }else  //dd
                        //           pp1.push(null);
                        // }
                        // pp2.push(pp1)
                        //   console.log('pp1',pp1);

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
              let am_leave='';let pm_leave=''; 
              let t_am='';let p_am=''; 
              if(jQuery("."+sec_index).find('.amleave').val()){
                  am_leave=jQuery("."+sec_index).find('.amleave').val();      
              }
              if(jQuery("."+sec_index).find('.pmleave').val()){
                  pm_leave=jQuery("."+sec_index).find('.pmleave').val()
              }  
                // console.log('a_leave',am_leave);
                // console.log('p_leave',pm_leave);

              let am1=jQuery("."+index).find('.am1_0').text().trim();
              let am2=jQuery("."+index).find('.am2_1').text().trim();

              let pm1=jQuery("."+index).find('.pm1_2').text().trim();
              let pm2=jQuery("."+index).find('.pm2_3').text().trim();

              if(am1=='' && am2=='' && pm1=='' && pm2=='' && am_leave==1 && pm_leave==1 ){
                    jQuery("."+sec_index).find(".thour").val('8.00');
                    return false;
              }else if(am1=='' && am2=='' && pm1=='' && pm2=='' && am_leave==2 && pm_leave==2){
                    jQuery("."+sec_index).find(".thour").val('0.00');
                    return false;
              }else if( (am_leave==1 && pm_leave=='') || (am_leave==1 && pm_leave==2) ){
                    t_am=4;
              }else if( (am_leave=='' && pm_leave==1) || (am_leave==2 && pm_leave==1) ){
                    p_am=4;
              }    



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
                const {t_hr, t_min}=this.totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm,t_am,p_am);               
                let res=isNaN((parseFloat(t_hr)+parseFloat(t_min)).toFixed(2))?'':(parseFloat(t_hr)+parseFloat(t_min)).toFixed(2);   
                   
                jQuery("."+sec_index).find(".thour").val(res==0?"0.00":Math.abs(res).toFixed(2));
            } 
            },
                
            totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm,t_am,p_am){
                if(t_am!=''){
                    total_am=4;
                }else if(auto_am1!='' && auto_am2!='' && auto_am1!=undefined && auto_am2!=undefined){
                    total_am= (+auto_am2[0] + (+auto_am2[1] / 60))-(+auto_am1[0] + (+auto_am1[1] / 60));  
                    if(total_am<0){
                        total_am=0;
                    }                
                }
                if(p_am!=''){
                    total_pm=4;
                }else if(auto_pm1!='' && auto_pm2!=''  && auto_pm1!=undefined && auto_pm2!=undefined){
                    total_pm=(+auto_pm2[0] + (+auto_pm2[1] / 60))-(+auto_pm1[0] + (+auto_pm1[1] / 60));  
                     if(total_pm<0){
                        total_pm=0;
                    }                
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
                    jQuery("."+sec_index).find(name).val((ap_split[0].toString().length==1?"0"+ap_split[0]:ap_split[0])+":"+ap_split[1]);
                    return ap_split!=''?ap_split:'';
                    // total_hour+=ap_split[0];
                    // total_minute+=ap_split[1];
            },           
        }, 
    }
</script>