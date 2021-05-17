<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Create User  <button class="btn btn-sm btn-primary float-right" @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i> Back</button> </h3>
                <hr/>
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="" class="col-md-3">First Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.first_name">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Last Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.last_name">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Username</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.username">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Email</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" v-model="data.email">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" v-model="data.password">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Confirm Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" v-model="data.confirm_password">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Role</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="data.role">
                                <option disabled value="">Please select role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{role.name | capitalize}}</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="storeUser">Create User</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        data(){
            return{
                data:{
                    first_name : '',
                    last_name : '',
                    username : '',
                    email : '',
                    password: '',
                    confirm_password : '',
                    role : ''
                },
                errors: [],
                roles: {}
            }
        },
        created(){
            this.getRoles();
        },
        methods:{
            getRoles(){
                axios.get('admin/getRoles')
                .then(response => {
                    this.roles = response.data.results
                })
                .catch(e => console.log(e))
            },
            storeUser(){
                this.errors = [];
                axios.post('admin/users-list',{
                    first_name: this.data.first_name,
                    last_name: this.data.last_name,
                    username: this.data.username,
                    email : this.data.email,
                    password : this.data.password,
                    confirm_password : this.data.confirm_password,
                    role: this.data.role
                })
                .then(response => {
                    this.$emit('created-user','User Created Successfully!')
                }
                    
                    )
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                })
            },
            flashErrors(errors){
                for(const[key,value] of Object.entries(errors)){
                    for(let item in value){
                        this.errors.push(value[item])
                    }
                }
            }
            
        }
    }
</script>