import AttendList from './components/AttendList.vue';
import Setting from './components/Setting.vue';
import Holiday from './components/Holiday.vue';
import SalaryHistory from './components/SalaryHistory.vue';
import AttendManage from './components/AttendManage.vue';
import salaryList from './components/salaryList.vue';

export const routes = [
    {
        name: 'attlist',
        path: '/attendList',
        component: AttendList
    },
    {
        name: 'setting',
        path: '/setting',
        component: Setting
    },
    {
        name: 'home',
        path: '/holidays',
        component: Holiday
    },  
    {
        name: 'salaryHistory',
        path: '/salaryHistory',
        component: SalaryHistory
    },
    {
        name: 'attenManage',
        path: '/attendManage',
        component: AttendManage
    }  ,
    {
        name: 'salaryList',
        path: '/salaryList',
        component: salaryList
    }

];