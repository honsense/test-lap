<template>
    <div>
        <v-container grid-list-md>
            <v-layout >
                <v-flex xs10 offset-xs1>
                    <v-jumbotron>
                        <div class="alert alert-danger" v-if="error">
                            <p>There was an error, unable to sign in with those credentials.</p>
                        </div>
                        <v-form ref="form" lazy-validation v-model="valid" >
                            <v-text-field 
                            v-model="username" 
                            label="Username" 
                            required
                            :rules="[v => !!v || 'username is required!']"
                            ></v-text-field>

                            <v-text-field 
                            v-model="password" 
                            label="password" 
                            :type="'password'" 
                            required
                            :rules="[v => !!v || 'password is required!']"
                            ></v-text-field>

                            <v-btn 
                            @click="login"
                            :disabled="!valid"
                            large class="mx-0" 
                            color="primary"
                            >Login</v-btn>
                        </v-form>
                    </v-jumbotron>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                username: '',
                password: '',
                error: false,
                valid: true,
            }
        },

        mounted(){
        },

        methods: {
            login(){
                if (this.$refs.form.validate()){
                    var app = this
                    this.$auth.login({
                        params: {
                            username: app.username,
                            password: app.password
                        }, 
                        success: function () {},
                        error: function (resp) {
                            this.error = true;
                            // alert(resp.response.data.msg)
                        },
                        rememberMe: true,
                        redirect: '/dashboard',
                        fetchUser: true,
                    });
                }
            },
        }
    }   
</script>