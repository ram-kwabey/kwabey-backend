<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card">
                <div class="card-body">
                    <h3>Create Permission</h3>
                    <hr/>
                    <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                        <ul>
                            <li v-for="error in errors" :key="error">{{error}}</li>
                        </ul>
                    </div>
                    <form action="#">
                        <div class="row form-group">
                            <label for="" class="col-md-3">Permission Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" v-model="data.permission">
                            </div>
                            <button v-if=accessAdd class="btn btn-primary" @click.prevent="storePermission">Create Permission</button>
                        </div>
                    
                    </form>
                </div>
                <hr>
                <div class="card-body">
                    <h3>All Permissions list</h3>
                    <hr>
                    <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Permissions list</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <span class="badge bg-primary mr-1" v-for="permission in permissions" :key="permission.id">
                                            {{permission.name}}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default{
        data(){
            return{
                data:{
                    permission : '',
                },
                permissions: [],
                loading: false,
                errors: [],
                accessAdd : this.$gate.permissions.includes('create_user'),
            }
        },
        created(){
            this.loading = true
            this.getPermissions()
        },
        methods:{
            
            getPermissions(){
                axios.get('admin/permissions')
                .then(response => {
                this.permissions = response.data
                    setTimeout(() => {
                        this.loading = false
                    }, 500);
                })
                .catch(error => console.log(error))
            },
            storePermission(){
                this.errors = [];
                axios.post('admin/user-permission',{
                    name: this.data.permission,
                })
                .then(response => {
                    console.log(response)
                    this.$toaster.success('Permission added successfully!')
                    this.data.permission = ''
                    this.getPermissions()
                })
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

