<template>
    <div class="form">
        <form @submit.prevent="login(credentials)">
            <md-card >
                <md-field>
                    <label for="username">User Name</label>
                    <md-input name="username" id="username" v-model="credentials.email"></md-input>
                    <!-- <span class="md-error" v-if="!$v.form.requester.required">This field is required</span> -->                
                </md-field>
                <md-field>
                    <label for="password">Requester</label>
                    <md-input name="password" id="password" v-model="credentials.password"></md-input>
                    <!-- <span class="md-error" v-if="!$v.form.requester.required">This field is required</span> -->
                </md-field>
                <md-card-actions>
                    <md-button class="md-primary">Close</md-button>
                    <md-button type="submit" class="md-primary">Login</md-button>
                </md-card-actions>
            </md-card>
        </form>
    </div>
</template>

<script>

export default {
  name: 'login',
  components:{
      logged: false
  },

  data() {
    return {
      credentials:{
          email: null,
          password: null
      }
    }
  },
  methods: {
      
    login(){
        var app = this
        this.$auth.login({
            params: {
                email: app.credentials.email,
                password: app.credentials.password
            }, 
            success: function () {},
            error: function (resp) {
                console.log(resp);
                // alert(resp.response.data.msg)
            },
            rememberMe: true,
            redirect: '/dash',
            fetchUser: true,
        });             
        // this.$http({
        //     method: 'post',
        //     url: 'auth/login',
        //     body: {email: app.credentials.email, password: app.credentials.password},
        //     rememberMe: true,
        //     fetchUser: true
        // })
        // .then(function () {console.log('gj')},
        // console.log('bj'));
        // alert(resp.response.body.msg)
    },
    herp(){console.log(this)}
  }
}
</script>

<style>
        .form{
            margin-top: 60px;
            margin-left: 30px;
            margin-right: 30px;
            margin-bottom: 60px;
        }
</style>
