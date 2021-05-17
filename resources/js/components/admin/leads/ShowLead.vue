<template>
    <div>
        <div class="text-center" v-if="loading" style="margin-top: 10%">
            <img src="/img/loader.svg" />
        </div>
        <div class="lead-container" v-else>
            <div class="page-title d-flex">
                <h3>
                    Appointment Activity
                </h3>

                <div class="ml-auto">
                    <button class="btn btn-primary btn-sm" @click="editLead"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn btn-sm btn-success" @click="$router.go(-1)"><i class="fas fa-undo"></i> Back
                    </button>
                </div>
            </div>
            <!-- modal window for editLead -->
            <div class="modal fade" id="editLead" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="moduleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Appointment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="#">
                            <div class="modal-body">
                                <div class="row">
                                    <div v-if="errors.length > 0" class="alert alert-danger" role="alert">
                                        <ul>
                                            <li v-for="error in errors" :key="error">{{ error }}</li>
                                        </ul>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Source</label>
                                        <select class="form-control" v-model="lead.source">
                                            <option v-for="source in sources" :key="source.id" :value="source.id">
                                                {{source.name | capitalize}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Product</label>

                                        <select class="form-control" v-model="lead.product_id">
                                            <option value="" selected>Select Product</option>
                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                {{ product.name | capitalize }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Pitch</label>

                                        <input type="text" class="form-control" v-model="lead.pitch" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Revenue</label>

                                        <input type="text" class="form-control" v-model="lead.revenue" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Cash Collected</label>

                                        <input type="text" class="form-control" v-model="lead.cash_collected" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Deposit</label>

                                        <input type="text" class="form-control" v-model="lead.deposit" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Outcome</label>

                                        <select class="form-control" v-model="lead.outcome"
                                            @change="handleComment(lead.leadId)" disabled>
                                            <option value="" selected>Select Outcome</option>
                                            <option v-for="outcome in outcomeFields" data-toggle="modal"
                                                data-target="#commentModal" :key="outcome.id" :value="outcome.id">
                                                {{ outcome.name | capitalize }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Appointment Date</label>

                                        <datetime type="datetime" class="form-control" v-model="data.appointment_date"
                                            format="yyyy-MM-dd HH:mm:ss" :min-datetime="disabledDates"></datetime>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Close Date</label>
                                        <datepicker
                                        class="form-control custom-class"
                                        v-model="data.date_closed"
                                        :disabled-dates="disabledCloseDate"
                                        :open-date="new Date()"
                                      ></datepicker>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="">Lead</label>

                                        <select class="form-control" v-model="lead.client_id">
                                            <option value="" selected>Select Client</option>
                                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                                {{ client.name | capitalize }} ({{ client.email }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6" v-show="canChangeAssignee">
                                        <label for="">Assigned to</label>

                                        <select class="form-control" v-model="lead.assigned_to">
                                            <option value="" selected>Select user</option>
                                            <option v-for="assignee in assignees" :key="assignee.id"
                                                :value="assignee.id">
                                                {{ assignee.name | capitalize }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="submit" class="btn btn-primary" @click.prevent="storeUpadatedLead">
                                    Update Appointments
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card bg-light">
                        <div class="card-header bg-primary">
                            <h3 class="card-title x-title ">Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="x-element x-action">


                                <div class=" row">
                                    <div class="col-md-6">
                                        <div class=" row">

                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="far fa-calendar-alt"></i> Appointment Date :</span>
                                                <span
                                                    class="text-block ml-2 font-weight-bold">{{lead.appointment_date}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-user-tie"></i>
                                                    Prosp. Name :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{lead.client.name}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-phone-alt"></i>
                                                    Prosp. Number :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{lead.client.phone}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-envelope"></i>
                                                    Prosp. Email :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{lead.client.email}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"> <i class="fab fa-sourcetree"></i>
                                                    Source :</span>
                                                <span v-if="lead.source_data" class="text-block ml-2 font-weight-bold">
                                                    {{lead.source_data.name}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="fas fa-shopping-cart"></i> Product :</span>
                                                <span class="text-block ml-2 font-weight-bold" v-if="lead.product">
                                                    {{lead.product.name}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"> <i
                                                        class="fas fa-balance-scale"></i> Pitch :</span>
                                                <span class="text-block ml-2 font-weight-bold"> {{lead.pitch}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class=" row">

                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="fas fa-search-dollar"></i> Revenue :</span>
                                                <span class="text-block ml-2 font-weight-bold"> {{lead.revenue}}
                                                    USD</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="fas fa-hand-holding-usd"></i> Cash Collected :</span>
                                                <span class="text-block ml-2 font-weight-bold"> {{lead.cash_collected}}
                                                    USD</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="fas fa-funnel-dollar"></i> Deposit :</span>
                                                <span class="text-block ml-2 font-weight-bold"> {{lead.deposit}}
                                                    USD</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i
                                                        class="far fa-calendar-alt"></i> Date Closed :</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{lead.date_closed}}</span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"> <i class="fas fa-poll"></i>
                                                    Outcome :</span>
                                                <span class="ml-2 font-weight-bold outcome-font">
                                                    {{lead.outcome_status.name}}</span>
                                                <span v-if="this.lead.outcome_status.id != 9">
                                                    <a v-if="outcome_condition" @click="getModal" id="modals" class="link text-secondary ml-2"
                                                        data-toggle="modal" data-target="#commentModal">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="col-md-12 d-flex">
                                                <span class="heading-text text-muted"><i class="fas fa-user"></i>
                                                    Assigned to:</span>
                                                <span class="text-block ml-2 font-weight-bold">
                                                    {{lead.assigned.name}}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Modal for outcome -->
                                <div class="modal fade" id="commentModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Outcome</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="">Outcome</label>
                                                        <select class="form-control" @change="fetchForm"
                                                            v-model="lead.outcome">
                                                            <option disabled value="">Select Outcome</option>
                                                            <option v-for="outcome in outcomeFields" :key="outcome.id"
                                                                :value="outcome.id">
                                                                {{ outcome.name | capitalize }}
                                                            </option>
                                                        </select>

                                                        <!-- suboutcome forms populate automatically wrt the dropdown item selected -->
                                                        <form>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div v-for="(frm,key) in form.fields" :key="key">
                                                                        <div v-if="frm.label != null && frm.type === 1">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <input type="text" class="form-control"
                                                                                v-model="suboutcome_form[frm.label]">
                                                                        </div>

                                                                        <div v-if="frm.label != null && frm.type === 2">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <select class="form-control"
                                                                                v-model="suboutcome_form[frm.label]">
                                                                                <option value="" disabled selected>
                                                                                    Please select</option>
                                                                                <option
                                                                                    v-for="(val,index) in frm.value.split(',')"
                                                                                    :key="index" :value="val">{{val}}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div v-if="frm.label != null && frm.type === 3">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <div v-for="(item,i) in frm.value.split(',')"
                                                                                :key="i">
                                                                                <input type="radio" class="radio"
                                                                                    :id="item" :value="item"
                                                                                    v-model="suboutcome_form[frm.label]"><label
                                                                                    :for="item">{{item}}</label>
                                                                            </div>

                                                                        </div>
                                                                        <div v-if="frm.label != null && frm.type === 4">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <div v-for="(item,i) in frm.value.split(',')"
                                                                                :key="i">
                                                                                <input type="checkbox" :name="item"
                                                                                    class="check" :id="item"
                                                                                    :value="item"
                                                                                    @change="changed($event,item,frm.label)"><label
                                                                                    :for="item">{{item}}</label>
                                                                            </div>

                                                                        </div>
                                                                        <div v-if="frm.label != null && frm.type ===5">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <datetime type="date" class="form-control"
                                                                                :value="suboutcome_form[frm.label]"
                                                                                placeholder="Select Date" format="yyyy-MM-dd"
                                                                                @input="suboutcome_form[frm.label] = fixDate($event)"
                                                                                :min-datetime="disabledDates">
                                                                            </datetime>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div v-for="(frm) in form.lead_fields"
                                                                        :key="frm.label">
                                                                        <div v-if="frm.label != null">
                                                                            <label
                                                                                for="">{{frm.label | capitalize}}</label>
                                                                            <input type="text" class="form-control"
                                                                                v-model="suboutcome_form[frm.label]">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <button type="submit" class="btn btn-primary"
                                                        @click.prevent="handleSubForm($event)">
                                                        Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- modal ends -->

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-header bg-primary">
                            <h3 class="card-title x-title ">Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="x-element x-action">
                                <div class="col-md-12 d-flex">
                                    <span class="heading-text text-muted"><i class="fas fa-id-card"></i> Appointment ID
                                        :</span>
                                    <span class="text-block ml-2 font-weight-bold">#{{ lead.id }}</span>
                                </div>
                                <div class="col-md-12 d-flex">
                                    <span class="heading-text text-muted"><i class="far fa-calendar-alt"></i> Created By
                                        :</span>
                                    <span class="text-block ml-2 font-weight-bold">{{ creator.name }}</span>
                                </div>
                                <div class="col-md-12 d-flex">
                                    <span class="heading-text text-muted"><i class="far fa-calendar-alt"></i> Date
                                        Created :</span>
                                    <span class="text-block ml-2 font-weight-bold"> {{
                          moment(lead.created_at).format("DD-MM-YYYY")
                        }}</span>
                                </div>
                                <div class="col-md-12 d-flex">
                                    <span class="heading-text text-muted"><i class="fas fa-id-card"></i> Lead ID
                                        :</span>
                                    <span class="text-block ml-2 font-weight-bold"> #{{ lead.client_id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card w-100">
                        <div class="card-header bg-primary">
                            <h3 class="card-title x-title">Activity</h3>
                        </div>
                        <div class="card-body">
                            <div class="row" v-for="(comment) in comments" :key="comment.id">
                                <div class="col-12 comment mb-4">
                                    <div v-if="comment.inserted_values">
                                        <span><img
                                            class="img-circle img-sm mr-3"
                                            :src="comment.user.avatar ? '/storage/profile_pic/'+comment.user.avatar : '/admin/img/dummy_profile.png'"
                                            alt="User Image"
                                        />
                                        <b style="font-size:14px">{{comment.user.name | capitalize}}</b></span>
                                            <small style="float:right">
                                                {{ moment(comment.created_at).format("DD-MM-YYYY, h:mm a") }}
                                            </small>
                                        <br>
                                         <span>Outcome --
                                            <i>"{{comment.outcome.name | capitalize}}"</i></span>
                                        <div class="col-9 ml-4" v-for="(value,key) in comment.inserted_values"
                                            :key="key">
                                            
                                            {{key | capitalize}} -- {{value}}
                                        </div>
                                    </div>
                                    <div v-if="comment.checked_values">
                                        <div class="col-9 ml-4" v-for="(value,key) in comment.checked_values"
                                            :key="key">
                                            <div v-for="(val,i) in value" :key="i">
                                                <span v-if="val">{{i | capitalize}} -- {{val}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card w-100">
                                <div class="card-header bg-primary">
                                    <h3 class="card-title x-title">Add Notes</h3>
                                </div>
                                <div class="card-body">
                                    <textarea class="form-control" @input="countWords" placeholder="Type notes here..."
                                        v-model="notes" rows="4"></textarea>
                                    <span :class="{'text-danger': hasError}"><b>Remaining Characters :</b>
                                        {{maxLength}}</span>
                                </div>
                                <div class="card-footer">
                                    <input v-if="show_notes_btn" type="button" @click.prevent="saveNotes" class="btn btn-primary" value="Save"
                                        :disabled="!notes.length > 0 || notes.length > totalLength">
                                    <div v-else>
                                        <input type="button" @click.prevent="saveNotes" class="btn btn-success" value="Update"
                                            :disabled="!notes.length > 0 || notes.length > totalLength">
                                        <input type="button" @click.prevent="cancelNotes" class="btn btn-danger" value="Cancel">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card w-100">
                                <div class="card-header bg-primary">
                                    <h3 class="card-title x-title">Notes</h3>
                                </div>
                                <div class="card-body">
                                    <div v-if="data_notes.length">
                                        <div class="notes" v-for="note in data_notes"
                                            :key="note.id"><b>{{note.user.name | capitalize}}</b> 
                                            <small class="float-right">{{moment(note.updated_at).format("DD-MM-YYYY, h:mm a")}}
                                                <a v-if="note.user_id == auth_user" class="link text-primary ml-2 edit_note" title="Edit Note" @click.prevent="edit_note(note)">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            </small>
                                            <br>
                                            <span>{{note.notes}}</span><br>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <p>No notes added yet!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import {
        bus
    } from '/resources/js/app.js';

    export default {
        data() {
            return {
                form: {},
                suboutcome_form: {},
                checkedlist: [],
                comment: {
                    comment: "",
                    lead_id: "",
                    outcome_id: "",
                },
                moment: moment,
                comments: {},
                lead: {},
                creator: {},
                loading: false,
                notes: '',
                note_id: '',
                data_notes: {},
                show_notes_btn:true,
                auth_user: this.$gate.user.id,
                maxLength: 320,
                totalLength: 320,
                hasError: false,
                sources: {},
                products: {},
                outcomeFields: {},
                clients: {},
                errors: [],
                disabledDates: new Date().toISOString(),
                disabledCloseDate: {
                    from :  new Date()
                },
                data: {
                    appointment_date: '',
                    date_closed: ''
                },
                canChangeAssignee: this.$gate.roles.includes('Admin') || this.$gate.roles.includes('Sdr'),
                assignees: {},
            };
        },
        watch: {
            '$route.params.lead_id': function (lead_id) {
                if (lead_id != undefined) {
                    this.showNotes()
                    this.showlead()
                }
            }
        },
        computed:{
            outcome_condition(){
                if(this.lead.outcome_status.id != 2){
                    return true;
                }
                
            }
        },
        created() {
            this.loading = true;
            this.showlead();
            this.showNotes()
        },

        methods: {
            cancelNotes(){
                this.notes = ''
                this.show_notes_btn = true
                this.note_id = ''
            },
            async edit_note(note){
                try {
                    this.notes = note.notes
                    this.note_id = note.id
                    this.show_notes_btn = false
                } catch (error) {
                    console.log(error)
                }
            },
            
            fixDate(event){
                return moment(event).format('YYYY-MM-DD');
            },
            async handleSubForm(event) {
                try {
                    event.target.disabled = true
                    const res = await axios.post('/admin/saveSubOutcomeForm', {
                        'inputs': this.suboutcome_form,
                        'outcome_id': this.lead.outcome,
                        'lead_id': this.lead.id,
                        'user_id': this.$gate.user.id,
                        'checked_values': this.checkedlist,
                        'task_role' : this.form.task_role,
                        'due_days' : this.form.due_days,
                        'task_label' : this.form.task_label
                    })

                    this.showlead()
                    this.suboutcome_form = {}
                    this.checkedlist = []

                    this.$toaster.success("Outcome changed successfully!");
                    event.target.disabled = false
                    $("#commentModal").modal("hide");
                    bus.$emit('data_changed', 'anydata')
                } catch (error) {
                    this.$toaster.error("No inputs found. Please try again!");
                    event.target.disabled = false
                }
            },
            changed(e, item, label) {
                if (e.target.checked == false) {
                    let index = this.checkedlist.findIndex(x => x[label] === item)
                    this.checkedlist.splice(index, 1)
                }
                if (e.target.checked == true) {
                    this.checkedlist.push({
                        [label]: item
                    })
                }
            },
            getModal() {
                this.getOutcomeStatus()
                this.fetchForm()
            },
            async fetchForm() {

                try {
                    const response = await axios.post('/admin/fetchForm', {
                        outcome_id: this.lead.outcome,
                        // lead_id: this.comment.lead_id
                    })
                    if (response) {
                        this.checkedlist = []
                        this.form = {}
                        this.suboutcome_form = {}
                        this.form = response.data.suboutcome
                    }
                } catch (error) {
                    console.log(error)
                }
            },
            storeUpadatedLead() {

                this.errors = [];
                axios
                    .post("/admin/updateLead/" + this.lead.id, {
                        source: this.lead.source,
                        product_id: this.lead.product_id,
                        pitch: this.lead.pitch,
                        revenue: this.lead.revenue,
                        cash_collected: this.lead.cash_collected,
                        deposit: this.lead.deposit,
                        outcome: this.lead.outcome,
                        appointment_date: this.data.appointment_date,
                        client_id: this.lead.client_id,
                        date_closed: this.data.date_closed,
                        assigned_to: this.lead.assigned_to,
                    })
                    .then((response) => {
                        $("#editLead").modal("hide");
                        this.data.leadId = "";
                        this.lead.source = "";
                        this.lead.product = "";
                        this.lead.pitch = "";
                        this.lead.revenue = "";
                        this.lead.cash_collected = "";
                        this.lead.deposit = "";
                        this.lead.outcome = "";
                        this.data.appointment_date = "";
                        this.lead.client_id = "";
                        this.data.date_closed = "";
                        this.data.assigned_to = "";
                        this.showlead();
                        this.showNotes()
                        this.$toaster.success('Appointment updated successfully!')
                    })
                    .catch((errors) => {
                        if (errors.response.status === 422) {
                            this.flashErrors(errors.response.data.errors);
                        }
                    });
            },
            getClients() {
                axios
                    .get("/admin/getClients")
                    .then((response) => {
                        this.clients = response.data.results;
                    })
                    .catch((e) => console.log(e));
            },
            getAssignees() {
                axios
                    .get("/admin/getClosers")
                    .then((response) => {
                        this.assignees = response.data.results;
                    })
                    .catch((e) => console.log(e));
            },
            getOutcomeStatus() {
                axios
                    .get("/admin/getAllLeadStatus")
                    .then((response) => {
                        setTimeout(() => {
                            this.loading = false;
                        }, 500);
                        this.outcomeFields = response.data.results;
                    })
                    .catch((e) => console.log(e));
            },
            getProducts() {
                axios
                    .get("/admin/getAllProducts")
                    .then((response) => {
                        this.products = response.data.results;
                    })
                    .catch((e) => console.log(e));
            },
            editLead() {
                this.getSources()
                this.getProducts()
                this.getOutcomeStatus()
                this.getClients()
                this.getAssignees()
                this.data.appointment_date = moment.utc(this.lead.appointment_date).local().toISOString();
                this.data.date_closed = this.lead.date_closed ?
                    moment.utc(this.lead.date_closed).local().toISOString() :
                    "";
                $('#editLead').modal('show')
            },
            getSources() {
                axios.get('/admin/getAllLeadSources')
                    .then(response => {
                        this.sources = response.data.results
                    })
                    .catch(e => console.log(e))
            },
            countWords() {
                if (this.notes.length > this.totalLength) {
                    this.hasError = true
                } else {
                    this.hasError = false
                }
                this.maxLength = this.totalLength - this.notes.length;
            },
            async saveNotes() {
                try {
                    let lead_id = this.$route.params.lead_id;
                    const res = await axios.post('saveNotes', {
                        notes: this.notes,
                        lead_id: lead_id,
                        note_id: this.note_id
                    })
                    if (res) {
                        this.$toaster.success('Note added successfully!')
                        this.notes = ''
                        this.note_id = ''
                        this.showNotes()
                        this.show_notes_btn = true
                    }
                } catch (error) {
                    this.$toaster.error('Please fill some notes!')
                }
            },
            async showlead() {
                try {
                    Cookies.remove("show_id");
                    let lead_id = this.$route.params.lead_id;
                    const response = await axios.get("/admin/showLead/" + lead_id);
                    this.loading = false;
                    this.comments = response.data.comments;
                    this.lead = response.data.lead;
                    this.creator = response.data.user;
                    
                } catch (error) {
                    console.log(error);
                }
            },
            async showNotes() {
                try {
                    let lead_id = this.$route.params.lead_id;
                    const res = await axios.get("/admin/showNotes/" + lead_id);
                    this.data_notes = res.data.notes
                } catch (error) {
                    console.log(error)
                }
            },
            flashErrors(errors) {
                for (const [key, value] of Object.entries(errors)) {
                    for (let item in value) {
                        this.errors.push(value[item]);
                    }
                }
            },
        },
    };

</script>
<style scoped>
    .comment {
        background-color: #EDEFF1;
        padding: 8px;
        border-radius: 3px;
        font-family: roboto;
    }
    .notes{
        background-color: #EDEFF1;
        padding:5px;
        border-radius:3px;
        margin-bottom:10px;
    }
    .text-danger {
        color: red;
    }

    #modals,.edit_note {
        cursor: pointer;
    }

    .outcome-font {
        font-size: 14px;
    }

</style>
