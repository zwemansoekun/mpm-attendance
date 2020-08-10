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
            
            <div class="container mt-5">
                <!-- //form -->
                    <div class="row">
                        <div class="col-md-4">
                              <button type="button" class="btn btn-primary">出勤簿生成</button>
                        </div>
                         <div class="col-md-4 offset-md-2">
                            <button type="button" class="btn btn-primary">全て自動計算</button>
                         </div> 
                    </div>                 
                 
                    <div class="row mt-3">
                          <div class="col-md-4"></div>
                          <div class="col-md-4 offset-md-2">
                              <button type="button" class="btn btn-primary pull-right">空のところだけ自動計算</button>
                          </div>
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
                            <th scope="col">AM</th>
                            <th scope="col">PM</th>
                            <th scope="col">Total Hours</th>
                            <th scope="col" class="w-25 p-3"></th>
                            </tr>
                        </thead>
                        <tbody>                           
                            
                            <tr v-bind:key="index" v-for="index in day_count">                                
                                <th scope="row">{{index}} {{ days[new Date(year+"/"+month+"/"+index).getDay()]}}</th>
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
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
                day_count:'',
                days:['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
            }
        },
        created() {
            this.axios
                .get('http://localhost:5000/employees')
                .then(response => {
                    // console.log(response);
                    this.emps=response.data;

                    // console.log(this.emps);
                });

            this.axios
            .post('http://localhost:5000/attendances/attendanceDate')
            .then(response => {
                // console.log(response);
                this.dates=response.data;
                // console.log(this.dates);
            });
        },
        watch: {
           select_employee:function (val) {
               if(val!='' && this.select_date!=''){
                   this.attendForm();
               }
                if(val!=''){
                     let split_name=val.split(" ");
                     this.emp_no=split_name[0];
                     val=val.replace(this.emp_no,'');
                    this.emp_name=val;
                }  
            },
            select_date:function (val) {
                if(val!='' && this.select_employee!=''){
                     this.attendForm();
                }
                  if(val!=''){
                     let split_date=val.split("/");
                     console.log(split_date);
                     this.year=split_date[0];
                     console.log(this.year);
                     val=val.replace(this.year+"/",'');
                    this.month=val;
                    console.log(this.month);
                    this.day_count=new Date(this.year, this.month, 0).getDate()
                    console.log(this.day_count);
                 
                }  
            },           
        },
        methods: {
            delete(id) {
                this.axios
                    .delete(``)
                    .then(response => {                       
                    });
            },
            attendForm(){
                
            }
        }
    }
</script>