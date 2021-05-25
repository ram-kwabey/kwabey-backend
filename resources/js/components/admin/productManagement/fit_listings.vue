<template>
    
<div class="card">
    <div class="card-body">
        <h3>Fit Listing  

            <button class="btn btn-primary btn-sm float-right" v-on:click="addFitModal()">
                    <i class="fas fa-plus"></i> Add Fit
            </button>
            <!-- <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleUsersCsv($event)">Export <i class="fas fa-file-export"></i></button> -->
        </h3>
        <div class="content patient-detail">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-default">
                            <data-table url="admin/fit-list" :columns="columns" ref="fitTable" :filters="filters">
                                <div slot="filters" slot-scope="{ tableFilters, perPage }">
                                    <div class="row mb-2">
                                        <div class="col-md-3">
                                            <select class="form-control" v-model="tableFilters.length">
                                                <option :key="page" v-for="page in perPage">{{ page }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" v-model="filters.type" required>
                                                <option v-for="option in data_type" :key="option.id" :value="option.type" >{{ option.type }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input
                                                name="name"
                                                class="form-control"
                                                v-model="tableFilters.search"
                                                placeholder="Search Fit">
                                        </div>
                                    </div>
                                </div>
                            </data-table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="fitModal" tabindex="-1" role="dialog" aria-labelledby="fitModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fitModalLabel" v-if="editMode">Edit Fit</h5>
                            <h5 class="modal-title" id="fitModalLabel" v-else >Add Fit</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? createUpdateFit() : createUpdateFit()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <alert-error :form="form"></alert-error>
                                <div class="form-group">
                                    <label for="title" class="col-md-3">Title</label>
                                    <input v-model="form.name" type="text" name="name" id="name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Title">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="col-md-3">Description</label>
                                    <textarea id="description" v-model="form.description" size="lg" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" placeholder="Description"></textarea>
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" v-if="editMode" >Update</button>
                                <button type="submit" class="btn btn-primary" v-else>Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal End /-->
        </div>
    </div>
</div>
</template>

<script>
    import FitListActions from './fit_list_actions.vue'
    export default {
        components:{
            FitListActions
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            this.showLoader();
        },
        data() {
            return {               
                pagination: {},
                totalRecord: 0,
                current_page : '',
                last_page : '',
                search : '',
                editMode:false,
                form: new Form({
                    id: '',
                    name: '',
                    description:'',
                }),
                loading: false,
                results : {},
                success : null,
                alert_success: false,
                user: null,
                data_type:[
                    {
                    id:1,
                    type:'All'
                    },
                    {
                    id:2,
                    type: 'Archived'
                    }
                ],
                columns: [
                    {
                        label: 'Title',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'Description',
                        name: 'description',
                        orderable: true,
                    },
                    {
                        label: 'Slug',
                        name: 'slug',
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
                        component: FitListActions, 
                    },
                ],
                filters: {
                    role: '',
                    type: 'All'
                },
                rows: [],
                page: 1,
                per_page: 10,
               
            }
        },
        methods: {
            dataTableEvent(payload,data){
                if(payload == 'edit'){
                    this.editFit(data);
                }
                if(payload == 'restoreFit'){
                    this.restoreFit(data);
                }
                if(payload == 'deleteFit'){
                    this.deleteFit(data);
                }
            },
            addFitModal () {
                this.form.reset();
                this.form.clear();
                this.editMode = false;
                this.form.status = 1;
                $("#fitModal").modal('show');
            },
            createUpdateFit() {
                // Submit the form via a POST request
                this.showLoader();
                this.form.post('/admin/store-fit')
                .then(({ data }) => { 
                    this.getFit();
                    Toast.fire({ icon: 'success', title: 'Fit added successfully' });
                    $("#fitModal").modal('hide');
                    this.removeLoader();
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.removeLoader();
                });
            },
            editFit(data){
                this.form.reset();
                this.form.clear();
                this.form.fill(data);
                this.editMode = true;
                $("#fitModal").modal('show');
            },
            deleteFit(data){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to do this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete('admin/delete-fit/' + data.id,{_method:'DELETE'}).then(({data})=>{
                            if(data.status == "success"){
                                this.getFit();
                                Swal.fire('Deleted!', 'Fit has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            restoreFit(data){
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to do this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, restore it!",
                }).then((result) => {
                    if (result.value) {
                        axios.post("/admin/restore-fit/" + data.id).then(({ data }) => {
                            if (data.status == "success") {
                                this.getFit();
                                Swal.fire("Restored!", "Fit has been restored.", "success");
                            } else {
                                Swal.fire("Failed!", "Something went wrong.", "warning");
                            }
                        }).catch(({ data }) => {
                            Swal.fire("Failed!", "Something went wrong.", "warning");
                        });
                    }
                })
            },
            getFit(){
                this.$refs.fitTable.getData();
            },
            showLoader()
            {
                var loading_icon = '<div class="dataprocessing"><div class="loading-icon"><i class="fa fa-spinner fa-spin"></i></div></div>';
                $("body").append(loading_icon);
            },
            removeLoader()
            {
                $("div.dataprocessing").remove();

            },
        }


    }
</script>
