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
    </div>    
</template>

<script>
    export default {
        data() {
            return {
                select_employee:'',
                select_date:'',
                emps:[],
                dates:[]
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
        methods: {
            deleteBook(id) {
                this.axios
                    .delete(``)
                    .then(response => {                       
                    });
            }
        }
    }
</script>