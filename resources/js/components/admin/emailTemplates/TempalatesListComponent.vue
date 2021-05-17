<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div v-else>
            <div class="card" v-if="active.dashboard">
                <div class="card-body">
                    <h3>Manage Templates <button class="btn btn-primary btn-sm float-right" @click="setActive('createTemplate')"><i class="fas fa-plus"></i> Template</button></h3>
                    <data-table url="admin/template-list" :columns="columns" ref="templateTable">
                        <div slot="filters" slot-scope="{ tableFilters, perPage }">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <select class="form-control" v-model="tableFilters.length">
                                        <option :key="page" v-for="page in perPage">{{ page }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-3">
                                    <input
                                        name="name"
                                        class="form-control"
                                        v-model="tableFilters.search"
                                        placeholder="Search Email Template">
                                </div>
                            </div>
                        </div>
                    </data-table>
                </div>
            </div>
            <createTemplate v-if="active.createTemplate" v-on:view-dashboard="setActive('dashboard')" v-on:created-template="flashMessageandReload"></createTemplate>

            <div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true"> 
                <div class="modal-dialog modal-lg" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit template</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="#">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                                            <ul>
                                                <li v-for="error in errors" :key="error">{{error}}</li>
                                            </ul>
                                        </div>
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
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" @click.prevent="storeUpadatedTemplate">Update Template</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import createTemplate from './CreateTemplate.vue'
    import TemplateListActions from './TemplateListActions.vue'

    export default{
        components:{
            createTemplate,
            TemplateListActions
        },
        created(){
            this.loading = true
            setTimeout(() => {
                this.loading = false
            }, 1000);
        },
        data(){
            return{
                active:{
                    createTemplate: false,
                    dashboard: true,
                },
                errors: [],
                loading: false,
                columns: [
                    {
                        label: 'Template name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Subject',
                        name: 'subject',
                        orderable: true,
                    },
                    {
                        label: 'Actions',
                        name: 'Actions',
                        orderable: false,
                        classes: { 
                            'btn': true,
                            'btn-primary': true,
                            'btn-sm': true,
                        },
                        event: "click",
                        handler: this.dataTableEvent,
                        component: TemplateListActions, 
                    },
                ],
                data:{
                    id: '',
                    name: '',
                    subject: '',
                    variables: '',
                    content: '',
                },
            }
        },
        methods: {
            getTemplates(){
                this.$refs.templateTable.getData()
            },
            setActive(component){
                //get all the keys of active object and make them false
                Object.keys(this.active).forEach(key => this.active[key] = false)
                this.active[component] = true
            },
            refreshPage(){
                this.setActive('dashboard')
            },
            flashMessageandReload(event){
                this.refreshPage()
                this.$toaster.success(event)
            },
            dataTableEvent(payload,id,data){
                if(payload == 'edit'){
                    this.updateTemplate(id,data);
                }
            },
            reloadWithoutFlash(){
                this.setActive('dashboard')
                this.getTemplates()
            },
            flashErrors(errors){
                for(const[key,value] of Object.entries(errors)){
                    for(let item in value){
                        this.errors.push(value[item])
                    }
                }
            },
            updateTemplate(id,data){
                this.data.id = id;
                this.data.name = data.name;
                this.data.subject = data.subject;
                this.data.variables = data.variables;
                this.data.content = data.content;
                $("#templateModal").modal('show');
            },
            storeUpadatedTemplate(){
                this.errors = [];
                axios.post('admin/updateTemplate/'+this.data.id,{
                    name: this.data.name,
                    subject: this.data.subject,
                    variables: this.data.variables,
                    content: this.data.content,
                })
                .then(response => {
                    $("#templateModal").modal('hide');
                    this.data.id = '';
                    this.data.name = '';
                    this.data.subject = '';
                    this.data.variables = '';
                    this.data.content = '';
                    this.getTemplates();
                    Toast.fire({
                        icon: 'success',
                        title: 'Template updated successfully'
                    })
                })
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
        }
    }
</script>