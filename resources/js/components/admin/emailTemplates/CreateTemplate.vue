<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Create Template <button class="btn btn-sm btn-success float-right " @click="$emit('view-dashboard')">
                    <i class="fas fa-undo"></i> Back</button></h3>
                <hr/>
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="" class="col-md-3">Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.name">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Subject</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.subject">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Variables</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.variables">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Content</label>
                        <div class="col-md-9">
                            <ckeditor v-model="data.content"></ckeditor>
                        </div>
                    </div>
                    <button class="btn btn-primary" @click.prevent="storeTemplate">Create Template</button>
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
                    subject: '',
                    content: '',
                    variables: '',
                },
                errors: [],
            }
        },
        created(){
        },
        methods:{
            storeTemplate(){
                this.errors = [];
                axios.post('admin/store-template',{
                    name: this.data.name,
                    subject: this.data.subject,
                    content: this.data.content,
                    variables: this.data.variables,
                })
                .then(response => 
                    this.$emit('created-template','Template Created Successfully!')
                )
                .catch(errors => {
                    if(errors.response.status ===  422){
                        this.flashErrors(errors.response.data.errors)
                    }
                    if(errors.response.status ===  500){
                        Toast.fire({
                            icon: 'error',
                            title: 'Something went wrong'
                        })
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