<template>
    <div class="box" v-loading="loading">
        <el-row :gutter="20" class="top">
            <el-col :span="24">
                <h1>FTP 个人独立版</h1>
            </el-col>
        </el-row>
        <div class="c"></div>
        <el-table :data="dir_list" style="width: 100%">
            <el-table-column prop="name" label="名称" width="180"></el-table-column>
            <el-table-column prop="size" label="大小" width="180"></el-table-column>
            <el-table-column prop="type" label="类型"></el-table-column>
            <el-table-column prop="date" label="修改时间"></el-table-column>
            <el-table-column prop="permissions" label="权限"></el-table-column>
            <el-table-column prop="user" label="所有者"></el-table-column>
        </el-table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dir_list: [],
                loading: true
            };
        },
        mounted() {
            var account = JSON.parse(localStorage.getItem("account"));
            if (!account) {
                this.$message({
                    message: '登录过期',
                    type: 'warning'
                });
                this.$router.push('/account_index')
            }
            console.log("start");
            axios({url: 'ftp_index', method: 'post', data: account}).then((response)=>{
                console.log(response.status);
                if(response.data.status == 200){
                    this.loading = false;
                    this.dir_list = response.data.data;
                }else {
                    this.$message({message: '认证失败', type: 'warning'});
                    this.$router.push('/account_index')
                 }
            }).catch(function (error) {
                console.log(11111);
                this.$message({message: '登录错误',type: 'warning'});
                this.$router.push('/account_index')
            });
        },
        methods: {}
    };
</script>
<style scoped="scoped">
    .box {
        width: 960px;
        overflow: hidden;
        margin: 0 auto;
    }

    .top {
        margin-bottom: 200px;
    }

    .c {
        clear: both
    }

</style>
