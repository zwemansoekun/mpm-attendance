<template>
    <div class="col-md-10"> 
            <div class="container">
                <div class="row">
                     <div class="col-md-4"> 
                        <select class="form-control" id="selectEmployee" name="employ_selected" required focus v-model="select_employee">
                            <option value="" disabled selected>Please select employee</option>        
                        
                            <option v-bind:key="emp.id" v-for="emp in emps"  >{{ emp.id }} {{emp.name }}</option>
                        
                        </select>                      
                     </div>  
                    <div class="col-md-4 offset-md-2"> 
                        <select class="form-control" id="selectDate" name="date_selected" required focus v-model="select_date">
                            <option value="" disabled selected>Please select Year/Month</option>        
                        
                            <option v-bind:key="date.id" v-for="date in dates"  >{{ date.recordedDateTime }}</option>
                            
                        </select>                       
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
                              <button type="button" class="btn btn-primary">出勤簿生成</button>
                        </div>
                         <div class="col-md-4 offset-md-2">
                              <button type="button" class="btn btn-primary" style="width: 220px;">全て自動計算</button>
                         </div> 
                    </div>                 
                 
                    <div class="row mt-3">
                        <div class="col-md-4"></div>
                        <div class="col-md-4 offset-md-2">
                            <button type="button" class="btn btn-primary" style="width: 220px;">空のところだけ自動計算</button>
                        </div>
                    </div>
                    <div>

                    </div>
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
                                    <th style="width: 100px;" scope="row">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</th>
                                    <td colspan="4"    style="text-align: center;padding:0px">
                                     
                                        <div v-if="day!==null">
                                            <tr :class="`mainIndex_${dayindex}`" >              
                                          
                                                <td style="width: 16.4%;" v-bind:key="index"  v-for="(date,index) in day" v-bind:class="`bg-${checkColor(date,index)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date==null)};`">   
                                                     <!-- //"`main_${dayindex}`"     -->
                                                     <!-- _${index} -->
                                                    <div v-if="date!== null" :class="ampm_index(index)">                                        
                                                    
                                                        {{date.am_pm}}                                                        
                                                        
                                                    </div>

                                                </td>     
                                                <td style="width: 16.4%;" >  
                                                </td>  
                                                <td style="width: 20.4%;" >  
                                                    
                                                </td>                                                                      
                                            </tr>
                                        
                                            <tr  :class="`index_${dayindex}`">                                           
                                                <td  style="width: 200px;padding:0px" ><input name="am1" class="form-control input-sm"  style="text-align: center;" type="text"></td>
                                                <td  style="width: 200px;padding:0px" ><input name="am2" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                <td  style="width: 200px;padding:0px" ><input name="pm1" class="form-control input-sm"  style="text-align: center;" type="text"></td>      
                                                <td  style="width: 200px;padding:0px" ><input name="pm2" class="form-control input-sm"  style="text-align: center;" type="text"></td>  
                                                <td  style="width: 200px;padding:0px" ><input name="thour" class="form-control input-sm" style="text-align: center;" type="text"></td>           
                                                <td  style="width: 200px;padding:0px;" ><button type="button" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,$event)">自動計算</button></td>                                                     
                                            </tr>

                                        </div>
                                        <div v-else>                                           
                                            <tr > 
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
                                             <tr > 
                                                <!-- {{name_of_day}} -->
                                                   <!-- // style="width: 371.05px;height:50px;background-color:#FFDAB9"                  -->
                                                    <!-- style="width: 371.05px;height:50px;" -->
                                              <!-- :style="`{background-color:${checkBgColor(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],null)}`" -->
                                                <td style="width: 371.05px;height:50px;" class="abcde" oncontextmenu="return false;"> 
                                                </td>   
                                                <!-- <td style="width: 200px" > 
                                                </td>  -->
                                                <td style="width: 368.05px;height:50px;" > 
                                                </td> 
                                                <!-- <td style="width: 200px" >  -->
                                                <!-- </td>    -->
                                                <td style="width: 182px;height:50px;">  
                                                </td>  
                                                <td style="width: 200px;height:50px;" >  
                                                    <button v-if="['Sat','Sun'].includes(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])==false" type="button" class="btn btn-secondary" @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,$event)">自動計算</button>  
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
                default_ampm:{"am":'',"pm":''},
                name_of_day:true
                // checkColor:''           
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
            select_employee:function (val) {

                if(val!=''){

                let split_name=val.split(" ");
                this.emp_no=split_name[0];

                val=val.replace(this.emp_no,'');
                this.emp_name=val;

                }  

                if(val!='' && this.select_date!=''){                            
                     //    this.attendForm();
                    this.ampm_calling();
                 
                }
              
            },
            select_date:function (val) {            
                if(val!=''){
                    let split_date=val.split("/");                  
                     this.year=split_date[0];
                  
                     val=val.replace(this.year+"/",'');
                    this.month=val;
                   
                }  
                 if(val!='' && this.select_employee!=''){              
                    //  this.attendForm();
                     this.ampm_calling();
                }
            },      
            // checkColor:function(val){
            //        return 'warning';
            // }
        },
         mounted(){
              this.day_name();
              this.ampm_index();       
              this.checkColor();
              this.checkBgColor();
              this.mouse_rclick();
        },      
        methods: {   
            mouse_rclick:function(){ 
            var menu = [{
                    name: 'create',
                    // img: 'images/create.png',
                    title: 'create button',
                    fun: function () {
                        alert('i am add button')
                    }
                    }, {
                    name: 'update',
                    // img: 'images/update.png',
                    title: 'update button',
                    fun: function () {
                        alert('i am update button')
                    }
                }, {
                    name: 'delete',
                    // img: 'images/delete.png',
                    title: 'delete button',
                    fun: function () {
                        alert('i am delete button')
                    }
                }];

                jQuery(document).ready(function(){
                    jQuery(document).on("contextmenu", ".abcde", function(e){
                            jQuery(this).contextMenu(menu,{
                                'triggerOn':'click',
                                'displayAround': 'cursor',
                                'mouseClick': 'right'
                            });
                    });
                }) 
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
                     let val_split=val!==''?val.am_pm.split(":"):'';
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
            ampm_calling(){                
                    let that = this;
                    this.ampm_by_day_arr=[];
                    this.ampm_by_day=[];
                    this.ampm_inner_arr=[];
                    this.ampm_arr=[];
                     
                if(this.year!='' && this.month!='' && this.emp_no!=''){                    
                   
                    this.dayCount=new Date(this.year,this.month, 0).getDate();

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

                        response.data.forEach(function(res, index) {
                                if (res.recordedDateTime === that.memory && that.memory !== "") {
                                    that.memory = res.recordedDateTime;
                                    that.ampm_inner_arr.push(res);
                                } else if (res.recordedDateTime !== that.memory && that.memory !== "") {
                                    that.ampm_arr.push(that.ampm_inner_arr);
                                    that.ampm_inner_arr = [];
                                    that.memory = res.recordedDateTime;
                                    that.ampm_inner_arr.push(res);
                                } else {
                                    that.memory = res.recordedDateTime;                                
                                    that.ampm_inner_arr.push(res);
                                }
                                if(response.data.length - 1 === index) {
                                    that.ampm_arr.push(that.ampm_inner_arr);
                                }
                        });
                        that.memory='';       
                  
                        for(let i=1;i<=this.dayCount;i++){
                            for(let j = 0; j < that.ampm_arr.length; j++) {
                                    let connect_to_dayampm = that.ampm_arr[j];                                         
                                for(let k = 0; k < connect_to_dayampm.length; k++) {
                                           
                                    if(new Date(that.year+"/"+that.month+"/"+i).getDate()===new Date(connect_to_dayampm[k].recordedDateTime).getDate()){
                                        
                                        that.ampm_by_day.push(connect_to_dayampm[k]); 
                                     
                                        if (connect_to_dayampm.length - 1 === k) {        
                                      
                                            if(that.ampm_by_day.length<5 && that.ampm_by_day.length!==4){                                            
                                                let add_arr=4-that.ampm_by_day.length;
                                                for(let a=0;a<add_arr;a++){                                             
                                                    that.ampm_by_day.push(null); //{"am_pm":"00:00","hour":"00","minute":"00"}
                                                }
                                            }                                       
                                            let store_arr=[];
                                       
                                            for(let i=0;i<that.ampm_by_day.length;i++){
                                        
                                                if(that.ampm_by_day[i]!=null){
                                                    if(that.ampm_by_day[i].hour<12){        
                                                                                            
                                                       store_arr[0]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                  
                                                    }else if(that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0  && that.ampm_by_day[i].hour<13 && store_arr[1]==undefined && store_arr[0]!=undefined ){  //&& store_arr[0]!==undefined
                                                    
                                                        store_arr[1]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                       
                                                    }else if( ( (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0 ) || (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0   ) )&& that.ampm_by_day[i].hour<17 && store_arr[2]==null){ //&& store_arr[1]!==undefined   //&& store_arr[1]===undefined                                                    
                                                      
                                                       store_arr[2]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                     
                                                    }else if(that.ampm_by_day[i].hour>=17 && that.ampm_by_day[i].minute>=0){
                                                
                                                        store_arr[3]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                      
                                                    }
                                                    if(store_arr[0]==undefined){
                                                        store_arr[0]=null;
                                                    }
                                                    if(store_arr[1]==undefined){
                                                        store_arr[1]=null;
                                                    }
                                                    if(store_arr[2]==undefined){
                                                        store_arr[2]=null;
                                                    }
                                                    if(store_arr[3]==undefined){
                                                        store_arr[3]=null;
                                                    }
                                        
                                                }
                                            } 

                                            that.ampm_by_day_arr.push(store_arr);
                                            that.status=1; 
                                            that.ampm_by_day=[]; 
                                        }
                                    }

                                }
                            }
                            if(that.status===1){                        
                               that.status=0;                                
                            }else{
                               that.ampm_by_day_arr.push(null);
                            }
                        }                              
                    });
                        //  let that = this;
                    this.axios
                    .get('http://127.0.0.1:8000/api/setting/delayTime/'+this.year+"/"+this.month)//+this.year+"/"+this.month
                    .then(response => { 
                          that.default_ampm.am=response.data.am;
                          that.default_ampm.pm=response.data.pm;
                    }); 

                } 

            },
            showTimer(index,sec_index,e){  //mainIndex_0
            //   console.log("sec",sec_index);
              let am1=jQuery("."+index).find('.am1_0').text().trim();
              let am2=jQuery("."+index).find('.am2_1').text().trim();

              let pm1=jQuery("."+index).find('.pm1_2').text().trim();
              let pm2=jQuery("."+index).find('.pm2_3').text().trim();
           
              let ap_split1=am1!==''?am1.split(":"):'';
              let ap_split2=am2!==''?am2.split(":"):'';
              let ap_split3=pm1!==''?pm1.split(":"):'';
              let ap_split4=pm2!==''?pm2.split(":"):'';
         
              if(am1=='' && am2==''){                   
                jQuery("."+sec_index).find("[name=am1]").parent().attr('colspan','2').find("[name=am1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
                jQuery("."+sec_index).find("[name=am2]").parent().remove();
                jQuery("."+sec_index).find('td:first').css({'text-align':'center','width':'220px'}).text("-");
              }else if(pm1== '' && pm2==''){
                jQuery("."+sec_index).find("[name=pm1]").parent().attr('colspan','2').find("[name=pm1]").remove();//.text("-").attr('rowspan','2')  .find('[name=am2]').parents().remove();
                jQuery("."+sec_index).find("[name=pm2]").parent().remove();
                jQuery("."+sec_index).find('td:nth-child(3)').css({'text-align':'center','width':'220px'}).text("-");
              }

                this.ampm_time_check(ap_split1,"am1",sec_index);     
                this.ampm_time_check(ap_split2,"am2",sec_index);   
                this.ampm_time_check(ap_split3,"pm1",sec_index);   
                this.ampm_time_check(ap_split4,"pm2",sec_index); 

            },
            ampm_time_check(ap_split,name,sec_index){
                
                if(ap_split===''){ 
                      return false;   
                }

                if(name==="am2"){
                    ap_split[0]=12;ap_split[1]="00";     
                }else if(name==="pm2"){
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
                    jQuery("."+sec_index).find("[name="+name+"]").val(ap_split[0]+":"+ap_split[1]);
            },           
        }, 
    }
</script>