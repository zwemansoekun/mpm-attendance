<template>
    <div class="col-md-10"> 
        <div class="container">
            <div class="row">
                <div class="col-md-4">                     
                    <select class="form-control" id="selectEmployee" @change="empChange($event)"  name="employ_selected" required focus v-model="select_employee">
                        <option value="" disabled selected>Please select employee</option>
                        <option v-bind:key="emp.id" v-for="emp in emps" v-bind:label="employeeCodeAndName(emp)"> {{ emp.id }} {{ emp.employeeId }} {{emp.name }}</option>
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
                <strong >No data to display.</strong> <!--データはありませんでした。-->
        </div>

        <div class="container-fluid mt-5" v-if="form_open">
            <table class="table table-borderless">
                <tr>
                    <td class="bg-danger"></td>
                    <td>Late / early leave</td> <!-- 遅刻・早退-->
                </tr>
                <tr>
                    <td class="bg-warning"></td>
                    <td>Late (check if it is acceptable)</td><!-- 遅刻(許容範囲か要確認) -->
                </tr>
                <tr>
                    <td style="background-color:#FBE5D6"></td>
                    <td>No stamp</td><!-- 打刻なし-->
                </tr>
                <tr>
                    <td class="table-success"></td>
                    <td>holiday</td><!--休日 -->
                </tr>
            </table>
        </div>

        <div class="container mt-5" v-if="form_open">
                            
            <div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn" style="background-color:#E7E6E6" onclick="this.blur();" @click="csvOutput(select_employee,select_date)">出勤簿生成</button><!--Attendance book generation-->
                </div>
                <div class="col-md-4 offset-md-2">
                    <button type="button" class="btn" style="width: 220px;background-color:#E7E6E6;" onclick="this.blur();" @click="allButtonClick()">All automatic calculation</button><!--全て自動計算-->
                </div> 
            </div>                 
                <form id="form" class="" @submit.prevent="attendSave"  autocomplete="on">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <input name="emp_no" ref="myButton" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${emp_no}`">
                            <button type="submit" class="btn btn-primary" onclick="this.blur();" >Register</button><!--登録-->
                        </div>
                        <div class="col-md-4 offset-md-2">
                            <button type="button" class="btn" onclick="this.blur();" @click="filterInput()" style="width: 220px;color: red;background-color:#E7E6E6">Automatic calculation only in the empty</button><!--空のところだけ自動計算-->
                        </div>
                    </div>
             
                    <div class="row mt-5">
                        <div class="col-md-4"> {{this.select_date}}</div>                          
                    </div>  
                    <div class="row">
                        <div class="col-md-4"> Employee No.{{emp_code}}</div>                          
                    </div>    
                    <div class="row">
                        <div class="col-md-4"> Name: {{emp_name}}</div>                          
                    </div> 
               
                    <table id="attendTable" class="table table-bordered">
                        <thead class="bg-info text-white">
                            <tr>
                                <td style="text-align: center;width: 10%;"></td>                          
                                <td colspan="2" style="text-align: center;width: 30%;" >AM</td>   
                                <td colspan="2" style="text-align: center;width: 30%;">PM</td>
                                <td style="text-align: center;width: 15%;">Total Hours</td>
                                <td style="text-align: center;width: 15%;"></td>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(day,dayindex) in data_combine" >
                            
                                <template v-if="day!==null && holidays[dayindex+1]!=(dayindex+1) ">  
                                    <tr v-bind:key="dayindex"  v-bind:style="`height:${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])=='table-secondary'?'3.3em':''}`" v-bind:class="`mainIndex_${dayindex} ${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])}`">
                                        
                                            <template v-if="day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])=='table-secondary'">
                                                <td  rowspan="2"><div style="text-align: center;">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</div></td>
                                                <td  rowspan="2" colspan="2"> </td>
                                                <td  rowspan="2" colspan="2"> </td> 
                                                <td  rowspan="2"> </td>
                                                <td  rowspan="2"> </td>
                                            </template>

                                            <template v-else v-for="(date,key) in day"> 
                                             
                                                <template  v-if="key==0">
                                                
                                                    <td v-bind:key="'A'+key" style="text-align: center;" rowspan="2"><div style="text-align: center;">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</div></td>
                                                   <template v-if="day[0].length==0">
                                                        <td  v-bind:key="'B'+key" colspan="2"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am1==null)};height: 3.3em;`" ></td>
                                                        <td  v-bind:key="'D'+key" colspan="2"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am1==null)};height: 3.3em;`" ></td>
                                                        <td  v-bind:key="'F'+key" ></td>
                                                        <td  v-bind:key="'G'+key"></td>
                                                   </template>
                                                   <template v-else>
                                                          <td v-bind:key="'B'+key" style="text-align: center;" v-bind:class="`bg-${checkColor(date.am1,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am1==null)};height: 3.3em;`" >
                                                        <div class="am1_0"> 
                                                            {{date.am1}}
                                                        </div>
                                                    </td>
                                                    <td v-bind:key="'C'+key" style="text-align: center;"  v-bind:class="`bg-${checkColor(date.am2,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.am2==null)};height: 3.3em;`">
                                                        <div class="am2_1"> 
                                                            {{date.am2}}
                                                        </div>    
                                                    </td>
                                                    <td v-bind:key="'D'+key" style="text-align: center;" v-bind:class="`bg-${checkColor(date.pm1,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.pm1==null)};height: 3.3em;`">
                                                        <div class="pm1_2"> 
                                                            {{date.pm1}}
                                                        </div>
                                                    </td>
                                                    <td v-bind:key="'E'+key" style="text-align: center;" v-bind:class="`bg-${checkColor(date.pm2,key)}`"  :style="`background-color:${checkBgColor(year,month,dayindex+1,date.pm2==null)};height: 3.3em;`">
                                                        <div class="pm2_3"> 
                                                            {{date.pm2}}
                                                        </div>    
                                                    </td>
                                                    <td v-bind:key="'F'+key" style="text-align: center;"></td>
                                                    <td v-bind:key="'G'+key" style="text-align: center;"></td>
                                                   </template>       
                                                  
                                                
                                                </template>   
                                                
                                            </template>
                                    </tr>
                                    <tr v-bind:key="'A'+dayindex" v-bind:style="`height:${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])=='table-secondary'?'2.6em':''}`" v-bind:class="`index_${dayindex} ${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])}`">
                                            <template v-if="day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()])=='table-secondary'">
                                                  
                                            </template>    
                                        
                                            <template v-else v-for="(date,key) in day"> 
                                        
                                                <template  v-if="key==1">

                                                    <template v-if="day[0].length==0"> 
                                                        <!-- 2.6em -->
                                                         <template v-if="date.am_leave==1">
                                                                <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">〇<input type="hidden" name="am_leave[]" value="1" class="amleave"></td>
                                                        </template>     
                                                        <template v-else-if="date.am_leave==2">
                                                                <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">-<input type="hidden" name="am_leave[]" value="2" class="amleave"></td>
                                                        </template>
                                                        <template v-else>
                                                                <td v-bind:key="'B'+key" class="paid-leave1 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                        </template>

                                                        <template v-if="date.pm_leave==1">
                                                                <td  v-bind:key="'D'+key"  class="paid-leave2 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">〇<input type="hidden" name="pm_leave[]" value="1" class="pmleave"></td>
                                                        </template>     
                                                        <template v-else-if="date.pm_leave==2">
                                                                <td  v-bind:key="'D'+key"  class="paid-leave2 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">-<input type="hidden" name="pm_leave[]" value="2" class="pmleave"></td>
                                                        </template>
                                                        <template v-else>
                                                                <td v-bind:key="'D'+key" class="paid-leave2 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                        </template>
                                                     
                                                       
                                                        <td v-bind:key="'F'+key"  style="padding: 0px;">
                                                            <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" :value="`${date.total_hours}`!=undefined?date.total_hours:''">
                                                            <input name="late_coming[]" class="form-control input-sm late_coming" style="text-align: center;" type="hidden" :value="`${date.late_coming}`!=undefined?date.late_coming:''">
                                                            <input name="leaving_early[]" class="form-control input-sm leaving_early" style="text-align: center;" type="hidden" :value="`${date.leaving_early}`!=undefined?date.leaving_early:''">
                                                            <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${parseInt(dayindex+1).toString().length==1?'0'+(parseInt(dayindex)+1):(parseInt(dayindex)+1)}`">
                                                        </td>
                                                        <td v-bind:key="'G'+key"  style="padding: 0px;text-align: center;" >
                                                            <input name="id[]" class="form-control input-sm idx" style="text-align: center;" type="hidden" :value="`${date.id?date.id:''}`">
                                                            <button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn" style="background-color:#E7E6E6"  @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">Auto calculate</button><!--自動計算-->
                                                        </td>
                                                    </template>
                                                    <template v-else>
                                                                
                                                            <template v-if="formchange=='edit'">   
                                                                
                                                                <template v-if="day[0].am1==null && day[0].am2== null ">
                                                                        <template v-if="date.am_leave==1">
                                                                            <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">〇<input type="hidden" name="am_leave[]" value="1" class="amleave"></td>
                                                                        </template>     
                                                                        <template v-else-if="date.am_leave==2">
                                                                                <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">-<input type="hidden" name="am_leave[]" value="2" class="amleave"></td>
                                                                        </template>
                                                                        <template v-else>
                                                                                <td v-bind:key="'B'+key" class="paid-leave1 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                                        </template>                                                                                   
                                                                </template>
                                                                <template v-else>
                                                                        <td v-bind:key="'B'+key"  style="padding: 0px;">
                                                                            <template v-if="day[0].am1!== null">   
                                                                                <input :name="`am1[]`"  @change="updateInput"  :class="`form-control input-sm am1`"  style="text-align: center;" type="text" :value="`${date.am1}`!=undefined?date.am1:''"> 
                                                                            </template>
                                                                            <template v-else>
                                                                                <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am1}`!=undefined?date.am1:null"> 
                                                                            </template> 
                                                                        </td>
                                                                        <td v-bind:key="'C'+key"  style="padding: 0px;">
                                                                            <template v-if="day[0].am2!== null">   
                                                                                <input :name="`am2[]`"  @change="updateInput" :class="`form-control input-sm am2`"  style="text-align: center;" type="text" :value="`${date.am2}`!=undefined?date.am2:''"> 
                                                                            </template>
                                                                            <template v-else>
                                                                                <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am2}`!=undefined?date.am2:null"> 
                                                                            </template> 
                                                                        </td>
                                                                </template>        
                                                                
                                                                

                                                                 <template v-if="day[0].pm1==null && day[0].pm2==null ">
                                                                       <template v-if="date.pm_leave==1">
                                                                                <td  v-bind:key="'D'+key"  class="paid-leave2 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">〇<input type="hidden" name="pm_leave[]" value="1" class="pmleave"></td>
                                                                        </template>     
                                                                        <template v-else-if="date.pm_leave==2">
                                                                                <td  v-bind:key="'D'+key"  class="paid-leave2 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">-<input type="hidden" name="pm_leave[]" value="2" class="pmleave"></td>
                                                                        </template>
                                                                        <template v-else>
                                                                                <td v-bind:key="'D'+key" class="paid-leave2 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                                        </template>           
                                                                </template>
                                                                <template v-else>
                                                                <td v-bind:key="'D'+key"  style="padding: 0px;">
                                                                     <template v-if="day[0].pm1!== null"> 
                                                                        <input :name="`pm1[]`"  @change="updateInput"  :class="`form-control input-sm pm1`"  style="text-align: center;" type="text" :value="`${date.pm1}`!=undefined?date.pm1:''">  
                                                                     </template>
                                                                     <template v-else>
                                                                            <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.pm1}`!=undefined?date.pm1:null"> 
                                                                    </template>    
                                                                </td>
                                                                <td v-bind:key="'E'+key"  style="padding: 0px;">
                                                                    <template v-if="day[0].pm2!== null"> 
                                                                        <input :name="`pm2[]`"  @change="updateInput"  :class="`form-control input-sm pm2`"  style="text-align: center;" type="text" :value="`${date.pm2}`!=undefined?date.pm2:''"> 
                                                                    </template>
                                                                     <template v-else>
                                                                            <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.pm2}`!=undefined?date.pm2:null"> 
                                                                    </template>    
                                                                </td>
                                                                </template>
                                                                <td v-bind:key="'F'+key"  style="padding: 0px;">
                                                                    <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" :value="`${date.total_hours}`!=undefined?date.total_hours:''">
                                                                    <input name="late_coming[]" class="form-control input-sm late_coming" style="text-align: center;" type="hidden" :value="`${date.late_coming}`!=undefined?date.late_coming:''">
                                                                    <input name="leaving_early[]" class="form-control input-sm leaving_early" style="text-align: center;" type="hidden" :value="`${date.leaving_early}`!=undefined?date.leaving_early:''">
                                                                    <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${parseInt(dayindex+1).toString().length==1?'0'+(parseInt(dayindex)+1):(parseInt(dayindex)+1)}`">
                                                                </td>
                                                                <td v-bind:key="'G'+key"  style="padding: 0px;text-align: center;">
                                                                    <input name="id[]" class="form-control input-sm idx" style="text-align: center;" type="hidden" :value="`${date.id?date.id:''}`">
                                                                    <button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn" style="background-color:#E7E6E6"  @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">Auto calculate</button>
                                                                </td>
                                                            
                                                            </template>
                                                                    <template v-else>
                                                                        <template v-if="day[0].am1==null && day[0].am2== null ">
                                                                            <!-- <template v-if="date.am_leave==1">
                                                                                <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">〇<input type="hidden" name="am_leave[]" value="1" class="amleave"></td>
                                                                            </template>     
                                                                            <template v-else-if="date.am_leave==2">
                                                                                    <td  v-bind:key="'B'+key"  class="paid-leave1 align-middle text-center"  colspan="2" style="height:2.6em;padding:0px;">-<input type="hidden" name="am_leave[]" value="2" class="amleave"></td>
                                                                            </template>
                                                                            <template v-else> -->
                                                                                    <td v-bind:key="'B'+key" class="paid-leave1 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                                            <!-- </template>                                                                                    -->
                                                                        </template>
                                                                        <template v-else>
                                                                            <td v-bind:key="'B'+key"  style="padding: 0px;">
                                                                                <template v-if="day[0].am1!==null">   
                                                                                <input :name="`am1[]`"  @change="updateInput"  :class="`form-control input-sm am1`"  style="text-align: center;" type="text" @value="`${old(am1)}`"> 
                                                                                </template>
                                                                                <template v-else>
                                                                                    <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am1}`!=undefined?date.am1:null"> 
                                                                                </template> 
                                                                            </td>
                                                                            <td v-bind:key="'C'+key"  style="padding: 0px;">
                                                                                <template v-if="day[0].am2!==null"> 
                                                                                <input :name="`am2[]`"  @change="updateInput" :class="`form-control input-sm am2`"  style="text-align: center;" type="text" @value="`${old(am2)}`"> 
                                                                                </template>
                                                                                <template v-else>
                                                                                    <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.am2}`!=undefined?date.am2:null"> 
                                                                                </template> 
                                                                            
                                                                            </td>
                                                                        </template>

                                                                        <template v-if="day[0].pm1==null && day[0].pm2== null ">
                                                                             <td v-bind:key="'D'+key" class="paid-leave2 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                                        </template>
                                                                         <template v-else>
                                                                            <td v-bind:key="'D'+key"  style="padding: 0px;">
                                                                                <template v-if="day[0].pm1!==null">   
                                                                                <input :name="`pm1[]`"  @change="updateInput"  :class="`form-control input-sm pm1`"  style="text-align: center;" type="text" @value="`${old(pm1)}`">  
                                                                                </template>
                                                                                <template v-else>
                                                                                    <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.pm1}`!=undefined?date.pm1:null"> 
                                                                                </template> 
                                                                            </td>
                                                                            <td v-bind:key="'E'+key"  style="padding: 0px;">
                                                                            
                                                                                <template v-if="day[0].pm2!==null">   
                                                                                    <input :name="`pm2[]`"  @change="updateInput"  :class="`form-control input-sm pm2`"  style="text-align: center;" type="text" @value="`${old(pm2)}`"> 
                                                                                </template>
                                                                                <template v-else>
                                                                                    <input   class="form-control input-sm"  style="text-align: center;" type="text" readonly :value="`${date.pm2}`!=undefined?date.pm2:null"> 
                                                                                </template>                                                                            
                                                                            </td>
                                                                         </template>

                                                                        <td v-bind:key="'F'+key"  style="padding: 0px;">
                                                                            <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" @value="`${old(total_hours)}`">
                                                                            <input name="late_coming[]" class="form-control input-sm late_coming" style="text-align: center;" type="hidden" :value="`${date.late_coming}`!=undefined?date.late_coming:''">
                                                                            <input name="leaving_early[]" class="form-control input-sm leaving_early" style="text-align: center;" type="hidden" :value="`${date.leaving_early}`!=undefined?date.leaving_early:''">
                                                                            <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${parseInt(dayindex+1).toString().length==1?'0'+(parseInt(dayindex)+1):(parseInt(dayindex)+1)}`">
                                                                        </td>
                                                                        <td v-bind:key="'G'+key"  style="padding: 0px;text-align: center;">
                                                                            <!-- <input name="id[]" class="form-control input-sm idx" style="text-align: center;" type="hidden" :value="`${date.id?date.id:''}`"> -->
                                                                            <button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn" style="background-color:#E7E6E6"  @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">Auto calculate</button>
                                                                        </td>
                                                                    </template> 
                                                    </template>                                                
                                                </template> 
                                            </template>
                                    </tr>   
                                </template>
                                <template v-else>
                                    <!-- {{holidays[dayindex+1]==(dayindex+1) }} -->
                                    <tr v-bind:key="dayindex"  v-bind:style="`height:${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))==('table-secondary' || 'table-success')?'3.3em':''}`" v-bind:class="`mainIndex_${dayindex} ${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))}`">
                                        <td  style="text-align: center;" rowspan="2"><div style="text-align: center;">{{dayindex+1}} {{ days[new Date(year+"/"+month+"/"+(dayindex+1)).getDay()]}}</div></td>     
                                       
                                        <template v-if="`${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))}`!=''">
                                            <td rowspan="2" colspan="2"></td>
                                            <td rowspan="2" colspan="2"></td>
                                            <td rowspan="2" ></td>
                                            <td rowspan="2" ></td>
                                        </template>
                                        <template v-else>
                                                <td  colspan="2"  :style="`background-color:${checkBgColor(year,month,dayindex+1,1)};height: 3.3em;`" ></td>
                                                <td   colspan="2"  :style="`background-color:${checkBgColor(year,month,dayindex+1,1)};height: 3.3em;`" ></td>
                                                <td ></td>
                                                 <td ></td>  
                                        </template> 
                                    </tr>
                                    <tr v-bind:key="'A'+dayindex" v-bind:style="`height:${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))==('table-secondary' || 'table-success')?'2.6em':''}`" v-bind:class="`index_${dayindex} ${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))}`">
                                               
                                               
                                               <template v-if="`${day_name(days[new Date(year+'/'+month+'/'+(dayindex+1)).getDay()],holidays[dayindex+1]==(dayindex+1))}`==''">
                                                       <td class="paid-leave1 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td> 
                                                        <td class="paid-leave2 align-middle text-center" colspan="2" style="height:2.6em;padding:0px;"></td>
                                                        <td  style="padding: 0px;">
                                                                    <input name="total_hours[]" class="form-control input-sm thour" style="text-align: center;" type="text" @value="`${old(total_hours)}`">
                                                                    <input name="late_coming[]" class="form-control input-sm late_coming" style="text-align: center;" type="hidden" >
                                                                    <input name="leaving_early[]" class="form-control input-sm leaving_early" style="text-align: center;" type="hidden" >
                                                                    <input name="date[]" class="form-control input-sm date" style="text-align: center;" type="hidden" :value="`${year}-${month}-${parseInt(dayindex+1).toString().length==1?'0'+(parseInt(dayindex)+1):(parseInt(dayindex)+1)}`">
                                                        </td>
                                                        <td style="padding: 0px;text-align: center;">
                                                                    <input name="id[]" class="form-control input-sm idx" style="text-align: center;" type="hidden" >
                                                                    <button type="button" onclick="this.blur();" :id="`autobut${dayindex}`" class="btn" style="background-color:#E7E6E6"  @click="showTimer(`mainIndex_${dayindex}`,`index_${dayindex}`,'')">Auto calculate</button>
                                                        </td>  
                                               </template>                                   
                                    </tr>  
                                </template> 
                           </template>   
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
                emp_code:'',
                emp_no:'',
                emp_name:'',
                year:'',
                month:'',
                days:['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                memory:'',
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
                data_combine:[],
                formchange:'',
                holidays:[],
            }
        },     
        created() {
            this.axios
                .get(process.env.MIX_APP_API_URL+'/employees')
                .then(response => {
                
                    this.emps=response.data;
                  
                });
            this.axios
                .get(process.env.MIX_APP_API_URL+'/attendances/all/date')
                .then(response => {
             
                    this.dates=response.data.filter(function (el) {
                        return el['recordedDateTime'] != null;
                    });
            
                });   
       
        },
        computed: {
            errorsFun:function(){              
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
        },      
        methods: {  
            getHolidays:function(){
                let that=this;let holiday=[];
                this.axios  
                .get(process.env.MIX_APP_URL+'/holiday/'+that.year+that.month)
                .then(response => {
                    
                    holiday=response.data;                  
                   if(holiday){
                  
                       for(let v in holiday){                        
                           that.holidays[holiday[v].day]=holiday[v].day;                         
                       }
                     
                   }

                });   
            },
            loadingAlert:function(){
                  let that=this;
                   that.$swal({
                        title: 'Please wait!',
                        // add a custom html tags by defining a html method.
                        html: 'Loading......',
                         timer: 300000,
                        showCloseButton: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        focusConfirm: false,
                        allowOutsideClick: false,
                        //  onBeforeOpen: () => {
                        //     that.$swal.showLoading();
                        //     },
                        })   
            },  
            employeeCodeAndName: function(value){
                return value.employeeId+' '+value.name;
            },
            empChange:function(event){   

                let val='';
                if(event.target.value!=''){
                    val=event.target.value;
                    let split_name=val.split(" ");
                    this.emp_no=split_name[0];
                    this.emp_code=split_name[1];

                    val=val.replace(this.emp_no,'');
                    val=val.replace(this.emp_code,'');
                    this.emp_name=val;
                }              
                if(val!='' && this.select_date!=''){   
                    this.loadingAlert();
                    this.update_call();          
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
                      this.loadingAlert();
                      this.update_call();
                }
            },
            attendSave:function(){
                let those=this;             
                var validator = jQuery('#form').validate({
                  
                    rules: {
                            "am1[]": "required",
                            "am2[]": "required",
                            "pm1[]": "required",
                            "pm2[]": "required",
                            // "total_hours[]":"required",
                            // "am_leave[]":"required",
                            // "pm_leave[]":"required",
                    },  
                    errorPlacement: function(error,element) {
                        return true;
                    },
                });
                let temp_arr=[];let am1=null,am2=null,pm1=null,pm2=null,thour=null,date=null;let am_leave=null,pm_leave=null;
                let id=null,late_coming=0,leaving_early=0;
                if(jQuery('#form').valid()){
                     
                     $('#attendTable tr[class^=index_]').each(function (key,value) {
                      
                         let that=this;
                         $(that).find('td').each(function (index,v2) {
                             let isLastElement = index == $(that).find('td').length-1;
                          
                            if(jQuery(this).find('.am1').val()){
                                am1=jQuery(this).find('.am1').val();
                            }
                            if(jQuery(this).find('.am2').val()){
                                am2=jQuery(this).find('.am2').val();
                            }
                            if(jQuery(this).find('.pm1').val()){
                                pm1=jQuery(this).find('.pm1').val();
                            }
                            if(jQuery(this).find('.pm2').val()){
                                pm2=jQuery(this).find('.pm2').val();
                            }
                            if(jQuery(this).find('.thour').val()){
                                thour=jQuery(this).find('.thour').val();
                            }
                            if(jQuery(this).find('.late_coming').val()){
                                late_coming=jQuery(this).find('.late_coming').val();
                            }
                            if(jQuery(this).find('.leaving_early').val()){
                                leaving_early=jQuery(this).find('.leaving_early').val();
                            }
                            if(jQuery(this).find('.date').val()){
                                date=jQuery(this).find('.date').val();
                            }
                            if(jQuery(this).find('.amleave').val()){
                                am_leave=jQuery(this).find('.amleave').val();
                            }
                              if(jQuery(this).find('.pmleave').val()){
                                pm_leave=jQuery(this).find('.pmleave').val();
                            }                       
                            if(jQuery(this).find('.idx').val()){
                                id=jQuery(this).find('.idx').val();
                            }
                           
                            if (isLastElement) {
                                        
                               if(am1=='' && am2=='' && am_leave==null){                               
                                   am_leave=0;
                               }
                               if(pm1=='' && pm2=='' && pm_leave==null){                                  
                                   pm_leave=0;
                               }
                               if(id!=''){
                                  temp_arr.push({"id":id,"date": date,"emp_no":those.emp_no,"total_hours":thour,"am1": am1 ,"am2":am2,"pm1":pm1,"pm2":pm2,"am_leave":am_leave,"pm_leave":pm_leave,"leaving_early":leaving_early,"late_coming":late_coming}); 
                               }else
                                  temp_arr.push({"date": date,"emp_no":those.emp_no,"total_hours":thour,"am1": am1 ,"am2":am2,"pm1":pm1,"pm2":pm2,"am_leave":am_leave,"pm_leave":pm_leave,"leaving_early":leaving_early,"late_coming":late_coming}); 
                             
                                date=null;am1=null;am2=null;pm1=null;pm2=null;thour=null,am_leave=null;id=null;pm_leave=null;leaving_early=0;late_coming=0;
                                return false;
                            }
                         });    
                   });                   
                    those.loadingAlert();
                    this.axios({
                      url:process.env.MIX_APP_URL+"/attendList",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList",
                      method: 'post',
                      data: temp_arr
                    })
                    .then(function (response) {
                      

                        // your action after success                      
                        if(response.data){
                            if(response.data.message==true){
                                  those.data_combine=[];
                                  those.update_call();  
                                    those.$fire({
                                    title: "Success",
                                    text: "Data registration is successfully saved.",
                                    type: "success",
                                    timer: 300000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    // position:'center',
                                }).then(r => {                              
                                });
                            }else{
                                those.$swal.close();
                                those.$fire({
                                title: "Fail！！",
                                text: "Data cannot be registered.",
                                type: "error",
                                timer: 1500,
                                showCancelButton: false,
                                showConfirmButton: false,
                                // position:'center',
                                }).then(r => {                             
                                }); 
                                those.errors=response.data.errors;                             
                            }                       
                        }
                    })
                    .catch(function (error) {
                       
                    });
                }else{
                  
                     those.$fire({
                        title: "Fail！！",
                        text: "Please fill in the data.",
                        type: "warning",
                        timer: 3000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        // position:'center',
                        }).then(r => {                     
                        }); 
                    return false;
                }
               
            },
            updateInput:function(event){ 

                   let am1=null,am2=null,pm1=null,pm2=null;let total_am=0,total_pm=0;
                   am1=$(event.target).parent().parent().find('.am1').val()!=undefined?$(event.target).parent().parent().find('.am1').val().split(":"):'';
                   am2=$(event.target).parent().parent().find('.am2').val()!=undefined?$(event.target).parent().parent().find('.am2').val().split(":"):'';
                   pm1=$(event.target).parent().parent().find('.pm1').val()!=undefined?$(event.target).parent().parent().find('.pm1').val().split(":"):'';
                   pm2=$(event.target).parent().parent().find('.pm2').val()!=undefined?$(event.target).parent().parent().find('.pm2').val().split(":"):'';
                
                  const {t_hr, t_min,late_coming,leaving_early}=this.totalHourCal(am1,am2,pm1,pm2,total_am,total_pm,'',''); 

                  let res=isNaN((parseFloat(t_hr)+parseFloat(t_min)).toFixed(2))?'':(parseFloat(t_hr)+parseFloat(t_min)).toFixed(2);

                jQuery(event.target).parent().parent().find(".thour").val(Math.abs(res).toFixed(2));

                jQuery(event.target).parent().parent().find(".late_coming").val(Math.abs(late_coming).toFixed(2));
                jQuery(event.target).parent().parent().find(".leaving_early").val(Math.abs(leaving_early).toFixed(2));

            }, 
            allButtonClick:function(){ 
                $('[id^=autobut]').click();
            },  
            filterInput:function(){
             
                $('#attendTable tbody').find('td input').filter(function () {
                     return this.value === ""
                }).addClass('checkColumn').parent().parent().parent().find('[type=button]').click().parent().parent().parent().find('td input').removeClass('checkColumn');
              
            },
            mouse_rclick:function(){ 
        
                let class_name='';let parent_class_name='';
                let that=this; 
                $(function() {

                    jQuery(document).on("contextmenu","[class^=paid-leave1],[class^=paid-leave2]", function(e){
                            parent_class_name=jQuery(this).parent().attr('class');                            
                            // if(parent_class_name==undefined){
                            //      parent_class_name=jQuery(this).parent().find('[class^=index_]').attr('class');
                            // }
                            class_name=jQuery(this).attr('class').split(' ')[0];                         
                    });

                    $.contextMenu({
                        selector:".paid-leave1,.paid-leave2",
                        callback: function(key, options) {
                           
                            if(jQuery(this).text().trim()!=""){
                                jQuery(this).text('');
                            } 
                            if(key=='o'){
                                   
                                let dakoku=that.showTimer(parent_class_name,class_name,'circle');
                              
                                if(class_name=='paid-leave1'){
                                    jQuery("."+parent_class_name).find('.amleave').remove();
                                    if(dakoku!='dakoku')
                                    $('<input/>').attr({ type: 'hidden',name: 'am_leave[]',value:1,class:'amleave'}).appendTo(jQuery("."+parent_class_name).find('.paid-leave1'));
                                   
                                }else{
                                    jQuery("."+parent_class_name).find('.pmleave').remove();
                                    if(dakoku!='dakoku')
                                    $('<input/>').attr({ type: 'hidden',name: 'pm_leave[]',value:1,class:'pmleave'}).appendTo(jQuery("."+parent_class_name).find('.paid-leave2'));
                                }
                               
                                   jQuery("."+parent_class_name).find('[id^=autobut]').click();

                            }else{
                                let dakoku=that.showTimer(parent_class_name,class_name,'dash');

                                if(class_name=='paid-leave1'){
                                    jQuery("."+parent_class_name).find('.amleave').remove();
                                    if(dakoku!='dakoku')
                                    $('<input/>').attr({ type: 'hidden',name: 'am_leave[]',value:2,class:'amleave'}).appendTo(jQuery("."+parent_class_name).find('.paid-leave1'));
                                }else{
                                    jQuery("."+parent_class_name).find('.pmleave').remove();
                                    if(dakoku!='dakoku')
                                    $('<input/>').attr({ type: 'hidden',name: 'pm_leave[]',value:2,class:'pmleave'}).appendTo(jQuery("."+parent_class_name).find('.paid-leave2'));
                                }
                                jQuery("."+parent_class_name).find('[id^=autobut]').click();    
                            }
                        },
                        items: {
                            "o": {name: "paid", icon: "fa-circle"},
                            "-": {name: "absent", icon: "fa-window-minimize"},
                        }
                    });
                });
            },
            checkBgColor:function(year,month,dayindex,index){
               
                let custom_date=year+"/"+("0" + month).slice(-2)+("0" +parseInt(dayindex)).slice(-2);                
                let val=this.days[new Date(year+'/'+month+'/'+parseInt(dayindex)).getDay()];
                let cur_date=new Date().getFullYear()+"/"+("0" + parseInt(new Date().getMonth()+1)).slice(-2)+"/"+("0" +new Date().getDate()).slice(-2); 
               
                if(index==1){                
                    return (val!=="Sat" && val!=="Sun" && cur_date>custom_date)? '#FBE5D6' : '';
                }else if(index==true && cur_date>custom_date){
                    return '#FBE5D6';
                }

            },
            checkColor:function(val,index){  
               
                let val_split='';              
                   
                if( (index==0 || index==2) && val!=null){                   
                      val_split=val!==''?val.split(":"):'';
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
           day_name(res,holiday=false){
                {   
                    if(holiday==true){
                         return 'table-success';
                    }else{
                         return (res==="Sat" || res==="Sun")? 'table-secondary' : '';
                    }                    
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

                    that.getHolidays();

                    let up_data={
                        "emp_no":this.emp_no,
                        "date":this.select_date,
                        "year":this.year,
                        "month":this.month,
                    };
                    this.axios({
                      url:process.env.MIX_APP_URL+"/attendList/getmonth",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList/getmonth",
                      method: 'post',
                      data:up_data,
                    })                  
                    .then(response=>{ 
                        that.get_attend_data=[];
                        that.data_combine=[];
                        that.data_combine=[];
                        that.check_attend_data=false;                        
                        that.get_attend_data=response.data;

                        that.ampm_calling(that.get_attend_data);                  
                    })
                    .catch(function (error) {
                         that.$swal.close();                      
                    }); 

           },   
            ampm_calling(attend_data=''){        
                     
                    let that = this;
                    let those=this;
                    this.ampm_by_day_arr=[];
                    this.ampm_by_day=[];
                    this.ampm_inner_arr=[];
                    this.ampm_arr=[];
                    let tem_store=[];let pre_store={};
               
                if(this.year!='' && this.month!='' && this.emp_no!=''){                    
                   
                    this.dayCount=new Date(this.year,this.month, 0).getDate();
                    this.axios
                    .get(process.env.MIX_APP_API_URL+"/attendances/ampm/"+this.emp_no+"/"+this.year+this.month)                 
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
                
                        let memory_record={};let push_record=[];
                        response.data.forEach(function(res, index) {
                                if (res.recordedDateTime === that.memory && that.memory !== "") {
                                    that.memory = res.recordedDateTime;
                                
                                     memory_record[res.recordedDateTime]= res                                   
                                        
                                    that.ampm_inner_arr.push(res);
                                } else if (res.recordedDateTime !== that.memory && that.memory !== "") {
                                    push_record.push(memory_record);
                                    that.ampm_arr.push(that.ampm_inner_arr);//.slice(0,4)
                                    memory_record={};
                                    that.ampm_inner_arr = [];
                                    that.memory = res.recordedDateTime;
                                      
                                    memory_record[ res.recordedDateTime]= res 
                                 
                                    that.ampm_inner_arr.push(res);
                                } else {
                                    that.memory = res.recordedDateTime; 
                                    memory_record[ res.recordedDateTime]= res 
                                    that.ampm_inner_arr.push(res);
                                }
                                if(response.data.length - 1 === index) {
                                    push_record.push(memory_record);   
                                    that.ampm_arr.push(that.ampm_inner_arr);//.slice(0,4)
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
                                        
                                                if(that.ampm_by_day[i]!=null ){                                                
                                                    if(that.ampm_by_day[i].hour<12 && pre_store['am1']==undefined){   
                                                        pre_store['am1']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }else if(pre_store['am1']!=undefined && that.ampm_by_day[i].minute>=0  && that.ampm_by_day[i].hour<13 && pre_store['am2']==undefined && pre_store['am1']!=undefined ){  //&& store_arr[0]!==undefined   //that.ampm_by_day[i].hour>=12
                                                        pre_store['am2']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }else if( ( (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0 ) || (that.ampm_by_day[i].hour>=12 && that.ampm_by_day[i].minute>=0   ) )&& that.ampm_by_day[i].hour<17 && pre_store['pm1']==null){ //&& store_arr[1]!==undefined   //&& store_arr[1]===undefined                                                    
                                                        pre_store['pm1']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                     
                                                    }else if(pre_store['pm1']!=undefined && that.ampm_by_day[i].minute>=0){//that.ampm_by_day[i].hour>=17
                                                        pre_store['pm2']=that.ampm_by_day[i]?that.ampm_by_day[i].am_pm:null;                                                      
                                                    }
                                                    if(pre_store['am1']==undefined){
                                                       pre_store['am1']=null;
                                                    }
                                                    if(pre_store['am2']==undefined){
                                                        pre_store['am2']=null;
                                                    }
                                                    if(pre_store['pm1']==undefined){
                                                        pre_store['pm1']=null;
                                                    }
                                                    if(pre_store['pm2']==undefined){
                                                        pre_store['pm2']=null;
                                                    }
                                        
                                                }
                                            } 
                                           
                                            tem_store.push({"0":pre_store,"1":[]});                                         
                                            pre_store={};//tar=[];
                                          
                                            that.status=1; 
                                            that.ampm_by_day=[]; 
                                        }
                                    }
                                }
                            }
                                if(that.status===1){                        
                                    that.status=0;                                
                                }else{
                                    tem_store.push(null);
                                }
                        }   
                                       
                       
                        if(attend_data.length!=0){
                               let k=0;
                                for(let i=1;i<=this.dayCount;i++){                                
                                   if(k<attend_data.length){
                                        for(let j=(k!=0?k:0+(i-1));j<attend_data.length;j++){                                   
                                            if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')==attend_data[j].date){                                
                                
                                                if(tem_store[i-1]!=null){
                                                    that.data_combine.push({"0":tem_store[i-1][0],"1":attend_data[j]});
                                                }else{
                                                    that.data_combine.push({"0":[],"1":attend_data[j]});
                                                }
                                            // data_combine.push(attend_data[j]);
                                                ++k;                                          
                                                break;
                                            }else if(moment(that.year+"/"+that.month+"/"+i).format('YYYY-MM-DD')<attend_data[j].date){
                                                k=j;
                                                that.data_combine.push(null);
                                                break;
                                            }else{          
                                                 that.data_combine.push(null);
                                                 continue;
                                            }
                                        }
                                    }//else  //dd
                                //   that.data_combine.push(null);
                                }
                                 that.formchange="edit";
                        }else{
                          
                            that.data_combine=tem_store;
                              
                        }
                    });

                    this.axios
                    .get(process.env.MIX_APP_URL+'/api/setting/delayTime/'+this.year+"/"+this.month)//+this.year+"/"+this.month
                    .then(response => { 
                          that.default_ampm.am=response.data.am;
                          that.default_ampm.pm=response.data.pm;
                    });

                }
                that.$swal.close();
            },
         
        showTimer(index,sec_index,day_leave=''){  
              let am_leave='';let pm_leave=''; 
              let am1=null,am2=null,pm1=null,pm2=null;
              let t_am=null;let p_am=null; 

             

              if(day_leave==''){
                if(jQuery("."+sec_index).find('td .amleave').val()){
                    am_leave=jQuery("."+sec_index).find('td .amleave').val();      
                }
                if(jQuery("."+sec_index).find('td .pmleave').val()){
                    pm_leave=jQuery("."+sec_index).find('td .pmleave').val()
                }  
                am1=jQuery("."+index).find('.am1_0').text().trim();
                am2=jQuery("."+index).find('.am2_1').text().trim();

                pm1=jQuery("."+index).find('.pm1_2').text().trim();
                pm2=jQuery("."+index).find('.pm2_3').text().trim();
                   
              }else{
                if(jQuery("."+index).find('td .amleave').val()){
                  am_leave=jQuery("."+index).find('td .amleave').val();      
                }
                if(jQuery("."+index).find('td .pmleave').val()){
                  pm_leave=jQuery("."+index).find('td .pmleave').val()
                } 
                am1=jQuery("."+index).prev('tr').find('[class^="mainIndex_"]').find('.am1_0').text().trim();
                am2=jQuery("."+index).prev('tr').find('[class^="mainIndex_"]').find('.am2_1').text().trim();

                pm1=jQuery("."+index).prev('tr').find('[class^="mainIndex_"]').find('.pm1_2').text().trim();
                pm2=jQuery("."+index).prev('tr').find('[class^="mainIndex_"]').find('.pm2_3').text().trim();
                  
              }         


              if(am1=='' && am2=='' && pm1=='' && pm2=='' && am_leave==1 && pm_leave==1 ){
                    if(day_leave==''){
                        jQuery("."+sec_index).find(".thour").val('8.00');
                        return false;
                    }                   

              }else if(am1=='' && am2=='' && pm1=='' && pm2=='' && am_leave==2 && pm_leave==2){
                   if(day_leave==''){
                        jQuery("."+sec_index).find(".thour").val('0.00');
                        return false;
                    }
                  
              }else if( ( (am_leave==1 && am1=='' && am2=='' ) && pm_leave=='') || ( (am_leave==1 && am1=='' && am2=='' ) && pm_leave==2) ){
                    t_am=4;
              }else if( (am_leave=='' && (pm_leave==1 && pm1=='' && pm2=='' ) ) || (am_leave==2 && (pm_leave==1 && pm1=='' && pm2=='' ) ) ){
                    p_am=4;
              }
             

              if(day_leave=='circle' || day_leave=='dash'){           
              
                    let leave_sign=day_leave=='circle'?"〇":"-";
                    if(am1=='' && am2=='' && sec_index=='paid-leave1'){ 
                          
                        if(jQuery("."+index).children('td').length==6){
                            jQuery("."+index).find("td:first").attr('colspan','2').find("td:first").remove();
                            jQuery("."+index).find("td:nth-child(2)").remove();
                            jQuery("."+index).find('td:first').css({'text-align':'center'});                       
                        }
                         jQuery("."+index).find("[class^=paid-leave1]").text(leave_sign).css({'height':'2.6em','padding':'0px'});
                         
                    }else if(pm1== '' && pm2=='' && sec_index=='paid-leave2'){
 
                        if(jQuery("."+index).children('td').length==6){
                            jQuery("."+index).find("td:nth-child(3)").attr('colspan','2').find("td:nth-child(3)").remove();
                            jQuery("."+index).find("td:nth-child(4)").remove();
                            jQuery("."+index).find('td:nth-child(3)').css({'text-align':'center'});                       
                        }  
                         jQuery("."+index).find("[class^=paid-leave2]").text(leave_sign).css({'height':'2.6em','padding':'0px'});                       
                    }
            }
          
            else{
              let ap_split1=am1!==''?am1.split(":"):'';
              let ap_split2=am2!==''?am2.split(":"):'';
            
              let ap_split3=pm1!==''?pm1.split(":"):'';
              let ap_split4=pm2!==''?pm2.split(":"):'';
              
           
              let auto_am1,auto_am2,auto_pm1,auto_pm2;

                if(day_leave==''){
                        if(!jQuery("."+sec_index).find(".am1").hasClass('checkColumn') && !jQuery("."+sec_index).find(".am2").hasClass('checkColumn')
                        && !jQuery("."+sec_index).find(".pm1").hasClass('checkColumn') && !jQuery("."+sec_index).find(".pm2").hasClass('checkColumn')){   
                            auto_am1=this.ampm_time_check(ap_split1,".am1",sec_index);     
                            auto_am2=this.ampm_time_check(ap_split2,".am2",sec_index);   
                            auto_pm1=this.ampm_time_check(ap_split3,".pm1",sec_index);   
                            auto_pm2=this.ampm_time_check(ap_split4,".pm2",sec_index);                            
                       }else{
                                if(jQuery("."+sec_index).find(".am1").hasClass('checkColumn')){
                                        auto_am1=this.ampm_time_check(ap_split1,".am1",sec_index);   
                                    
                                    }
                                if(jQuery("."+sec_index).find(".am2").hasClass('checkColumn')){
                                    auto_am2=this.ampm_time_check(ap_split2,".am2",sec_index);   
                                
                                }
                                if(jQuery("."+sec_index).find(".pm1").hasClass('checkColumn')){
                                    auto_pm1=this.ampm_time_check(ap_split3,".pm1",sec_index);   
                                
                                }
                                if(jQuery("."+sec_index).find(".pm2").hasClass('checkColumn') ){
                                    auto_pm2=this.ampm_time_check(ap_split4,".pm2",sec_index); 
                                
                                } 
                       }
                }
               
                if(day_leave==''){
                // karanotokoro                 
                    if( auto_am1==undefined || auto_am2==undefined || auto_pm1==undefined || auto_pm2==undefined ){
                               
                                if(auto_am1==undefined ){
                                   let name1=".am1";
                                   let split_ap1=jQuery("."+sec_index).find(name1).val()!=undefined?jQuery("."+sec_index).find(name1).val().split(":"):'';
                                       auto_am1=this.split_amppm(split_ap1,name1);
                                }
                                if(auto_am2==undefined ){
                                   let name2=".am2";
                                   let split_ap2=jQuery("."+sec_index).find(name2).val()!=undefined?jQuery("."+sec_index).find(name2).val().split(":"):'';
                                       auto_am2=this.split_amppm(split_ap2,name2);
                                }
                                if(auto_pm1==undefined ){
                                  let name3=".pm1";
                                  let split_ap3=jQuery("."+sec_index).find(name3).val()!=undefined?jQuery("."+sec_index).find(name3).val().split(":"):'';
                                      auto_pm1=this.split_amppm(split_ap3,name3);
                                }
                                if(auto_pm2==undefined ){
                                  let  name4=".pm2";
                                  let split_ap4=jQuery("."+sec_index).find(name4).val()!=undefined?jQuery("."+sec_index).find(name4).val().split(":"):'';  
                                      auto_pm2=this.split_amppm(split_ap4,name4);
                                }
                    }                          
                    let total_am=0;let total_pm=0; 
                    const {t_hr, t_min,late_coming,leaving_early}=this.totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm,t_am,p_am);               
              
                    let res=isNaN((parseFloat(t_hr)+parseFloat(t_min)).toFixed(2))?'':(parseFloat(t_hr)+parseFloat(t_min)).toFixed(2);   
                    
                    jQuery("."+sec_index).find(".thour").val(res==0?"0.00":Math.abs(res).toFixed(2));
                    jQuery("."+sec_index).find(".late_coming").val(Math.abs(late_coming).toFixed(2));
                    jQuery("."+sec_index).find(".leaving_early").val(Math.abs(leaving_early).toFixed(2));

               } 
                if(  ( (am_leave==1 || am_leave==2 )&& (am1!='' || am2!='') ) || ( (pm_leave==1 || pm_leave==2 )&& (pm1!='' || pm2!='') )  ){
                        return 'dakoku';
                }
            }
        },
            split_amppm(split_ap,name){
                       if(split_ap!=''){
                            let h1,m1='';
                        
                            h1=split_ap[0]<10?"0"+parseInt(split_ap[0]):split_ap[0];
                            m1=split_ap[1]<10?"0"+parseInt(split_ap[1]):split_ap[1];
                    
                        
                            if( ((h1+":"+m1).search(/^\d{2}:\d{2}$/) != -1) &&
                                ((h1+":"+m1).substr(0,2) >= 0 && (h1+":"+m1).substr(0,2) <= 24) &&
                                ((h1+":"+m1).substr(3,2) >= 0 && (h1+":"+m1).substr(3,2) <= 59) ){
                                    // auto_am1=[];auto_am2=[];auto_pm1=[];auto_pm2=[];
                                if(name==".am1"){
                                   let auto_am1=[];
                                    
                                    auto_am1[0]=h1;      //this.ampm_time_check(split_ap,"am1",sec_index);   
                                    auto_am1[1]=m1;
                                    return auto_am1;
                                }
                                if(name==".am2"){
                                   let auto_am2=[];
                                
                                    auto_am2[0]=h1;           //this.ampm_time_check(split_ap,"am2",sec_index);   
                                    auto_am2[1]=m1;  
                                    return auto_am2;
                                }
                                if(name==".pm1"){
                                    let auto_pm1=[];
                                
                                    auto_pm1[0]=h1;            //this.ampm_time_check(split_ap,"pm1",sec_index);  
                                    auto_pm1[1]=m1;  
                                    return auto_pm1;                            
                                }
                                if(name==".pm2"){
                                   let auto_pm2=[];
                                
                                    auto_pm2[0]=h1;            //this.ampm_time_check(split_ap,"pm2",sec_index);  
                                    auto_pm2[1]=m1;  
                                    return auto_pm2;
                                }       
                            }
                        } 
                        return '';
            },  
            totalHourCal(auto_am1,auto_am2,auto_pm1,auto_pm2,total_am,total_pm,t_am,p_am){

                let late_coming=0,leaving_early=0;
                if(auto_am1!='' && auto_am1!=undefined && (  ( parseInt(auto_am1[0])==8 && 5<parseInt(auto_am1[1]) || 9<=parseInt(auto_am1[0])   )   ) ){
                    late_coming+=(+auto_am1[0] + (+auto_am1[1] / 60))-(8);  
                     
                }  
                if(auto_pm1!='' && auto_pm1!=undefined &&  (  ( parseInt(auto_pm1[0])==13 && 15<parseInt(auto_pm1[1]) || 14<=parseInt(auto_pm1[0])   )   ) ){
                    late_coming+=(+auto_pm1[0] + (+auto_pm1[1] / 60))-(13);  
                     
                }
                if(auto_am2!='' && auto_am2!=undefined && parseInt(auto_am2[0])<12){
                    leaving_early+=12-(+auto_am2[0] + (+auto_am2[1] / 60));
                     
                }
                if(auto_pm2!='' && auto_pm2!=undefined && parseInt(auto_pm2[0])<17){
                    leaving_early+=17-(+auto_pm2[0] + (+auto_pm2[1] / 60));
                      
                }
                if(t_am!='' && t_am!=null){
                    total_am=4;
                }else if(auto_am1!='' && auto_am2!='' && auto_am1!=undefined && auto_am2!=undefined){
                    total_am= (+auto_am2[0] + (+auto_am2[1] / 60))-(+auto_am1[0] + (+auto_am1[1] / 60));  
                    if(total_am<0){
                        total_am=0;
                    }                
                }
                if(p_am!=''  && p_am!=null){
                    total_pm=4;
                }else if(auto_pm1!='' && auto_pm2!=''  && auto_pm1!=undefined && auto_pm2!=undefined){
                    total_pm=(+auto_pm2[0] + (+auto_pm2[1] / 60))-(+auto_pm1[0] + (+auto_pm1[1] / 60));  
                     if(total_pm<0){
                        total_pm=0;
                    }                
                }
                return {
                    t_hr: total_am==''?0:total_am,
                    t_min: total_pm==''?0:total_pm,
                    late_coming:late_coming,
                    leaving_early:leaving_early,
                };            
            }, 
            ampm_time_check(ap_split,name,sec_index){
                
                if(ap_split===''){ 
                      return false;   
                }

                if( (name==".am2" || name==".pm2") && (ap_split[1]>0 && ap_split[1]<30 ) ){
                    // ap_split[0]=12;
                    ap_split[1]="00";     
                }else if((name==".am2" || name==".pm2") && (ap_split[1]>30 && ap_split[1]<60 ) ){
                    // ap_split[0]=17;
                    ap_split[1]="30";  
                }else{
                    if( (ap_split[0]<8 && ap_split[1]<60) || (ap_split[0]==8 && ap_split[1]<6) ){
                        ap_split[0]=8;ap_split[1]="00";     
                    }else if(ap_split[0]==13 && ap_split[1]<16){
                        ap_split[0]=13;ap_split[1]="00"; 
                    }else if( ( ap_split[0]==8  && ( ap_split[1]>=6 && ap_split[1]<=30) ) || (ap_split[0]>8 && (ap_split[1]>0 && ap_split[1]<=30) )    ) {
                        ap_split[0]=ap_split[0];ap_split[1]="30"; 
                    }else if(ap_split[0]>=8 && (ap_split[1]>=30 && ap_split[1]<=60 ) ){
                        ap_split[0]=parseInt(ap_split[0])+1;ap_split[1]="00"; 
                    }else if(ap_split[0]==13 && (ap_split[1]>=16 && ap_split[1]<=30 ) ){
                        ap_split[0]=13;ap_split[1]="30"; 
                    }   
                } 
                    jQuery("."+sec_index).find(name).val((ap_split[0].toString().length==1?"0"+ap_split[0]:ap_split[0])+":"+ap_split[1]);
                    return ap_split!=''?ap_split:'';
            },
            csvOutput(employee,select_date){
                   let that=this;
                   let where_data={
                        "emp_no":this.emp_no,
                        "date":this.select_date,
                        "year":this.year,
                        "month":this.month,
                    };
                    this.axios({
                      url:process.env.MIX_APP_URL+"/attendList/getmonth",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/attendList/getmonth",
                      method: 'post',
                      data:where_data,
                    })                 
                    .then(response=>{      
                        if(response!='' && response.data.length!=0 ){

                            let split_string =[]; let date;
                            split_string = select_date.split("/");
                            date = split_string[0] + split_string[1];
                            const link = document.createElement('a');
                            link.href = process.env.MIX_APP_URL+'/attendList/csvOutput/'+employee+'/'+ date;
                            document.body.appendChild(link);
                            link.click();

                        }else{
                                that.$fire({
                                title: "Fail！！",
                                text: "No data to display.",//データはありませんでした。
                                type: "error",
                                timer: 3500,
                                showCancelButton: false,
                                showConfirmButton: false,                              
                                }).then(r => {                             
                                }); 
                        }
                    }).catch(() => console.log('error occured'))         

            },          
        }, 
    }
</script>