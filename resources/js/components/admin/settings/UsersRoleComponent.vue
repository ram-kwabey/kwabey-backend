<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card" v-if="active.UserRole">
                <div class="card-body col-md-9">
                    <h3>Create Role</h3>
                    <hr/>
                    <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                        <ul>
                            <li v-for="error in errors" :key="error">{{error}}</li>
                        </ul>
                    </div>
                    <form action="#">
                        <div class="row form-group">
                            <label for="">Name</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" v-model="name">
                            </div>
                            <button class="btn btn-primary" @click.prevent="storeRole">Create Role</button>
                        </div>
                        
                    </form>
                </div>
                <div class="card-body col-md-12">
                    <h3>User Roles list</h3>
                    <hr/>
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>

                        <tbody v-if="roles !== null">
                            <tr v-for="role in form.permissions" :key="role.id">
                                <td>
                                    <b>{{role.name}}</b><br>
                                    <button class="btn btn-success btn-sm mt-3" @click="editPermission(role)">Edit Permissions</button>
                                </td>
                                <td><span v-for="permission in role.permissions" :key="permission.id">
                                    {{permission.name}},</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <EditPermission 
                v-if="active.EditPermission"
                :roles="role"
                v-on:view-userRole="setActive('UserRole')"
                >
            </EditPermission>
        </div>
    </div>
</template>

<script>
    import EditPermission from './EditPermission.vue'
    export default{
        components:{
            EditPermission
        },
        data(){
            return{
                name : '',
                role: '',
                errors: [],
                roles: {},
                count: 0,
                form:{
                    permissions: [],                    
                },
                loading: false,
                active:{
                    UserRole: true,
                    EditPermission: false,
                },
                
            }
        },
        created(){
            this.loading = true
            this.getCheckedPermissions();
        },
        methods:{
            editPermission(role){
                this.role = role
                this.setActive('EditPermission')
            },
            setActive(component){
                //get all the keys of active object and make them false
                this.getCheckedPermissions();
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
            getCheckedPermissions(){
                axios.get('admin/checkedPermissions')
                .then(response => {
                    setTimeout(() => {
                        this.loading = false
                    }, 500);
                    this.form.permissions = response.data
                })
                .catch(e => console.log(e))
            },

            async storeRole(){
                this.errors = [];
                try {
                    const name = this.name
                    const response = await axios.post('admin/user-role',{name:name})
                    this.name = ''
                    this.getCheckedPermissions()
                    this.$toaster.success('Role added successfully!')
                } catch (errors) {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                }
                
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