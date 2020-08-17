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
                                <th scope="col"></th>                          
                                <td style="text-align: center;width: 400px;" >AM</td>   
                                <td style="text-align: center;width: 400px;">PM</td>
                                <th scope="col">Total Hours</th>
                                <th scope="col" class="w-25 p-3"></th>
                            </tr>
                        </thead>
                        <tbody> 
                                <tr v-bind:key="dayindex" v-for="(day,dayindex) in ampm_by_day_arr">                                 
                                    <th style="width: 100px;" scope="row">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</th>
                                    <td colspan="2"    style="text-align: center;padding:0px">
                                        
                                        <tr>                              
                                            <td style="width: 200px;"  v-bind:key="index"  v-for="(date,index) in day" >   
                                                
                                                <div v-if="date!== null">                                             
                                                
                                                    {{date.am_pm}}                                                        
                                                    
                                                </div>

                                            </td>     
                                                                                                            
                                        </tr>
                                    
                                        <tr>                                           
                                            <td  style="width: 200px;" ></td>
                                            <td  style="width: 200px;" ></td>      
                                            <td  style="width: 200px;" ></td>      
                                            <td  style="width: 200px;" ></td>                                            
                                        </tr>
                                    </td>
                                    <td></td>
                                    <td></td>
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
                data_check_messg:false
           
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
                     
        },
        methods: {           
           
            ampm_calling(){                

                    this.ampm_by_day_arr=[];
                    this.ampm_by_day=[];
                    this.ampm_inner_arr=[];
                    this.ampm_arr=[];
                     
                if(this.year!='' && this.month!='' && this.emp_no!=''){                    
                    // this.axios
                    // .get("http://localhost:5000/attendances/countDay/"+this.emp_no+"/"+this.year+this.month)                 
                    // .then(response => {
                    //     console.log(response);
                    //     this.countDay=response.data;
                    //     console.log(this.dates);
                    // });
                    this.dayCount=new Date(this.year,this.month, 0).getDate();

                    this.axios
                    .get("http://localhost:5000/attendances/ampm/"+this.emp_no+"/"+this.year+this.month)                 
                    .then(response => { 

                        console.log(response.data.length);    
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
                               
                      
                        let that = this;                        

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
                        // console.log("a",that.ampm_arr);
                        for(let i=1;i<=this.dayCount;i++){
                            for(let j = 0; j < that.ampm_arr.length; j++) {
                                    let connect_to_dayampm = that.ampm_arr[j];                                         
                                for(let k = 0; k < connect_to_dayampm.length; k++) {
                                           
                                    if(new Date(that.year+"/"+that.month+"/"+i).getDate()===new Date(connect_to_dayampm[k].recordedDateTime).getDate()){
                                        
                                        that.ampm_by_day.push(connect_to_dayampm[k]); 
                                        // that.memory=new Date(connect_to_dayampm[k].recordedDateTime).getDate();
                                   
                                        if (connect_to_dayampm.length - 1 === k) {        
                                      
                                            if(that.ampm_by_day.length<5 && that.ampm_by_day.length!==4){                                            
                                                let add_arr=4-that.ampm_by_day.length;
                                                for(let a=0;a<add_arr;a++){                                             
                                                    that.ampm_by_day.push(null); //{"am_pm":"00:00","hour":"00","minute":"00"}
                                                }
                                            }
                                            // console.log("app",that.ampm_by_day);
                                            let store_arr=[];
                                       
                                            for(let i=0;i<that.ampm_by_day.length;i++){

                                            //   console.log(that.ampm_by_day[i].hour);

                                                if(that.ampm_by_day[i]!==null){
                                                    if(that.ampm_by_day[i].hour<12){        
                                                        // console.log(0);                                            
                                                       store_arr[0]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                  
                                                    }else if(that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0  && that.ampm_by_day[i].hour<13 && store_arr[1]===null ){  //&& store_arr[0]!==undefined
                                                        //   console.log(1);   
                                                        store_arr[1]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                       
                                                    }else if( ( (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0 ) || (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0   ) )&& that.ampm_by_day[i].hour<17 && store_arr[2]===null){ //&& store_arr[1]!==undefined   //&& store_arr[1]===undefined                                                    
                                                        //   console.log(that.ampm_by_day[i]);
                                                        //   console.log(2);   
                                                       store_arr[2]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                     
                                                    }else if(that.ampm_by_day[i].hour>=17 && that.ampm_by_day[i].minute>=0){
                                                        //   console.log(3);   
                                                        store_arr[3]=that.ampm_by_day[i]?that.ampm_by_day[i]:null;                                                      
                                                    }
                                                    if(store_arr[0]===undefined){
                                                        store_arr[0]=null;
                                                    }
                                                    if(store_arr[1]===undefined){
                                                        store_arr[1]=null;
                                                    }
                                                    if(store_arr[2]===undefined){
                                                        store_arr[2]=null;
                                                    }
                                                    if(store_arr[3]===undefined){
                                                        store_arr[3]=null;
                                                    }
                                        
                                                }
                                            }                               
                                            
                                            // console.log("store_arr",store_arr);

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
                }    
            },
            // startFrom (arr, idx) {                                      
            //      return arr.slice(idx)
            // }
        },     
    }
</script>