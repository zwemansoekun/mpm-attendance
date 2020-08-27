import AttendList from './components/AttendList.vue';
import Setting from './components/Setting.vue';
import Holiday from './components/Holiday.vue';

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
    }    
];