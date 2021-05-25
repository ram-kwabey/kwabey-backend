<template>
    
<div class="card">
    <div class="card-body">
        <h3>Sub Category Listing  

            <button class="btn btn-primary btn-sm float-right" v-on:click="addSubCategoryModal()">
                    <i class="fas fa-plus"></i> Add Sub Category
            </button>
            <!-- <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleUsersCsv($event)">Export <i class="fas fa-file-export"></i></button> -->
        </h3>Sub Category
        <div class="content patient-detail">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-default">
                            <data-table url="admin/sub-category-list" :columns="columns" ref="subCategoryTable" :filters="filters">
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
                                                placeholder="Search Sub Category">
                                        </div>
                                    </div>
                                </div>
                            </data-table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="subCategoryModal" tabindex="-1" role="dialog" aria-labelledby="subCategoryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="subCategoryModalLabel" v-if="editMode">Edit Sub Category</h5>
                            <h5 class="modal-title" id="subCategoryModalLabel" v-else >Add Sub Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? createUpdateSubCategory() : createUpdateSubCategory()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <alert-error :form="form"></alert-error>
                                <div class="form-group">
                                    <label for="title" class="col-md-3">Name</label>
                                    <input v-model="form.name" type="text" name="name" id="name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Title">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                 <div class="form-group">
                                    <label for="category_id" class="col-md-3">Category</label>
                                    <select v-model="form.category_id" type="text" name="category_id" id="category_id" class="form-control" >
                                        <option :value="categoryData.id" v-for="categoryData in categoryDataArr">{{ categoryData.name }}</option>                                        
                                    </select>
                                    <has-error :form="form" field="category_id"></has-error>
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
    import SubCategoryListActions from './sub_category_list_actions.vue'
    export default {
        components:{
            SubCategoryListActions
        },
        mounted() {
            console.log('Component mounted.')
        },
        created() {
            this.showLoader();
            this.getCategoryInfo();
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
                        component: SubCategoryListActions, 
                    },
                ],
                filters: {
                    role: '',
                    type: 'All'
                },
                rows: [],
                page: 1,
                per_page: 10,
                categoryDataArr:[],
            }
        },
        methods: {
            dataTableEvent(payload,data){
                if(payload == 'edit'){
                    this.editSubCategory(data);
                }
                if(payload == 'restoreSubCategory'){
                    this.restoreSubCategory(data);
                }
                if(payload == 'deleteSubCategory'){
                    this.deleteSubCategory(data);
                }
            },
            addSubCategoryModal () {
                this.form.reset();
                this.form.clear();
                this.editMode = false;
                this.form.status = 1;
                $("#subCategoryModal").modal('show');
            },
            createUpdateSubCategory() {
                // Submit the form via a POST request
                this.showLoader();
                this.form.post('/admin/store-subcategory')
                .then(({ data }) => { 
                    this.getSubCategory();
                    Toast.fire({ icon: 'success', title: 'Sub Category added successfully' });
                    $("#subCategoryModal").modal('hide');
                    this.removeLoader();
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.removeLoader();
                });
            },
            editSubCategory(data){
                this.form.reset();
                this.form.clear();
                this.form.fill(data);
                this.editMode = true;
                $("#subCategoryModal").modal('show');
            },
            deleteSubCategory(data){
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
                        axios.delete('admin/delete-subcategory/' + data.id,{_method:'DELETE'}).then(({data})=>{
                            if(data.status == "success"){
                                this.getSubCategory();
                                Swal.fire('Deleted!', 'Sub Category has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                        });
                    }
                })
            },
            restoreSubCategory(data){
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
                        axios.post("/admin/restore-subcategory/" + data.id).then(({ data }) => {
                            if (data.status == "success") {
                                this.getSubCategory();
                                Swal.fire("Restored!", "Sub Category has been restored.", "success");
                            } else {
                                Swal.fire("Failed!", "Something went wrong.", "warning");
                            }
                        }).catch(({ data }) => {
                            Swal.fire("Failed!", "Something went wrong.", "warning");
                        });
                    }
                })
            },
            getSubCategory(){
                this.$refs.subCategoryTable.getData();
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
            getCategoryInfo(){
                axios.get('admin/get-category').then(({data})=>{
                    if(data.status == "success"){
                        this.categoryDataArr = data.categoryData;
                    }
                });
            },
        }


    }
</script>
