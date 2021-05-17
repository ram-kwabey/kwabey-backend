<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Add Lead <button class="btn btn-sm btn-success float-right" @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i>Back</button></h3>
                <hr />
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="" class="col-md-3">Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.name" placeholder="John Doe" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Email</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.email" placeholder="email@example.com" />
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Phone</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.phone" placeholder="1234567890" />
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="addClient">Add Lead</button>
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
                    name : '',
                    email : '',
                    phone : ''
                },
                errors: []
            }
        },
        created(){
        },
        methods:{
            addClient(){
                this.errors = [];
                axios.post('admin/clients-list',{
                    name: this.data.name,
                    phone: this.data.phone,
                    email: this.data.email
                })
                .then(response => 
                    this.$emit('added-client','Lead added successfully!')
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