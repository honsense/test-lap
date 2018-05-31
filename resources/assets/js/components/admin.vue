<template>
    <div>
        <v-container grid-list-md>
            <v-layout >
                <v-flex xs10 offset-xs1>
                    <v-jumbotron>
                        <div class="alert alert-danger" v-if="error">
                            <p>There was an error, unable to sign in with those credentials.</p>
                        </div>
                        <div class="alert alert-success" v-if="success">
                            <p>Success.</p>
                        </div>
                        <v-form ref="form" lazy-validation v-model="valid" >
                            <v-text-field 
                            v-model="username" 
                            label="Username" 
                            required
                            :rules="[v => !!v || 'username is required!']"
                            ></v-text-field>

                            <v-btn 
                            @click="passwordReset"
                            :disabled="!valid"
                            large class="mx-0" 
                            color="primary"
                            >Reset</v-btn>
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
                error: false,
                valid: true,
                success: false,
            }
        },

        mounted(){
        },

        methods: {
            passwordReset(){
                if (this.$refs.form.validate()){
                    var app = this
                    this.$http.post('passwordReset', {username: app.username}
                    )
                    .then(res =>
                    {
                        if(res.status = 200){
                            app.success = true;
                            app.$refs.form.reset();
                        }
                    })
                    .catch(e => {error = true});
                }
            },
        }
    }   
</script>