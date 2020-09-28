<template>
    <div class="col-md-10">            
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4"> 
                    <select class="form-control" id="selectDate"  @change="dateChange($event)" name="date_selected" required focus v-model="select_date">
                        <option value=null disabled selected>Please select Year/Month</option>        
                        <option v-bind:key="date.id" v-for="date in dates">{{ date.recordedDateTime }}</option>   
                    </select>              
                </div>
            </div>

            <div class="alert alert-success mt-5" role="alert" v-if="data_check_messg">
                 <strong >データは成功しました。</strong> 
            </div>

            <table class="table table-bordered mt-5" v-if="formChange">
                <thead>
                    <tr>
                        <th scope="col">年月</th>
                        <th scope="col">支給日</th>
                        <th scope="col">JPN/MMK</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="d in attendDelays" :key="d.id">
                        <td>
                            {{ d.month}}
                        </td>
                        <td>
                            {{ paymentDate(d.month)}}
                        </td>
                        <td>
                            <div class="row" v-if="d.moneyDelayError">
                                <div class="col text-danger">{{d.moneyDelayErrorMsg}}</div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.money">
                                </div>
                                <div class="col"><button type="button" class="btn btn-primary" @click="updateDelayMoney(d.id ,d)" onclick="this.blur();">編集</button></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        
        <div class="container-fluid" v-if="!formChange">
                <div class="row" >
                    <div class="col-md-4 mt-4"> 
                        <h4><strong>給与手当一覧　{{this.select_date}}分</strong></h4>      
                    </div>
                </div>
                <!-- <form id="form" class=null @submit.prevent="SalarySave"  autocomplete="on"> -->
                <div class="row justify-content-md-center mt-4"> 
                    <button type="button" @click="engineerCost" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">エンジニアコスト一覧表</button>
                      
                    <button data-toggle="modal" :disabled='payslipBtnDisable' data-target="#payslip" class="btn mr-3" style="background-color:#E7E6E6" onclick="this.blur();">
                        給与明細作成
                    </button>
                        
                    <button type="submit" form="form" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">編集</button>

                </div>
                 <form id="form" class=null @submit.prevent="SalarySave"  autocomplete="on">
                
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

                <div class="alert alert-danger" role="alert" v-if="data_check_messg1"  id="check-alert"   style="text-align: center">
                     <button type="button" class="close" data-dismiss="alert">x</button>
                    <strong >データはありませんでした。</strong> 
                </div>
            
                <!-- Modal -->
                <div class="modal fade" data-backdrop="false" id="payslip" tabindex="-1" role="dialog" aria-labelledby="payslipTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">    
                             <div class="modal-body">
                                <div class="container-fluid">                           
                                    <table class="table table-sm table-bordered">
                                        <thead>                      
                                            <th class="align-middle text-center">
                                                <input class="align-middle text-center check-all" style="width:1.5em;height:1.5em;" type='checkbox' @click='checkAll()' v-model='isCheckAll'> 
                                            </th>
                                            <th class="align-middle text-center">
                                                社員番号
                                            </th>
                                            <th class="align-middle text-center">
                                                名前
                                            </th>                        
                                        </thead>    
                                        <tbody>             
                                            <tr v-bind:key="key" v-for='(salary,key) in salaries'>
                                                <td class="align-middle text-center">
                                                           <input type='checkbox' name="checks[]" class="align-middle text-center" style="width:1.5em;height:1.5em;" v-bind:value='salary' v-model='salarymodal' @change='updateCheckall()'>
                                                            <input type='hidden' name="pay_empid[]" class="align-middle text-center pay_empid" style="width:1.5em;height:1.5em;" v-bind:value='salary.emp_id'>
                                                </td>
                                                <td class="align-middle text-center">
                                                            {{salary.emp_code}}
                                                </td>    
                                                <td class="align-middle text-center">
                                                            {{salary.emp_name}}
                                                        <div>({{salary.kana_name}})</div>
                                                </td>    
                                            </tr>    
                                        </tbody>   
                                    </table>   

                                    <footer class="col-sm-9 offset-sm-2 text-center mt-5">
                                        <button type="button" @click="payslipSubmit" class="btn btn-primary">生成</button>
                                        <button type="button" class="btn btn-secondary"  style="margin-left: 1em;margin-right: -5em;" data-dismiss="modal">キャンセル</button>
                                        <!-- <button @click="dialogClose()" type="button" class="btn btn-secondary" style="margin-left: 2em;margin-right: -4em;">キャンセル</button> -->
                                    </footer>
                                 </div> 
                            </div>
                        </div>
                    </div>
                </div>  
                
                <div class="row" >
                    <div class="col-md-9">                      
                     
                        <form id="form1" class=null  autocomplete="on" >
                         
                            <div class="scrolling-wrapper  flex-row flex-nowrap">                         
                                <table id="salaryTable" class="table table-sm table-md table-bordered">
                                    <span class="col-md-2 mt-4 table-borderless" style="background-color:#DEEBF7"> 
                                            {{this.paymentDate(this.select_date,1)}}     
                                    </span>
                                    <span class="col-md-2 mt-4 table-borderless"> 
                                            支給
                                    </span>  
                                    <tr>
                                        <th  class="border-bottom-0">
                                        
                                        </th>
                                        <th rowspan="3"  class="align-middle text-center">
                                            名前
                                        </th>
                                        <th rowspan="3" class="align-middle text-center">

                                        </th>
                                        <th colspan="5"  style="background-color:#FBE5D6" class="align-middle text-center">
                                            控除前
                                        </th>
                                        <th colspan="3"   style="background-color:#D9D9D9" class="align-middle text-center">
                                            控除額
                                        </th>
                                        <th colspan="3"  style="background-color:#D9D9D9" class="align-middle text-center">
                                            その他調整(控除の場合-)
                                        </th>
                                        <th rowspan="2" style="text-align: center;background-color:#DAE3F3"  class="align-middle text-center border-bottom-0">
                                            支給額
                                        </th>
                                    </tr>
                                    <tr>  
                                        <th   class="align-middle text-center border-bottom-0 border-top-0">
                                            従業員番号
                                        </th>
                                        <th  rowspan="2"  class="text-center align-middle"  style="background-color:#FBE5D6;">
                                            基本給
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                            通勤交通費
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                            JLPT
                                        </th>
                                        <th  rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                            ボーナス
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                            合計
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                            所得税
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                            SSB
                                        </th>
                                        <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                            遅刻欠勤早退
                                        </th> 
                                        <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;">

                                        </th>
                                        <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;">
                                            
                                        </th>
                                        <th rowspan="2" class="text-center" style="background-color:#D9D9D9;">
                                            
                                        </th>
                                        
                                    </tr>
                                    <tr>
                                        <th  class="border-top-0">
                                        </th>
                                        <th class="align-middle text-right border-top-0 cal_salaries" style="background-color:#DAE3F3;text-align:right;height: 34px;" v-text="total_payment">                                      
                                        </th>  
                                    </tr>

                                    <tbody>                              
                                        <template  v-for="(salary,key) in salaries">                                  
                                            
                                                    <tr v-bind:key="key">
                                                        <td rowspan="2"  class="text-center align-middle">{{emp_arr[salaries[key].emp_id]}}</td>
                                                        <td rowspan="2" class="text-center align-middle">{{name_arr[salaries[key].emp_id]}}<div>({{salaries[key].kana_name}})</div></td>
                                                        <td style="background-color:#D9D9D9" class="text-center align-middle">計算値</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle income1">{{Math.trunc(salaries[key].salary_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle trans_money1">{{(salaries[key].trans_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle jlpt1">{{(salaries[key].jlpt).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                                
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle total_salary1">{{parseInt(salaries[key].total).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle inc_tax1">{{salaries[key].income_tax}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle ssb1">{{salaries[key].ssb!=(undefined || 0)?salaries[key].ssb+"%":null}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle leave_late1">
                                                            {{salaries[key].late_leave_money!=(undefined || 0)?(salaries[key].late_leave_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):null}}
                                                        </td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">
                                                        </td>
                                                    </tr>
                                                    <tr v-bind:key="'A'+key" :class="`index_${key}`">
                                                        <td class="text-center align-middle">実際 
                                                            <input name="pay_month[]"  class="pay_month" style="text-align:right;width:100px;" type="hidden" :value="`${year}/${month}`">
                                                            <input name="employee_id[]"  class="employee_id" style="text-align:right;width:100px;" type="hidden" :value="`${salaries[key].emp_id}`">
                                                        </td>
                                                        <template v-if="get_salary_data.length==0">
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- @value="`${old(income)}`" -->
                                                            <input name="income[]" @change="updateInput" class="income" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${salaries[key]}`!=undefined?Math.trunc(salaries[key].salary_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="trans_money[]" @change="updateInput" class="trans_money" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${salaries[key]}`!=undefined?(salaries[key].trans_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="jlpt[]"  @change="updateInput" class="jlpt" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${salaries[key]}`!=undefined?(salaries[key].jlpt).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="bonus[]" @change="updateInput" class="bonus" style="text-align:right;width:100px;padding-right: 3px;" type="text" @value="`${old(bonus)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                    <!-- name="total_salary[]" @change="updateInput" -->
                                                                <input class="total_salary" readonly name="total_salary[]" style="text-align:right;width:150px;padding-right: 3px;" type="text"  :value="`${salaries[key]}`!=undefined?(parseInt(salaries[key].total)).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                                
                                                            <!-- 控除額 -->
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="income_tax[]" @change="updateInput"  class="inc_tax" style="text-align:right;width:100px;padding-right: 3px;" type="text"  @value="`${old(income_tax)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="ssb[]" @change="updateInput" class="ssb" style="text-align:right;width:100px;padding-right: 3px;" type="text" 
                                                                :value="
                                                                `${salaries[key].ssb}`!=(undefined || 0)? ( SsbMax*(salaries[key].ssb/100) ) :null 
                                                                ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="leave_late[]" @change="updateInput" class="leave_late" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${salaries[key].late_leave_money!=(undefined || 0)?(salaries[key].late_leave_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null}`">
                                                            </td>

                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju1[]" @change="updateInput" class="adju1" style="text-align:right;width:100px;" type="text" @value="`${old(adju1)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju2[]" @change="updateInput" class="adju2" style="text-align:right;width:100px;" type="text" @value="`${old(adju2)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju3[]" @change="updateInput" class="adju3" style="text-align:right;width:100px;" type="text" @value="`${old(adju3)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- :value="``" -->
                                                                <input name="payment_amount[]" readonly class="payment_amount" style="text-align:right;width:150px;padding-right: 3px;" type="text" 
                                                                :value="`${salaries[key].payment}`.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')">
                                                                    <!-- :value="`${(parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)+parseInt(salaries[key].jlpt))-(parseInt(salaries[key].ssb))}`.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')"> -->
                                                            </td>
                                                        </template>
                                                        <template v-else>                                              
                                                                                                                    
                                                                <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="id[]" class="form-control input-sm idx1" style="text-align: center;" type="hidden" :value="`${get_salary_data[key]}`!=undefined?get_salary_data[key].id:null ">   
                                                                <input name="income[]" @change="updateInput" class="income" :style="`background-color:${checkBgColor(get_salary_data[key].income,salaries[key].salary_amount)};text-align:right;width:100px;padding-right: 3px;`" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].income).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,') :null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="trans_money[]" @change="updateInput" class="trans_money" :style="`background-color:${checkBgColor(get_salary_data[key].trans_money,salaries[key].trans_money)};text-align:right;width:100px;padding-right: 3px;`" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].trans_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="jlpt[]"  @change="updateInput" class="jlpt" :style="`background-color:${checkBgColor(get_salary_data[key].jlpt,salaries[key].jlpt)};text-align:right;width:100px;padding-right: 3px;`" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].jlpt).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="bonus[]" @change="updateInput" class="bonus" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].bonus).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                    <!-- name="total_salary[]" @change="updateInput" -->
                                                                <input class="total_salary" readonly name="total_salary[]" :style="`background-color:${checkBgColor(get_salary_data[key].total_salary,salaries[key].total)};text-align:right;width:100px;padding-right: 3px;`" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].total_salary).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>

                                                            <!-- 控除額 -->
                                                            <td class="text-right align-middle" style="padding: 0px;">                                                              
                                                                <input name="income_tax[]" @change="updateInput" class="inc_tax" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].income_tax).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="ssb[]" @change="updateInput" class="ssb" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].ssb).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="leave_late[]" @change="updateInput" class="leave_late" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].leave_late).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>

                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju1[]" @change="updateInput" class="adju1" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju1).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju2[]" @change="updateInput" class="adju2" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju2).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju3[]" @change="updateInput" class="adju3" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju3).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- :value="``" -->
                                                                <input name="payment_amount[]" readonly class="payment_amount" style="text-align:right;width:150px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].payment_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                            </td>
                                                        </template>    
                                                    </tr>                                    
                                        </template>
                                    </tbody>
                                </table>    
                            </div>                        
                        </form>   
                    </div>
                  
                    <div class="col-md-3">
                        <span  class="col-md-2 mt-4"></span>
                        <form id="form2" class=null autocomplete="on" >
                            <div class="scrolling-wrapper  flex-row flex-nowrap">
                        
                                <table id="ssbtable" class="table table-sm table-bordered" style="width: 300px;">
                                    <tr>
                                        <th  colspan="2" class="align-middle text-center">
                                                SSB
                                        </th>
                                        <th  rowspan="3" class="align-middle text-center">
                                                備考
                                        </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center" style="text-align: center;">
                                                総額
                                        </th>
                                        <th   class="align-middle text-center border-bottom-0" style="background-color:yellow">
                                                会社負担分
                                        </th>
                                    </tr>
                                    <tr>
                                        <th   class="align-middle text-right border-top-0 paid-ssb" style="background-color:yellow;height: 31px;"  v-text="total_c_paid">
                                                                                    
                                        </th>   
                                    </tr>
                                    <tbody>                             
                                        <template  v-for="key in salaries.length">
                                                    
                                            <tr v-bind:key="key">
                                        
                                            <td style="padding: 0px;width: 30%;text-align: right;background-color:#D9D9D9" class="ssb_total1">{{SsbPaid.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                        
                                            <td style="padding: 0px;width: 30%;text-align: right;background-color:#D9D9D9" class="ssb_c_paid1">{{SsbPaid.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                            <td style="padding: 0px;width: 40%;" rowspan="2">
                                                    <template v-if="get_salary_data.length==0"> 
                                                        <textarea name="remark[]" class="remark" style="text-align:left;width:100%;border: none;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" @value="`${old(remark)}`"></textarea>
                                                    </template>    
                                                    <template v-else> 
                                                        <textarea name="remark[]" class="remark" style="text-align:left;width:100%;border: none;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" :value="`${get_salary_data[key-1].ssbval}`!=undefined?get_salary_data[key-1].ssbval.remark:null "></textarea>
                                                    </template>
                                                    <!-- <input name="remark[]"  class="remark" style="text-align:left;width:100px;" type="text" :value="``"> -->
                                            </td>
                                        </tr>  
                                            <template v-if="get_salary_data.length==0"> 
                                                <tr v-bind:key="'A'+key" :class="`index_${key-1}`">
                                                    <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">
                                                            <input name="ssb_total[]" @change="ssbCalc" class="ssb_total" style="text-align:right;width:100%;" type="text" :value="`${SsbPaid.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')}`">
                                                    </td>
                                                    <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">
                                                            <input name="ssb_c_paid[]" @change="ssbCalc"  class="ssb_c_paid" :style="`background-color:${checkBgColor(SsbPaid,(SsbMax*(5-salaries[key-1].ssb)/100))};text-align:right;width:100px;`"  type="text" :value="`${(SsbMax*(5-salaries[key-1].ssb)/100).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')}`">
                                                    </td>
                                                </tr>     
                                            </template>
                                            <template v-else>
                                                <tr v-bind:key="'A'+key" :class="`index_${key-1}`">
                                                    <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">    
                                                            <input name="id[]" class="form-control input-sm idx2" style="text-align: center;" type="hidden" :value="`${get_salary_data[key-1].ssbval}`!=undefined?get_salary_data[key-1].id:null ">                                              
                                                            <input name="ssb_total[]" @change="ssbCalc" class="ssb_total" :style="`background-color:${checkBgColor(get_salary_data[key-1].ssbval.total_amount,SsbPaid)};text-align:right;width:100px;`"  type="text" :value="`${get_salary_data[key-1].ssbval}`!=undefined?Math.trunc(get_salary_data[key-1].ssbval.total_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                    </td>
                                                    <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">                                             
                                                            <input name="ssb_c_paid[]" @change="ssbCalc"  class="ssb_c_paid" :style="`background-color:${checkBgColor(get_salary_data[key-1].ssbval.c_paid,SsbPaid)};text-align:right;width:100px;`"  type="text" :value="`${get_salary_data[key-1].ssbval}`!=undefined?Math.trunc(get_salary_data[key-1].ssbval.c_paid).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):null ">
                                                    </td>
                                                </tr>     
                                            </template>
                                        </template>
                                    </tbody>    
                                </table>
                            </div>                
                       </form>
                    </div> 
                </div>
            </form>    
        </div>
    </div>    
</template>

<script>   

    export default {
        
        data() {
            return {             
                select_date:null,
                dates:[],
                setting:{},
                settings: [],
                delays:[],
                attendDelays:[],
                temp: [],
                data_check_messg:false,
                data_check_messg1:false,
                formChange:true,
                year:null,
                month:null,
                salaries:[],
                emps:[],
                emp_arr:[],
                name_arr:[],
                errors:null,
                focusRecord:null,
                ssbs:null,
                error_check_messg:false,
                get_salary_data:[],
                total_payment:null,           
                total_c_paid:null,
                isCheckAll: false,                
                salarymodal:[],
                selected:null,
                payslipBtnDisable: false,
            }
        },    
        computed: {
           errorsFun:function(){
                this.error_check_messg = true 
                return this.errors;
            },         
        },  
        mounted(){
           this.checkBgColor();
        },  
        watch : {
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
        created() {
            let that=this;
         
            this.axios({
                url:process.env.MIX_APP_URL+ "/settings",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/settings",
                method: 'get'
            })
            .then(function (response) {
                that.setting=response.data;
            })
            .catch(function (error) {
            });

            this.axios({
                url:process.env.MIX_APP_URL+"/setting/all",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/all",
                method: 'get'
            })
            .then(function (response) {
                that.settings=response.data;
            })
            .catch(function (error) {
            });

            this.axios({
                url:process.env.MIX_APP_URL+"/delayTimes",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTimes",
                method: 'get'
            })
            .then(function (response) {
                that.delays=response.data;
                that.delayDataCalculate();
            })
            .catch(function (error) {
            });

            this.axios
                .get('http://localhost:5000/attendances/all/date')
                .then(response => {
                    this.dates=response.data;
            }); 
            this.axios
                .get('http://localhost:5000/employees')
                .then(response => {
                
                    that.emps=response.data;
                   if(that.emps){
                  
                       for(let v in that.emps){                        
                           that.emp_arr[that.emps[v].id]=that.emps[v].employeeId;
                           that.name_arr[that.emps[v].id]=that.emps[v].name;
                       }
                     
                   }
                  
            });            
        },
        methods: { 
            engineerCost:function(){
                    let that=this;
                    let up_data={                       
                        "pay_month":that.select_date,
                    };
                    this.axios({
                      url:process.env.MIX_APP_URL+"/salaryList/getsalary",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/getsalary",
                      method: 'post',
                      data:up_data,
                    })                  
                    .then(response=>{                        
                        if(response!=null && response.data.length!=0 ){
                            const url = (window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/download/"+this.year+"-"+this.month;
                            const link = document.createElement('a')
                            link.href = url
                            // link.setAttribute('download',null ) // , 'file.png' or any other extension
                            document.body.appendChild(link)
                            link.click()
                        }else{
                                that.$fire({
                                title: "失敗！！",
                                text: "データはありませんでした。",
                                type: "error",
                                timer: 3500,
                                showCancelButton: false,
                                showConfirmButton: false,                              
                                }).then(r => {                             
                                }); 
                        }
                    })
                    .catch(() => console.log('error occured'))
            },
   
            payslipSubmit:function(){
                var empArray = [];
                $.each($("input[name^='checks']:checked"), function(){                      
                    empArray.push($(this).parent().parent().find('.pay_empid').val());
                });

                const link = document.createElement('a')
                let url =  (window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salary/export/"+ this.year+'-'+ this.month+'/'+ empArray;
                    
                //link.href =process.env.MIX_APP_API_URL+'/salary/export/'+ this.year+'-'+ this.month+'/'+ empArray
                link.href = url;
                document.body.appendChild(link)
                link.click()
                
            },
            checkAll: function(){

                this.isCheckAll = !this.isCheckAll;
                this.salarymodal = [];
                if(this.isCheckAll){ // Check all
                    for (var key in this.salaries) {
                        this.salarymodal.push(this.salaries[key]);
                    // this.salarymodal.push(key,{"emp_id":this.salaries[key].emp_id,"emp_code":this.salaries[key].emp_code,"emp_name":this.salaries[key].emp_name});                  
                    }
                }
               
            }, 
            updateCheckall: function(salary,salarymodal){               
                if(this.salarymodal.length == this.salaries.length){
                    this.isCheckAll = true;
                }else{
                    // $("input[class='check-all']").prop('checked', false); 
                    this.isCheckAll = false;
                }
            },  
            checkBgColor:function(main,update){    
                    if(parseInt(main)!=parseInt(update) && !isNaN(main) && !isNaN(update) ){
                        return 'yellow';
                    }else{
                        return null;
                    }
            }, 
            paymentDate(month,date_flash=null){
                var futureMonth = new Date(month);
                futureMonth.setMonth(futureMonth.getMonth() + 1);
                futureMonth.setDate(futureMonth.getDate()+ 9);
                if(date_flash!=null){
                   return moment(futureMonth).format('YYYY/MM/DD');     
                }else
                   return moment(futureMonth).format('MM/DD/YYYY');
            },
            updateDelayMoney(id , delayTime){
                if(delayTime.money == 0){
                    Vue.set(delayTime,"moneyDelayErrorMsg", "JPN/MMKを入力してください。");
                    Vue.set(delayTime,"moneyDelayError",true);
                    return ;
                }else if(!this.validateDecimal(delayTime.money) ){
                    Vue.set(delayTime,"moneyDelayErrorMsg", "JPN/MMKを数式で入力してください。");
                    Vue.set(delayTime,"moneyDelayError",true);
                    return ;
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/delayTime/updateMoney/"+id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTime/updateMoney/"+id,
                    method: 'post',
                    data: delayTime
                })
                .then(function (response) {
                    that.delays = response.data;
                    that.delayDataCalculate();

                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
            },
            delayDataCalculate(){
                this.attendDelays = [];
                this.temp = [];
                let results = [];
                if(this.delays.length == 0){
                    
                    this.dates.forEach(d => {
                        let tempSetting  = this.settings.filter(s => s.create_month < d.recordedDateTime);
                        tempSetting.sort(compareSetting);

                        if(tempSetting == 0){
                            let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am = this.setting.am;
                            data1.pm = this.setting.pm;
                            data1.money = this.setting.money;
                            this.attendDelays.push(data1);
                        }else {
                            let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am = tempSetting[tempSetting.length-1].am;
                            data1.pm = tempSetting[tempSetting.length-1].pm;
                            data1.money = tempSetting[tempSetting.length-1].money;
                            this.attendDelays.push(data1);
                        }
                    })
                     
                }else{
                    this.dates.forEach(d => {
                        let tempSetting  = this.settings.filter(s => s.create_month < d.recordedDateTime);
                        tempSetting.sort(compareSetting);

                        let sameMonth = this.delays.filter(a => a.month == d.recordedDateTime);
                        if(sameMonth.length == 0){
                            let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am =  tempSetting[tempSetting.length-1].am;
                            data1.pm =  tempSetting[tempSetting.length-1].pm;
                            data1.money =  tempSetting[tempSetting.length-1].money;
                            this.temp.push(data1);
                        }
                    })
                    this.attendDelays = this.delays.concat(this.temp);
                }
                function compare(a, b) {
                    if (a.month < b.month)
                        return -1;
                    if (a.month > b.month)
                        return 1;
                    return 0;
                }

                function compareSetting(a, b){
                    if (a.create_month < b.create_month)
                        return -1;
                    if (a.create_month > b.create_month)
                        return 1;
                    return 0;
                }

                this.attendDelays.sort(compare);
            },
            validateDecimal: function(number){
                var re = /^-?\d+(?:\.?\d+)?$/;
                return re.test(number);
            },
            SalarySave:function(){
                let that=this;
                // let those=this;
                var validator1 = jQuery('#form1').validate({
                  
                    rules: {
                            "pay_month[]": "required",
                            "income[]": "required",
                            // "trans_money[]": null,
                            // "jlpt[]": null,
                            // "bonus[]": null,
                            "total_salary[]":"required",
                            "income_tax[]":"required",
                            // "ssb[]":null,
                            // "leave_late[]":null,
                            "payment_amount[]":"required",
                    },  
                    messages: {                                
                            // "income[]": {
                            //     required: '基本給は必要です。',
                            // },     
                            // "total_salary[]": {
                            //     required: "合計は必要です。",    
                            // },     
                            // "income_tax[]": {
                            //     required: "所得税は必要です。",    
                            // },
                            // "payment_amount[]": {
                            //     required: "支給額は必要です。",    
                            // },
                    },
                    errorPlacement: function(error,element) {
                        return true;
                    },
                });

                
                var validator2 = jQuery('#form2').validate({
                  
                    rules: {
                            "ssb_total[]": "required",
                            "ssb_c_paid[]": "required",
                            // "remark[]": null,
                    }, 
                    errorPlacement: function(error,element) {
                        return true;
                    },
                });
                let temp_salary=[],pay_month=0,employee_id=0,income=0,trans_money=0,jlpt=0,bonus=0,inc_tax=0,ssb=0,leave_late=0,payment_amount=0,total_salary=0,adju1=null,adju2=null,adju3=null;//,
                let temp_ssb=[],ssb_total=0,ssb_c_paid=0,remark=null,id1=null,id2=null;
                if(jQuery('#form1').valid() && jQuery('#form2').valid()){
                    
                    // if(jQuery('#form1').valid()){
                        $('#salaryTable tr[class^=index_]').each(function (key,value) {
                            let that=this;   
                            $(that).find('td').each(function (index,v2) {
                             
                                    let isLastElement = index == $(that).find('td').length-1;
                                     
                                    if(jQuery(this).find('.pay_month').val()){                                       
                                        pay_month=jQuery(this).find('.pay_month').val();
                                    }
                                    if(jQuery(this).find('.employee_id').val()){                                      
                                        employee_id=jQuery(this).find('.employee_id').val();
                                    }
                                    if(jQuery(this).find('.income').val()){                                     
                                        income=jQuery(this).find('.income').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.trans_money').val()){
                                        trans_money=jQuery(this).find('.trans_money').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.jlpt').val()){
                                        jlpt=jQuery(this).find('.jlpt').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.bonus').val()){
                                        bonus=jQuery(this).find('.bonus').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.total_salary').val()){
                                        total_salary=jQuery(this).find('.total_salary').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.inc_tax').val()){
                                        inc_tax=jQuery(this).find('.inc_tax').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.ssb').val()){
                                        ssb=jQuery(this).find('.ssb').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.leave_late').val()){
                                        leave_late=jQuery(this).find('.leave_late').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.adju1').val()){
                                        adju1=jQuery(this).find('.adju1').val().toString().replace(/,/g , null);
                                    }
                                      if(jQuery(this).find('.adju2').val()){
                                        adju2=jQuery(this).find('.adju2').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.adju3').val()){
                                        adju3=jQuery(this).find('.adju3').val().toString().replace(/,/g , null);
                                    }
                                    if(jQuery(this).find('.payment_amount').val()){
                                        payment_amount=jQuery(this).find('.payment_amount').val().toString().replace(/,/g , null);                                       
                                    }
                                    if(jQuery(this).find('.idx1').val()){
                                       id1=jQuery(this).find('.idx1').val();
                                    }
                                    if (isLastElement) {                                        
                                        if(id1!=null){
                                              temp_salary.push({"id":id1,"pay_month":pay_month,"employee_id":employee_id,"income":income,"trans_money":trans_money,"jlpt":jlpt,"bonus":bonus,"income_tax":inc_tax,"ssb":ssb,"leave_late":leave_late,"payment_amount":payment_amount,"total_salary":total_salary,"adju1":adju1,"adju2":adju2,"adju3":adju3});//
                                        }else{
                                              temp_salary.push({"pay_month":pay_month,"employee_id":employee_id,"income":income,"trans_money":trans_money,"jlpt":jlpt,"bonus":bonus,"income_tax":inc_tax,"ssb":ssb,"leave_late":leave_late,"payment_amount":payment_amount,"total_salary":total_salary,"adju1":adju1,"adju2":adju2,"adju3":adju3});//
                                        }
                                      
                                        id1=null;pay_month=0;employee_id=0;income=0;trans_money=0;jlpt=0;bonus=0;inc_tax=0;ssb=0;leave_late=0;payment_amount=0;total_salary=0;adju1=null;adju2=null;adju3=null;
                                        return false;
                                    }
                            })                          
                        })
                    // }

                    // if(jQuery('#form2').valid()){
                        $('#ssbtable tr[class^=index_]').each(function (key,value) {
                            let that=this;
                            $(that).find('td').each(function (index,v2) {
                                let isLastElement = index == $(that).find('td').length-1;
                                    
                                if(jQuery(this).find('.ssb_total').val()){
                                    ssb_total=jQuery(this).find('.ssb_total').val().toString().replace(/,/g , null);
                                }
                                if(jQuery(this).find('.ssb_c_paid').val()){
                                    ssb_c_paid=jQuery(this).find('.ssb_c_paid').val().toString().replace(/,/g , null)
                                }                              
                                if(jQuery(this).closest("tr").prev('tr').find('.remark').val()){
                                    remark=jQuery(this).closest("tr").prev('tr').find('.remark').val();
                                }
                                if(jQuery(this).find('.idx2').val()){
                                    id2=jQuery(this).find('.idx2').val();
                                }
                                if (isLastElement) {
                                    if(id2!=null){
                                         temp_ssb.push({"id":id2,"total_amount":ssb_total,"c_paid":ssb_c_paid,"remark":remark});
                                    }else{
                                         temp_ssb.push({"total_amount":ssb_total,"c_paid":ssb_c_paid,"remark":remark});
                                    }
                                   
                                    id2=null;ssb_total=0;ssb_c_paid=0;remark=null;
                                    return false;
                                }
                            });
                        });
                    // }
                    


                }else{
                     that.$fire({
                        title: "失敗！！",
                        text: "データを記入してください。",
                        type: "warning",
                        timer: 3000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        // position:'center',
                        }).then(r => {                      
                        }); 
                    return false;
                }
              
                this.axios({
                      url:process.env.MIX_APP_URL+"/salaryList",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList",
                      method: 'post',
                      data: {"form1": temp_salary,"form2": temp_ssb}
                    })
                    .then(function (response) {                     

                        // your action after success                      
                        if(response.data){                            
                            if(response.data.message==true){
                                that.salaries=[];
                                that.get_salary_data=[];                            
                                that.update_call();  
                                that.$fire({
                                    title: "成功しました",
                                    text: "データの登録は成功しました。",
                                    type: "success",
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    // position:'center',
                                }).then(r => {                                
                                });
                            }else{
                                that.$fire({
                                title: "失敗！！",
                                text: "データの登録は出来ません。",
                                type: "error",
                                timer: 1500,
                                showCancelButton: false,
                                showConfirmButton: false,
                                // position:'center',
                                }).then(r => {                             
                                }); 
                                that.errors=response.data.errors;                             
                            } 
                        }
                    })
                    .catch(function (error) {
                    });
            },
            dateChange:function($event){
                let that=this;let val=null;
                
                
                if(event.target.value!=null){

                   
                        that.$swal({
                        title: 'お待ちください!',
                        // add a custom html tags by defining a html method.
                        html: 'Loading......',
                        // timer: 3000,
                        showCloseButton: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        focusConfirm: false,
                        allowOutsideClick: false,
                         onBeforeOpen: () => {
                            that.$swal.showLoading();
                            },
                        })                

                    that.formChange=false;
                    val=event.target.value;

                    let split_date=val.split("/");                  
                    this.year=split_date[0];
                  
                    val=val.replace(this.year+"/",null);
                    this.month=val;
                
                    that.total_payment=null;
                    that.company_ssb=null,
                    that.total_c_paid=null,
                  
                    that.update_call();
                    // that.$swal.close();

                }else{
                    return false;
                }
            },
            update_call:function(){               
                    let that=this;  
                    let up_data={                       
                        "pay_month":that.select_date,
                        // "employee_id ":
                    };
                    this.axios({
                      url:process.env.MIX_APP_URL+"/salaryList/getsalary",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/getsalary",
                      method: 'post',
                      data:up_data,
                    })                  
                    .then(response=>{                                        
                        that.get_salary_data=response.data;                      
                        that.salaryList(that.get_salary_data);                  
                    })
                    .catch(function (error) {
                        that.$swal.close();                      
                    }); 
            },
            salaryList:function(get_salary_data=null){
                    this.payslipBtnDisable = false;
                    let that=this;  
                    let tem_salary=[];
                    let salaries_arr=[];
                    let total_payment=0;
                    // let company_ssb=0;
                    let total_c_paid=0;
                    this.axios
                    .get((window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/"+this.year+"-"+this.month )                 
                    .then(response => {
                        // that.salaries=response.data;
                        tem_salary=response.data;                     
                        if(get_salary_data==null){
                            for(let i=0;i<tem_salary.length;i++){
                            
                                tem_salary[i]['total']=(parseInt(tem_salary[i].salary_amount)+parseInt(tem_salary[i].trans_money)+parseInt(tem_salary[i].jlpt))
                                tem_salary[i]['payment']=parseInt(tem_salary[i]['total'])-( (that.SsbMax*(parseInt(tem_salary[i].ssb)/100))+parseInt(tem_salary[i]['late_leave_money']) );
                                total_payment+=tem_salary[i]['payment'];
                                total_c_paid+=(that.SsbMax*((5-tem_salary[i].ssb)/100));
                                tem_salary[i]['c_paid']=(that.SsbMax*((5-tem_salary[i].ssb)/100));//(that.SsbMax*(3/100));
                           
                                // tem_salary[i]['emp_id']=tem_salary[i].emp_id;
                                tem_salary[i]['emp_code']=that.emp_arr[tem_salary[i].emp_id];
                                tem_salary[i]['emp_name']=that.name_arr[tem_salary[i].emp_id];

                            }
                        }else{
                              for(let i=0;i<tem_salary.length;i++){
                            
                                tem_salary[i]['total']=(parseInt(tem_salary[i].salary_amount)+parseInt(tem_salary[i].trans_money)+parseInt(tem_salary[i].jlpt))
                                tem_salary[i]['payment']=parseInt(tem_salary[i]['total'])-( (that.SsbMax*(parseInt(tem_salary[i].ssb)/100))+parseInt(tem_salary[i]['late_leave_money']) );

                                tem_salary[i]['c_paid']=(that.SsbMax*((5-tem_salary[i].ssb)/100));//(that.SsbMax*(3/100));
                                // tem_salary[i]['emp_id']=tem_salary[i].emp_id;
                                tem_salary[i]['emp_code']=that.emp_arr[tem_salary[i].emp_id];
                                tem_salary[i]['emp_name']=that.name_arr[tem_salary[i].emp_id];
                            }
                            for(let i=0;i<get_salary_data.length;i++){                          
                                total_payment+=parseInt(get_salary_data[i].payment_amount);
                                total_c_paid+=parseInt(get_salary_data[i].ssbval.c_paid);                          
                            }
                        }                      
                     
                        that.total_payment=total_payment;
                        // that.company_ssb=company_ssb;
                        that.salaries=tem_salary;
                        that.total_c_paid=total_c_paid;
                        if(that.salaries.length===0){  
                            that.$swal.close();                    
                            this.data_check_messg1= true
                            this.payslipBtnDisable = true;
                            setTimeout(() => {
                                this.data_check_messg1= false
                            },3500)
                            return false;
                        }
                        that.$swal.close();
                    })
                    .catch(function (error) {
                        that.$swal.close();                     
                    });
            },
            ssbCalc:function(event){
                    let cal_ssb=0,total_ssb=0,total_ssb1=0,ssb_c_paid=0,ssb_c_paid1=0;
                    total_ssb=$(event.target).parent().parent().find('.ssb_total').val()!=null?$(event.target).parent().parent().find('.ssb_total').val():0;
                    total_ssb1=$(event.target).parent().parent().prev('tr').find('.ssb_total1').text()!=null?$(event.target).parent().parent().prev('tr').find('.ssb_total1').text():0;
                 
                    ssb_c_paid=$(event.target).parent().parent().find('.ssb_c_paid').val()!=null?$(event.target).parent().parent().find('.ssb_c_paid').val():0;
                    ssb_c_paid1=$(event.target).parent().parent().prev('tr').find('.ssb_c_paid1').text()!=null?$(event.target).parent().parent().prev('tr').find('.ssb_c_paid1').text():0;
                    
                    if(parseInt(total_ssb.toString().replace(/,/g , null))!=parseInt(total_ssb1.toString().replace(/,/g , null))  ){//&& !isNaN(total_ssb) && !isNaN(total_ssb1)
                     
                        $(event.target).parent().parent().find('.ssb_total').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.ssb_total').css("background-color", null);
                    }

                    if(parseInt(ssb_c_paid.toString().replace(/,/g , null))!=parseInt(ssb_c_paid1.toString().replace(/,/g , null)) ){// && !isNaN(ssb_c_paid) && !isNaN(ssb_c_paid1)
                     
                      $(event.target).parent().parent().find('.ssb_c_paid').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.ssb_c_paid').css("background-color", null);
                    }

                 $("input[name^='ssb_c_paid']").each(function() {                    
                        if( !isNaN($(this).val().toString().replace(/,/g , null)) && $(this).val().toString().replace(/,/g , null)!=null) {
                             
                            cal_ssb+=parseInt($(this).val().toString().replace(/,/g , null));
                              
                        }                      
                    });
                     $("#ssbtable").find('.paid-ssb').text(cal_ssb.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
            },
            
            updateInput:function(event){ 
                    let that=this;
                    let b_salary=0,t_m=0,jlpt=0,bnu=0,total=0;  
                    let inc_tax=0,ssb=0,leave_late=0,payment_amount=0,adju1=0,adju2=0,adju3=0;

                    let b_salary1=0,t_m1=0,jlpt1=0,leave_late1=0,total1=0;

                    b_salary=$(event.target).parent().parent().find('.income').val()!=null?$(event.target).parent().parent().find('.income').val().toString().replace(/,/g , null):0;
                    b_salary1=$(event.target).parent().parent().prev('tr').find('.income1').text().trim()!=null?$(event.target).parent().parent().prev('tr').find('.income1').text().toString().replace(/,/g , null):0;
                  
                    t_m=$(event.target).parent().parent().find('.trans_money').val()!=null?$(event.target).parent().parent().find('.trans_money').val().toString().replace(/,/g , null):0;
                    t_m1=$(event.target).parent().parent().prev('tr').find('.trans_money1').text().trim()!=null?$(event.target).parent().parent().prev('tr').find('.trans_money1').text().toString().replace(/,/g , null):0;

                    jlpt=$(event.target).parent().parent().find('.jlpt').val()!=null?$(event.target).parent().parent().find('.jlpt').val().toString().replace(/,/g , null):0;
                    jlpt1=$(event.target).parent().parent().prev('tr').find('.jlpt1').text().trim()!=null?$(event.target).parent().parent().prev('tr').find('.jlpt1').text().toString().replace(/,/g , null):0;

                    bnu=$(event.target).parent().parent().find('.bonus').val()!=null?$(event.target).parent().parent().find('.bonus').val().toString().replace(/,/g , null):0;

                    inc_tax=$(event.target).parent().parent().find('.inc_tax').val()!=null?$(event.target).parent().parent().find('.inc_tax').val().toString().replace(/,/g , null):0;
                    ssb=$(event.target).parent().parent().find('.ssb').val()!=null?$(event.target).parent().parent().find('.ssb').val().toString().replace(/,/g , null):0;
                    leave_late=$(event.target).parent().parent().find('.leave_late').val()!=null?$(event.target).parent().parent().find('.leave_late').val().toString().replace(/,/g , null):0;
                    leave_late1=$(event.target).parent().parent().prev('tr').find('.leave_late1').text().trim()!=null?$(event.target).parent().parent().prev('tr').find('.leave_late1').text().toString().replace(/,/g , null):0;

                    adju1=$(event.target).parent().parent().find('.adju1').val()!=null?$(event.target).parent().parent().find('.adju1').val().toString().replace(/,/g , null):0;
                    adju2=$(event.target).parent().parent().find('.adju2').val()!=null?$(event.target).parent().parent().find('.adju2').val().toString().replace(/,/g , null):0;
                    adju3=$(event.target).parent().parent().find('.adju3').val()!=null?$(event.target).parent().parent().find('.adju3').val().toString().replace(/,/g , null):0;
                    
                    total=parseInt(b_salary)+parseInt(t_m)+parseInt(jlpt)+parseInt(bnu);
                    if(total<0){
                        total=0;
                    }
                    payment_amount=total - ( (parseInt(inc_tax)+parseInt(ssb)+parseInt(leave_late)) +  (parseInt(adju1)+parseInt(adju2)+parseInt(adju3)) ) ;
                    if(payment_amount<0){
                        payment_amount=0;
                    }

                    total1=$(event.target).parent().parent().prev('tr').find('.total_salary1').text().trim()!=null?$(event.target).parent().parent().prev('tr').find('.total_salary1').text().toString().replace(/,/g , null):0;

                    $(event.target).parent().parent().find('.total_salary').val(!isNaN(total)?total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):0) ;
                    $(event.target).parent().parent().find('.payment_amount').val(!isNaN(payment_amount)?payment_amount.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):0);
                    // that.salary_amounts.push(parseInt(salary_amount));
                    let cal_salary=0;
                    $("input[name^='payment_amount']").each(function() {                       
                            if($(this).val()!=null) {
                                cal_salary+=parseInt($(this).val().toString().replace(/,/g , null));
                                $("#salaryTable").find('.cal_salaries').text(cal_salary.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,").trim());
                            }                    
                    });
                    

                    if(parseInt(b_salary)!=parseInt(b_salary1) && !isNaN(b_salary) && !isNaN(b_salary1) ){
                         $(event.target).parent().parent().find('.income').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.income').css("background-color", null);
                    }

                    if(parseInt(t_m)!=parseInt(t_m1) && !isNaN(t_m) && !isNaN(t_m1) ){
                         $(event.target).parent().parent().find('.trans_money').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.trans_money').css("background-color", null);
                    }

                    if(parseInt(jlpt)!=parseInt(jlpt1) && !isNaN(jlpt) && !isNaN(jlpt1) ){
                         $(event.target).parent().parent().find('.jlpt').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.jlpt').css("background-color", null);
                    }
                   
                    if(parseInt(leave_late)!=parseInt(leave_late1) && !isNaN(leave_late) && !isNaN(leave_late1)  ){
                         $(event.target).parent().parent().find('.leave_late').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.leave_late').css("background-color", null);
                    }

                     if(parseInt(total)!=parseInt(total1) && !isNaN(total) && !isNaN(total1) ){
                         $(event.target).parent().parent().find('.total_salary').css("background-color", "yellow");
                    }else{
                         $(event.target).parent().parent().find('.total_salary').css("background-color", null);
                    }
            }, 
        }
    }
</script>
