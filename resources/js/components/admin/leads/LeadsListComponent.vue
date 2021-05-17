<template>
  <div>
    <div class="text-center" v-if="loading" style="margin-top: 10%">
      <img src="/img/loader.svg" />
    </div>
    <div v-else>
      <div class="card" v-if="active.dashboard">
      <div class="card-body">
        <h3>
          Manage Appointments
          <button
            class="btn btn-primary btn-sm float-right"
            @click="setActive('createLead')"
            v-if="canCreateLead"
          >
            <i class="fas fa-plus"></i> Appointment
          </button>
          
          <button class="btn btn-warning mr-2 btn-sm float-right" @click="handleLeadsCsv($event)">Export <i class="fas fa-file-export"></i></button>
          <button
            class="openbtn mr-2 btn btn-sm btn-secondary float-right"
            @click="openNav()"
          >
            <i class="fas fa-filter"></i>
          </button>
        </h3>
        <data-table
          url="admin/leads-list"
          :columns="filteredData"
          order-dir="desc"
          ref="leadTable"
          :filters="filters"
        >
          <div slot="filters" slot-scope="{ tableFilters, perPage }">
            <div class="row mb-2">
                <div class="col-md-3">
                    <select class="form-control" v-model="tableFilters.length">
                        <option :key="page" v-for="page in perPage">{{ page }}</option>
                    </select>
                </div>
                <div :class="canArchiveAppointment ? 'col-md-4' : 'col-md-6'">
                    <input type="hidden" v-model="tableFilters.filters.outcome">
                    <input type="hidden" v-model="tableFilters.filters.appointmentStartDate">
                    <input type="hidden" v-model="tableFilters.filters.appointmentEndDate">
                    <input type="hidden" v-model="tableFilters.filters.prospectName">
                    <input type="hidden" v-model="tableFilters.filters.prospectEmail">
                    <input type="hidden" v-model="tableFilters.filters.source">
                    <input type="hidden" v-model="tableFilters.filters.product">
                    <input type="hidden" v-model="tableFilters.filters.closeStartDate">
                    <input type="hidden" v-model="tableFilters.filters.closeEndDate">
                    <input type="hidden" v-model="tableFilters.filters.assignee">
                    <input type="hidden" v-model="tableFilters.filters.type">
                </div>
                <div v-if="canArchiveAppointment" class="col-md-2">
                  <select class="form-control" v-model="filters.type" required>
                    <option v-for="option in data_type" :key="option.id" :value="option.type" >{{ option.type }}</option>
                  </select>
                </div>
                <div class="col-md-3">
                    <input
                        name="name"
                        class="form-control"
                        v-model="tableFilters.search"
                        placeholder="Search Appointment">
                </div>
            </div>
            <div class="row mb-2">
              <div class="col-md-11">
                <label>Select Columns</label>
                <multiselect
                    v-model="filteredData"
                    :options="values"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false" 
                    :preserve-search="true" 
                    placeholder="Pick some" 
                    label="label"
                    track-by="name"
                    @select="dispatchAction"
                    @remove="dispatchClose"
                    >
                  </multiselect>
              </div>
              <div class="col-md-1">
                
                <button :disabled="col_pattern" class="btn btn-primary btn-sm float-right mt-5" @click="save_columns">Save Pattern</button>
              </div>
            </div>
          </div>
        
          <tbody slot="body" slot-scope="{ data }" class="laravel-vue-datatable-tbody">
            <tr
              @click="clicked(item.id,item)"
              class="laravel-vue-datatable-tbody-tr" :class="highlightedRow(item) ? dangerClass : object"
              :key="item.id"
              role="button"
              v-for="item in data">
                <td 
                  class="laravel-vue-datatable-td"
                  :key="column.name"
                  v-for="column in filteredData"
                  >
                    <data-table-cell
                        :value="item"
                        :name="column.name"
                        :meta="column.meta"
                        :comp="column.component"
                        :classes="column.classes"
                        :transform="column.transform"
                        :orderable="column.orderable"
                        :component="column.component"
                        :handler="column.handler"
                        :event="column.event"
                        :style="{'background-color' : column.name=='outcome_status' ? item.outcome_status.color : '', 'border-radius': '16px', padding: '5px 10px'}"
                        >
                    </data-table-cell>
                </td>
            </tr>
          </tbody>
        </data-table>

        <div id="mySidebar" class="sidebar bg-white shadow-lg px-0">
          <div class="modal-content1">
            <div class="modal-header bg-primary rounded-0">
              <h5 class="modal-title text-white">
                <i class="fas fa-filter"></i> Filter Appointments
              </h5>
              <a
                href="javascript:void(0)"
                class="closebtn"
                @click="closeNav()"
              >×</a>
            </div>
            <form action="#">
              <div class="modal-body">
                <div class="row">

                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Appointment date</label>
                      <div class="sidebar-filter">
                          <DateRangePicker
                              ref="picker"
                              :opens="opens"
                              :showDropdowns="showDropdowns"
                              :singleDatePicker="singleDatePicker"
                              v-model="dateRange"
                              @update="updateDateRange"
                          >
                          </DateRangePicker>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Outcome</label>
                      <select class="form-control form-control-sm" v-model="filters.outcome">
                        <option disabled value="">Select Outcome</option>
                        <option
                          v-for="outcome in outcomeFields"
                          :key="outcome.id"
                          :value="outcome.id"
                        >
                          {{ outcome.name | capitalize }}
                        </option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Prospect name</label>
                      <input
                        type="text"
                        v-model="filters.prospectName"
                        class="form-control form-control-sm"
                      />
                    </div>
                    <div class="form-group">
                      <label>Prospect Email</label>
                      <input
                        type="text"
                        v-model="filters.prospectEmail"
                        class="form-control form-control-sm"
                      />
                    </div>
                    <div class="form-group">
                      <label>Source</label>
                      <select class="form-control form-control-sm" v-model="filters.source">
                        <option disabled value="">Select Source</option>
                        <option v-for="source in sources" :key="source.id" :value="source.id">{{source.name | capitalize}}</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Product</label>
                      <select class="form-control form-control-sm" v-model="filters.product">
                        <option value="" selected>Select Product</option>
                        <option
                          v-for="product in products"
                          :key="product.id"
                          :value="product.id"
                        >
                          {{ product.name | capitalize }}
                        </option>
                      </select>
                    </div>
                    <div class="form-group" v-show="canChangeAssignee">
                      <label>Assigned to</label>
                      <select class="form-control form-control-sm" v-model="filters.assignee">
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
                    <div class="form-group">
                      <label>Close date</label>
                      <div class="sidebar-filter">
                          <DateRangePicker
                              ref="picker"
                              :opens="opens"
                              :showDropdowns="showDropdowns"
                              :singleDatePicker="singleDatePicker"
                              v-model="closeDateRange"
                              @update="updateCloseDateRange"
                          >
                          </DateRangePicker>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger btn-sm" @click="clearFilter()">
                  Reset
                </button>
              </div>
            </form>
          </div>
        </div>

      </div>
      </div>
      <createLead
      v-if="active.createLead"
      v-on:view-dashboard="setActive('dashboard')"
      v-on:created-lead="flashMessageandReload"
      ></createLead>
      <show-lead
      v-if="active.showLead"
      v-on:view-dashboard="setActive('dashboard')"
      ></show-lead>
      <!-- //modal -->
      <div
      class="modal fade"
      id="commentModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Outcome</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form>
            <div class="modal-body">
              <div class="form-group">
                <label for="" >Outcome</label>
                <select class="form-control" @change="fetchForm" v-model="comment.outcome_id">
                  <option disabled value="">Select Outcome</option>
                  <option
                    v-for="outcome in outcomeFields"
                    :key="outcome.id"
                    :value="outcome.id"
                    :disabled="outcome.id == 8"
                  >
                    {{ outcome.name | capitalize }}
                  </option>
                </select>

                <!-- suboutcome forms populate automatically wrt the dropdown item selected -->
                  <form>
                    <div class="row">
                      <div class="col-6">
                        <div v-for="(frm,key) in form.fields" :key="key">
                          <div v-if="frm.label != null && frm.type === 1">
                            <label for="">{{frm.label | capitalize}}</label>
                            <input type="text" class="form-control" v-model="suboutcome_form[frm.label]">
                          </div>
                          
                          <div v-if="frm.label != null && frm.type === 2">
                            <label for="">{{frm.label | capitalize}}</label>
                                <select class="form-control" v-model="suboutcome_form[frm.label]">
                                    <option value="" disabled selected>Please select</option>
                                    <option v-for="(val,index) in frm.value.split(',')" :key="index" :value="val">{{val}}</option>
                                </select>
                          </div>
                          <div v-if="frm.label != null && frm.type === 3">
                            <label for="">{{frm.label | capitalize}}</label>
                            <div v-for="(item,i) in frm.value.split(',')" :key="i">
                                <input type="radio" class="radio" :id="item" :value="item" v-model="suboutcome_form[frm.label]"><label :for="item">{{item}}</label>
                            </div>
                            
                          </div>
                          <div v-if="frm.label != null && frm.type === 4">
                            <label for="">{{frm.label | capitalize}}</label>
                            <div v-for="(item,i) in frm.value.split(',')" :key="i">
                                <input type="checkbox" :name="item" class="check" :id="item" :value="item" @change="changed($event,item,frm.label)"><label :for="item">{{item}}</label>
                            </div>
                            
                          </div>
                          <div v-if="frm.label != null && frm.type ===5">
                            <label for="">{{frm.label | capitalize}}</label>
                            <datetime
                              type="date"
                              class="form-control"
                              :value="suboutcome_form[frm.label]" placeholder="Select Date" format="yyyy-MM-dd" @input="suboutcome_form[frm.label] = fixDate($event)"
                              :min-datetime="disabledDates"
                              ></datetime>
                          </div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div v-for="(frm) in form.lead_fields" :key="frm.label">
                          <div v-if="frm.label != null">
                            <label for="">{{frm.label | capitalize}}</label>
                            <input type="text" class="form-control" v-model="suboutcome_form[frm.label]">
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
              <button type="submit" class="btn btn-primary" @click.prevent="handleSubForm($event)">
                Save
              </button>
            </div>
          </form>
        </div>
      </div>
      </div>
      <!-- modal ends -->
      <div
      class="modal fade"
      id="leadModal"
      tabindex="-1"
      role="dialog"
      data-backdrop="static"
      aria-labelledby="moduleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Appointment</h5>
            <button
              type="button"
              class="close"
              @click="remove_Cookie"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="#">
            <div class="modal-body">
              <div class="row">
                <div
                  v-if="errors.length > 0"
                  class="alert alert-danger"
                  role="alert"
                >
                  <ul>
                    <li v-for="error in errors" :key="error">{{ error }}</li>
                  </ul>
                </div>
                <div class="form-group col-md-6">
                  <label for="">Source</label>
                  <select class="form-control" v-model="data.source">
                      <option v-for="source in sources" :key="source.id" :value="source.id">{{source.name | capitalize}}</option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label for="">Product</label>

                  <select class="form-control" v-model="data.product_id">
                    <option value="" selected>Select Product</option>
                    <option
                      v-for="product in products"
                      :key="product.id"
                      :value="product.id"
                    >
                      {{ product.name | capitalize }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label for="">Pitch</label>

                  <input
                    type="text"
                    class="form-control"
                    v-model="data.pitch"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="">Revenue</label>

                  <input
                    type="text"
                    class="form-control"
                    v-model="data.revenue"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="">Cash Collected</label>

                  <input
                    type="text"
                    class="form-control"
                    v-model="data.cash_collected"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="">Deposit</label>

                  <input
                    type="text"
                    class="form-control"
                    v-model="data.deposit"
                  />
                </div>
                <div class="form-group col-md-6">
                  <label for="">Outcome</label>

                  <select
                    class="form-control"
                    v-model="data.outcome"
                    @change="handleComment(data.leadId)"
                    disabled
                  >
                    <option value="" selected>Select Outcome</option>
                    <option
                      v-for="outcome in outcomeFields"
                      data-toggle="modal"
                      data-target="#commentModal"
                      :key="outcome.id"
                      :value="outcome.id"
                    >
                      {{ outcome.name | capitalize }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label for="">Appointment Date</label>

                  <datetime
                    type="datetime"
                    class="form-control"
                    v-model="data.appointment_date"
                    format="yyyy-MM-dd HH:mm:ss"
                    :min-datetime="disabledDates"
                  ></datetime>
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
                  <label for="">Appointment</label>

                  <select class="form-control" v-model="data.client_id" disabled>
                    <option value="" selected>Select Appointment</option>
                      <option
                        v-for="client in clients"
                        :key="client.id"
                        :value="client.id"
                      >
                        {{ client.name | capitalize }} ({{ client.email }})
                      </option>
                  </select>
                </div>
                <div class="form-group col-md-6" v-show="canChangeAssignee">
                  <label for="">Assigned to</label>

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
            </div>
            <div class="modal-footer">
              <button
                type="button"
                @click="remove_Cookie"
                class="btn btn-danger"
                data-dismiss="modal"
              >
                Close
              </button>
              <button
                type="submit"
                class="btn btn-primary"
                @click.prevent="storeUpadatedLead"
              >
                Update Appointment
              </button>
            </div>
          </form>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script>
import createLead from "./CreateLead.vue";
import LeadsListAction from "./LeadsListAction.vue";
import showLead from "./ShowLead.vue";
import moment from "moment";
import {bus} from '/resources/js/app.js';
import Multiselect from 'vue-multiselect'

export default {
  components: {
    createLead,
    LeadsListAction,
    showLead,
    Multiselect
  },

  data() {
    return {
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
      form:{},
      suboutcome_form:{},
      checkedlist:[],
      loading: false,
      get_filtered_columns : [],
      col_pattern:false,
      comment: {
        comment: "",
        lead_id: "",
        outcome_id: "",
      },
      active: {
        createLead: false,
        dashboard: true,
        showLead: false,
      },
      columns: [
        {
          label: "Appointment Date",
          name: "appointment_date",
          orderable: true,
          transform: ({ data, name }) =>
            data[name]
              ? moment.utc(data[name]).local().format("h:mm A-dddd,MMMM Do,YYYY")
              : "",
        },
        {
          label: "Prospect Name",
          name: "client.name",
          orderable: false,
        },
        {
          label: "Prospect Number",
          name: "client.phone",
          orderable: false,
        },
        {
          label: "Prospect Email",
          name: "client.email",
          orderable: false,
        },
        {
          label: "Created by",
          name: "creator.name",
          orderable: false,
        },
        {
          label: "Source",
          name: "source_data",
          orderable: true,
          transform: ({ data, name }) => (data[name] ? data[name].name : ""),
        },
        {
          label: "Product",
          name: "product",
          orderable: false,
          transform: ({ data, name }) => (data[name] ? data[name].name : ""),
        },
        {
          label: "Pitch",
          name: "pitch",
          orderable: true,
        },
        {
          label: "Revenue",
          name: "revenue",
          orderable: true,
        },
        {
          label: "Cash Collected",
          name: "cash_collected",
          orderable: true,
        },
        {
          label: "Deposit",
          name: "deposit",
          orderable: true,
        },
        {
          label: "Date Closed",
          name: "date_closed",
          orderable: true,
          transform: ({ data, name }) =>
            data[name] ? moment.utc(data[name]).local().format("DD-MM-YYYY") : "",
        },
        {
          label: "Outcome",
          name: "outcome_status",
          orderable: true,
          transform: ({ data, name }) => data[name] ? data[name].name : "",
        },
        {
          label: "Created at",
          name: "created_at",
          orderable: true,
          transform: ({ data, name }) =>
            data[name]
              ? moment.utc(data[name]).local().format("h:mm A-dddd,MMMM Do,YYYY")
              : "",
        },
        {
          label: "Assigned to",
          name: "assigned",
          orderable: false,
          transform: ({ data, name }) => (data[name] ? data[name].name : ""),
        },
        {
          label: "Actions",
          name: "Actions",
          orderable: false,
          classes: {
            btn: true,
            "btn-primary": true,
            "btn-sm": true,
          },
          event: "click",
          handler: this.dataTableEvent,
          component: LeadsListAction,
        },
      ],
     values: [],
      data: {
        leadId: "",
        source: "",
        product_id: "",
        pitch: "",
        revenue: "",
        cash_collected: "",
        deposit: "",
        outcome: "",
        appointment_date: "",
        date_closed: "",
        client_id: "",
        assigned_to: "",
      },
      clients: {},
      assignees: {},
      outcomeFields: {},
      errors: [],
      products: {},
      sources: {},
      canCreateLead: this.$gate.permissions.includes('create_appointment'),
      canArchiveAppointment : this.$gate.permissions.includes('archive_appointment'),
      canChangeAssignee: this.$gate.roles.includes('Admin') ||  this.$gate.roles.includes('Sdr'),
      disabledDates: new Date().toISOString(),
      disabledCloseDate: {
        from :  new Date()
      },
      dangerClass: {
        "danger": true
      },
      object: {},
      filters: {
        appointmentStartDate: '',
        appointmentEndDate: '',
        prospectName: '',
        prospectEmail: '',
        source: '',
        product: '',
        closeStartDate: '',
        closeEndDate: '',
        assignee: '',
        outcome: '',
        type:'All'
      },
      dateRange: {
        startDate: null,
        endDate: null
      },
      closeDateRange: {
        startDate: null,
        endDate: null
      },
      opens: 'right',
      showDropdowns: true,
      singleDatePicker: "range"
    };
  },
  mounted() {
    this.getOutcomeStatus();
    this.getSources();
    this.getProducts();
    this.getAssignees();
  },
  created(){
    this.loading = true;
    this.get_table_filtered_columns()
    this.values = this.columns
    this.filters.outcome = this.$route.query.outcome ? this.$route.query.outcome : '';
    
  },
  computed:{
    filteredData:{
      
      get(){
        return this.columns.filter(column => this.get_filtered_columns.includes(column.name))
      },
      set(){
        return this.columns.filter(column => this.get_filtered_columns.includes(column.name))
      }
    }
  },
  watch: {
    '$route.query.outcome': function (outcome) {
      this.filters.outcome = outcome
      this.getLeads()
    },
    $route(to,from){
      if(from.name == 'showlead'){
        this.getLeads()
      }
    }
  },
  methods: {
    dispatchAction(val){
      this.get_filtered_columns.push(val.name)
      if(this.get_filtered_columns.length){
        this.col_pattern = false
      }
    },
    dispatchClose(val){
      let index = this.get_filtered_columns.findIndex(item => item == val.name)
      this.get_filtered_columns.splice(index,1)
      if(!this.get_filtered_columns.length){
        this.col_pattern = true
      }
    },
    async save_columns(){
      this.col_pattern = true
      try {
        const res = await axios.post('/admin/save_columns',{
          'columns' : this.get_filtered_columns
        })
        if(res){
          this.$toaster.success('Pattern saved successfully!')
          setTimeout(() => {
            this.col_pattern = false
          }, 2000);
        }
      } catch (error) {
        this.$toaster.error('Error in saving!')
        setTimeout(() => {
            this.col_pattern = false
          }, 2000);
      }
    },
    async get_table_filtered_columns(){
      try {
        
        const response = await axios.get('/admin/get_filtered_columns')
        if(response.data.filtered_data == null){
          this.columns.forEach((val, index) => {
            this.get_filtered_columns.push(val.name)
          })
        }
        else{
          this.get_filtered_columns = response.data.filtered_data.values
        }
        
      } catch (error) {
        console.log(error)
      }
    },
    fixDate(event){
        return moment(event).format('YYYY-MM-DD');
    },
    changed(e,item,label){
      if(e.target.checked == false){
        let index = this.checkedlist.findIndex(x => x[label] === item)
        this.checkedlist.splice(index,1)
      }
      if(e.target.checked == true){
        this.checkedlist.push({[label]: item}) 
      }
    },
    async handleLeadsCsv(event){
      try {
        event.target.disabled = true
        
        const response = await axios.post('admin/handleLeadsCsv',{
          'type': this.filters.type
        })
        this.$toaster.success('Downloading in progress...')
        if(response){
          let blob = new Blob([response.data], { type: 'text/csv' })
          let link = document.createElement('a')
          link.href = window.URL.createObjectURL(blob)
          link.download = 'export_appointments.csv'
          link.click()
        }
        event.target.disabled = false
      } catch (error) {
        event.target.disabled = false
        this.$toaster.error('Error in downloading!')
      }
    },
    async handleSubForm(event){
      try {
        event.target.disabled = true
        const res = await axios.post('admin/saveSubOutcomeForm',{
          'inputs' : this.suboutcome_form,
          'outcome_id': this.comment.outcome_id,
          'lead_id' : this.comment.lead_id,
          'user_id' : this.$gate.user.id,
          'checked_values' : this.checkedlist,
          'task_role' : this.form.task_role,
          'due_days' : this.form.due_days,
          'task_label' : this.form.task_label
        })
  
        this.getLeads()
        this.suboutcome_form = {}
        this.checkedlist = []
        
        this.$toaster.success("Outcome changed successfully!");
        event.target.disabled = false
        $("#commentModal").modal("hide");
        bus.$emit('data_changed','anydata')
      } catch (error) {
        this.$toaster.error("No inputs found. Please try again!");
        event.target.disabled = false
      }
    },
    async fetchForm(){
      try {
        
        const response = await axios.post('admin/fetchForm',{
          outcome_id: this.comment.outcome_id,
          // lead_id: this.comment.lead_id
        })
        if(response){
          this.checkedlist = []
          this.form = {}
          this.suboutcome_form = {}
          this.form = response.data.suboutcome
          
        }
        
      } catch (error) {
        console.log(error)
      }
    },
    remove_Cookie() {
      Cookies.remove("lead_id");
      Cookies.remove("lead_data");
    },
    
    handleComment(lead_id) {
      this.comment.lead_id = lead_id;
    },

    getLeads() {
      this.$refs.leadTable.getData();
    },
    getClients() {
      axios
        .get("admin/getClients")
        .then((response) => {
          this.clients = response.data.results;
        })
        .catch((e) => console.log(e));
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
      axios
        .get("admin/getAllProducts")
        .then((response) => {
          this.products = response.data.results;
        })
        .catch((e) => console.log(e));
    },
    getOutcomeStatus() {
      axios
        .get("admin/getAllLeadStatus")
        .then((response) => {
          setTimeout(() => {
            this.loading = false;
          }, 500);
          this.outcomeFields = response.data.results;
        })
        .catch((e) => console.log(e));
    },
    getSources(){
      axios.get('admin/getAllLeadSources')
      .then(response => {
        this.sources = response.data.results
      })
      .catch(e => console.log(e))
    },
    setActive(component, id = null) {
      //get all the keys of active object and make them false

      Object.keys(this.active).forEach((key) => (this.active[key] = false));
      this.active[component] = true;
    },
    refreshPage() {
      this.setActive("dashboard");
    },
    reloadWithoutFlash() {
      this.team = null;
      this.setActive("dashboard");
      this.getLeads();
    },
    flashMessageandReload(event) {
      this.refreshPage();
      this.$toaster.success(event);
    },
    flashErrors(errors) {
      for (const [key, value] of Object.entries(errors)) {
        for (let item in value) {
          this.errors.push(value[item]);
        }
      }
    },
    dataTableEvent(payload, id, data) {
      if (payload == "restore") {
        this.restore(id);
      }
      if (payload == "edit") {
        this.updateLead(id, data);
      }
      if (payload == "deleteLead") {
        this.deleteLead(id);
      }
      if (payload == "changeStatus") {
        this.comment.outcome_id = "";
        this.comment.lead_id = id;
        this.comment.outcome_id = data.outcome;
        this.fetchForm()
        $("#commentModal").modal("show");
      }
    },
    deleteLead(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to do this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          axios
            .delete("/admin/deleteLead/" + id)
            .then(({ data }) => {
              if (data.status == "success") {
                this.getLeads();
                Swal.fire("Deleted!", "Appointment has been deleted.", "success");
              } else {
                Swal.fire("Failed!", "Something went wrong.", "warning");
              }
            })
            .catch(({ data }) => {
              Swal.fire("Failed!", "Something went wrong.", "warning");
            });
        }
      });
    },
    restore(id){
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
          axios
            .post("/admin/restore_appointment/" + id)
            .then(({ data }) => {
              if (data.status == "success") {
                this.getLeads();
                Swal.fire("Restored!", "Appointment has been restored.", "success");
              } else {
                Swal.fire("Failed!", "Something went wrong.", "warning");
              }
            })
            .catch(({ data }) => {
              Swal.fire("Failed!", "Something went wrong.", "warning");
            });
        }
      });
    },
    updateLead(id, data) {
      this.data.leadId = id;
      this.data.source = data.source;
      this.data.product_id = data.product_id;
      this.data.pitch = data.pitch;
      this.data.revenue = data.revenue;
      this.data.cash_collected = data.cash_collected;
      this.data.deposit = data.deposit;
      this.data.outcome = data.outcome;
      this.data.appointment_date = moment.utc(data.appointment_date).local().toISOString();
      this.data.date_closed = data.date_closed
        ? moment.utc(data.date_closed).local().toISOString()
        : "";

      this.disabledCloseDate.to = new Date(this.data.appointment_date);
      this.data.client_id = data.client_id;
      this.data.assigned_to = data.assigned_to;
      this.getClients();
      this.getAssignees();
      this.getProducts();
      this.getSources();
      this.getOutcomeStatus();
      $("#leadModal").modal("show");
    },
    storeUpadatedLead() {
      this.remove_Cookie();
      
      this.errors = [];
      axios
        .post("admin/updateLead/" + this.data.leadId, {
          source: this.data.source,
          product_id: this.data.product_id,
          pitch: this.data.pitch,
          revenue: this.data.revenue,
          cash_collected: this.data.cash_collected,
          deposit: this.data.deposit,
          outcome: this.data.outcome,
          appointment_date: this.data.appointment_date,
          client_id: this.data.client_id,
          date_closed: this.data.date_closed,
          assigned_to: this.data.assigned_to,
        })
        .then((response) => {
          $("#leadModal").modal("hide");
          this.data.leadId = "";
          this.data.source = "";
          this.data.product = "";
          this.data.pitch = "";
          this.data.revenue = "";
          this.data.cash_collected = "";
          this.data.deposit = "";
          this.data.outcome = "";
          this.data.appointment_date = "";
          this.data.client_id = "";
          this.data.date_closed = "";
          this.getLeads();
          this.$toaster.success('Appointment updated successfully!')
        })
        .catch((errors) => {
          if (errors.response.status === 422) {
            this.flashErrors(errors.response.data.errors);
          }
        });
    },
    highlightedRow(item){
      return !item.pitch && !item.deposit && !item.revenue && !item.cash_collected
    },
    clicked(id,data){
      this.$router.push('/showlead/' + id);
      // this.updateLead(id, data)
    },
    clearFilter(){
      this.filters.appointmentStartDate  = '';
      this.filters.appointmentEndDate  = '';
      this.filters.prospectName  = '';
      this.filters.prospectEmail  = '';
      this.filters.source  = '';
      this.filters.product  = '';
      this.filters.closeStartDate  = '';
      this.filters.closeEndDate  = '';
      this.filters.assignee  = '';
      this.filters.outcome  = '';
      this.dateRange.endDate  = null;
      this.dateRange.endDate  = null;
      this.closeDateRange.endDate  = null;
      this.closeDateRange.endDate  = null;
    },
    updateDateRange(){
      this.filters.appointmentStartDate = moment(this.dateRange.startDate).format()
      this.filters.appointmentEndDate = moment(this.dateRange.endDate).format()
    },
    updateCloseDateRange(){
      this.filters.closeStartDate = moment(this.closeDateRange.startDate).format()
      this.filters.closeEndDate = moment(this.closeDateRange.endDate).format()
    },
    openNav() {
      document.getElementById("mySidebar").classList.add("open");
      document.getElementById("mySidebar").style.width = "400px";
    },
    closeNav() {
      document.getElementById("mySidebar").classList.remove("open");
      document.getElementById("mySidebar").style.width = "0";
    },
  },
};
</script>
<style scoped>
/* modal backdrop fix */
.modal:nth-of-type(even) {
  z-index: 1052 !important;
}
.modal-backdrop.show:nth-of-type(even) {
  z-index: 1051 !important;
}
.radio, .check{
 margin-right:5px;
}


</style>