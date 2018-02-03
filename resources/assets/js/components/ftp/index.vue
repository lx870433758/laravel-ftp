<template>
    <div class="box" v-loading="loading">
        <el-row :gutter="20" class="top">
            <el-col :span="24">
                <h1>FTP 网页管理</h1>
            </el-col>
        </el-row>
        <el-form :inline="true" :model="go_dir" class="demo-form-inline">
            <el-form-item label="当前目录">
                <el-input v-model="dir"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="goToDir">跳转</el-button>
            </el-form-item>
        </el-form>
        <el-button>返回上一级</el-button>
        <el-button @click="dialogVisible = true">新建文件夹</el-button>
        <el-dialog
                title="输入文件夹名称"
                :visible.sync="dialogVisible"
                width="20%"
                :before-close="handleClose">
            <el-input placeholder="请输入文件夹名称" prefix-icon="el-icon-message" v-model="add_dir_name">
            </el-input>
        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" @click="add_dir(add_dir_name)">确 定</el-button>
        </span>
        </el-dialog>
        <div class="c"></div>
        <el-table :data="dir_list" style="width: 100%" class="dir_list">
            <el-table-column label="名称" width="180">
                <template slot-scope="scope">
                    <el-button type="text" @click="listToDir(scope.row.name)"><img v-if="scope.row.type==1" class="file_img" src="/images/dir.jpg"><img class="file_img" v-else src="/images/file.jpg">{{scope.row.name}}</el-button>
                </template>
            </el-table-column>
            <el-table-column prop="size" label="大小" width="100"></el-table-column>
            <el-table-column label="类型" width="100">
                <template slot-scope="scope">
                    <span>{{scope.row.type ==1 ? '文件夹' : '文件'}}</span>
                </template>
            </el-table-column>
            <el-table-column prop="date" label="修改时间" width="180"></el-table-column>
            <el-table-column prop="permissions" label="权限" width="150"></el-table-column>
            <el-table-column prop="user" label="所有者"></el-table-column>
            <el-table-column label="操作" width="100">
                <template slot-scope="scope">
                    <el-button type="primary" size="mini" @click="remove(scope.row.name,scope.row.type)" icon="el-icon-delete"></el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                dir_list: [],
                loading: true,
                dir:"",
                go_dir:{},
                dialogVisible: false,
                add_dir_name:""
            };
        },
        created() {
            var account = JSON.parse(localStorage.getItem("account"));
            if (!account) {
                this.$message({
                    message: '登录过期',
                    type: 'warning'
                });
                this.$router.push('/account_index')
            }
            axios({url: '/get_rawlist', method: 'post', data: account}).then((response)=>{
                if(response.data.status == 200){
                    this.loading = false;
                    this.dir_list = response.data.data.list;
                    this.dir = response.data.data.dir;
                }else {
                    this.$message({message: '认证失败', type: 'warning'});
                    this.$router.push('/account_index')
                 }
            }).catch(function (error) {
                this.$message({message: '登录错误',type: 'warning'});
                this.$router.push('/account_index')
            });
        },
        methods: {
            goToDir(){
                this.loading = true;
                var account = JSON.parse(localStorage.getItem("account"));
                account.dir=this.dir;
                axios({url: '/get_rawlist', method: 'post', data: account}).then((response)=>{
                    if(response.data.status == 200){
                    this.loading = false;
                    this.dir_list = response.data.data.list;
                    this.dir = response.data.data.dir;
                }else {
                    this.$message({message: '认证失败', type: 'warning'});
                    this.$router.push('/account_index')
                }
            }).catch(function (error) {
                    this.$message({message: '登录错误',type: 'warning'});
                    this.$router.push('/account_index')
                });
            },
            listToDir(dir){
                this.loading = true;
                var account = JSON.parse(localStorage.getItem("account"));
                account.dir=this.dir+dir+'/';
                axios({url: '/get_rawlist', method: 'post', data: account}).then((response)=>{
                    if(response.data.status == 200){
                        this.loading = false;
                        this.dir_list = response.data.data.list;
                        this.dir = response.data.data.dir;
                    }else {
                        this.$message({message: '', type: 'warning'});
                        this.$router.push('./');
                     }
            }).catch(function (error) {
                    this.$message({message: '登录错误',type: 'warning'});
                    this.$router.push('/account_index')
                });
            },
            handleClose(done) {
                done();
            },
            add_dir(add_dir_name){
                this.loading = true;
                var account = JSON.parse(localStorage.getItem("account"));
                account.dir=this.dir;
                account.new_dir=add_dir_name;
                axios({url: '/add_dir', method: 'post', data: account}).then((response)=>{
                    if(response.data.status == 200){
                        this.loading = false;
                        this.$message({message: '创建成功', type: 'success'});
                        this.dialogVisible = false;
                        this.goToDir();
                    }else {
                        this.loading = false;
                        this.$message({message: '创建失败', type: 'warning'});
                     }
                })
            },
            remove(name,type){
                this.$confirm('确认删除吗？').then(_ => {
                    this.loading = true;
                    var account = JSON.parse(localStorage.getItem("account"));
                    account.dir=this.dir+name;
                    account.type=type;
                    axios({url: '/remove', method: 'post', data: account}).then((response)=>{
                    if(response.data.status == 200){
                        this.loading = false;
                        this.$message({message: '删除成功', type: 'success'});
                        this.goToDir();
                    }else {
                        this.loading = false;
                        this.$message({message: '删除失败', type: 'warning'});
                    }
                    })
                }).catch(_ => {});
            }
        }
    };
</script>
<style>
    .dir_list td{
        padding: 0;
    }
</style>
<style scoped="scoped">
    .box {
        width: 960px;
        overflow: hidden;
        margin: 0 auto;
    }
    .top {
        margin-bottom: 20px;
    }
    .c {
        clear: both
    }
    .file_img{
        width: 20px;height: 20px
    }
    .el-button--text{
        color: #2F3133;
    }
</style>
