<template>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">403 Unauthorized Page</h1>
                    </div>
                    <div class="col-sm-6"></div>
                </div>
            </div>
        </div>
        <div class="content">
          <div class="error-page">
            <h2 class="headline text-danger"> 403</h2>
            <div class="error-content">
              <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Page Unauthorized.</h3>
              <p>
                You are not authorized the page you were looking for.
                Meanwhile, you may <router-link to="/dashboard">return to dashboard</router-link>
              </p>
            </div>
          </div>
        </div>
        
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
