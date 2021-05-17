<template>
  <div>
    <section class="content" >
      <div class="container-fluid">
     

        <div class="row">
          <div class="col-md-3" v-if="userCard">
            <router-link class="nav-link info-box text-dark" to="/users">
              <span class="info-box-icon bg-danger"
                ><i class="far fa-user"></i
              ></span>

              <div class="info-box-content">
                <span class="info-box-text">{{ usersCount }}</span>
                <span class="info-box-number">Users</span>
              </div>
            </router-link>
          </div>
          
          <div class="col-md-3" >
            <router-link class="nav-link info-box text-dark" to="/users">
              <span class="info-box-icon bg-success"
                ><i class="fas fa-chalkboard-teacher"></i
              ></span>

              <div class="info-box-content">
                <span class="info-box-text">0</span>
                <span class="info-box-number">Users</span>
              </div>
            </router-link>
          </div>
          
          <div class="col-lg-3">
            <router-link class="nav-link info-box text-dark" to="/users">
              <span class="info-box-icon bg-warning"
                ><i class="fas fas fa-tasks"></i
              ></span>

              <div class="info-box-content">
                <span class="info-box-text">0</span>
                <span class="info-box-number">Users</span>
              </div>
            </router-link>
          </div>
          
          <div class="col-lg-3" >
            <router-link class="nav-link info-box text-dark" to="/users">
              <span class="info-box-icon bg-info"
                ><i class="far fa-handshake"></i
              ></span>

              <div class="info-box-content">
                <span class="info-box-text">0</span>
                <span class="info-box-number">Users</span>
              </div>
            </router-link>
          </div>


          <div class="col-lg-3">
            <div class="nav-link info-box text-dark">
              <span class="info-box-icon bg-danger"
                ><i class="fas fas fa-wallet"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text">$ 2333</span>
                <span class="info-box-number">Cash collected</span>
              </div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="nav-link info-box text-dark">
              <span class="info-box-icon bg-success"
                ><i class="fas fa-credit-card"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text">$ 1000000</span>
                <span class="info-box-number">Revenue</span>
              </div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="nav-link info-box text-dark">
              <span class="info-box-icon bg-warning"
                ><i class="fas fa-coins"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text">$ 0</span>
                <span class="info-box-number">Deposit</span>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
      </div>
      <!--/.container-fluid -->
    </section>
  </div>
</template>

<script>
import moment from "moment";

export default {
  components: {},
  created() {
    this.loading = true;
    this.getUsersCount();
  },
  mounted() {
    this.showWelcomeModal()
  },
  data() {
    return {
      usersCount: 0,
      loading: false,
      userCard: this.$gate.permissions.includes('list_users'),
    };
  },
  methods: {
    getUsersCount() {
      axios
        .get("admin/usersCount?filterBy="+this.filter)
        .then((response) => {
          this.usersCount = response.data.results;
        })
        .catch((e) => console.log(e));
    },
    async getRandomQuote() {
      var apiUrl = "https://type.fit/api/quotes"
      let response = await fetch(apiUrl,{
            mode: 'cors',
            headers: {
              'Content-Type': 'application/json',
            }
        })
      return await response.json()
    },
    async generateQuote() {
      var randomQuote = await this.getRandomQuote()

      var now = new Date();
      var hrs = now.getHours();
      var msg = "";

      if (hrs >=  0 && hrs < 12) msg = "Good morning";
      if (hrs >= 12 && hrs < 17) msg = "Good afternoon";
      if (hrs >= 17) msg = "Good evening";
      if(randomQuote.length > 0){
        var quote = _.shuffle(randomQuote)[0];
        this.quote = quote.text
        this.quoteAuthor = quote.author
        Swal.fire({
          title: '<h4 class="quote-title">'+msg+' '+this.$gate.user.name+'</h4>',
          html: '<h5>Thought of the day</h5><p>"'+this.quote+'"</p><p style="font-style: italic;"> - '+this.quoteAuthor+'</p>',
          showCloseButton: true,
          showConfirmButton: false
        })
     }
    },
    showWelcomeModal(){
      if(localStorage){
        let nextPopup = localStorage.getItem('isModalVisible')
        var today = moment();
        var tomorrow = moment(today).add(1, 'days');
        
        if(nextPopup){

          if(moment(nextPopup) <= today){
            this.generateQuote()
            localStorage.setItem('isModalVisible', tomorrow)
          }

        }else{
          this.generateQuote()
          localStorage.setItem('isModalVisible', tomorrow)
        }
      }else{
        this.generateQuote()
        localStorage.setItem('isModalVisible', tomorrow)
      }
    },
  },
};
</script>