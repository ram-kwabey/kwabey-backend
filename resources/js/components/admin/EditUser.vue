<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Edit User <button class="btn btn-sm btn-success float-right" @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i>Back</button></h3>
            
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
                        <label for="" class="col-md-3">Roles</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="data.role">
                                 <option disabled value="">Please select role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{role.name | capitalize}}</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="updateUser">Update</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user'],

    data(){
        return{
            data:{
                first_name : '',
                last_name : '',
                username : '',
                email: '',
                role: '',

            },
            roles: [],
            errors: []
        }
    },
    created(){
        this.getRoles();
        this.data = this.user
        this.data.role = this.data.roles[0].id
        
    },
    watch:{
        user(){
            this.data = this.user
        }
    },
    methods:{
        updateUser(){
            this.errors = [];
            axios.post('admin/updateUser/' + this.user.id,{
                _method: 'PUT',
                first_name: this.data.first_name,
                last_name: this.data.last_name,
                username: this.data.username,
                email: this.data.email,
                role: this.data.role
            }).then( response => {
                this.$emit('user-updated','User updated Successfully!')
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
        },
        getRoles(){
            axios.get('admin/getRoles')
                .then(response => {
                    this.roles = response.data.results
                })
                .catch(e => console.log(e))
        }
    }
}
</script>