/**
 * Created by admin on 2018/2/1.
 */
import AccountIndex from '../components/account/index.vue';
import FtpIndex from '../components/ftp/index.vue';
import FtpUpload from '../components/ftp/upload.vue';
import Page from '../components/ftp/page.vue';

export default[
    {
        path: '/account_index',
        component: AccountIndex,
        name: '账户首页'
    },
    {
        path: '/ftp_index',
        component: FtpIndex,
        name: 'ftp首页'
    },
    {
        path: '/ftp_upload',
        component: FtpUpload,
        name: '上传'
    },
    {
        path: '/page',
        component: Page,
        name: '账户首页'
    }
];