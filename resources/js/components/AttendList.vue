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
                              <button type="button" class="btn btn-primary">Primary1</button>
                        </div>
                         <div class="col-md-4 offset-md-2">
                            <button type="button" class="btn btn-primary">Primary2</button>
                         </div> 
                    </div>                 
                 
                    <div class="row mt-3">
                          <div class="col-md-4"></div>
                          <div class="col-md-4 offset-md-2">
                              <button type="button" class="btn btn-primary pull-right">Primary3</button>
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
                            <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <th scope="row">1</th>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                            </tr>
                            <tr>
                            <th scope="row">2</th>
                            <td>Jacob</td>
                            <td>Thornton</td>
                            <td>@fat</td>
                            </tr>
                            <tr>
                            <th scope="row">3</th>
                            <td>Larry</td>
                            <td>the Bird</td>
                            <td>@twitter</td>
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
                emp_name:''
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
               let split_name=val.split(" ");
               this.emp_no=split_name[0];
               this.emp_name=split_name[1]+" "+split_name[2];
            },
            select_date:function (val) {
                if(val!='' && this.select_employee!=''){
                     this.attendForm();
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