<template>
    <div>
        <div class="card">
            <div class="card-body">
                <h3>Create Appointment <button class="btn btn-sm btn-success float-right" @click="$emit('view-dashboard')">
                        <i class="fas fa-undo"></i> Back</button></h3>
                <hr />
                <div v-if="errors.length>0" class="alert alert-danger" role="alert">
                    <ul>
                        <li v-for="error in errors" :key="error">{{error}}</li>
                    </ul>
                </div>
                <form action="#">
                    <div class="row form-group">
                        <label for="" class="col-md-3">Lead</label>
                        <div class="col-md-8">
                            <select class="form-control" v-model="data.client_id">
                                <option value="" selected>Select Lead</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{client.name | capitalize}} ({{ client.email }})</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="btn btn-primary btn-sm float-right" @click="openClientModal"><i
                                    class="fas fa-plus"></i> Lead</div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Source</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="data.source">
                                <option value="" selected>Select Source</option>
                                <option v-for="source in sources" :key="source.id" :value="source.id">
                                    {{source.name | capitalize}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Product</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="data.product_id">
                                <option value="" selected>Select Product</option>
                                <option v-for="product in products" :key="product.id" :value="product.id">
                                    {{product.name | capitalize}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Pitch</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.pitch">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Revenue</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.revenue">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Cash Collected</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.cash_collected">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Deposit</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" v-model="data.deposit">
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="" class="col-md-3">Appointment Date</label>
                        <div class="col-md-9">
                            <datetime type="datetime" class="form-control" v-model="data.appointment_date"
                                format="yyyy-MM-dd HH:mm:ss" :min-datetime="disabledDates">
                            </datetime>
                        </div>
                    </div>
                    <div class="row form-group" v-show="canChangeAssignee">
                        <label for="" class="col-md-3">Assigned to</label>
                        <div class="col-md-9">
                            <select class="form-control" v-model="data.assigned_to">
                                <option value="" selected>Select user</option>
                                <option
                                  v-for="assignee in assignees"
                                  :key="assignee.id"
                                  :value="assignee.id"
                                >
                                  {{ assignee.name | capitalize }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <button class="btn btn-primary" @click.prevent="storeLead($event)">Create Appointment</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create lead</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div v-if="popupErrors.length>0" class="alert alert-danger" role="alert">
                                        <ul>
                                            <li v-for="error in popupErrors" :key="error">{{error}}</li>
                                        </ul>
                                    </div>
                                    <div class="row form-group">
                                        <label for="" class="col-md-3">Name</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" v-model="clientData.name"
                                                placeholder="John Doe" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="" class="col-md-3">Email</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" v-model="clientData.email"
                                                placeholder="email@example.com" />
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="" class="col-md-3">Phone</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" v-model="clientData.phone"
                                                placeholder="1234567890" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" @click.prevent="addClient">Create
                                Lead</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                data: {
                    source: '',
                    product_id: '',
                    pitch: '',
                    revenue: '',
                    cash_collected: '',
                    deposit: '',
                    appointment_date: '',
                    client_id: '',
                    assigned_to: '',
                },
                errors: [],
                clients: {},
                clientData: {
                    name: '',
                    email: '',
                    phone: ''
                },
                popupErrors: [],
                products: {},
                sources: {},
                disabledDates: new Date().toISOString(),
                canChangeAssignee: this.$gate.roles.includes('Admin') ||  this.$gate.roles.includes('Sdr'),
                assignees: {},
            }
        },
        created() {
            this.getClients();
            this.getProducts();
            this.getSources();
            this.getAssignees();
        },
        methods: {
            getClients() {
                axios.get('admin/getClients')
                    .then(response => {
                        this.clients = response.data.results
                    })
                    .catch(e => console.log(e))
            },
            getAssignees() {
              axios
                .get("admin/getClosers")
                .then((response) => {
                  this.assignees = response.data.results;
                })
                .catch((e) => console.log(e));
            },
            getProducts() {
                axios.get('admin/getAllProducts')
                    .then(response => {
                        this.products = response.data.results
                    })
                    .catch(e => console.log(e))
            },
            getSources() {
                axios.get('admin/getAllLeadSources')
                    .then(response => {
                        this.sources = response.data.results
                    })
                    .catch(e => console.log(e))
            },
            storeLead(event) {
                this.errors = [];
                event.target.diabled = true
                axios.post('admin/lead', {
                        source: this.data.source,
                        product_id: this.data.product_id,
                        pitch: this.data.pitch,
                        revenue: this.data.revenue,
                        cash_collected: this.data.cash_collected,
                        deposit: this.data.deposit,
                        appointment_date: this.data.appointment_date,
                        client_id: this.data.client_id,
                        assigned_to: this.data.assigned_to,
                    })
                    .then(response =>{
                        this.$emit('created-lead', 'Appointment Created Successfully!')
                        event.target.diabled = false
                    })
                    .catch(errors => {
                        if (errors.response.status === 422) {
                            this.flashErrors(errors.response.data.errors)
                        }
                        event.target.diabled = false
                    })
            },
            flashErrors(errors) {
                for (const [key, value] of Object.entries(errors)) {
                    for (let item in value) {
                        this.errors.push(value[item])
                    }
                }
            },
            openClientModal() {
                $("#clientModal").modal('show');
            },
            addClient() {
                this.errors = [];
                axios.post('admin/clients-list', {
                        name: this.clientData.name,
                        phone: this.clientData.phone,
                        email: this.clientData.email
                    })
                    .then(response => {
                        $("#clientModal").modal('hide');
                        this.getClients();
                        this.data.client_id = response.data.id
                        Toast.fire({
                            icon: 'success',
                            title: 'Lead added successfully'
                        })
                    })
                    .catch(errors => {
                        if (errors.response.status === 422) {
                            this.flashPopupErrors(errors.response.data.errors)
                        }
                    })
            },
            flashPopupErrors(errors) {
                for (const [key, value] of Object.entries(errors)) {
                    for (let item in value) {
                        this.popupErrors.push(value[item])
                    }
                }
            },
        }
    }

</script>
