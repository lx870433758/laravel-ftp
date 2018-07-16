<template>
    <div class="box">
        <el-row :gutter="20" class="top">
            <el-col :span="24">
                <h1>FTP 网页管理</h1>
            </el-col>
        </el-row>
        <div class="c"></div>
        <div class="left">
            <el-row>
            <el-popover
                    ref="popover4"
                    placement="bottom"
                    width="400"
                    trigger="click">
                <div class="addAccount">
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <div class="grid-content bg-purple">名称:</div>
                        </el-col>
                        <el-col :span="16">
                            <el-input v-model="name" placeholder="请输入自定义名称"></el-input>
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <div class="grid-content bg-purple">ip:</div>
                        </el-col>
                        <el-col :span="16">
                            <el-input v-model="ip" placeholder="请输入ip"></el-input>
                        </el-col>
                    </el-row>
                    <div class="c"></div>
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <div class="grid-content bg-purple">port:</div>
                        </el-col>
                        <el-col :span="16">
                            <el-input v-model="port" placeholder="请输入端口"></el-input>
                        </el-col>
                    </el-row>
                    <div class="c"></div>
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <div class="grid-content bg-purple">用户名:</div>
                        </el-col>
                        <el-col :span="16">
                            <el-input v-model="user_name" placeholder="请输入用户名"></el-input>
                        </el-col>
                    </el-row>
                    <div class="c"></div>
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <div class="grid-content bg-purple">密码:</div>
                        </el-col>
                        <el-col :span="16">
                            <el-input type="password" v-model="password" placeholder="请输入密码"></el-input>
                        </el-col>
                    </el-row>
                    <div class="c"></div>
                    <el-row :gutter="20">
                        <el-button class="sumbit" type="button" plain @click="account()">确认</el-button>
                    </el-row>
                    <div class="c"></div>
                </div>
            </el-popover>
            <el-button v-popover:popover4>添加账号</el-button>
            </el-row>
        </div>
        <div class="right">
            <el-row>
                <el-col :span="24">
                    账号列表：
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24" :key="index" v-for="(item,index) in account_list" id="account_list">
                    <el-button @click="login(index)">{{ item.name}}</el-button> <el-button type="danger" @click="delteAccount(index)">删除</el-button>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                name:"",ip: "", port: "", user_name: "", password: "",account_list:[]
            };
        },
        mounted() {
            this.account_list = JSON.parse(localStorage.getItem("account_list")) ? JSON.parse(localStorage.getItem("account_list")) : [];
            localStorage.setItem('account',null);
        },
        methods: {
            account() {
                this.account_list.push({'name':this.name,'ip':this.ip,'port':this.port,'user_name':this.user_name,'password':this.password});
                localStorage.setItem('account_list',JSON.stringify(this.account_list));
                // 执行操作
            },
            delteAccount(index){
                var arr =JSON.parse(localStorage.getItem("account_list"));
                arr.splice(index,1);
                localStorage.setItem('account_list',JSON.stringify(arr));
                this.account_list = arr;
            },
            login(index){
                var arr =JSON.parse(localStorage.getItem("account_list"));
                localStorage.setItem('account',JSON.stringify(arr[index]));
                this.$router.push('/ftp_index')
            }
        }
    };
</script>
<style scoped="scoped">
    .box {
        width: 960px;
        overflow: hidden;
        margin: 0 auto ;
    }
    .top{
        margin-bottom: 200px;
    }
    .left{
        width: 400px;
        overflow: hidden;
        float:left;
    }
    .right{
        width: 400px;
        overflow: hidden;
        float:right;
    }
    .c {
        clear: both
    }

    .el-row {
        font-size: 18px;
        height: 30px;
    }

    .addAccount .el-row {
        margin-bottom: 30px;
    }

    .sumbit {
        margin-left: 250px;
    }
    #account_list{
        height: 50px;
        margin-bottom: 10px;
    }
</style>
