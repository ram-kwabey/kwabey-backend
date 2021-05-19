<template>
    
<div class="card">
    <div class="card-body">
        <h3>Figure Listing  

            <button class="btn btn-primary btn-sm float-right" v-on:click="addFigureModal()">
                    <i class="fas fa-plus"></i> Add Figure
            </button>
            <!-- <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleUsersCsv($event)">Export <i class="fas fa-file-export"></i></button> -->
        </h3>
        <div class="content patient-detail">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-default questionnaires-table">

                            <div class="card-body">
                                 <div class="search-content-tab">
                                    <div class="row">
                                              <div class="col-md-3">
                                                  <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Search" v-model="search" @keyup.enter="searchTemplate()" @keyup="searchTemplateKeyup()">
                                                    <span class="error search_error"></span>
                                                  </div>
                                              </div>
                                              <div class="col-md-3">
                                                  <div class="form-group">
                                                      <button type="button" @click="searchTemplate()" class="btn btn-primary btn-gradient" ><i class="fa fa-search" aria-hidden="true"></i> Search</button>
                                                  </div>
                                              </div>
                                        </div>
                                </div>
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Amount</th>
                                            <th>After Approval Amount</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr v-for="source in pagination.data" :key="source.id">
                                            <td>{{ source.title}}</td>
                                            <td>{{ source.amount}}</td>
                                            <td>{{ source.after_approval_amount}}</td>
                                            <td>{{ source.created_at | dateFormat}}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" v-on:click="editModal(source)"><i class="fas fa-briefcase-medical"></i> Edit</button>
                                                
                                                <button  href="javascript:void(0)" class="btn btn-danger btn-sm" v-on:click="deleteSource(source.id)"><i class="fas fa-trash-alt"></i> Delete</button>
                                            </td>
                                        </tr>
                                        <tr v-if="totalRecord == 0"><td colspan="6" style="text-align: center;">No record found.</td></tr>
                                    </tbody>
                                </table>
								<span v-if="totalRecord > 10"> 
									  <pagination
									  :pagination="pagination"
									  :current_page="current_page"
									  :last_page="last_page"
									  ></pagination>
								</span>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="figureModal" tabindex="-1" role="dialog" aria-labelledby="figureModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="figureModalLabel" v-if="editMode">Edit Figure</h5>
                            <h5 class="modal-title" id="figureModalLabel" v-else >Add Figure</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? editFigure() : createFigure()" @keydown="form.onKeydown($event)">
                            <div class="modal-body">
                                <alert-error :form="form"></alert-error>
                                <div class="form-group">
                                    <label for="title" class="col-md-3">Title</label>
                                    <input v-model="form.title" type="text" name="title" id="title"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('title') }" placeholder="Source Title">
                                    <has-error :form="form" field="title"></has-error>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="col-md-3">Description</label>
                                    <textarea id="description" v-model="form.description" size="lg" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" placeholder="Large textarea"></textarea>
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
    export default {
        created() {
            this.showLoader();
            //this.getSources();
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
                    title: '',
                    description:'',
                })
            }
        },
        methods: {
            addFigureModal () {
                this.form.reset();
                this.form.clear();
                this.editMode = false;
                this.form.status = 1;
                $("#figureModal").modal('show');
            },
            editModal (data) {
                // Make a request for a get sources 
                this.form.reset();
                this.form.clear();
                this.form.fill(data);
                this.editMode = true;
                
                $("#figureModal").modal('show');
            },
            createFigure() {
                // Submit the form via a POST request
                this.showLoader();
                this.form.post('admin/store-figure')
                .then(({ data }) => { 
                    //this.getSources();
                    Toast.fire({ icon: 'success', title: 'Figure added successfully' });
                    $("#figureModal").modal('hide');
                    //this.$Progress.finish()
                    this.removeLoader();
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.$Progress.finish()
                    this.removeLoader();
                });
            },
            editSource(event) {
                this.removeLoader();
                this.$Progress.start();
                this.form.put('api/appointmentSources/' + this.form.id)
                .then(({ data }) => { 
                    this.getSources();
                    Toast.fire({ icon: 'success', title: 'Source updated successfully' });
                    $("#sourceModal").modal('hide');
                    this.$Progress.finish()
                    this.removeLoader();
                })
                .catch((error)=>{
                    Swal.fire('Failed!', 'Something went wrong.', 'warning');
                    this.$Progress.finish()
                    this.removeLoader();
                });
                
            },

            deleteSource (id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                       
                        this.showLoader();

                        axios.delete('api/appointmentSources/'+id).then(({data})=>{
                            console.log(data.status);
                            if(data.status == "success"){
                                this.getSources();
                                Swal.fire('Deleted!', 'Source has been deleted.', 'success')
                            }else{
                                Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            }
                            
                            this.removeLoader();

                        }).catch(({data})=>{
                            Swal.fire('Failed!', 'Something went wrong.', 'warning')
                            this.removeLoader();
                        });
                    }
                })
            },
            searchTemplateKeyup(){
                if(this.search == ''){
                    this.totalRecord = 0;
                    this.current_page = {};
                    this.pagination = 1;
                    this.last_page = '';
                    this.getSources();
                }
            },
            searchTemplate(){
                if(this.search != ''){
                    $('.search_error').text('');
                    this.totalRecord = 0;
                    this.current_page = 1;
                    this.pagination = {};
                    this.last_page = '';
                    this.getSources();
                }else{
                    $('.search_error').text('This field is required.');
                    return false;
                }
            },
            getSources () {
                this.showLoader();
                this.pageURL('/api/getSourcesWithPaginate'); 
            },
            pageURL (url) {
                let param = {"search" : this.search }
                axios.get(url,{ params: param}).then(({ data }) => {
                    this.current_page = data.current_page;
                    this.last_page    = data.last_page;
                    this.pagination   = data; 
                    this.totalRecord  = data.total;
                   
                    this.removeLoader();
                });
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
