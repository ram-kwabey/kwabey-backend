<template>
    
<div class="card">
    <div class="card-body">
        <h3>Color Listing  

            <button class="btn btn-primary btn-sm float-right" v-on:click="addColorModal()">
                    <i class="fas fa-plus"></i> Add Color
            </button>
            <!-- <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleUsersCsv($event)">Export <i class="fas fa-file-export"></i></button> -->
        </h3>
        <div class="content patient-detail">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-default">
                            <data-table url="admin/color-list" :columns="columns" ref="colorTable" :filters="filters">
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
                                                placeholder="Search Color">
                                        </div>
                                    </div>
                                </div>
                            </data-table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="colorModal" tabindex="-1" role="dialog" aria-labelledby="colorModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="colorModalLabel" v-if="editMode">Edit Color</h5>
                            <h5 class="modal-title" id="colorModalLabel" v-else >Add Color</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? createUpdateColor() : createUpdateColor()" @keydown="form.onKeydown($event)">
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
    import ColorListActions from './color_list_actions.vue'
    export default {
        components:{
            ColorListActions
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
                        component: ColorListActions, 
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
                    this.editColor(data);
                }
                if(payload == 'restoreColor'){
                    this.restoreColor(data);
                }
                if(payload == 'deleteColor'){
                    this.deleteColor(data);
                }
            },
            addColorModal () {
                this.form.reset();
                this.form.clear();
                this.editMode = false;
                this.form.status = 1;
                $("#colorModal").modal('show');
            },
            createUpdateColor() {
                // Submit the form via a POST request
                this.showLoader();
                this.form.post('/admin/store-color')
                .then(({ data }) => { 
                    this.getColor();
                    Toast.fire({ icon: 'success', title: 'Color added successfully' });
                    $("#colorModal").modal('hide');
                    this.removeLoader();
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.removeLoader();
                });
            },
            editColor(data){
                this.form.reset();
                this.form.clear();
                this.form.fill(data);
                this.editMode = true;
                $("#colorModal").modal('show');
            },
            deleteColor(data){
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
                        axios.delete('admin/delete-color/' + data.id,{_method:'DELETE'}).then(({data})=>{
                            if(data.status == "success"){
                                this.getColor();
                                Swal.fire('Deleted!', 'Color has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            restoreColor(data){
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
                        axios.post("/admin/restore-color/" + data.id).then(({ data }) => {
                            if (data.status == "success") {
                                this.getColor();
                                Swal.fire("Restored!", "Color has been restored.", "success");
                            } else {
                                Swal.fire("Failed!", "Something went wrong.", "warning");
                            }
                        }).catch(({ data }) => {
                            Swal.fire("Failed!", "Something went wrong.", "warning");
                        });
                    }
                })
            },
            getColor(){
                this.$refs.colorTable.getData();
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
